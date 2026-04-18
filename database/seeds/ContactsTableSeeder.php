<?php

use App\Domain\Contacts\Models\Contact;
use Illuminate\Database\Seeder;

class ContactsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $keysAvailable = Contact::$keysAvailable;

        foreach ($keysAvailable as $item) {
            DB::table('contacts')->insert([
                [
                    'key' => $item,
                    'value' => null
                ]
            ]);
        }
    }
}
