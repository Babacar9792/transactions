<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // $clients = [
        //     "prenom" => "Babacar",
        //     "nom" => "Sy",
        //     "telephone" => "771234567"

        // ];
        $clients = [
            [
                "prenom" => "Babacar",
                "nom" => "Sy",
                "telephone" => "771234567"

            ],
            [
                "prenom" => "Jean",
                "nom" => "Mendy",
                "telephone" => "781234567"

            ],
            [
                "prenom" => "Paul",
                "nom" => "Da sylva",
                "telephone" => "761234567"
            ]
        ];
        Client::insert($clients);
        //
    }
}
