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
        DB::table('products')->insert([
            'name' => 'produk 1',
            'description' => 'deskripsi produk 1',
            'qty' => 4,
            'price' => 25000,
            'image' => 'ini gambar',
            'rating' => 5,
        ]);
    }
}