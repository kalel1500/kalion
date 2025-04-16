<?php

namespace Thehouseofel\Kalion\Database\Seeders;

use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Thehouseofel\Kalion\Infrastructure\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        User::factory()
            ->sequence(fn (Sequence $seq) => ['email' => 'test' . $seq->index+1 . '@example.com'])
            ->count(9)
            ->create();
    }
}
