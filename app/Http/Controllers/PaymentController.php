<?php
namespace App\Http\Controllers;

use App\User;
use App\Carritos;
use App\Producto;
use App\Compras;
//use Illuminate\Support\Facades\Redirect;
use DB;



use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;

/** All Paypal Details class **/
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use Redirect;
use Session;
use URL;

class PaymentController extends Controller
{
    private $_api_context;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

        /** PayPal api context **/
        $paypal_conf = \Config::get('paypal');
        $this->_api_context = new ApiContext(new OAuthTokenCredential(
            $paypal_conf['client_id'],
            $paypal_conf['secret'])
        );
        $this->_api_context->setConfig($paypal_conf['settings']);

    }
    
    public function index()
    {
        return view('paywithpaypal');
    }

    public function store($idProducto)
    {
        $cantidadSolicitada = Input::get('cantidadArticulo');
        $product = Producto::find($idProducto);
        $usuarioLogeado = User::find(auth()->user()->id);
        
        if ($product->stock >= $cantidadSolicitada ) {
            $carrito = new Carritos;
            $carrito->id_usuario = auth()->user()->id;
            $carrito->id_producto = $product->id;
            $carrito->cantidad = $cantidadSolicitada;
            $carrito->precio = ($product->precio * $cantidadSolicitada);
        
            //$cantidadRestante = ($product->stock - $carrito->cantidad);
            //$product->stock = $cantidadRestante;
            //$product->save();
        
            if ($carrito->save()) {
                Session::flash('message', 'Carrito añadido correctamente');
                Session::flash('class', 'success');
                
            } else {
                Session::flash('message', 'Ha ocurrido un error');
                Session::flash('class', 'danger');
            }
            return Redirect::to('/details/' . $idProducto );
        
        }else{
            Session::flash('message', 'Ha ocurrido un error, la cantidad solicitada no esta disponible.');
            Session::flash('class', 'danger');

            return Redirect::to('/details/' . $idProducto);
        }

    }
    public function payWithpaypal(Request $request)
    {

        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        $item_1 = new Item();

        $item_1->setName('Item 1') /** item name **/
            ->setCurrency('USD')
            ->setQuantity(1)
            ->setPrice($request->get('amount')); /** unit price **/

        $item_list = new ItemList();
        $item_list->setItems(array($item_1));

        $amount = new Amount();
        $amount->setCurrency('USD')
            ->setTotal($request->get('amount'));

        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($item_list)
            ->setDescription('Your transaction description');

        $redirect_urls = new RedirectUrls();
        $redirect_urls->setReturnUrl(URL::to('status')) /** Specify return URL **/
            ->setCancelUrl(URL::to('status'));

        $payment = new Payment();
        $payment->setIntent('Sale')
            ->setPayer($payer)
            ->setRedirectUrls($redirect_urls)
            ->setTransactions(array($transaction));
        /** dd($payment->create($this->_api_context));exit; **/
        try {

            $payment->create($this->_api_context);

        } catch (\PayPal\Exception\PPConnectionException $ex) {

            if (\Config::get('app.debug')) {

                \Session::put('error', 'Connection timeout');
                return Redirect::to('/');

            } else {

                \Session::put('error', 'Some error occur, sorry for inconvenient');
                return Redirect::to('/');

            }

        }

        foreach ($payment->getLinks() as $link) {

            if ($link->getRel() == 'approval_url') {

                $redirect_url = $link->getHref();
                break;

            }

        }

        /** add payment ID to session **/
        Session::put('paypal_payment_id', $payment->getId());

        if (isset($redirect_url)) {

            /** redirect to paypal **/
            return Redirect::away($redirect_url);

        }

        \Session::put('error', 'Unknown error occurred');
        return Redirect::to('/');

    }

    public function getPaymentStatus()
    {
        /** Get the payment ID before session clear **/
        $payment_id = Session::get('paypal_payment_id');

        /** clear the session payment ID **/
        Session::forget('paypal_payment_id');
        if (empty(Input::get('PayerID')) || empty(Input::get('token'))) {

            \Session::put('error', 'Payment failed');
            return Redirect::to('/');

        }

        $payment = Payment::get($payment_id, $this->_api_context);
        $execution = new PaymentExecution();
        $execution->setPayerId(Input::get('PayerID'));

        /**Execute the payment **/
        $result = $payment->execute($execution, $this->_api_context);

        if ($result->getState() == 'approved') {

            \Session::put('success', 'Payment success');
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

                    Session::flash('message', 'Compras realizadas correctamente');
                    Session::flash('class', 'success');
                    
                    $product = Producto::find($carritoPrevio->id_producto);
                    $cantidadRestante = ($product->stock - $carritoPrevio->cantidad);
                    $product->stock = $cantidadRestante;
                    $product->save();
                    $carrito = Carritos::find($idCarrito);                    
                    $carrito->delete();
                    
                } else {
                    Session::flash('message', 'Ha ocurrido un error');
                    Session::flash('class', 'danger');
                }
        }else{

            Session::flash('message', 'Ha ocurrido un error, la cantidad solicitada no esta disponible.');
            Session::flash('class', 'danger');
            
        }

        }
        
            return Redirect::to('/cart');

        }

        \Session::put('error', 'Payment failed');
        return Redirect::to('/');

    }

}
