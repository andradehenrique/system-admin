<?php

declare(strict_types=1);

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

final class IssueTable extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('issue', ['id' => false, 'primary_key' => 'id']);
        $table
            ->addColumn('id', 'integer', ['signed' => false, 'identity' => true, 'null' => false])
            ->addColumn('title', 'string', ['null' => false])
            ->addColumn('description', 'text', ['limit' => MysqlAdapter::TEXT_MEDIUM, 'null' => false])
            ->addColumn('solution', 'text', ['limit' => MysqlAdapter::TEXT_MEDIUM, 'null' => true])
            ->addColumn('project_id', 'integer', ['signed' => false, 'null' => false])
            ->addColumn('priority_id', 'integer', ['signed' => false, 'null' => false])
            ->addColumn('category_id', 'integer', ['signed' => false, 'null' => false])
            ->addColumn('status_id', 'integer', ['signed' => false, 'null' => false])
            ->addColumn('assigned_id', 'integer', ['null' => true])
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addColumn('related_issue_id', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('finished_at', 'timestamp')
            ->addTimestamps()
            ->addForeignKey('project_id', 'project', ['id'])
            ->addForeignKey('priority_id', 'priority', ['id'])
            ->addForeignKey('category_id', 'category', ['id'])
            ->addForeignKey('status_id', 'status', ['id'])
            ->addForeignKey('assigned_id', 'system_user', ['id'])
            ->addForeignKey('user_id', 'system_user', ['id'])
            ->save();
    }

    public function down()
    {
        $table = $this->table('issue');
        $table->dropForeignKey('project_id')->save();
        $table->dropForeignKey('priority_id')->save();
        $table->dropForeignKey('category_id')->save();
        $table->dropForeignKey('status_id')->save();
        $table->dropForeignKey('assigned_id')->save();
        $table->dropForeignKey('user_id')->save();
        $table->drop()->save();
    }
}
