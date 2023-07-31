<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Compte;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        //
    }
    public function transaction(Request $request)
    {
        $tableau = ["ORANGE MONEY", "WAVE", "WARI", "COMPTE BANCAIRE"];
        ["fournisseur" => $fournisseur] = $request; 
        ["montant" => $montant] = $request; 
        ["type_transaction" => $type_transaction] = $request;
        ["destinataire" => $destinataire] = $request; 
        ["expediteur" => $expediteur] = $request;
        if( !in_array(strtoupper($fournisseur), $tableau))
        {
            return ["message" => "Ce fournisseur n'est pas encore pris en charge"];
        }
        if(!Client::where("telephone", $expediteur)->exists())
        {
            return ["message" => "Le numero de l'expediteur n'est pas correct"];
        }

        $numberCompteDestinataire = $this->getNumberCompte($fournisseur, $destinataire);
        if(!Compte::where("numero_compte", $numberCompteDestinataire )-> exists())
        {
            return ["message" => "Ce destinataire n'a pas de compte chez ce fournisseur"];
        }
        

    }

    public function getNumberCompte($fournisseur, $telephone)
    {
        $table = explode(" ",$fournisseur);
        if(count($table) == 2)
        {
            return $table[0][0].$table[1][0]."_".$telephone;
        }
        return $fournisseur[0].$fournisseur[2]."_".$telephone;

    }
}
