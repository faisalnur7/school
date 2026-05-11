<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventoryBooksSeeder extends Seeder
{
    public function run(): void
    {
        $books = json_decode(file_get_contents(base_path('plans/books_with_class_id.json')), true);

        $rows = array_map(fn($book) => [
            'category_id'          => 1,
            'item_type'            => 'classwise',
            'name'                 => $book['name'],
            'purchase_price'       => $book['price'] !== null ? $book['price'] - 30 : 0,
            'selling_price'        => $book['price'] ?? 0,
            'current_stock'        => 0,
            'minimum_stock_alert'  => 5,
            'unit'                 => 'pcs',
            'is_active'            => true,
            'school_class_id'      => $book['school_class_id'],
            'group_id'             => null,
            'created_at'           => now(),
            'updated_at'           => now(),
        ], $books);

        DB::table('inventory_items')->insert($rows);
    }
}
