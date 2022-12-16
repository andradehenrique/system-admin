<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class ProjectMemberTable extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('project_member', ['id' => false, 'primary_key' => 'id']);
        $table
            ->addColumn('id', 'integer', ['signed' => false, 'identity' => false, 'null' => false])
            ->addColumn('project_id', 'integer', ['signed' => false, 'null' => false])
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addForeignKey('project_id', 'project', ['id'])
            ->addForeignKey('user_id', 'system_user', ['id'])
            ->save();
    }

    public function down()
    {
        $table = $this->table('project_member');
        $table->dropForeignKey('project_id')->save();
        $table->dropForeignKey('user_id')->save();
        $table->drop()->save();
    }
}
