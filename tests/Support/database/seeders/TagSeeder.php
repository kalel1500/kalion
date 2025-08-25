<?php

namespace Thehouseofel\Kalion\Tests\Support\Database\Seeders;

use Illuminate\Database\Seeder;
use Thehouseofel\Kalion\Tests\Support\Models\Tag;
use Thehouseofel\Kalion\Tests\Support\Models\TagType;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear 3 TagTypes
        $types = TagType::factory(3)->create();

        // Para cada Type, añadir 3 Tags
        $types->each(function ($tag) {
            Tag::factory(3)->for($tag)->create();
        });
    }
}
