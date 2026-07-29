<?php

namespace Database\Seeders;

use App\Core\Seeder;
use App\Models\Category;
use App\Models\Post;

class PostSeeder extends Seeder
{
    /**
     * Seed posts and assign random categories.
     */
    public function run(): void
    {
        $categories = Category::all();
        $categoriesCount = count($categories);

        if ($categoriesCount === 0) {
            echo "No categories found. Cannot seed posts.\n";
            return;
        }

        $loremParagraphs = [
            "Далеко-далеко за словесными горами в стране гласных и согласных живут рыбные тексты. Вдали от всех живут они в своих буквенных домах на берегу Семантика большого языкового океана. Маленький ручеек Даль журчит по всей стране и обеспечивает ее всеми необходимыми правилами.",
            "Город Смысла заманивает их туда своим великолепием, но однажды маленькая строчка рыбного текста по имени Лорем ипсум решила уйти в большой мир грамматики. Великий Оксмокс предупреждал ее о злых запятых, диких знаках вопроса и коварных точках с запятой, но текст не дал сбить себя с толку.",
            "Он собрал свои семь заглавных букв, подпоясал инициал заглавной буквой и пустился в дорогу. Взобравшись на первую вершину Италийских гор, он оглянулся назад, на силуэт своего родного города Гласных, на заголовок своей деревни Алфавит и на заголовок своего переулка Подзаголовок.",
            "Грустный риторический вопрос скатился по его щеке, но он продолжал свой путь. По дороге встретил он рукопись. Рукопись предупредила его: «Моя родина — большая страна, в ней злые языковые правила будут рвать тебя на части, а коварные знаки препинания возьмут тебя в плен».",
            "Но наш маленький текст не испугался. Он продолжил свой путь, и вскоре великий океан Семантики поглотил его, превратив в вечный символ свободы выражения мыслей, вдохновляющий дизайнеров и писателей по всему миру на создание прекрасного.",
        ];

        // Seed 77 posts
        for ($i = 1; $i <= 77; $i++) {
            $name = "Интересная статья №{$i} о современных трендах";
            $description = "Краткое описание для статьи №{$i}. Здесь содержится увлекательное превью контента.";
            
            // Generate longer text using random slice of paragraphs
            $text = "Это полный текст интересной статьи под номером {$i}.\n\n" . 
                    implode("\n\n", array_slice($loremParagraphs, 0, rand(2, 5))) . 
                    "\n\nСпасибо за прочтение этой статьи!";
            
            $views = rand(0, 1500);
            
            // Random date in the last 180 days to populate date queries
            $daysAgo = rand(0, 180);
            $createdAt = date('Y-m-d H:i:s', strtotime("-{$daysAgo} days"));

            $post = new Post([
                'img_path' => "https://picsum.photos/id/" . (($i * 7) % 100 + 10) . "/600/400",
                'name' => $name,
                'description' => $description,
                'text' => $text,
                'views' => $views,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            $post->save();

            // Link to 1 or 2 random categories
            shuffle($categories);
            $linkCount = rand(1, 2);
            $catIdsToLink = [];
            for ($k = 0; $k < $linkCount; $k++) {
                $catIdsToLink[] = $categories[$k]->id;
            }

            $post->syncCategories($catIdsToLink);
        }
    }
}
