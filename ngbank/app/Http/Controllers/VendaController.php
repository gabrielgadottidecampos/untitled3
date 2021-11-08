<?php

namespace App\Http\Controllers;

use App\Models\Venda;
use App\Repositories\EquipeRepository;
use App\Repositories\VendaRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VendaController extends Controller
{

    // contrutor para setar o classe funcionario
    public function __construct(Venda $venda){
        $this->venda = $venda;
    }
// função para listar todos os arquivos do banco
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $vendaRepository = new VendaRepository($this->venda);
        if($request->has('atributos_funcionario')){
            $atributos_funcionarios = $request->atributos_funcionarios;
            $vendaRepository->selectAtributosRegistrosRelacionados('funcionario:id,'.$atributos_funcionarios);

        }else{
            $vendaRepository->selectAtributosRegistrosRelacionados('funcionario');
        }
        if($request->has('filtro')){

            $vendaRepository->filtro($request->filtro);
        }

        if($request->has('atributos')){
            $atributos = $request->atributos;
            $vendaRepository->selectAtributos($request->atributos);
        }
//------------------------------------------------------------------

        return response()->json($vendaRepository->getResultado(),200);

    }

// metodo para criar nova venda
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->venda->rules(), $this->venda->feedback());

        $venda = $this->venda->create([
            'equipe_id' => $request ->equipe_id,
            'funcionario_id' => $request ->funcionario_id,
            'valor_venda' => $request->valor_venda,

        ]);
        return response()->json($venda,201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Venda  $venda
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $venda = $this->venda->with('funcionarios')->find($id);
        // validação da pesquisa ************************
        if($venda === null){
            return response()->json(['erro' => 'A Equipe pesquisado não existe'],404);
        }
        return response()->json($venda, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Venda  $venda
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $venda = $this->venda->find($id);
        //validação do update **************
        if($venda === null){
            return response()->json(['erro' => 'Impossivel realizar a solucitação. A Equipe pesquisado não existe'], 401);
        }
        // validação do metodo da requisição para impor as regras e feedback *********
        if($request->method() === 'PATCH'){
            $regrasDinamicas = array();
            foreach ($venda->rules() as $input => $regra){
                if (array_key_exists($input,$request->all())){
                    $regrasDinamicas[$input] = $regra;
                }
            }
            $request->validate($regrasDinamicas, $this->venda->feedback());
        }else{
            $request->validate($this->venda->rules(), $this->venda->feedback());
        }

        //preecher o objeto $equipe com os dados do request
        $venda->fill($request->all());
        $venda->save();

        return response()->json($venda, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Venda  $venda
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $venda = $this->venda->find($id);
        // validação do metodo destroy ********
        if($venda === null){
            return response()->json(['erro' => 'Impossivel excluir a equipe. A Equipe pesquisado não existe'],401);
        }
        $venda->delete();
        return response()->json(['msg' => 'A Equipe foi removida com sucesso'],200);


    }
}
