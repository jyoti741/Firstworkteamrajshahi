<?php

namespace App\Helpers;

use App\Models\Category;

class FoodImageHelper
{
    public static array $availableImages = [
        'burger' => [
            'path' => 'images/foods/burger.jpg',
            'name' => 'Burger / বার্গার',
            'emoji' => '🍔',
            'keywords' => ['burger', 'বার্গার', 'cheeseburger', 'hamburger', 'patty'],
        ],
        'fuchka' => [
            'path' => 'images/foods/fuchka.jpg',
            'name' => 'Fuchka / ফুচকা',
            'emoji' => '🥣',
            'keywords' => ['fuchka', 'ফুচকা', 'phuchka', 'pani puri', 'panipuri', 'golgappa'],
        ],
        'chotpoti' => [
            'path' => 'images/foods/chotpoti.jpg',
            'name' => 'Chotpoti / চটপটি',
            'emoji' => '🍲',
            'keywords' => ['chotpoti', 'চটপটি', 'chot poti', 'ghugni'],
        ],
        'biryani' => [
            'path' => 'images/foods/biryani.jpg',
            'name' => 'Biryani / বিরিয়ানি',
            'emoji' => '🍚',
            'keywords' => ['biryani', 'বিরিয়ানি', 'বিরিয়ানি', 'kacchi', 'কাচ্চি', 'polao', 'পোলাও', 'rice', 'ভাত', 'khichuri', 'খিচুড়ি', 'tehari', 'তেহারী'],
        ],
        'tea' => [
            'path' => 'images/foods/tea.jpg',
            'name' => 'Tea / চা',
            'emoji' => '🫖',
            'keywords' => ['tea', 'চা', 'chai', 'dudh cha', 'milk tea', 'black tea', 'lebu cha', 'green tea', 'coffee', 'কফি'],
        ],
        'fries' => [
            'path' => 'images/foods/fries.jpg',
            'name' => 'French Fries / ফ্রাইজ',
            'emoji' => '🍟',
            'keywords' => ['fries', 'french fries', 'ফ্রাইজ', 'chips', 'potato', 'পটেটো', 'wedges', 'nachos'],
        ],
        'chicken' => [
            'path' => 'images/foods/chicken.jpg',
            'name' => 'Crispy Chicken / চিকেন',
            'emoji' => '🍗',
            'keywords' => ['chicken', 'চিকেন', 'wings', 'wing', 'nugget', 'nuggets', 'drumstick', 'broast', 'fry chicken', 'fried chicken', 'মুরগি'],
        ],
        'pizza' => [
            'path' => 'images/foods/pizza.jpg',
            'name' => 'Pizza / পিজ্জা',
            'emoji' => '🍕',
            'keywords' => ['pizza', 'পিজ্জা', 'calzone'],
        ],
        'shawarma' => [
            'path' => 'images/foods/shawarma.jpg',
            'name' => 'Shawarma / Roll / শর্মা',
            'emoji' => '🌯',
            'keywords' => ['shawarma', 'sharma', 'শরমা', 'শাওয়ার্মা', 'roll', 'রোল', 'wrap', 'kebab', 'kabab', 'sandwich', 'স্যান্ডউইচ', 'sub'],
        ],
        'juice' => [
            'path' => 'images/foods/juice.jpg',
            'name' => 'Juice & Drinks / জুস ও ড্রিংকস',
            'emoji' => '🥤',
            'keywords' => ['juice', 'জুস', 'shake', 'milkshake', 'smoothie', 'স্মুদি', 'coke', 'cola', 'pepsi', 'soda', 'drink', 'beverage', 'পানি', 'water', 'lemonade', 'lassi', 'লাচ্ছি'],
        ],
    ];

    public static string $defaultImage = 'images/foods/default.jpg';

