<?php


use Illuminate\Database\Seeder;

class TCarTypesTableSeeder extends Seeder
{
    private $data = [
        'Легковая',
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
            DB::table('tcar_types')->insert([
                [
                    'id' => $index,
                    'priority' => $index,
                ]
            ]);
            DB::table('tcar_type_translations')->insert([
                [
                    'tcar_type_id' => $index,
                    'title' => $item,
                    'locale' => 'ru',
                ]
            ]);
            $index++;
        }
    }
}
