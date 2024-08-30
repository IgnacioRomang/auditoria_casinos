<?php
namespace App\Denuncias;

use Illuminate\Database\Eloquent\Model;

class EstadoPagina extends Model
{
  protected $connection = 'mysql';
  protected $table = 'paginas_estados';
  protected $primaryKey = 'id_estado';
  protected $visible = array('id_estado','descripcion');
  public $timestamps = false;

  public function estados(){
    return $this->hasMany('App\Denuncias\EstadoPagina','id_estado','id_estado');
  }
}