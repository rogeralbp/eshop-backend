<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Producto;
use App\Categoria;
use App\Carritos;
use App\Http\Controllers\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Session;
use DB;

class ProductoController extends Controller
{
    public function index()
    {
        $products = DB::table('productos')->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
        ->select('productos.*', 'categorias.nombre AS nombre_categoria')
        ->get();
        return  response()->json($products);
    }
   
    public function store(Request $request)
    {
        $banderaSKU = Producto::where('sku',$request->sku)->exists();

        if (!$banderaSKU) {
            
            $file = $request->file('imagen');
            $name = 'product_' . time() . '.' . $file->getClientOriginalExtension();
            $path =  public_path() . '\\images_products\\';
            $file->move($path, $name);
            $finalPath = '\\images_products\\'. $name ;
        
            $info= array(
                'nombre'=>$request->nombre,
                'descripcion'=>$request->descripcion,
                'imagen'=>$finalPath,
                'categoriaPadre'=>$request->categoriaPadre,
                'stock'=>$request->stock,
                'precio'=>$request->precio,
                'sku'=>$request->sku
            );
            
            $product = Producto::create($info);
            $response = Response::make(json_encode(['data'=>$product], 201)->header('Location','http://127.0.0.1:2000/products'.$product->id)->header('Content-Type','application/json'));
            return $response;
        }
        
    }
    
    public function destroy($id)
    {
        $product = Producto::find($id);
        $imagen = $product->imagen;
        unlink(public_path() . $imagen);

        if (!$product) {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se puede encontrar el producto.'])],404);
        }
        $product->delete();
        return response()->json(null,204);
    }

    public function show($id)
    {
        $product = Producto::find($id);

        if (!$product) {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se puede encontrar el producto.'])],404);
        }
        return response()->json(['status'=>'ok','data'=>$product],200);
    }

    public function update(Request $request, $id)
    {
        $file = $request->file('imagen');
        $productOriginal = Producto::find($id);
        $imagenOriginal = $productOriginal->imagen;

        if(!$file){
            
            $product = Producto::find($id);
            
            $product->nombre = $request->nombre;
            $product->descripcion = $request->descripcion;
            $product->imagen = $imagenOriginal;
            $product->categoria_id = $request->categoriaPadre;
            $product->stock = $request->stock;
            $product->precio = $request->precio;
            $product->sku = $request->sku;
            $product->save();
            
            return response()->json(['status'=>'ok','data'=>$product],200);
        }
        else
        {
            $name = 'product_' . time() . '.' . $file->getClientOriginalExtension();
            $path =  public_path() . '\\images_products\\';
            $file->move($path, $name);
            $finalPath = '\\images_products\\'. $name ;

            //borrando la antigua imagen
            unlink(public_path() . $imagenOriginal);

            $product = Producto::find($id);
            $product->nombre = $request->nombre;
            $product->descripcion = $request->descripcion;
            $product->imagen = $imagenOriginal;
            $product->categoria_id = $request->categoriaPadre;
            $product->stock = $request->stock;
            $product->precio = $request->precio;
            $product->sku = $request->sku;

            return response()->json(['status'=>'ok','data'=>$product],200);
        }
        
    }

    public function detailProduct($id) {

        $product = Producto::find($id);
        return json_encode($product) ;
    }

    public function cartItems($id) {
     
        $carritos = Carritos::where('id_usuario',$id)->count();
        return $carritos;
    }
}
