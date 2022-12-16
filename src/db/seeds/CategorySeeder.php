<?php


use Phinx\Seed\AbstractSeed;

class CategorySeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function run(): void
    {
        $data = [
            [
                'id' => 1,
                'description' => 'Bug'
            ],
            [
                'id' => 2,
                'description' => 'Feature'
            ],
            [
                'id' => 3,
                'description' => 'Débito técnico'
            ],
            [
                'id' => 4,
                'description' => 'Tarefa'
            ],
            [
                'id' => 5,
                'description' => 'Suporte'
            ],
        ];

        $category = $this->table('category');
        $category->insert($data)
            ->saveData();
    }
}