    public static array $categoryKeywords = [
        'burger' => 'images/foods/burger.jpg',
        'বার্গার' => 'images/foods/burger.jpg',
        'chicken' => 'images/foods/chicken.jpg',
        'চিকেন' => 'images/foods/chicken.jpg',
        'fries' => 'images/foods/fries.jpg',
        'side' => 'images/foods/fries.jpg',
        'ফ্রাইজ' => 'images/foods/fries.jpg',
        'drink' => 'images/foods/juice.jpg',
        'beverage' => 'images/foods/juice.jpg',
        'juice' => 'images/foods/juice.jpg',
        'ড্রিংকস' => 'images/foods/juice.jpg',
        'চা' => 'images/foods/tea.jpg',
        'tea' => 'images/foods/tea.jpg',
        'biryani' => 'images/foods/biryani.jpg',
        'বিরিয়ানি' => 'images/foods/biryani.jpg',
        'fuchka' => 'images/foods/fuchka.jpg',
        'ফুচকা' => 'images/foods/fuchka.jpg',
        'chotpoti' => 'images/foods/chotpoti.jpg',
        'চটপটি' => 'images/foods/chotpoti.jpg',
        'pizza' => 'images/foods/pizza.jpg',
        'পিজ্জা' => 'images/foods/pizza.jpg',
        'roll' => 'images/foods/shawarma.jpg',
        'রোল' => 'images/foods/shawarma.jpg',
        'combo' => 'images/foods/default.jpg',
        'কম্বো' => 'images/foods/default.jpg',
        // Common street food cart categories
        'স্ট্রিট ফুড' => 'images/foods/fuchka.jpg',
        'street food' => 'images/foods/fuchka.jpg',
        'street' => 'images/foods/fuchka.jpg',
        'ফাস্ট ফুড' => 'images/foods/burger.jpg',
        'fast food' => 'images/foods/burger.jpg',
        'fastfood' => 'images/foods/burger.jpg',
        'প্রধান খাবার' => 'images/foods/biryani.jpg',
        'main dish' => 'images/foods/biryani.jpg',
        'main food' => 'images/foods/biryani.jpg',
        'meal' => 'images/foods/biryani.jpg',
        'নাস্তা' => 'images/foods/chotpoti.jpg',
        'snack' => 'images/foods/chotpoti.jpg',
        'snacks' => 'images/foods/chotpoti.jpg',
        'পানীয়' => 'images/foods/tea.jpg',
        'পানীয়' => 'images/foods/tea.jpg',
        'মিষ্টান্ন' => 'images/foods/default.jpg',
        'sweet' => 'images/foods/default.jpg',
        'sweets' => 'images/foods/default.jpg',
        'dessert' => 'images/foods/default.jpg',
    ];

    public static function matchImage(?string $nameEn, ?string $nameBn = null, ?int $categoryId = null): array
    {
        $text = mb_strtolower(trim(($nameEn ?? '') . ' ' . ($nameBn ?? '')), 'UTF-8');

        if (!empty($text)) {
            foreach (self::$availableImages as $key => $info) {
                foreach ($info['keywords'] as $keyword) {
                    if (str_contains($text, $keyword)) {
                        return [
                            'key' => $key,
                            'name' => $info['name'],
                            'path' => $info['path'],
                            'emoji' => $info['emoji'],
                            'matched_by' => 'item',
                        ];
                    }
                }
            }
        }

        if ($categoryId) {
            $cat = Category::find($categoryId);
            if ($cat) {
                $catText = mb_strtolower($cat->name . ' ' . ($cat->name_bn ?? ''), 'UTF-8');
                foreach (self::$categoryKeywords as $catKey => $imgPath) {
                    if (str_contains($catText, $catKey)) {
                        $matchedName = $cat->name;
                        foreach (self::$availableImages as $k => $info) {
                            if ($info['path'] === $imgPath) {
                                $matchedName = $info['name'];
                                break;
                            }
                        }
                        return [
                            'key' => $catKey,
                            'name' => $matchedName,
                            'path' => $imgPath,
                            'emoji' => $cat->icon ?: '🍔',
                            'matched_by' => 'category',
                        ];
                    }
                }
            }
        }

        return [
            'key' => 'default',
            'name' => 'Food Item',
            'path' => self::$defaultImage,
            'emoji' => '🍔',
            'matched_by' => 'default',
        ];
    }
}
