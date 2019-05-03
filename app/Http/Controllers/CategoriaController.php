<?php

namespace App\Http\Controllers;

use App\Categoria;
use App\Producto;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Session;
use DB;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Categoria::orderBy('id', 'ASC')->get();
        return  response()->json($categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $categoria = Categoria::create($request->all());
        $response = response()->json((['data'=>$categoria]), 201)->header('Location','http://127.0.0.1:2000/api/categories'.$categoria->id)->header('Content-Type','application/json');
        return $response;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $categoria = Categoria::find($id);

        if (!$categoria) {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se puede encontrar la categoria.'])],404);
        }
        return response()->json(['status'=>'ok','data'=>$categoria],200);
    }

    

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $categoria = Categoria::find($id)->update($request->all());
        if (!$categoria) {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se puede encontrar la categoria.'])],404);
        }
        return response()->json(['status'=>'ok','data'=>$categoria],200);
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {   
        $categoria = Categoria::find($id);
        $bandera = Producto::where('nombre_categoria',$categoria->nombre);

        if (!$categoria) {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se puede encontrar la categoria.'])],404);
        
        }else{

            if($bandera){
                return response()->json(['errors'=>array(['code'=>404,'message'=>'No se puede eliminar la categoria, existen productos que dependen de la misma.'])],404);
            }

            $categoria->delete();
            return response()->json(null,204);
        }
    }
}
