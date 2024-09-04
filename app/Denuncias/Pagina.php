<?php
namespace App\Denuncias;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Denuncias\Denuncia;
use App\Denuncias\EstadoPagina;

class Pagina extends Model
{
  use SoftDeletes;

  protected $connection = 'mysql';
  protected $table = 'paginas';
  protected $primaryKey = 'id_pagina';
  protected $visible = array('id_pagina', 'usuario', 'pagina', 'pag_url');
  protected $fillable = ['usuario', 'pagina', 'pag_url'];

  public function estado(){
    return $this->belongsTo(EstadoPagina::class,'id_estado','id_estado');
  }

  public function denuncia(){
    return $this->belongsToMany(Denuncia::class,'pagina_en_denunciada','id_pagina', 'id_denuncia');
  }
}