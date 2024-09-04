<?php
namespace App\Denuncias;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Denuncias\Pagina;
use App\Denuncias\EstadoDenuncia;

class Denuncia extends Model
{
  use SoftDeletes;

  protected $connection = 'mysql';
  protected $table = 'denuncias';
  protected $primaryKey = 'id_denuncia';
 
  public function estado(){
    return $this->belongsTo(EstadoDenuncia::class,'id_denuncia_estados','id_denuncia_estados');
  }

  public function paginas() { 
    return $this->belongsToMany(Pagina::class,'pagina_en_denunciada','id_denuncia', 'id_pagina');
  }
}