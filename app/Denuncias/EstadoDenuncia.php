<?php
namespace App\Denuncias;

use Illuminate\Database\Eloquent\Model;

class EstadoDenuncia extends Model
{
  protected $connection = 'mysql';
  protected $table = 'denuncia_estados';
  protected $primaryKey = 'id_denuncia_estados';
  protected $visible = array('id_denuncia_estados','descripcion');
  public $timestamps = false;

  public function estados(){
    return $this->hasMany('App\Denuncias\EstadoDenuncia','id_denuncia_estados','id_denuncia_estados');
  }
}

