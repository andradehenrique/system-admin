<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class IssueFileTable extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('issue_file', ['id' => false, 'primary_key' => 'id']);
        $table
            ->addColumn('id', 'integer', ['signed' => false, 'identity' => false, 'null' => false])
            ->addColumn('issue_id', 'integer', ['signed' => false, 'null' => false])
            ->addColumn('file_path', 'string', ['null' => false])
            ->addForeignKey('issue_id', 'issue', ['id'])
            ->save();
    }

    public function down()
    {
        $table = $this->table('issue_file');
        $table->dropForeignKey('issue_id')->save();
        $table->drop()->save();
    }
}
