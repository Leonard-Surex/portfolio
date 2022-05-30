<?php
    namespace Core\Database;

    use Core\Database\Database;
    use App\Models\DataProperty;
    use App\Models\DataModel;

    class TableValidator
    {
        public static function checkTable(DataModel $model)
        {
            $table = $model->getTable();
            $columns = $model->getColumns();

            // Table does not exist
            if (!static::tableExists($table)) {
              static::newTable($table);
            }

            $queryDescribe = "DESCRIBE " . $table . ";";
            $fields = Database::Query($queryDescribe, []);

            // Validate Columns
            $columns->forEach(function($column) use ($table, $fields) {
                if (!static::checkColumn($column, $fields)) {
                    static::rebuildColumn($table, $column, static::columnExists($column, $fields));
                }
            });            
        }

        public static function tableExists($table): bool
        {
            $query = "SHOW TABLES LIKE '{$table}';";
            $likeTable = Database::Query($query, []);
            return count($likeTable) === 0 ? false : true;
        }

        private function rebuildColumn(string $table, DataProperty $column, bool $update)
        {
            $query = "ALTER TABLE {$this->table}";
            $query .= ($update ? ' MODIFY COLUMN ' : ' ADD ');
            $query .= static::buildColumnQuery($column);
            $query .= ';';
            Database::Query($query, []);
        }

        private static function newTable(string $table)
        {
            $tableQuery = "CREATE TABLE IF NOT EXISTS {$table} (";
            $columns->forEach(function($column) use (&$tableQuery) {
                $tableQuery .= static::buildColumnQuery($column);
            });
            $tableQuery .= ");";
            Database::Query($tableQuery, []);
        }

        private function buildColumnQuery(DataProperty $column)
        {
            $query = "{$column->name} {$column->type}";
            $column->attributes->forEach(function($value, $key) use (&$query) {
                $query .= " {$value} {$key}";
            });

            return $query;
        }

        private static function columnExists(DataProperty $column, array $fields): bool
        {
            foreach ($fields as $field)
            {
                if ($field['Field'] == $column->name) {
                    return true;
                }
            }
            return false;
        }

        private static function checkColumn(DataProperty $column, array $fields): bool
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
        
    }