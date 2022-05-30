<?php
    namespace Core\Database;

    use Core\Database\IDatabase;

    class Database implements IDatabase {
        private static string $serverName = DATABASE_CONFIG["serverName"];
        private static string $userName = DATABASE_CONFIG["userName"];
        private static string $password = DATABASE_CONFIG["password"];
        private static string $database = DATABASE_CONFIG["database"];

        private static $connection;

        public static function Query(string $query, array $params) {
            self::Connect();

            $statement = self::$connection->prepare($query);
            if (count($params) > 0) {
                $statement->execute($params);
            } else {
                $statement->execute();
            }
            
            return $statement->fetchAll();
        }

        private static function Connect() {         
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

            if (!isset(self::$connection)) {
                if (!function_exists('mysqli_init') && !extension_loaded('mysqli')) {
                    echo "php extension mysqli must be loaded.<br />";
                    die;
                }

                self::$connection = new \PDO("mysql:host=" . self::$serverName . ";dbname=" . self::$database . ";", self::$userName, self::$password);
            }
        }
    }