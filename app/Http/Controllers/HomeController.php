<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Compras;
use App\Carritos;
use App\Categoria;
use App\Producto;
use Illuminate\Support\Facades\Redirect;
use Session;
use DB;

class HomeController extends Controller
{
    

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id_usuario)
    {
        $categories = Categoria::orderBy('id', 'ASC')->where('categoria_padre','NINGUNA')->get();
        $usuarios =  DB::table('users')->where('tipo_usuario','CL')->count();
        $productos =  DB::table('compras')->count();
        $compras = DB::table('compras')->sum('monto');
        $carritos = DB::table('carritos')->where('id_usuario',$id_usuario)->count();
        
        $data =  array('cantidadUsuarios'=> $usuarios,'cantidadProductosAdquiridos'=> $productos,'montoCompras'=> $compras,'carritos'=> $carritos,'categories'=> $categories);
        return json_encode($data); 
    }

    public function estadistics($idUsuario)
    {
        //$id = auth()->user()->id;
        $compra = Compras::find($id);
        $productosAdquiridos =  DB::table('compras')->where('id_usuario',$id)->count();
        $montoTotal = DB::table('compras')->where('id_usuario',$id)->sum('monto');
        $carritos = DB::table('carritos')->where('id_usuario',$id)->count();
        $data = array('productosAdquiridos'=> $productosAdquiridos,'montoTotal'=> $montoTotal,'compra'=> $id,'carritos'=> $carritos);
        return json_encode($data); 
    }

    public function cart($idUsuario){

        //$id = auth()->user()->id;
        $usuarioLogeado = User::find($id);
        $carritos = DB::table('carritos')->where('id_usuario',$id)->count();
        $precioTotal = DB::table('carritos')->where('id_usuario',$id)->sum('precio');
        
        $carritosProductos =  DB::table('carritos')->join('productos', 'carritos.id_producto', '=', 'productos.id')
        ->select('carritos.*', 'carritos.id AS id_carrito', 'productos.*')
        ->where('carritos.id_usuario',$id)->get();
        //dd($carritosProductos);
        $data = array('carritos'=> $carritos,'usuarioLogeado'=> $usuarioLogeado,'carritosProductos'=> $carritosProductos,'precioTotal'=> $precioTotal);
        return json_encode($data);
    }

    public function explore($idCategoria){

        $id = auth()->user()->id;
        $usuarioLogeado = User::find(1);
        $categoria = Categoria::find($idC);
        $categories = Categoria::orderBy('id', 'ASC')->get();
        $productosInventario = DB::table('productos')->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
        ->select('productos.*', 'categorias.nombre AS nombre_categoria')
        ->where('categorias.id', $idC)
        //->where('productos.stock', '>',  0 )
        ->get();;
        $categoriasHijas = DB::table('categorias')->where('categoria_padre',$categoria->nombre)->get();
        $carritos = DB::table('carritos')->where('id_usuario',1)->count();
        $data = array('carritos'=> $carritos,'categories'=> $categories,'productosInventario'=> $productosInventario,'categoriasHijas'=> $categoriasHijas);
        return json_encode($data);
    }

    public function history($idUsuario){

        //$id = auth()->user()->id;
        $carritos = DB::table('carritos')->where('id_usuario',$id)->count();
        $usuarioLogeado = User::find(auth()->user()->id);
        $comprasHistorial = DB::table('compras')->join('productos', 'compras.id_producto', '=', 'productos.id')
        ->select('compras.*','productos.*','compras.id AS id_compra')
        ->where('id_usuario',$id)
        ->get();
        $data = array('comprasHistorial'=> $comprasHistorial,'carritos'=> $carritos,'usuarioLogeado'=> $usuarioLogeado);
        return json_encode($data);
    }

    public function details($idProducto,$idUsuario){
        
        $usuarioLogeado = User::find($id);
        $product = Producto::find($idProducto);
        $id = auth()->user()->id;
        $carritos = DB::table('carritos')->where('id_usuario',$id)->count();
        $data = array('carritos'=> $carritos,'product'=> $product);
        return json_encode($data);
    }

    public function recordSales(){

        $comprasHistorial = DB::table('compras')
        ->join('productos', 'compras.id_producto', '=', 'productos.id')
        ->join('users', 'compras.id_usuario', '=', 'users.id')
        ->select('compras.*','productos.*','productos.nombre AS nombre_producto','productos.descripcion AS descripcion_producto','users.*','users.id AS uId','users.nombre AS nombre_usuario','compras.id AS id_compra')
        ->get();
        return json_encode($comprasHistorial);
    }


    public function generar(){

        $users = DB::table('users')
        ->select(['id','nombre','apellido1','apellido2','telefono','email','direccion'])
        ->get();
        $view= \View::make('reportes' , compact('users'))->render();
        $pdf= \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        return $pdf->stream('informe'.'.pdf');

        //return view('admin.recordSales')->with('comprasHistorial', $comprasHistorial);
    }
    public function imprimir(){
        $comprasHistorial = DB::table('compras')
        ->join('productos', 'compras.id_producto', '=', 'productos.id')
        ->join('users', 'compras.id_usuario', '=', 'users.id')
        ->select('compras.*','productos.*','productos.nombre AS nombre_producto','productos.descripcion AS descripcion_producto','users.*','users.id AS uId','users.nombre AS nombre_usuario','compras.id AS id_compra')
        ->get();
        //$today = Carbon::now()->format('d/m/Y');
        $pdf = \PDF::loadView('reportes');
        return $pdf->download('ejemplo.pdf');
}
        
   }



