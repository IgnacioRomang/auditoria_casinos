<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ContadorHorario;
use App\DetalleContadorHorario;
use Validator;
use Illuminate\Support\Facades\DB;
use App\TipoMoneda;
use App\Http\Controllers\UsuarioController;

class AlertasContadoresController extends Controller
{
  private static $atributos = [
  ];
  private static $instance;

  public static function getInstancia() {
    if (!isset(self::$instance)) {
      self::$instance = new AlertasContadoresController();
    }
    return self::$instance;
  }

  public function buscarTodo(){
    $user = UsuarioController::getInstancia()->quienSoy()['usuario'];
    return view('seccionAlertasContadores',['casinos' => $user->casinos,'tipo_monedas' => TipoMoneda::all()]);
  }

  public function buscarContadores(Request $request){
    $user = UsuarioController::getInstancia()->quienSoy()['usuario'];
    $cas = [];
    foreach($user->casinos as $c) $cas[] = $c->id_casino;

    $reglas = [];
    if($request->id_casino != "") $reglas[] = ['ch.id_casino','=',$request->id_casino];
    if($request->id_tipo_moneda != "") $reglas[] = ['ch.id_tipo_moneda','=',$request->id_tipo_moneda];
    if($request->fecha_desde != "") $reglas[] = ['ch.fecha','>=',$request->fecha_desde];
    if($request->fecha_hasta != "") $reglas[] = ['ch.fecha','<=',$request->fecha_hasta];
    $sort_by = ['columna' => 'ch.id_contador_horario','orden' => 'desc'];
    if(!empty($request->sort_by)) $sort_by = $request->sort_by;

    $resultados = DB::table('contador_horario as ch')
    ->select('ch.id_contador_horario','ch.fecha','c.nombre as casino','tm.descripcion as moneda',DB::raw('RAND()>0.5 as alertas_validadas'))
    ->join('casino as c','c.id_casino','=','ch.id_casino')
    ->join('tipo_moneda as tm','tm.id_tipo_moneda','=','ch.id_tipo_moneda')
    ->whereIn('ch.id_casino',$cas)->where($reglas)
    ->orderBy($sort_by['columna'],$sort_by['orden'])
    ->paginate($request->page_size);
    return $resultados;
  }

  public function obtenerDetalles($id_contador_horario){
    $detalles = DB::table('detalle_contador_horario')
    ->select('maquina.nro_admin','detalle_contador_horario.id_detalle_contador_horario')
    ->join('maquina','maquina.id_maquina','=','detalle_contador_horario.id_maquina')
    ->where('id_contador_horario',$id_contador_horario)->get();
    return ['detalles' => $detalles,'alertas' => 9999999];
  }

  public function obtenerDetalleCompleto($id_detalle_contador_horario){
    //@STUB: tal vez guardar los demas horarios en un CSV y consultarlos aca, total es algo que se consultaria 1 sola vez
    //Si guardamos el CSV que mandan ellos, tendrian que mandarlo ordenado por NRO_ADMIN y luego por HORA para hacer la busqueda eficiente.
    $detalles = DB::table('detalle_contador_horario as dch')
    ->selectRaw('"07:00" as hora,IFNULL(dch.isla,"SIN INF.") as isla, dch.coinin, dch.coinout, dch.jackpot, dch.progresivo')
    ->where('dch.id_detalle_contador_horario',$id_detalle_contador_horario)->get();
    $alertas = [
      [
        'hora' => '9:99', 'descripcion' => 'TEST!'
      ],
      [
        'hora' => '99:09', 'descripcion' => '......TEST!'
      ]
    ];
    return ['estado' => 'SIN DETALLES','detalles' => $detalles,'alertas' => $alertas,'observaciones' => 'OBSERVACIONES TEST'];
  }
}
