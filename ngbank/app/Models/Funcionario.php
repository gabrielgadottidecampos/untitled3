<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Funcionario extends Model
{
    use HasFactory;
    protected $fillable = ['equipe_id', 'nome', 'imagem','data_nascimento'];

    // metodo de regras para funcionario *******************************************************************************
    public function rules()
    {
        return [
            'equipe_id' => 'exists:equipes,id',
            'nome' => 'required|min:3',
            'imagem' => 'required|file|mimes:png,jpeg,jpg',
            'data_nascimento' => 'required|date'
        ];
    }

    // metodo de feedback das regras funcionario ***********************************************************************

    public function feedback()
    {
        return[
            'required' => 'O campo :attribute é obrigatório',
            'imagem.mimes' => 'O Arquivo deve ser .png, jpg, jpeg',
            'nome.min' => 'O nome deve conter no minimo 3 caracteres',
            'data_nascimento.date' => 'é nescessario digitar uma data valida'
        ];
    }

    // função de relacionamento entre Equipe e Funcionario *************************************************************
    public function equipe()
    {
        // um funcionario pertence a uma equipe *********
        return $this->belongsTo('App\Models\Equipe'); // pentence a
    }
}
