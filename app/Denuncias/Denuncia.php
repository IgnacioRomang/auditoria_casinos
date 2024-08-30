<?php
namespace App\Denuncias;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Denuncia extends Model
{
  use SoftDeletes;

  protected $connection = 'mysql';
  protected $table = 'paginas_denuncias';
  protected $primaryKey = 'id_denuncia';
 
  public function estado(){
    return $this->belongsTo('App\Denuncias\EstadoDenuncia','id_estado','id_estado');
  }

  public function paginas(){
    return $this->hasMany('App\Denuncias\Pagina','id_denuncia','id_denuncia');
  }
}