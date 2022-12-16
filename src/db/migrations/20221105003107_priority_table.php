<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class PriorityTable extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('priority', ['id' => false, 'primary_key' => 'id']);
        $table
            ->addColumn('id', 'integer', ['signed' => false, 'identity' => false, 'null' => false])
            ->addColumn('description', 'string', ['limit' => 20, 'null' => false])
            ->addColumn('color', 'string', ['limit' => 7])
            ->save();
    }

    public function down()
    {
        $this->table('priority')->drop()->save();
    }
}
