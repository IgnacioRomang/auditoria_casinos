<?php
namespace App\Denuncias;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pagina extends Model
{
  use SoftDeletes;

  protected $connection = 'mysql';
  protected $table = 'paginas';
  protected $primaryKey = 'id_paginas';
  protected $visible = array('id_paginas', 'usuario', 'pagina', 'pag_url');
  protected $fillable = ['usuario', 'pagina', 'pag_url'];

  public function estado(){
    return $this->belongsTo('App\Denuncias\EstadoPagina','id_estado','id_estado');
  }

  public function denuncia(){
    return $this->belongsToMany('App\Denuncias\Denuncia','pagina_en_denunciada','id_denuncia','id_denuncia');
  }
}