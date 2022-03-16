<?php


use Phinx\Seed\AbstractSeed;

class ExampleSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function run()
    {
        $faker = Faker\Factory::create();
        $data = [
            [
                'name' => 'José Geraldino',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus et lorem purus. Nullam egestas egestas volutpat. Sed pellentesque neque id odio venenatis'
            ],
            [
            'name' => 'Maria Geraldina',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus et lorem purus. Nullam egestas egestas volutpat. Sed pellentesque neque id odio venenatis'
        ]
        ];

        $this->table('example_table_phinx')->insert($data)->saveData();
    }
}
