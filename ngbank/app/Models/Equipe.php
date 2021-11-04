<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipe extends Model
{
    use HasFactory;
    protected $fillable = ['nome', 'imagem'];

// metodo para indicar as regras ***************************************************************************************
    public function rules()
    {
        return [
            'nome' => 'required|min:3',
            'imagem' => 'required|file|mimes:png,jpeg,jpg'
        ];
    }
// metodo para indicar os feedback das regas ***************************************************************************
    public function feedback()
    {
        return [
        'required' => 'O campo :attribute é obrigatório',
        'imagem.mimes' => 'O Arquivo deve ser .png, jpg, jpeg',
        'nome.min' => 'O nome deve conter no minimo 3 caracteres'
    ];
    }
}
