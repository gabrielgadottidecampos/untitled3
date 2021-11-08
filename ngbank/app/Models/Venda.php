<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venda extends Model
{
    use HasFactory;
    protected $fillable = ['equipe_id', 'funcionario_id', 'valor_venda'];
// função para regras das vendas
    public function rules()
    {
        return [
          'equipe_id' => 'exists:equipes,id',
          'funcionario_id' => 'exists:funcionarios,id',
          'valor_venda' => 'required|numeric|between:0,1000000'
        ];
    }

    public function feedback()
    {
        return[
            'equipe_id.exists' => 'Por favor adicione um ID valido, A equipe selecionada não existe',
            'funcionario_id.exists' => 'Por favor adicione um ID valido, O Funcionario selecionada não existe',
            'valor_venda.required' => 'Valor da venda é obrigatório',
            'valor_venda.numeric' => 'O valor tem que ser númerico'
        ];
    }

    public function funcionario()
    {
        return $this->belongsTo('App\Models\Funcionario');
    }

}
