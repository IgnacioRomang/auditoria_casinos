<?php
namespace App\Autoexclusion;

use Illuminate\Database\Eloquent\Model;

class Autoexcluido extends Model
{
  protected $connection = 'mysql';
  protected $table = 'ae_datos';
  protected $primaryKey = 'id_autoexcluido';
  protected $visible = array('id_autoexcluido','apellido','nombres',
                              'nombre_localidad','nombre_provincia','nro_domicilio',
                              'nro_dni', 'telefono', 'correo',
                              'domicilio', 'id_sexo','fecha_nacimiento',
                              'id_ocupacion','id_estado_civil','id_capacitacion'
                              );
  protected $fillable = ['apellido','nombres',
                              'nombre_localidad','nombre_provincia','nro_domicilio',
                              'nro_dni', 'telefono', 'correo',
                              'domicilio', 'id_sexo',
                              'fecha_nacimiento','id_ocupacion',
                              'id_estado_civil','id_capacitacion'];

  public $timestamps = false;

  public function contacto(){
    return $this->hasOne('App\Autoexclusion\ContactoAE','id_autoexcluido','id_autoexcluido');
  }
  public function estado(){
    return $this->hasOne('App\Autoexclusion\EstadoAE','id_autoexcluido','id_autoexcluido');
  }
  public function importacion(){
    return $this->hasOne('App\Autoexclusion\ImportacionAE','id_autoexcluido','id_autoexcluido');
  }
  public function encuesta(){
    return $this->hasOne('App\Autoexclusion\Encuesta','id_autoexcluido','id_autoexcluido');
  }

  public function ocupacion(){
    return $this->belongsTo('App\Autoexclusion\OcupacionAE','id_ocupacion','id_ocupacion');
  }
  public function estadoCivil(){
    return $this->belongsTo('App\Autoexclusion\EstadoCivilAE','id_estado_civil','id_estado_civil');
  }
  public function capacitacion(){
    return $this->belongsTo('App\Autoexclusion\CapacitacionAE','id_capacitacion','id_capacitacion');
  }
  //id_provincia
  //id_localidad
  //id_sexo
  //id_ocupacion
  //id_estado_civil
  //id_capacitacion
}
