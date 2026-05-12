<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventoryBooksSeeder extends Seeder
{
    public function run(): void
    {
        $books = [
            ['name' => 'শৈশব বাংলা ব্যাকরণ (পাঞ্জেরী)', 'price' => 160, 'school_class_id' => 4],
            ['name' => 'ছোটদের হাতের লিখা (পাঞ্জেরী)', 'price' => 150, 'school_class_id' => 4],
            ['name' => 'Active English (পাঞ্জেরী)', 'price' => 140, 'school_class_id' => 4],
            ['name' => 'Handwriting for kids (পাঞ্জেরী)', 'price' => 150, 'school_class_id' => 4],
            ['name' => 'সোনামনিদের মজার গণিত শেখা (দি ভরসাইট পাবলিকেশন)', 'price' => 160, 'school_class_id' => 4],
            ['name' => 'ইসলাম ও নৈতিক শিক্ষা (পাঞ্জেরী)', 'price' => 150, 'school_class_id' => 4],
            ['name' => 'হিন্দুধর্ম ও নৈতিক শিক্ষা (পাঞ্জেরী)', 'price' => 95, 'school_class_id' => 4],
            ['name' => 'ছোটদের সাধারণ জ্ঞান (পাঞ্জেরী)', 'price' => 130, 'school_class_id' => 4],
            ['name' => 'ছবি আঁকি রং করি -১ (পাঞ্জেরী)', 'price' => 95, 'school_class_id' => 4],
            ['name' => 'শৈশব বাংলা ব্যাকরণ (পাঞ্জেরী)', 'price' => 200, 'school_class_id' => 5],
            ['name' => "Early Learner's English Grammar (পাঞ্জেরী)", 'price' => 200, 'school_class_id' => 5],
            ['name' => 'সোনামণিদের নতুন বিশ্ব-১ (দি ভরসাইট পাবলিকেশন)', 'price' => 180, 'school_class_id' => 5],
            ['name' => 'সোনামণিদের কম্পিউটার শিক্ষার আসর-২ (ভরসাইট পাবলিকেশন)', 'price' => 190, 'school_class_id' => 5],
            ['name' => 'চলো আঁকতে শিখি-২ (নবকরণ পাবলিকেশন)', 'price' => 150, 'school_class_id' => 5],
            ['name' => 'হিন্দু ধর্ম ও নৈতিক শিক্ষা (পাঞ্জেরী)', 'price' => null, 'school_class_id' => 5],
            ['name' => 'শৈশব বাংলা ব্যাকরণ (পাঞ্জেরী)', 'price' => 350, 'school_class_id' => 6],
            ['name' => "Early Learner's English Grammar (পাঞ্জেরী)", 'price' => 350, 'school_class_id' => 6],
            ['name' => 'সোনামণিদের নতুন বিশ্ব-৩ (ওরিয়েন্ট পাবলিকেশন)', 'price' => 210, 'school_class_id' => 6],
            ['name' => 'এসো কম্পিউটার শিখি (পাঞ্জেরী)', 'price' => 190, 'school_class_id' => 6],
            ['name' => 'চলো আঁকতে শিখি-৩ (নবারুন পাবলিকেশন)', 'price' => 140, 'school_class_id' => 6],
            ['name' => 'ছোটদের জ্যামিতি শিক্ষা (পাঞ্জেরী)', 'price' => 150, 'school_class_id' => 6],
            ['name' => 'শৈশব বাংলা ব্যাকরণ (পাঞ্জেরী)', 'price' => 380, 'school_class_id' => 7],
            ['name' => "Early Learner's English Grammar (পাঞ্জেরী)", 'price' => 380, 'school_class_id' => 7],
            ['name' => 'এসো কম্পিউটার শিখি চতুর্থ ভাগ (পাঞ্জেরী)', 'price' => 160, 'school_class_id' => 7],
            ['name' => 'সাধারণ জ্ঞান বাংলাদেশ ও বিশ্ব দ্বিতীয় ভাগ (পাঞ্জেরী)', 'price' => 180, 'school_class_id' => 7],
            ['name' => 'রং রেখায় আঁকা শিখি-৪ (দি ওরিয়েন্ট পাবলিকেশন)', 'price' => null, 'school_class_id' => 7],
            ['name' => 'ছোটদের জ্যামিতিক শিক্ষা (পাঞ্জেরী)', 'price' => 150, 'school_class_id' => 7],
            ['name' => 'শৈশব বাংলা ব্যাকরণ (পাঞ্জেরী)', 'price' => 400, 'school_class_id' => 8],
            ['name' => "Early Learner's English Grammar (পাঞ্জেরী)", 'price' => 400, 'school_class_id' => 8],
            ['name' => 'সোনামণিদের নতুন বিশ্ব-৪ (দি ওরিয়েন্ট পাবলিকেশন)', 'price' => 225, 'school_class_id' => 8],
            ['name' => 'সোনামণিদের নতুন বিশ্ব-৪ (দি ওরিয়েন্ট পাবলিকেশন)', 'price' => 220, 'school_class_id' => 9],
            ['name' => 'রং রেখায় আঁকা শিখি (৫) দি ওরিয়েন্ট পাবলিকেশন', 'price' => 150, 'school_class_id' => 9],
            ['name' => 'সোনামণিদের নতুন বিশ্ব-৪ (দি ওরিয়েন্ট পাবলিকেশন)', 'price' => 220, 'school_class_id' => 10],
            ['name' => 'রং রেখায় আঁকা শিখি (৫) দি ওরিয়েন্ট পাবলিকেশন', 'price' => 150, 'school_class_id' => 10],
            ['name' => 'সোনামণিদের নতুন বিশ্ব-৪ (দি ওরিয়েন্ট পাবলিকেশন)', 'price' => 220, 'school_class_id' => 11],
            ['name' => 'সোনামণিদের নতুন বিশ্ব-৪ (দি ওরিয়েন্ট পাবলিকেশন)', 'price' => 220, 'school_class_id' => 12],
            ['name' => 'শিশুপাঠ বাংলা পড়া (অরবিট পাবলিকেশন)', 'price' => 130, 'school_class_id' => 3],
            ['name' => 'সোনামনিরা লেখা শেখো -১ম ভাগ (নবারুন পাবলিকেশন)', 'price' => 170, 'school_class_id' => 3],
            ['name' => 'Active English Level-1 (পাঞ্জেরী পাবলিকেশন)', 'price' => 140, 'school_class_id' => 3],
            ['name' => 'Cambridge picture word book Level-1 (অরবিট পাবলিকেশন)', 'price' => null, 'school_class_id' => 3],
            ['name' => 'HANDWRITING FOR KIDS Level-3 (পাঞ্জেরী পাবলিকেশন)', 'price' => 150, 'school_class_id' => 3],
            ['name' => 'ছোটদের ধারাপাত গণিত শেখা (রংধনু পাবলিকেশন)', 'price' => 120, 'school_class_id' => 3],
            ['name' => 'Count and write Number 1-100 (Book-2) (The orient publication)', 'price' => 175, 'school_class_id' => 3],
            ['name' => 'সোনামনিদের আরবি ইসলাম ও নৈতিক শিক্ষা (দি ওরিয়েন্ট পাবলিকেশন)', 'price' => 110, 'school_class_id' => 3],
            ['name' => 'হিন্দুধর্ম ও নৈতিক শিক্ষা (পাঞ্জেরী পাবলিকেশন) (১ম ভাগ)', 'price' => 75, 'school_class_id' => 3],
            ['name' => 'ছোটদের সাধারন জ্ঞান -২য় ভাগ (পাঞ্জেরী)', 'price' => 110, 'school_class_id' => 3],
            ['name' => 'এসো কম্পিউটার শিখি -১ম ভাগ (পাঞ্জেরী)', 'price' => 115, 'school_class_id' => 3],
            ['name' => 'ছবি আঁকি রং করি -৩য় ভাগ (শিল্পী জহিরুল ইসলাম) (পাঞ্জেরী)', 'price' => 95, 'school_class_id' => 3],
            ['name' => 'সোনামনিদের বাংলা পড়া (নবারুন পাবলিকেশন)', 'price' => 155, 'school_class_id' => 2],
            ['name' => 'ছোটদের হাতের লিখা -২য় ভাগ (পাঞ্জেরী)', 'price' => 150, 'school_class_id' => 2],
            ['name' => 'Fun with ABC (পাঞ্জেরী)', 'price' => 110, 'school_class_id' => 2],
            ['name' => 'Writing & Practice Alphabet (দি ওরিয়েন্ট পাবলিকেশন)', 'price' => 200, 'school_class_id' => 2],
            ['name' => 'Rhymes for fun Level-3 (Kinderbooks)', 'price' => 80, 'school_class_id' => 2],
            ['name' => 'ছন্দে ছন্দে গণিত পাঠ -১ম ভাগ (Kinderbooks)', 'price' => 130, 'school_class_id' => 2],
            ['name' => 'Count and write Number 1-100 (Book-2) (The orient publication)', 'price' => 180, 'school_class_id' => 2],
            ['name' => 'সহজ ইসলাম ও নৈতিক শিক্ষা (দি ওরিয়েন্ট পাবলিকেশন)', 'price' => 130, 'school_class_id' => 2],
            ['name' => 'ছোটদের হিন্দুধর্ম শিক্ষা (কিভার বুক্স পাবলিকেশন) (২য় ভাগ)', 'price' => 110, 'school_class_id' => 2],
            ['name' => 'ছোটদের সাধারন জ্ঞান -১ম ভাগ (পাঞ্জেরী)', 'price' => 95, 'school_class_id' => 2],
            ['name' => 'আঁকা শেখো রং করো -১ (শিল্পী জহিরুল ইসলাম)', 'price' => 120, 'school_class_id' => 2],
            ['name' => 'ওরিয়েন্ট সহজ বর্ণমালা পাঠ (দি ওরিয়েন্ট পাবলিকেশন)', 'price' => 110, 'school_class_id' => 1],
            ['name' => 'ছোটদের হাতের লিখা -১ম ভাগ (পাঞ্জেরী)', 'price' => 150, 'school_class_id' => 1],
            ['name' => 'My Book of ABC Alphabet (Authentica Publication)', 'price' => 150, 'school_class_id' => 1],
            ['name' => 'Premier Handwriting (Ananda book Publication)', 'price' => 100, 'school_class_id' => 1],
            ['name' => 'Rhymes for the play group (Nobarun Publication)', 'price' => 150, 'school_class_id' => 1],
            ['name' => 'Count and write Number 1-50 (Book-1) (The orient publication)', 'price' => 160, 'school_class_id' => 1],
            ['name' => 'গণতে শেখা লিখতে শেখা ১-৫০ (Kinderbooks)', 'price' => 140, 'school_class_id' => 1],
            ['name' => 'ছবি আঁকা রং করি ১ম ভাগ (পাঞ্জেরী)', 'price' => 150, 'school_class_id' => 1],
        ];

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
