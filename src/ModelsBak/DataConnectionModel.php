<?php
    namespace App\Models;

    use App\Models\DataModel;
    use Core\Database\IDatabase;

    class DataConnectionModel extends DataModel
    {
        public int $connectionId;

        public mixed $leftId;

        public mixed $rightId;

        public function __construct(IDatabase $database, DataProperty $leftProperty=null, DataProperty $rightProperty=null, string $table='connection')
        {
            parent::__construct($database);

            $this->table = $table;

            $this->column('connectionid', 'connectionId')
                    ->isType("int(11)")
                    ->primary()
                    ->autoIncrement()
                    ->notNull();

            $this->column($leftProperty->name, 'leftId')
                    ->isType($leftProperty->type)
                    ->refersTo($leftProperty)
                    ->notNull();

            $this->column($rightProperty->name, 'rightId')
                    ->isType($rightProperty->type)
                    ->refersTo($rightProperty)
                    ->notNull();
            
            if (ENVIRONMENT_CONFIG['mode'] == 'dev') {
                $this->checkTable();
            }
        }
    }