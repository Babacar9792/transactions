<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            "destinataire" => "required",
            "montant" => "required"
            //
        ];
    }

    public function messages()
    {
        return [
            "montant.required" => "Vous devez entrez un montant",
            "destinataire.required" => "Vous devez donner le numero du destinataire"
        ];
    }
}
