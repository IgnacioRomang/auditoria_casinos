<?php

namespace App\Http\Controllers\Autoexclusion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\Autoexclusion\AutoexclusionController;
use Validator;

use App\Casino;
use App\Plataforma;
use App\Autoexclusion as AE;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class APIAEController extends Controller
{
    private static $atributos = [];

    private static $instance;
    public static function getInstancia($actualizar = true){
      if (!isset(self::$instance)){
          self::$instance = new APIAEController($actualizar);
      }
      return self::$instance;
    }

    public function __construct($actualizar = true){//Actualizar estados antes de cada request
      if($actualizar) AutoexclusionController::getInstancia(false)->actualizarVencidosRenovados();
    }

    public function fechas(Request $request,string $dni){
        //No actualizo (false) porque ya se actualiza al crear al ser construido el controlador por Laravel
        $id = AutoexclusionController::getInstancia(false)->existeAutoexcluido($dni);
        //0 No tuvo, -1 Ya tuvo y estan vencidos
        if($id <= 0) return $this->errorOut(['error' => 'SIN AE']);
        
        $ae = AE\Autoexcluido::find($id);
        //No deberia pasar pero lo dejo chequeado por las dudas
        if(is_null($ae)) return $this->errorOut(['error' => 'ERROR UNREACHABLE']);

        $e = $ae->estado;
        $ret = ['fecha_ae' => $e->fecha_ae,'fecha_cierre_ae' => $e->fecha_cierre_ae];
        if($ae->es_primer_ae){
            $ret['fecha_renovacion']  = $e->fecha_renovacion;
            $ret['fecha_vencimiento'] = $e->fecha_vencimiento;
            if(!is_null($e->fecha_revocacion_ae)) $ret['fecha_revocacion_ae'] = $e->fecha_revocacion_ae;
        }
        return $ret;
    }
    
    public function finalizar(Request $request,string $dni){
        $AEC = AutoexclusionController::getInstancia(false);
        //No actualizo (false) porque ya se actualiza al crear al ser construido el controlador por Laravel
        $id = $AEC->existeAutoexcluido($dni);
        if($id <= 0) return $this->errorOut(['error' => 'SIN AE']);
        $ret = $AEC->cambiarEstadoAE($id,4);//Fin. por AE
        return $ret !== 1? $ret : response()->json('Finalizado',200);
    }
    
    private function verificarConflictoFechas($dni,$fecha_ae,bool $finalizado){//Verifico que no pise a algun AE ya en la BD
        $q = DB::table('ae_datos as aed')->select('aee.*')
        ->join('ae_estado as aee','aee.id_autoexcluido','=','aed.id_autoexcluido')
        ->whereNull('aed.deleted_at')->whereNull('aee.deleted_at')
        ->where('aed.nro_dni','=',$dni);
    
        /*
        Agarra dos casos (el de abajo es el que se agregaria)
    
        fecha_ae                                         fecha_cierre_ae
            ┌──────────────────────────────────────────────┐
            │                                              │
                │                                              │
                └──────────────────────────────────────────────┘
            $fecha_ae
        fecha_ae                                         fecha_cierre_ae
            ┌──────────────────────────────────────────────┐
            │                                              │
                │                      │
                └──────────────────────┘
            $fecha_ae
        */
        $dentro_algun_completo =  (clone $q)->whereNull('aee.fecha_revocacion_ae')
        ->where('aee.fecha_ae','<=',$fecha_ae)->where('aee.fecha_cierre_ae','>=',$fecha_ae)
        ->count() > 0;
        if($dentro_algun_completo) return 1;
    
        /*
        Agarra dos casos (el de abajo es el que se agregaria)
    
        fecha_ae                 fecha_vencimiento
            ┌──────────────────────┐
            │                      │
                │                                              │
                └──────────────────────────────────────────────┘
            $fecha_ae
        fecha_ae                 fecha_vencimiento
            ┌──────────────────────┐
            │                      │
                │                      │
                └──────────────────────┘
            $fecha_ae
        */
        $dentro_algun_finalizado = (clone $q)->whereNotNull('aee.fecha_revocacion_ae')
        ->where('aee.fecha_ae','<=',$fecha_ae)->where('aee.fecha_vencimiento','>=',$fecha_ae)
        ->count() > 0;
        if($dentro_algun_finalizado) return 2;
    
        $fecha_fin = null;
        {
          $fechas = AutoexclusionController::getInstancia(false)->generarFechas($fecha_ae);
          if($finalizado) $fecha_fin = $fechas->fecha_vencimiento;
          else            $fecha_fin = $fechas->fecha_cierre_ae;
        }
    
        /*
        Agarra dos casos (el de abajo es el que se agregaria)
                                  fecha_ae                                         fecha_cierre_ae
                                        ┌──────────────────────────────────────────────┐
                                        │                                              │
                                │                      │
                                └──────────────────────┘
                                                  $fecha_fin
                                  fecha_ae                                         fecha_cierre_ae
                                        ┌──────────────────────────────────────────────┐
                                        │                                              │
        │                                              │
        └──────────────────────────────────────────────┘
                                                  $fecha_fin
        */
        $se_extiende_dentro_de_alguno_ya_existente_completo = (clone $q)->whereNull('aee.fecha_revocacion_ae')
        ->where('aee.fecha_ae','<=',$fecha_fin)->where('aee.fecha_cierre_ae','>=',$fecha_fin)
        ->count() > 0;
        if($se_extiende_dentro_de_alguno_ya_existente_completo) return 3;
    
        /*
        Agarra dos casos (el de abajo es el que se agregaria)
                                  fecha_ae                    fecha_vencimiento
                                        ┌──────────────────────┐
                                        │                      │
                                │                      │
                                └──────────────────────┘
                                                  $fecha_fin
                                  fecha_ae                    fecha_vencimiento
                                        ┌──────────────────────┐
                                        │                      │
        │                                              │
        └──────────────────────────────────────────────┘
                                                  $fecha_fin
        */
        $se_extiende_dentro_de_alguno_ya_existente_finalizado = (clone $q)->whereNotNull('aee.fecha_revocacion_ae')
        ->where('aee.fecha_ae','<=',$fecha_fin)->where('aee.fecha_vencimiento','>=',$fecha_fin)
        ->count() > 0;
        if($se_extiende_dentro_de_alguno_ya_existente_finalizado) return 4;
    
        return 0;
    }
    
    public function agregar(Request $request){
        $validator = Validator::make($request->all(), [
          'ae_datos.nro_dni'          => 'required|integer',
          'ae_datos.apellido'         => 'required|string|max:100',
          'ae_datos.nombres'          => 'required|string|max:150',
          'ae_datos.fecha_nacimiento' => 'required|date',
          'ae_datos.sexo'             => 'required|string|max:4|exists:ae_sexo,codigo',
          'ae_datos.domicilio'        => 'required|string|max:100',
          'ae_datos.nro_domicilio'    => 'required|integer',
          'ae_datos.piso'             => 'nullable|string|max:5',
          'ae_datos.dpto'             => 'nullable|string|max:5',
          'ae_datos.codigo_postal'    => 'required|string|max:10',
          'ae_datos.nombre_localidad' => 'required|string|max:200',
          'ae_datos.nombre_provincia' => 'required|string|max:200',
          'ae_datos.telefono'         => 'required|string|max:200',
          'ae_datos.correo'           => 'required|string|max:100',
          'ae_datos.ocupacion'        => 'nullable|string|max:4|exists:ae_ocupacion,codigo',
          'ae_datos.capacitacion'     => 'nullable|string|max:4|exists:ae_capacitacion,codigo',
          'ae_datos.estado_civil'     => 'nullable|string|max:4|exists:ae_estado_civil,codigo',
          'ae_estado.fecha_ae'        => 'required|date',
          'ae_estado.fecha_revocacion_ae' => 'nullable|date'
        ], array(), self::$atributos)->after(function($validator){
          if($validator->errors()->any()) return;
          $data = $validator->getData();
          $se_puede_agregar = $this->verificarConflictoFechas($data['ae_datos']['nro_dni'],$data['ae_estado']['fecha_ae'],false);
          if($se_puede_agregar > 0){//Aca tal vez deberiamos dejar que conflicte... simplemente verificar que no tenga AE vigentes...
            return $validator->errors()->add('ae_datos.nro_dni','AE VIGENTE');
          }
          if($data['ae_estado']['fecha_ae'] > date('Y-m-d')){
            return $validator->errors()->add('ae_estado.fecha_ae','No puede agregar un AE en esa fecha');
          }
          if(!empty($data['ae_estado']['fecha_revocacion_ae'])){//Si envia uno finalizado
            //Verificar que sea su primer autoexclusion
            $AEC = AutoexclusionController::getInstancia(false);
            if($AEC->existeAutoexcluido($data['ae_datos']['nro_dni']) != 0){
              return $validator->errors()->add('ae_estado.fecha_revocacion_ae','No puede finalizar un AE repetido');
            }
            //Verificar que la fecha de revocacion tenga sentido (este dentro de (frenov,fvencimiento])
            $fs = $AEC->generarFechas($data['ae_estado']['fecha_ae']);
            if($data['ae_estado']['fecha_revocacion_ae'] <= $fs->fecha_renovacion){
              return $validator->errors()->add('ae_estado.fecha_revocacion_ae','No puede finalizar un AE en esa fecha');
            }
            if($data['ae_estado']['fecha_revocacion_ae'] > $fs->fecha_vencimiento){
              return $validator->errors()->add('ae_estado.fecha_revocacion_ae','No puede finalizar un AE en esa fecha');
            }
            if($data['ae_estado']['fecha_revocacion_ae'] > date('Y-m-d')){
              return $validator->errors()->add('ae_estado.fecha_revocacion_ae','No puede finalizar un AE en esa fecha');
            }
          }
        });
    
        if($validator->errors()->any()) return $this->errorOut($validator->errors());
    
        $request = $request->all();
    
        //Sexo siempre viene asi que en realidad el tercer valor nunca se usa
        $except = ['sexo'         => ['id_sexo',        'ae_sexo', 'X'],
                   'ocupacion'    => ['id_ocupacion',   'ae_ocupacion', 'NC'],
                   'capacitacion' => ['id_capacitacion','ae_capacitacion', 'NC'],
                   'estado_civil' => ['id_estado_civil','ae_estado_civil', 'NC']];
    
        foreach($except as $key => $defecto){//Pongo valores por defecto "No contesta" si no lo envia.
          if(!array_key_exists($key,$request['ae_datos'])) $request['ae_datos'][$key] = $defecto[2];
        }
    
        $api_token = AuthenticationController::getInstancia()->obtenerAPIToken();
        DB::transaction(function() use($request,$api_token,$except){
          $ae = new AE\Autoexcluido;
          $ae_datos = $request['ae_datos'];
    
          foreach($ae_datos as $key => $val){
            if(!array_key_exists($key,$except)) $ae->{$key} = $val;
            else{
              $table = $except[$key][1];
              $id_name = $except[$key][0];
              $row = DB::table($table)->select($id_name)->where('codigo',$val)->get()->first();
              $ae->{$id_name} = $row->{$id_name};
            }
          }
          $ae->save();
    
          $contacto = new AE\ContactoAE;
          $contacto->id_autoexcluido = $ae->id_autoexcluido;
          $contacto->save();
    
          $ae_estado = $request['ae_estado'];
          $ae_estado['id_usuario'] = AuthenticationController::getInstancia()->obtenerIdUsuario();
          if(empty($ae_estado['fecha_revocacion_ae'])){
            $ae_estado['id_nombre_estado'] = 1;//Vigente
          }else{
            $ae_estado['id_nombre_estado'] = 4;//Fin. por AE
          }
          $ae_estado['id_plataforma'] = ($api_token->metadata ?? [])['id_plataforma'] ?? null;
          $AEC = AutoexclusionController::getInstancia(false);
          $AEC->setearEstado($ae,$ae_estado);
          $AEC->subirImportacionArchivos($ae,[]);
        });
    
        return response()->json('Agregado',200);
    }

    private function errorOut($map){
        return response()->json($map,422);
    }
}
