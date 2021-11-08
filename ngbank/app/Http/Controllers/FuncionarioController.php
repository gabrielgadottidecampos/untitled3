<?php

namespace App\Http\Controllers;

use App\Models\Funcionario;
use App\Repositories\EquipeRepository;
use App\Repositories\FuncionarioRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class FuncionarioController extends Controller
{

    // contrutor para setar o classe funcionario
    public function __construct(Funcionario $funcionario){
        $this->funcionario = $funcionario;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)    {

        $funcionarioRepository = new FuncionarioRepository($this->funcionario);

        if($request->has('atributos_equipe')){
            $atributos_equipe = $request->atributos_equipe;
            $funcionarioRepository->selectAtributosRegistrosRelacionados('equipe:id,'.$atributos_equipe);

        }else{
            $funcionarioRepository->selectAtributosRegistrosRelacionados('equipe');
        }

//filtros --------------------------------------------------------------------------------------------------------------
        if($request->has('filtro')){

            $funcionarioRepository->filtro($request->filtro);
        }
//atributos ------------------------------------------------------------------------------------------------------------
        if($request->has('atributos')){
            $funcionarioRepository->selectAtributos($request->atributos);
        }
//------------------------------------------------------------------

        return response()->json($funcionarioRepository->getResultado(),200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // regras e feedbak de validações *************************

        $request->validate($this->funcionario->rules(), $this->funcionario->feedback());

        // salvando imagem ******************************************
        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens/funcionarios', 'public');


        $funcionario = $this->funcionario->create([
            'equipe_id' => $request ->equipe_id,
            'nome' => $request->nome,
            'imagem' => $imagem_urn,
            'data_nascimento' => $request->data_nascimento
        ]);
        return response()->json($funcionario,201);
    }

    /**
     * Display the specified resource.
     *
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $funcionario = $this->funcionario->with('equipe')->find($id);
        // validação da pesquisa ************************
        if($funcionario === null){
            return response()->json(['erro' => 'O Funcionario pesquisado não existe'],404);
        }
        return response()->json($funcionario, 200);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $funcionario = $this->funcionario->find($id);
        //validação do update **************
        if($funcionario === null){
            return response()->json(['erro' => 'Impossivel realizar a solucitação. O Funcionario pesquisado não existe'], 401);
        }
        // validação do metodo da requisição para impor as regras e feedback *********
        if($request->method() === 'PATCH'){
            $regrasDinamicas = array();
            foreach ($funcionario->rules() as $input => $regra){
                if (array_key_exists($input,$request->all())){
                    $regrasDinamicas[$input] = $regra;
                }
            }
            $request->validate($regrasDinamicas, $this->funcionario->feedback());
        }else{
            $request->validate($this->funcionario->rules(), $this->funcionario->feedback());
        }
        // remove o arquivo antigo caso o novo arquivo tenha sido enviado ***********
        if($request->file('imagem')){
            Storage::disk('public')->delete($funcionario->imagem);
        }

        // salvando imagem ******************************************
        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens/funcionarios', 'public');

        $funcionario->fill($request->all());
        $funcionario->imagem = $imagem_urn;
        $funcionario->save();

      // $funcionario->update([
      //     'equipe_id' => $request ->equipe_id,
      //     'nome' => $request->nome,
      //     'imagem' => $imagem_urn,
      //     'data_nascimento' => $request->data_nascimento
      // ]);
      return response()->json($funcionario, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $funcionario = $this->funcionario->find($id);
        // validação do metodo destroy ********
        if($funcionario === null){
            return response()->json(['erro' => 'Impossivel excluir o funcionario. O Funcionaario pesquisado não existe'],401);
        }

        // remove o arquivo antigo caso o novo arquivo tenha sido enviado ***********
        Storage::disk('public')->delete($funcionario->imagem);

        $funcionario->delete();
        return response()->json(['msg' => 'O Funcionario foi removida com sucesso'],200);
    }
}
