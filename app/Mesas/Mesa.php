<?php

namespace App\Mesas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mesa extends Model
{
  use SoftDeletes;
  protected $connection = 'mysql';
  protected $table = 'mesa_de_panio';
  protected $primaryKey = 'id_mesa_de_panio';
  protected $visible = array('id_mesa_de_panio','nro_mesa','nombre','descripcion',
                             'id_juego_mesa','id_casino','id_moneda','id_sector_mesas');


  protected $fillable = ['nro_mesa','nombre','descripcion',
                             'id_juego_mesa','id_casino','id_moneda','id_sector_mesas'];

  public function sector(){
    return $this->belongsTo('App\Mesas\SectorMesas','id_sector_mesas','id_sector_mesas');
  }

  public function juego(){
    return $this->belongsTo('App\Mesas\JuegoMesa','id_juego_mesa','id_juego_mesa');
  }

  public function casino(){
    return $this->belongsTo('App\Casino','id_casino','id_casino');
  }

  public function moneda(){
    return $this->belongsTo('App\Mesas\Moneda','id_moneda','id_moneda');
  }

  public function mesa_a_pedido(){
    return $this->hasMany('App\Mesas\Mesa','id_mesa_de_panio','id_mesa_de_panio');
  }

  public function tipo_mesa(){
    return $this->belongsTo('App\Mesas\TipoMesa','id_tipo_mesa','id_tipo_mesa');
  }

  public function getTableName(){
    return $this->table;
  }
  public function getId(){
    return $this->id_mesa_de_panio;
  }
}