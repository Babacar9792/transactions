<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Compte;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PHPUnit\Event\Tracer\Tracer;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Expr\Cast\Array_;

class TransactionController extends Controller
{
    public $minimumByFournisseur = ["ORANGE MONEY" => 500, "WARI" => 1000, "WAVE" => 500, "COMPTE BANCAIRE" => 10000];
    public $longueurCodeOM = 23;
    public $longueurCodeCB = 30;

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
        // return "wou";


        /* 
        
                {
                "destinataire" : "771234567",
                "montant" : 500,
                "type_transaction" : "retrait",
                "expediteur" : "",
                "fournisseur" : "Wave"

            }
        */
        $tableau = ["ORANGE MONEY", "WAVE", "WARI", "COMPTE BANCAIRE"];
        ["fournisseur" => $fournisseur] = $request;
        ["montant" => $montant] = $request;
        ["type_transaction" => $type_transaction] = $request;
        ["destinataire" => $destinataire] = $request;
        ["expediteur" => $expediteur] = $request;
        $fournisseur = strtoupper($fournisseur);
        $transaction = new Transaction();
        if (!in_array($fournisseur, $tableau)) {
            return ["message" => "Ce fournisseur n'est pas encore pris en charge", "statut" => 404];
        }
        $numberCompteDestinataire = $transaction->getNumberCompte($fournisseur, $destinataire);
        $compte = Compte::where("numero_compte", $numberCompteDestinataire)->get();
        // return $compte[0]->id;
        if (count($compte) == 0) {
            return ["message" => "Le destinataire  ". $destinataire ." n'a pas de compte chez ce fournisseur", "statut" => 404];
        }
        if ($montant < $this->minimumByFournisseur[$fournisseur]) {
            return ["message" => "Le montant doit etre superieur à " . $this->minimumByFournisseur[$fournisseur]];
        }
        if ($type_transaction == "retrait") {

            return $transaction->retrait($montant, $fournisseur, $destinataire);
        }
        // retrait
        return $transaction->depot($montant, $fournisseur, $expediteur, $destinataire);
    }

    public function transfert(Request $request)
    {

        // $transaction = new Transaction();

        $validator = Validator::make($request->all(), [
            'montant' => 'required|numeric',
        ]);
        ["destinataire" => $destinataire] = $request;
        ["expediteur" => $expediteur] = $request;
        ["fournisseur" => $fournisseur] = $request;
        ["typeTransaction" => $typeTransaction] = $request;
        ["montant" => $montant] = $request;
        $typeTransaction = strtoupper($typeTransaction);
        $fournisseur = strtoupper($fournisseur);
        // return "bonjour";
        if ($fournisseur == "WAVE") {

            return $this->wave($expediteur, $destinataire, $montant);
        }
        if ($fournisseur == "ORANGE MONEY") {
            return $this->orangeMoney($expediteur, $destinataire, $montant, $typeTransaction);
        }
        if ($fournisseur == "COMPTE BANCAIRE") {

            return $this->compteBancaire($expediteur, $destinataire, $montant, $typeTransaction);
        }
        return $this->wari($request);
    }


    public function wari($request)
    {
        ["destinataire" => $destinataire] = $request;
        ["expediteur" => $expediteur] = $request;
        ["fournisseur" => $fournisseur] = $request;
        ["typeTransaction" => $typeTransaction] = $request;
        ["montant" => $montant] = $request;
        ["prenomNomExpe" => $prenomNomExpe] = $request;
        ["prenomNomDestinataire" => $prenomNomDestinaatire] = $request;
        // return $destinataire;
        // [""];
        $transaction = new Transaction();
        $numExpediteur = $transaction->getNumberCompte("WARI", $expediteur);
        // $numDestinataire = $transaction->getNumberCompte("WARI", $destinataire);
        $enregistrementExpedieteur = Compte::where("numero_compte", $numExpediteur)->first();
        if (!$enregistrementExpedieteur) {
            $info = explode(" ", $prenomNomExpe);
            if (count($info) == 1) {
                return ["message" => "Veullez renseigner le nom de l'expediteur", "statut" => 404];
            }
            $nom = array_pop($info);
            try {

                DB::beginTransaction();
                $client = new Client();
                $client->prenom = implode(" ", $info);
                $client->nom = $nom;
                $client->telephone = $expediteur;
                $client->save();

                $compte = new Compte();
                $compte->fournisseur = "WARI";
                $compte->numero_compte = "WR__" . $expediteur;
                $compte->client_id =   $client->id;
                $compte->solde = $montant;
                $compte->save();


                DB::commit();
                $enregistrementExpedieteur = $compte;


                //code...
            } catch (\Throwable $th) {
                DB::rollBack();
                return ["message" => " une erreur s'est produit" . $th->getMessage()];
                //throw $th;
            }
        }
        try {
            DB::beginTransaction();
            Compte::where("id" , $enregistrementExpedieteur->id)->update(["solde" => $enregistrementExpedieteur->solde - $montant]);
            $code = $this->goodCode(25);
            Transaction::insert([
                [
                    "montant" => $montant - ($montant * 2) / 100,
                    "type_transaction" => "transfert recu",
                    "destinataire" => $destinataire,
                    "compte_id" => $enregistrementExpedieteur->id
                ]
            ]);
            DB::commit();
            return ["message" => "transfert reussi", "code" => $code, "statut" => 200];
        } catch (\Throwable $th) {
            return ["message" => "Une erreur s'est produite " .$th->getMessage(), "statut" => 404];
            //throw $th;
        }
    }











    public function compteBancaire($expediteur, $destinataire, $montant, $typeTransaction)
    {
        $transaction = new Transaction();
        $numExpediteur = $transaction->getNumberCompte("COMPTE BANCAIRE", $expediteur);
        $numDestinataire = $transaction->getNumberCompte("COMPTE BANCAIRE", $destinataire);
        $valideAccount = $this->haveAnAccount($numDestinataire, $numExpediteur);
        if (count($valideAccount) == 0) {
            return ["message" => "Aucun des numeros demendant le service n'a de compte wave", "statut" => 404];
        } else if (count($valideAccount) == 1) {
            return $numDestinataire == $valideAccount[0]->numero_compte ? ["message" => "le numero " . $expediteur . " n'a pas de compte","statut" => 404] : ["message" => "le numero " . $destinataire . " n'a pas de compte", "statut" => 404];
        } else {
            // Verification du solde 
            $soldeExpediteur = $valideAccount[0]->solde;
            $soldeDestinataire = $valideAccount[1]->solde;
            if ($valideAccount[1]->numero_compte == $numExpediteur) {
                $soldeExpediteur = $valideAccount[1]->solde;
                $soldeDestinataire = $valideAccount[0]->solde;
            }
            if ($montant < $this->minimumByFournisseur["COMPTE BANCAIRE"]) {
                return ["message" => " Montant insuffisant. Il doit etre superieur à " . $this->minimumByFournisseur["WAVE"], "statut" => 404];
            }
            if ($montant > $soldeExpediteur || $montant < 0) {
                return ["message" => "Votre solde ne vous permet pas d'effectuer une telle opération", "statut" => 404];
            }
            // $montant = $montant - $montant / 100;
            // return $valideAccount;
            Compte::where(["numero_compte" => $numDestinataire])->update(["solde" => $soldeDestinataire + $montant - ($montant * 5) / 100]);
            Compte::where(["numero_compte" => $numExpediteur])->update(["solde" => $soldeExpediteur - $montant]);

            //transfert permanent 
            if ($typeTransaction == "PERMANENT") {

                Transaction::insert([[
                    "montant" => $montant - ($montant * 5) / 100,
                    "type_transaction" => "transfert recu",
                    "destinataire" => $destinataire,
                    "compte_id" => $valideAccount[0]->id
                ], [
                    "montant" => $montant - ($montant * 5) / 100,
                    "type_transaction" => "transfert",
                    "destinataire" => $expediteur,
                    "compte_id" => $valideAccount[1]->id
                ]]);
                return ["message" => "Transfert réussi", "statut" => 200];
            }


            // Transfert immediat
            $code = $this->goodCode($this->longueurCodeCB);
            Transaction::insert([[
                "montant" => $montant - ($montant * 5) / 100,
                "type_transaction" => "transfert recu",
                "destinataire" => $destinataire,
                "compte_id" => $valideAccount[0]->id,
                "code" => $code
            ], [
                "montant" => $montant - ($montant * 5) / 100,
                "type_transaction" => "transfert",
                "destinataire" => $expediteur,
                "compte_id" => $valideAccount[1]->id,
                "code" => $code
            ]]);
            return ["message" => "Transfert réussi", "code" => $code, "statut" => 200];

            // $code = $this->goodCode();
        }
    }






    public function wave($expediteur, $destinataire, $montant)
    {

        $transaction = new Transaction();
        $numExpediteur = $transaction->getNumberCompte("WAVE", $expediteur);
        $numDestinataire = $transaction->getNumberCompte("WAVE", $destinataire);
        $valideAccount = $this->haveAnAccount($numDestinataire, $numExpediteur);
        // return $this->haveAnAccount($numDestinataire, $numExpediteur);
        if (count($valideAccount) == 0) {
            return ["message" => "Aucun des numeros demendant le service n'a de compte wave", "statut" => 404];
        } else if (count($valideAccount) == 1) {
            return $numDestinataire == $valideAccount[0]->numero_compte ? ["mesaage" => "le numero " . $expediteur . " n'a pas de compte"] : ["message" => "le numero " . $destinataire . " n'a pas de compte", "statut" => 404];
        } else {
            // Verification du solde 
            $soldeExpediteur = $valideAccount[0]->solde;
            $soldeDestinataire = $valideAccount[1]->solde;
            if ($valideAccount[1]->numero_compte == $numExpediteur) {
                $soldeExpediteur = $valideAccount[1]->solde;
                $soldeDestinataire = $valideAccount[0]->solde;
            }
            if ($montant < $this->minimumByFournisseur["WAVE"]) {
                return ["message" => " Montant insuffisant. Il doit etre superieur à " . $this->minimumByFournisseur["WAVE"], "statut" => 404];
            }
            if ($montant > $soldeExpediteur || $montant < 0) {
                return ["message" => "Votre solde ne vous permet pas d'effectuer une telle opération", "statut" => 404];
            }
            // $montant = $montant - $montant / 100;
            // return $valideAccount;
            Compte::where(["numero_compte" => $numDestinataire])->update(["solde" => $soldeDestinataire + $montant - $montant / 100]);
            Compte::where(["numero_compte" => $numExpediteur])->update(["solde" => $soldeExpediteur - $montant]);
            Transaction::insert([[
                "montant" => $montant,
                "type_transaction" => "transfert recu",
                "destinataire" => $destinataire,
                "compte_id" => $valideAccount[0]->id
            ], [
                "montant" => $montant,
                "type_transaction" => "transfert",
                "destinataire" => $expediteur,
                "compte_id" => $valideAccount[1]->id
            ]]);
            return ["message " => "Transfert réussi", "statut" => 200];
        }
        //    $num  Compte::whereIn("numero_compte", [$numDestinataire, $numExpediteur])->get();
        // return;
    }










    public function orangeMoney($expediteur, $destinataire, $montant, $typeTransaction)
    {
        $transaction = new Transaction();
        $numExpediteur = $transaction->getNumberCompte("ORANGE MONEY", $expediteur);
        $numDestinataire = $transaction->getNumberCompte("ORANGE MONEY", $destinataire);
        // Transfert sans code
        if ($typeTransaction == "SANS CODE") {

            $valideAccount = $this->haveAnAccount($numDestinataire, $numExpediteur);

            if (count($valideAccount) == 0) {
                return ["message" => "Aucun des numeros demendant le service n'a de compte wave", "statut" => 404];
            } else if (count($valideAccount) == 1) {
                return $numDestinataire == $valideAccount[0]->numero_compte ? ["mesaage" => "le numero " . $expediteur . " n'a pas de compte"] : ["message" => "le numero " . $destinataire . " n'a pas de compte", "statut" => 404];
            } else {
                // Verification du solde 
                $soldeExpediteur = $valideAccount[0]->solde;
                $soldeDestinataire = $valideAccount[1]->solde;
                if ($valideAccount[1]->numero_compte == $numExpediteur) {
                    $soldeExpediteur = $valideAccount[1]->solde;
                    $soldeDestinataire = $valideAccount[0]->solde;
                }
                if ($montant < $this->minimumByFournisseur["WAVE"]) {
                    return ["message" => " Montant insuffisant. Il doit etre superieur à " . $this->minimumByFournisseur["WAVE"], "statut" => 404];
                }
                if ($montant > $soldeExpediteur || $montant < 0) {
                    return ["message" => "Votre solde ne vous permet pas d'effectuer une telle opération", "statut" => 404];
                }
                Compte::where(["numero_compte" => $numDestinataire])->update(["solde" => $soldeDestinataire + $montant - $montant / 100]);
                Compte::where(["numero_compte" => $numExpediteur])->update(["solde" => $soldeExpediteur - $montant]);
                Transaction::insert([[
                    "montant" => $montant - $montant / 100,
                    "type_transaction" => "transfert recu",
                    "destinataire" => $destinataire,
                    "compte_id" => $valideAccount[0]->id
                ], [
                    "montant" => $montant - $montant / 100,
                    "type_transaction" => "transfert",
                    "destinataire" => $expediteur,
                    "compte_id" => $valideAccount[1]->id
                ]]);
                return ["message" => "Transfert réussi", "statut" => 200];
            }


            //-------------------
        }
        /// Transfert avec code 
        $expe = Compte::where("numero_compte", $numExpediteur)->first();
        if (!$expe) {
            return ["message" => "Le numero " . $expediteur . " n'a pas de compte", "statut" => 404];
        }
        if ($expe->solde < $montant) {
            return ["message" => "Votre solde ne vous permet pas d'effectuer une telle opération", "statut" => 404];
        }
        Compte::where("id", $expe->id)->update(["solde" => $expe->solde - $montant]);
        $code = $this->goodCode($this->longueurCodeOM);
        Transaction::insert([
            [
                "montant" => $montant - $montant / 100,
                "type_transaction" => "transfert recu",
                "destinataire" => $destinataire,
                "compte_id" => $expe->id,
                "code" => $code
            ]
        ]);

        return ["message" => "tranfert reussi", "code" => $code, "statut" => 200];
    }








    public function haveAnAccount($numDestinataire, $numExpediteur)
    {

        /* 
        * cette fonction renvoie par l'ordre les enregistrements qui ont ces numeros de compte passés en parametre
        * Par ordre c'est à dire celui de l'xpediteur d'abord ensuite celui du destinataire;

        */
        return Compte::whereIn("numero_compte", [$numDestinataire, $numExpediteur])->get();
    }





    public function getcode($nombreDechiffre)
    {
        $code = "";
        for ($i = 0; $i < $nombreDechiffre; $i++) {
            # code...
            $code = $code . rand(0, 9);
        }
        return $code;
    }







    public function goodCode($nombreDechiffre)
    {
        $code = $this->getcode($nombreDechiffre);
        $codes = Transaction::all()->pluck('code');
        while (in_array($code, [...$codes])) {
            $code = $this->getcode($nombreDechiffre);
        }
        return $code;
    }
}
