<?php
    namespace App\Models;

    use Core\Database\IDatabase;
    use Core\Database\Database;
    use Core\Collections\Collection;

    use App\Models\DataConnectionModel;
    use App\Models\DataProperty;

    abstract class DataModel {
        protected string $table;

        private IDatabase $database;
        private Collection $columns;

        private array $fields;

        public function __construct(IDatabase $database)
        {
            $this->database = $database;                    
            $this->columns = new Collection();
        }

        public function __get($name) {
            $this->columns->forEach(function($column) use ($name) {
                if ($column->name == $name) {
                    if (isset($column->joinTable)) {
                        // Many to Many
                        // database, DataProperty $leftProperty, DataProperty $rightProperty, string $table
                        
                        $dataConnection = new DataConnectionModel($this->database, $column->target, $column->source, $column->joinTable);
                        echo "------<br/>";
                        $connections = $dataConnection->fetchAll("{$column->target->name} = ?", [$this->{$column->target->property}]);
                        echo json_encode($connections) . '<br />';

                    } else if (isset($column->target)) {
                        // Referal
                        if (isset($column->target->property)) {
                            $class = new \ReflectionClass(get_class($column->target->parent));
                            return $class->fetchAll($column->target->name . " = ?;", $this->{$column->property})->first();                            
                        }
                    } else {
                        // Non Referal
                        return $this->{$column->property};
                    }
                }
            });
        }

        public function fetchAll(string $condition, array $values): Collection {
            $all = new Collection();

            $query = "SELECT * FROM {$this->table} WHERE {$condition};";
            $results = $this->database->query($query, $values);

            foreach ($results as $result)
            {
                
                $model = new static($this->database);
                $this->columns->forEach(function($column) use (&$model, $result) {
                    if (!$column->connection) {
                        $model->{$column->property} = $result[$column->name];
                    }
                });
                $all->add($model);
            }

            return $all;
        }

        public function column(string $name, string $property=null): DataProperty
        {
            if ($property == null) {
                $property = $name;
            }
            $dataProperty = new DataProperty($name, $this, $property, $this);
            $this->columns->add($dataProperty);
            return $dataProperty;
        }

        public function getProperty(string $name): DataProperty
        {
            return $this->columns->where(fn ($column) => $column->name == $name)->first();
        }

        protected function checkTable(): bool
        {
            if ($this->tableExists()) {
                if ($this->tableExpired()) {
                    $this->rebuildTable();
                } else {
                    // Table exists and is valid
                    return true;
                }    
            } else {
                $this->newTable();
            }
            return false;
        }

        protected function tableExpired(): bool
        {
            $queryDescribe = "DESCRIBE " . $this->table . ";";
            $fields = $this->database->query($queryDescribe, []);
            $this->fields = $fields;
            $expired = false;

            $this->columns->forEach(function ($column) use ($fields, &$expired)
            {
                if (!$expired) {
                    $expired = (!$this->checkColumn($column, $fields));
                    if ($expired) {
                        $this->checkColumnMsg($column, $fields);
                    }
                }
            });

            return $expired;
        }        

        protected function rebuildTable()
        {  
            $this->columns->forEach(function ($column) {
                // is column expired
                if (!$this->checkColumn($column, $this->fields) && !$column->connection) {
                    // Does column exist
                    $found = false;
                    foreach($this->fields as $field)
                    {
                        if (!$found && $column->name === $field['Field']) {
                            $found = true;
                        }
                    }                    
                    $this->rebuildColumn($column, $found);
                }
            });
        }

        private function tableExists(): bool
        {
            $query = "SHOW TABLES LIKE '{$this->table}';";
            $likeTable = $this->database->query($query, []);

            return count($likeTable) === 0 ? false : true;
        }

        private function newTable() {
            $query = "CREATE TABLE IF NOT EXISTS {$this->table}(";
            $this->columns->forEach(function($column, $index) use (&$query) {
                if ($index != 0) {
                    $query .= ", ";    
                }
                if (!$column->connection) {
                    $query .= $this->rebuildColumnQuery($column, false);
                }    
            });
            $query .= ");";
            $this->database->query($query, []);
        }

        private function rebuildColumnQuery(DataProperty $column)
        {
            $query = "{$column->name} {$column->type}";
            $column->attributes->forEach(function($value, $key) use (&$query) {
                $query .= " {$value} {$key}";
            });

            return $query;
        }

        private function rebuildColumn(DataProperty $column, bool $update=true)
        {
            $query = "ALTER TABLE {$this->table}";
            if ($update) {
                $query .= ' MODIFY COLUMN ';
            } else {
                $query .= ' ADD ';
            }
            $query .= $this->rebuildColumnQuery($column, $update);
            $query .= ';';

            $this->database->query($query, []);
        }

        private function checkColumn(DataProperty $column, array $fields): bool
        {
            foreach ($fields as $field)
            {
                if ($field['Field'] == $column->name) {
                    return 
                            (strtolower($field['Type']) !== strtolower($column->type)) ? false :                            
                            (($field['Null'] === 'YES' && $column->getAttribute('NULL') === 'NOT') ? false :
                            (($field['Null'] === 'NO' && $column->getAttribute('NULL') === null) ? false :
                            (($field['Key'] ===  'PRI' && $column->getAttribute('KEY') !== 'PRIMARY') ? false :
                            (($field['Key'] !==  'PRI' && $column->getAttribute('KEY') === 'PRIMARY') ? false :
                            (($field['Default'] !== $column->getAttribute('DEFAULT')) ? false :
                            (($field['Extra'] === 'auto_increment' && $column->getAttribute('AUTO_INCREMENT') === null) ? false :
                            (($field['Extra'] !== 'auto_increment' && $column->getAttribute('AUTO_INCREMENT') !== null) ? false :
                            true)))))));
                }
            }

            return false;
        }

        private function checkColumnMsg(DataProperty $column, array $fields): bool
        {
            foreach ($fields as $field)
            {
                if ($field['Field'] == $column->name) {
                    if (strtolower($field['Type']) !== strtolower($column->type)) echo "Type mismatch.<br/>";                            
                    if ($field['Null'] === 'YES' && $column->getAttribute('NULL') === 'NOT') echo "Null mismatch.<br/>";
                    if ($field['Null'] === 'NO' && $column->getAttribute('NULL') === null) echo "Null mismatch.<br/>";
                    if ($field['Key'] ===  'PRI' && $column->getAttribute('KEY') !== 'PRIMARY') echo "Key mismatch.<br/>";
                    if ($field['Key'] !==  'PRI' && $column->getAttribute('KEY') === 'PRIMARY') echo "Key mismatch.<br/>";
                    if ($field['Default'] !== $column->getAttribute('DEFAULT')) echo "Default mismatch.<br/>";
                    if ($field['Extra'] === 'auto_increment' && $column->getAttribute('AUTO_INCREMENT') === null) echo "Extra mismatch.<br/>";
                    if ($field['Extra'] !== 'auto_increment' && $column->getAttribute('AUTO_INCREMENT') !== null) echo "Extra mismatch.<br/>";
                }
            }

            return false;
        }
    }