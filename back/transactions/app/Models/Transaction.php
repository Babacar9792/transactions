<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Transaction extends Model
{
    use HasFactory;

    public function depot($montant, $fournisseur, $expediteur, $destinataire)
    {
        $fournisseur = strtoupper($fournisseur);
        if (!Client::where("telephone", $expediteur)->exists()) {
            return ["statut" => 404, "message" => "Le numero de l'expediteur ".$expediteur." n'existe pas dans la base de données"];
        }

        $numberCompteDestinataire = $this->getNumberCompte($fournisseur, $destinataire);
        if (!Compte::where("numero_compte", $numberCompteDestinataire)->exists()) {
            return ["message" => "Le numero ". $destinataire ."n'a pas de compte chez ce fournisseur", "statut" => 404];
        }
        try {
            DB::beginTransaction();
            $compte = Compte::where("numero_compte", $numberCompteDestinataire)->get()[0];
            Compte::where("id", $compte->id)->update(["solde" => $compte->solde + $montant]);
            Transaction::insert([
                "montant" => $montant,
                "type_transaction" => "depot",
                "destinataire" => $expediteur,
                "compte_id" => $compte->id
            ]);
            // $comptes->
            DB::commit();
            return ["message" => "Depot reussi", "statut" => 200];
        } catch (\Throwable $th) {
            DB::rollBack();
            return ["message" => "une erreur s'est produit" . $th->getMessage()];
            //throw $th;
        }
    }


    public function retrait($montant, $fournisseur, $destinataire)
    {
        $numberCompteDestinataire = $this->getNumberCompte($fournisseur, $destinataire);
        if (!Compte::where("numero_compte", $numberCompteDestinataire)->exists()) {
            return ["message" => "Ce destinataire n'a pas de compte chez ce fournisseur", "statut" => 404];
        }
        $compte = Compte::where("numero_compte", $numberCompteDestinataire)->get();

        if ($montant > $compte[0]->solde) {
            return ["message" => "Le solde de votre compte ne vous permet pas d'eefectuer une telle operation" , "statut" => 404];
        }

        try {
            DB::beginTransaction();
            Compte::where("id", $compte[0]->id)->update(["solde" => $compte[0]->solde - $montant]);
            Transaction::insert([
                "montant" => $montant,
                "type_transaction" => "retrait",
                "destinataire" => $destinataire,
                "compte_id" => $compte[0]->id
            ]);
            DB::commit();
            return ["message" => "Retrait reussi" , "statut" => 200];
        } catch (\Throwable $th) {
            DB::rollBack();
            return ["message" => "Un probleme s'est produit" . $th->getMessage() , "statut" => 404];
            //throw $th;
        }
    }

    public function getNumberCompte($fournisseur, $telephone)
    {
        $table = explode(" ", $fournisseur);
        if (count($table) == 2) {
            return $table[0][0] . $table[1][0] . "_" . $telephone;
        }
        return $fournisseur[0] . $fournisseur[2] . "_" . $telephone;
    }

}
