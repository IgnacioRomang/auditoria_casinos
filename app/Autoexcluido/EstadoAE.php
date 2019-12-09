<?php

namespace App\Autoexcluido;

use Illuminate\Database\Eloquent\Model;

class EstadoAE extends Model
{
  protected $connection = 'mysql';
  protected $table = 'ae_estado';
  protected $primaryKey = 'id_estado';
  protected $visible = array('id_estado','id_nombre_estado','id_casino',
                              'fecha_ae','fecha_vencimiento',
                              'fecha_renovacion', 'fecha_cierre_ae',
                              'id_usuario',  'id_autoexcluido',
                              );
  protected $fillable = ['id_nombre_estado','id_casino',
                              'fecha_ae','fecha_vencimiento',
                              'fecha_renovacion', 'fecha_cierre_ae',
                              'id_usuario',  'id_autoexcluido',];

  public $timestamps = false;
}
