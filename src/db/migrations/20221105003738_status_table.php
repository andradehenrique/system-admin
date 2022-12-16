<?php

declare(strict_types=1);

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

final class StatusTable extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('status', ['id' => false, 'primary_key' => 'id']);
        $table
            ->addColumn('id', 'integer', ['signed' => false, 'identity' => false, 'null' => false])
            ->addColumn('description', 'string', ['limit' => 40, 'null' => false])
            ->addColumn('color', 'string', ['limit' => 7])
            ->addColumn('final_status', 'boolean', ['default' => 0, 'null' => false])
            ->addColumn('order_number', 'tinyinteger', ['limit' => MysqlAdapter::INT_TINY, 'signed' => false, 'identity' => false, 'null' => false])
            ->save();
    }

    public function down()
    {
        $this->table('status')->drop()->save();
    }
}
