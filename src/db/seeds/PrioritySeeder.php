<?php


use Phinx\Seed\AbstractSeed;

class PrioritySeeder extends AbstractSeed
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
                'description' => 'Alta',
                'color' => '#F44336'
            ],
            [
                'id' => 2,
                'description' => 'Normal',
                'color' => '#FFC107'
            ],
            [
                'id' => 3,
                'description' => 'Baixa',
                'color' => '#4CAF50'
            ],
        ];

        $priority = $this->table('priority');
        $priority->insert($data)
            ->saveData();
    }
}
