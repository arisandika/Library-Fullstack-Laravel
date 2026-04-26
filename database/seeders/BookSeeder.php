<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $books = [
            [
                'book_code' => 'BK-001',
                'title' => 'Clean Code',
                'publish_year' => 2008,
                'author' => 'Robert C. Martin',
                'stock' => 5,
                'image' => 'clean-code.jpg',
            ],
            [
                'book_code' => 'BK-002',
                'title' => 'The Pragmatic Programmer',
                'publish_year' => 1999,
                'author' => 'Andrew Hunt & David Thomas',
                'stock' => 3,
                'image' => 'pragmatic-programmer.jpg',
            ],
            [
                'book_code' => 'BK-003',
                'title' => 'Design Patterns',
                'publish_year' => 1994,
                'author' => 'Erich Gamma',
                'stock' => 4,
                'image' => 'design-patterns.jpg',
            ],
            [
                'book_code' => 'BK-004',
                'title' => 'Refactoring',
                'publish_year' => 2018,
                'author' => 'Martin Fowler',
                'stock' => 2,
                'image' => 'refactoring.jpg',
            ],
            [
                'book_code' => 'BK-005',
                'title' => 'Laravel Up & Running',
                'publish_year' => 2019,
                'author' => 'Matt Stauffer',
                'stock' => 6,
                'image' => 'laravel.jpg',
            ],
            [
                'book_code' => 'BK-006',
                'title' => 'You Don’t Know JS Yet',
                'publish_year' => 2020,
                'author' => 'Kyle Simpson',
                'stock' => 7,
                'image' => 'ydkjs.jpg',
            ],
            [
                'book_code' => 'BK-007',
                'title' => 'Eloquent JavaScript',
                'publish_year' => 2018,
                'author' => 'Marijn Haverbeke',
                'stock' => 5,
                'image' => 'eloquent-js.jpg',
            ],
            [
                'book_code' => 'BK-008',
                'title' => 'Introduction to Algorithms',
                'publish_year' => 2009,
                'author' => 'Thomas H. Cormen',
                'stock' => 3,
                'image' => 'clrs.jpg',
            ],
            [
                'book_code' => 'BK-009',
                'title' => 'Atomic Habits',
                'publish_year' => 2018,
                'author' => 'James Clear',
                'stock' => 10,
                'image' => 'atomic-habits.jpg',
            ],
            [
                'book_code' => 'BK-010',
                'title' => 'Deep Work',
                'publish_year' => 2016,
                'author' => 'Cal Newport',
                'stock' => 4,
                'image' => 'deep-work.jpg',
            ],
        ];

        foreach ($books as $book) {
            Book::create($book);
        }
    }
}
