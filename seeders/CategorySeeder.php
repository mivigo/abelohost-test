<?php

namespace Database\Seeders;

use App\Core\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Seed categories table.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Технологии', 'description' => 'Все о новейших технологиях, программировании и гаджетах.'],
            ['name' => 'Дизайн', 'description' => 'Веб-дизайн, интерфейсы, UX/UI и графический дизайн.'],
            ['name' => 'Путешествия', 'description' => 'Удивительные места по всему миру, советы туристам и отчеты о поездках.'],
            ['name' => 'Спорт', 'description' => 'Здоровый образ жизни, фитнес, мировые новости спорта и тренировки.'],
            ['name' => 'Бизнес', 'description' => 'Стартапы, личные финансы, инвестиции и экономика.'],
            ['name' => 'Кулинария', 'description' => 'Рецепты вкусных блюд со всего мира, кулинарные секреты и обзоры ресторанов.'],
            ['name' => 'Искусство', 'description' => 'Живопись, музыка, литература, кино и выставки.'],
        ];

        foreach ($categories as $catData) {
            $category = new Category($catData);
            $category->save();
        }
    }
}
