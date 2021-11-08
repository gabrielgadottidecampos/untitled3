<?php

namespace App\Http\Controllers;

use App\Models\Equipe;
use App\Repositories\EquipeRepository;
use Illuminate\Http\Request;
use Ramsey\Uuid\Type\Integer;
use Illuminate\Support\Facades\Storage;
class EquipeController extends Controller
{
    // contrutor para setar o classe Equipe
    public function __construct(Equipe $equipe){
        $this->equipe = $equipe;
    }

// metodo para listar todos os registros do banco **********************************************************************
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $equipeRepository = new EquipeRepository($this->equipe);


        if($request->has('atributos_funcionarios')){
            $atributos_funcionarios = $request->atributos_funcionarios;
            $equipeRepository->selectAtributosRegistrosRelacionados('funcionarios:id,'.$atributos_funcionarios);

        }else{
            $equipeRepository->selectAtributosRegistrosRelacionados('funcionarios');
        }


        if($request->has('filtro')){

            $equipeRepository->filtro($request->filtro);
        }

        if($request->has('atributos')){
            $atributos = $request->atributos;
            $equipeRepository->selectAtributos($request->atributos);
        }
//------------------------------------------------------------------

        return response()->json($equipeRepository->getResultado(),200);

    }

// metodo para adicionar um registro no banco **************************************************************************
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
     // regras e feedbak de validações *************************

        $request->validate($this->equipe->rules(), $this->equipe->feedback());

        // salvando imagem ******************************************
        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens/equipes', 'public');


        $equipe = $this->equipe->create([
            'nome' => $request->nome,
            'imagem' => $imagem_urn
        ]);
        return response()->json($equipe,201);

    }

 // metodo para buscar um registro no banco ****************************************************************************
    /**
     * Display the specified resource.
     *
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $equipe = $this->equipe->with('funcionarios')->find($id);
        // validação da pesquisa ************************
        if($equipe === null){
            return response()->json(['erro' => 'A Equipe pesquisado não existe'],404);
        }
        return response()->json($equipe, 200);
    }

 // metodo para editar um registro no banco ****************************************************************************
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $equipe = $this->equipe->find($id);
        //validação do update **************
        if($equipe === null){
            return response()->json(['erro' => 'Impossivel realizar a solucitação. A Equipe pesquisado não existe'], 401);
        }
        // validação do metodo da requisição para impor as regras e feedback *********
        if($request->method() === 'PATCH'){
            $regrasDinamicas = array();
            foreach ($equipe->rules() as $input => $regra){
                if (array_key_exists($input,$request->all())){
                    $regrasDinamicas[$input] = $regra;
                }
            }
            $request->validate($regrasDinamicas, $this->equipe->feedback());
        }else{
            $request->validate($this->equipe->rules(), $this->equipe->feedback());
        }
        // remove o arquivo antigo caso o novo arquivo tenha sido enviado ***********
        if($request->file('imagem')){
            Storage::disk('public')->delete($equipe->imagem);
        }

        // salvando imagem ******************************************
        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens/equipes', 'public');

        //preecher o objeto $equipe com os dados do request
        $equipe->fill($request->all());
        $equipe->imagem = $imagem_urn;
        $equipe->save();
        // $equipe->update([
        //     'nome' => $request->nome,
        //     'imagem' => $imagem_urn
        // ]);
        return response()->json($equipe, 200);
    }

 // metodo para deletar um registro do banco ***************************************************************************
    /**
     * Remove the specified resource from storage.
     *
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $equipe = $this->equipe->find($id);
        // validação do metodo destroy ********
        if($equipe === null){
            return response()->json(['erro' => 'Impossivel excluir a equipe. A Equipe pesquisado não existe'],401);
        }

        // remove o arquivo antigo caso o novo arquivo tenha sido enviado ***********
        Storage::disk('public')->delete($equipe->imagem);

        $equipe->delete();
        return response()->json(['msg' => 'A Equipe foi removida com sucesso'],200);
    }
}
