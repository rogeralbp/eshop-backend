<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Producto; 
use Log; 
use Mail;
use App\Mail\NotificacionEmail;

class SendEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'productos:email {stock}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notificacion de productos con bajo Stock';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $receiver = 'roportaperez@gmail.com';         
        $stock = $this->argument('stock');         
        $producto = Producto::where('stock', '<=', $stock)->get();          
        Mail::to($receiver)->send(new NotificacionEmail($producto));          
        Log::info("Email de Notificacion Enviado.");
    }
}
