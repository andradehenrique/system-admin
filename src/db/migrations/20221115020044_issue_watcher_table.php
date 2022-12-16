<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class IssueWatcherTable extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('issue_watcher', ['id' => false, 'primary_key' => 'id']);
        $table
            ->addColumn('id', 'integer', ['signed' => false, 'identity' => false, 'null' => false])
            ->addColumn('issue_id', 'integer', ['signed' => false, 'null' => false])
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addForeignKey('issue_id', 'issue', ['id'])
            ->addForeignKey('user_id', 'system_user', ['id'])
            ->save();
    }

    public function down()
    {
        $table = $this->table('issue_watcher');
        $table->dropForeignKey('issue_id')->save();
        $table->dropForeignKey('user_id')->save();
        $table->drop()->save();
    }
}
