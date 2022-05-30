<?php
    namespace Core\Database;

    interface IDatabase {
        static function Query(string $query, array $params);
    }