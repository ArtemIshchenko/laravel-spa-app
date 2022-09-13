<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Desk;
use App\Models\DeskList;
use App\Models\Card;
use App\Models\Task;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $desks = Desk::factory(2)->create();
        foreach ($desks as $desk) {
            $deskList = DeskList::factory(3)->create([
                'desk_id' => $desk->id,
            ]);
            foreach ($deskList as $list) {
                $cards = Card::factory(5)->create([
                    'desk_list_id' => $list->id,
                ]);
                foreach ($cards as $card) {
                    $tasks = Task::factory(7)->create([
                        'card_id' => $card->id,
                    ]);
                }
            }
        }
    }
}
