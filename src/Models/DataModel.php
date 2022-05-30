<?php
    namespace App\Models;

    use Core\Collections\Collection;
    use Core\Database\Database;

    abstract class DataModel
    {
        protected static string $table;

        public static Collection $columns;

        public static function fetchAll(string $condition, array $values)
        {
            $all = new Collection();

            $query = 'SELECT * FROM ' . static::$table . ' WHERE ' . $condition . ';';
            $results = Database::query($query, $values);

            foreach ($results as $result)
            {                
                $model = new static();
                static::$columns->forEach(function($column) use (&$model, $result) {
                    $model->{$column->property} = $result[$column->name];
                });
                $all->add($model);
            }

            return $all;
        }

        public static function column(string $column, ?string $field=null): DataProperty
        {
            if (!isset(static::$columns)) {
                static::$columns = new Collection();
            }                        
            $column = new DataProperty($column, $field ?? $column, get_called_class());
            static::$columns->add($column);
            return $column;
        }
        
        public static function getColumns()
        {
            if (!isset(static::$columns)) {
                static::$columns = new Collection();
            }
            return static::$columns;
        }

        public static function getTable()
        {
            return static::$table;
        }
    }