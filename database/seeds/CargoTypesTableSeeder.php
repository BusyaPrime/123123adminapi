<?php


use Illuminate\Database\Seeder;

class CargoTypesTableSeeder extends Seeder
{
    private $data = [
        'Деревянный',
        'Стеклянный',
        'Скоропортящийся',
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
            DB::table('cargo_types')->insert([
                [
                    'id' => $index,
                    'priority' => $index,
                ]
            ]);
            DB::table('cargo_type_translations')->insert([
                [
                    'cargo_type_id' => $index,
                    'title' => $item,
                    'locale' => 'ru',
                ]
            ]);
            $index++;
        }
    }
}
