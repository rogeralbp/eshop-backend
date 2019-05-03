<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Carritos;
use App\Producto;
use App\Compras;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Session;
use DB;

class CompraController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $carritoPrevios =  DB::table('carritos')->join('productos', 'carritos.id_producto', '=', 'productos.id')
        ->select('carritos.*', 'carritos.id AS id_carrito', 'productos.*')
        ->where('carritos.id_usuario',auth()->user()->id)->get();
        
        foreach($carritoPrevios as $carritoPrevio){

        $compra =  new Compras;
        $compra->id_usuario = $carritoPrevio->id_usuario;
        $compra->id_producto = $carritoPrevio->id_producto;
        $compra->monto = ($carritoPrevio->precio * $carritoPrevio->cantidad);
        $compra->cantidad = $carritoPrevio->cantidad;        
        $hoy = getdate();
        $fechaActual =$hoy['year'].'-'.$hoy['mon'].'-'.$hoy['mday'];
        $compra->fecha = $fechaActual;
        
        $product = Producto::find($carritoPrevio->id_producto);
        $idCarrito = $carritoPrevio->id_carrito;
        if ( $product->stock >= $carritoPrevio->cantidad ) {
                
                if ($compra->save()) {

                    $product = Producto::find($carritoPrevio->id_producto);
                    $cantidadRestante = ($product->stock - $carritoPrevio->cantidad);
                    $product->stock = $cantidadRestante;
                    $product->save();
                    $carrito = Carritos::find($idCarrito);                    
                    $carrito->delete();

                    return response()->json(['status'=>'ok','data'=>$compra],200);

                } else {
                    return response()->json(['errors'=>array(['code'=>404,'message'=>'No se puede ejecutar la compra.'])],404);
        
                }
        }else{
            
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se puede ejecutar la compra.'])],404);
        }

        }
        
        //return Redirect::to('/cart');
    }
    public function paypal(){
        //$id = auth()->user()->id;
        $usuarioLogeado = User::find(1);
        $carritos = DB::table('carritos')->where('id_usuario',1)->count();
        $precioTotal = DB::table('carritos')->where('id_usuario',1)->sum('precio');
        
        $carritosProductos =  DB::table('carritos')->join('productos', 'carritos.id_producto', '=', 'productos.id')
        ->select('carritos.*', 'carritos.id AS id_carrito', 'productos.*')
        ->where('carritos.id_usuario',1)->get();
        //dd($carritosProductos);

        $data= array('usuarioLogeado'=>$usuarioLogeado,'carritos'=>$carritos,'carritosProductos'=>$carritosProductos,'precioTotal'=>$precioTotal);
        return json_encode($data);
        
        //return view('paywithpaypal');
    }
}
