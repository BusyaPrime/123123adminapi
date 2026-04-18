<?php


use Illuminate\Database\Seeder;

class LoadTypesTableSeeder extends Seeder
{
    private $data = [
        'Тяжелая',
        'Открытая'
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $index = 1;
        foreach ($this->data as $item) {
            DB::table('load_types')->insert([
                [
                    'id' => $index,
                    'priority' => $index,
                ]
            ]);
            DB::table('load_type_translations')->insert([
                [
                    'load_type_id' => $index,
                    'title' => $item,
                    'locale' => 'ru',
                ]
            ]);
            $index++;
        }
    }
}
