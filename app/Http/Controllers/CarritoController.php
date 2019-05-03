<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Producto;
use App\Carritos;
use App\User;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Session;
use DB;

class CarritoController extends Controller
{
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $idProducto)
    {
        $cantidadSolicitada = $request->cantidadArticulo;
        $product = Producto::find($idProducto);
        $usuarioLogeado = User::find(auth()->user()->id);
        
        if ($product->stock >= $cantidadSolicitada ) {
            $carrito = new Carritos;
            $carrito->id_usuario = auth()->user()->id;
            $carrito->id_producto = $product->id;
            $carrito->cantidad = $cantidadSolicitada;
            $carrito->precio = ($product->precio * $cantidadSolicitada);
            
            $carrito->save();
            //return Redirect::to('/details/' . $idProducto );
            $response = response()->json((['data'=>$carrito]), 201)->header('Content-Type','application/json');
            return $response;

        }else{
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se puede guardar el item en el carrito, cantidad solicitada mayo que la cantidad disponible.'])],404);
        }

    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $carrito = Carritos::find($id);

        if (!$carrito) {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se puede encontrar el carrito.'])],404);
        
        }else{

            $carrito->delete();
            return response()->json(null,204);
        }

        //return Redirect::to('/cart');
    }
}
