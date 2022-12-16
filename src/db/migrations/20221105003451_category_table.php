<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CategoryTable extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('category', ['id' => false, 'primary_key' => 'id']);
        $table
            ->addColumn('id', 'integer', ['signed' => false, 'identity' => false, 'null' => false])
            ->addColumn('description', 'string', ['limit' => 40, 'null' => false])
            ->addColumn('color', 'string', ['limit' => 7])
            ->save();
    }

    public function down()
    {
        $this->table('category')->drop()->save();
    }
}
