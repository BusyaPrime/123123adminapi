<?php


use Illuminate\Database\Seeder;

class CarTypesTableSeeder extends Seeder
{
    private $data = [
        'Эвакуатор',
        'Каблук',
        'Портер',
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
            DB::table('car_types')->insert([
                [
                    'id' => $index,
                    'priority' => $index,
                ]
            ]);
            DB::table('car_type_translations')->insert([
                [
                    'car_type_id' => $index,
                    'title' => $item,
                    'locale' => 'ru',
                ]
            ]);
            $index++;
        }
    }
}
