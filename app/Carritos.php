<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Carritos extends Model
{
    protected $table = "carritos";
    protected $fillable=['id_usuario', 'id_producto','cantidad','precio',];
}
