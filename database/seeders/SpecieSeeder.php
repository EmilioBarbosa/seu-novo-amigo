<?php

namespace Database\Seeders;

use App\Models\Specie;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SpecieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table("species")->insert([
            [
                "name" => "Cachorro"
            ],
            [
                "name" => "Gato"
            ]
        ]);

    }
}
