<?php

use Illuminate\Database\Seeder;

class RegionsTableSeeder extends Seeder
{

    protected $geo = [];

    protected $data = [
        ['title' => 'Ташкент'],
        ['title' => 'Ташкентская область'],
        ['title' => 'Андижанская область'],
        ['title' => 'Бухарская область'],
        ['title' => 'Ферганская область'],
        ['title' => 'Джизакская область'],
        ['title' => 'Республика Каракалпакстан'],
        ['title' => 'Кашкадарьинская область'],
        ['title' => 'Хорезмская область'],
        ['title' => 'Наманганская область'],
        ['title' => 'Навоийская область'],
        ['title' => 'Самаркандская область'],
        ['title' => 'Сырдарьинская область'],
        ['title' => 'Сурхандарьинская область'],
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->geo = json_decode(file_get_contents((__DIR__).'/resources/geo.json'), true);
        foreach ($this->data as $i => $datum) {
            $polygon = [];
            if(isset($this->geo['features'][$i]['geometry']['coordinates'][0][0])) {
                foreach ($this->geo['features'][$i]['geometry']['coordinates'][0][0] as $c => $coordinate) {
                    $polygon[0][] = ['lat' => $coordinate[1], 'lng' => $coordinate[0]];
                }
            } else {
                $polygon[0] = [];
            }
            if(isset($this->geo['features'][$i]['geometry']['coordinates'][0][1])) {
                foreach ($this->geo['features'][$i]['geometry']['coordinates'][0][1] as $c => $coordinate) {
                    $polygon[1][] = ['lat' => $coordinate[1], 'lng' => $coordinate[0]];
                }
            } else {
                $polygon[1] = [];
            }

            DB::table('regions')->insert([
                [
                    'title' => $datum['title'],
                    'polygon' => json_encode($polygon),
                ]
            ]);
        }
    }
}
