<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ActionTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define the action types employee can perform on cusotmers
        $actionTypes = [
            'call' => 'Call the customer',
            'visit' => 'Visit the customer',
            'follow_up' => 'Follow up with the customer',
        ];

        foreach ($actionTypes as $name => $description) {
            \DB::table('action_types')->insert([
                'name' => $name,
                'description' => $description,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
