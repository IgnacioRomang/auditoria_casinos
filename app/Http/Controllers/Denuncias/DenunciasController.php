<?php

namespace App\Http\Controllers\Denuncias;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AuthenticationController;
use Illuminate\Support\Facades\Log;
use Validator;

use App\Casino;
use App\Plataforma;
use Dompdf\Dompdf;
use View;
use PDF;
use GuzzleHttp\Client;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

use App\Denuncias\Denuncia;
use App\Denuncias\Pagina;
use App\Denuncias\EstadoDenuncia;

class DenunciasController extends Controller
{
    private static $atributos = [];

    // Singleton
    private static $instance;

    public static function getInstancia($actualizar = true){
      if (!isset(self::$instance)){
          self::$instance = new Paginas($actualizar);
      }
      return self::$instance;
    }

    public function __construct($actualizar = true){//Actualizar estados antes de cada request
    }
    // End Singleton

    // Vistas
    public function index(Request $req){
        // TODO : quitar denuncias , lo deje como ejemplo 
        $denuncias = Pagina::all();
        return view('Denuncias.index', ['paginas' => $denuncias]);
      }

    // Metodos

    public function agregar_denuncia_nueva(Request $req){
      $validator = Validator::make($req->all(), [
        'paginas_id' => 'required|array'], array(), self::$atributos);

      if ($validator->fails()) {
        return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
      }
      $paginas = PaginasController::getInstancia()->obtener_paginas_by_id($req->paginas_id);
      $nueva_denuncia = new Denuncia();
      $estado = EstadoDenuncia::find(1);
      // relacion many to one 
      $nueva_denuncia->estado()->associate($estado);
      $nueva_denuncia->save();
      foreach ($paginas as $pagina){
        $err = PaginasController::getInstancia()->actualizar_estatado($pagina);
        if($err){
          // relacion many to many
          $nueva_denuncia->paginas()->attach($paginas->id_pagina);
          $paginas->denuncia()->attach($nueva_denuncia->id_denuncia);
        }
      }
      $nueva_denuncia->save();
      return response()->json(['denuncia' => $nueva_denuncia], Response::HTTP_OK);
    }

    public function obtener_denuncias(Request $req){
      $reglas = Array();
      $filters = [
        'page_url' => 'paginas.pag_url',
        'fecha_creacion_ini' => 'denuncias.created_at',
        'fecha_creacion_fin' => 'denuncias.created_at',
        'fecha_denuncia_ini' => 'denuncias.fecha_denuncia',
        'fecha_denuncia_fin' => 'denuncias.fecha_denuncia',
      ];
      
      foreach($filters as $key => $column){
        if (!empty($req->$key)) {
          switch($key){
            case 'page_url':
              $reglas[] = [$column, 'LIKE','%' . $req->$key . '%'];
              break;
            case 'fecha_creacion_fin':
            case 'fecha_denuncia_fin':
              // si es fecha _ bla bla _ d 
              $reglas[] = [$column, '>=', $req->$key];
              break;
            case 'fecha_creacion_ini':
            case 'fecha_denuncia_ini':
              // si es fecha_blabla_h
              $reglas[] = [$column, '<=', $req->$key];
              break;
          }
        }
      }
      $sort_by = ['columna' => 'denuncias.id_denuncia', 'orden' => 'desc'];
      if(!empty($req->sort_by)){
        $sort_by = $req->sort_by;
      }

      $resultados = DB::table('denuncias')
        ->select('denuncias.*',
                  'denuncia_estados.descripcion as estado_descripcion',
                  DB::raw('COUNT(DISTINCT pagina_en_denunciada.id_pagina) as paginas_count')
                  )
        ->leftJoin('denuncia_estados', 'denuncia_estados.id_denuncia_estados' , '=' , 'denuncias.id_denuncia_estados')
        ->leftJoin('pagina_en_denunciada', 'denuncias.id_denuncia', '=', 'pagina_en_denunciada.id_denuncia')
        ->whereNull('denuncias.deleted_at')
        ->when($sort_by,function($query) use ($sort_by){
          return $query->orderBy($sort_by['columna'],$sort_by['orden']);
        })
        ->where($reglas)
        ->groupBy('denuncias.id_denuncia', 'denuncia_estados.descripcion')
        ->paginate($req->page_size);

      return response()->json(['denuncias' => $resultados]);
    }
    

    // Utiles
}