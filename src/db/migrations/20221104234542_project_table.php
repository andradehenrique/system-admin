<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class ProjectTable extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('project', ['id' => false, 'primary_key' => 'id']);
        $table
            ->addColumn('id', 'integer', ['signed' => false, 'identity' => false, 'null' => false])
            ->addColumn('description', 'string', ['limit' => 40, 'null' => false])
            ->save();
    }

    public function down()
    {
        $this->table('project')->drop()->save();
    }
}
