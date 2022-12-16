<?php


use Phinx\Seed\AbstractSeed;

class StatusSeeder extends AbstractSeed
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
                'description' => 'Novo',
                'color' => '#8BC34A',
                'order_number' => 1,
                'final_status' => 0
            ],
            [
                'id' => 2,
                'description' => 'Em andamento',
                'color' => '#00BCD4',
                'order_number' => 2,
                'final_status' => 0
            ],
            [
                'id' => 3,
                'description' => 'Finalizado',
                'color' => '#9E9E9E',
                'order_number' => 3,
                'final_status' => 1
            ],
            [
                'id' => 4,
                'description' => 'Cancelado',
                'color' => '#607D8B',
                'order_number' => 10,
                'final_status' => 1
            ]
        ];

        $status = $this->table('status');
        $status->insert($data)
            ->saveData();
    }
}
