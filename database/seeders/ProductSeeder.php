<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $categories_products = [
            'Makanan' => [
                'Nasi Goreng' => 'Nasi Goreng dengan Telur',
                'Nasi Uduk' => 'Nasi Uduk dengan Telur dan sambal'
            ],
            'Minuman'=> [
                'Es Jeruk' => 'Es Jeruk dingin',
                'Jus Strawberry' => 'Jus Strawberry dingin',
            ],
        ];

        foreach ($categories_products as $category => $products) {
            
            $category_id = DB::table('category')->where('name', $category)->first()->id;
            
            foreach ($products as $product_name => $product_desc) {
                $new_product_id = DB::table('product')->insertGetId([
                    'name' => $product_name,
                    'description' => $product_desc,
                    'enable' => true,
                ]);

                DB::table('category_product')->insert([
                    'product_id' => $new_product_id,
                    'category_id' => $category_id,
                ]);
            }
        }
    }
}
