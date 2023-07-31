<?php

namespace Database\Seeders;

use App\Models\Compte;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $comptes = [
            [
                "fournisseur"  => "ORANGE MONEY",
                "numero_compte" => "OM_771234567",
                "client_id" => 1
            ],
            [
                "fournisseur"  => "WAVE",
                "numero_compte" => "WV_771234567",
                "client_id" => 1
            ],
            [
                "fournisseur"  => "WARI",
                "numero_compte" => "WR_771234567",
                "client_id" => 1
            ],
            [
                "fournisseur"  => "COMPTE BANCAIRE",
                "numero_compte" => "CB_771234567",
                "client_id" => 1
            ],
            [
                "fournisseur"  => "ORANGE MONEY",
                "numero_compte" => "OM_781234567",
                "client_id" => 2
            ],
            [
                "fournisseur"  => "WAVE",
                "numero_compte" => "WV_781234567",
                "client_id" => 2
            ],
            [
                "fournisseur"  => "WAVE",
                "numero_compte" => "WV_761234567",
                "client_id" => 3
            ],
            [
                "fournisseur"  => "COMPTE BANCAIRE",
                "numero_compte" => "CB_761234567",
                "client_id" => 3
            ]
            ];

            Compte::insert($comptes);
    }
}
