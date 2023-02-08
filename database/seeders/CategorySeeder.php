<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $categories = [
            'Makanan',
            'Minuman',
        ];

        foreach ($categories as $category) {
            DB::table('category')->insert([
                'name' => $category,
                'enable' => true,
            ]);
        }
        
    }
}
