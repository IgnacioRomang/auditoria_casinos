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

use App\Denuncias\Pagina;
use App\Denuncias\EstadoPagina;

class DenunciasController extends Controller
{
    private static $atributos = [];

    // Singleton
    private static $instance;

    public static function getInstancia($actualizar = true){
      if (!isset(self::$instance)){
          self::$instance = new DenunciasController($actualizar);
      }
      return self::$instance;
    }

    public function __construct($actualizar = true){//Actualizar estados antes de cada request
      if($actualizar) $this->updateEstadoDeDenuncias();
    }
    // End Singleton

    public function updateEstadoDeDenuncias()
    {
      //TODO: Implement updateDenuncias() method.
    }

    public function agregar_pagina_nueva(Request $req){

      $validator = Validator::make($req->all(), [
        'usuario' => 'required',
        'pag_url' => 'required'], array(), self::$atributos);

      if ($validator->fails()) {
        return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
      }
      $nueva_pagina = new Pagina();
      $nueva_pagina->usuario = $req->input('usuario');
      $nueva_pagina->pagina = $this->get_pagina($req->input('pag_url'));
      $nueva_pagina->pag_url = $req->input('pag_url');
      $estado = EstadoPagina::find(1);
      $nueva_pagina->estado()->associate($estado);
      $nueva_pagina->save();
      return response()->json(['pagina' => $nueva_pagina], Response::HTTP_OK);
    }

    public function obtener_paginas(Request $req){
      $reglas = Array();
      $filters = [
        'usuario' => 'paginas.usuario',
        'url' => 'paginas.pagina',
        'page_url' => 'paginas.pag_url',
        'fecha_creacion_ini' => 'paginas.created_at',
        'fecha_creacion_fin' => 'paginas.created_at',
        'fecha_denuncia_ini' => 'paginas.fecha_denuncia',
        'fecha_denuncia_fin' => 'paginas.fecha_denuncia',
      ];
      
      foreach($filters as $key => $column){
        if (!empty($req->$key)) {
          switch($key){
            case 'usuario':
            case 'url':
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
      $sort_by = ['columna' => 'paginas.id_pagina', 'orden' => 'desc'];
      if(!empty($req->sort_by)){
        $sort_by = $req->sort_by;
      }

      $resultados = DB::table('paginas')
        ->join('paginas_estados'         , 'paginas.id_estado' , '=' , 'paginas_estados.id_estado')
        ->whereNull('paginas.deleted_at')
        ->when($sort_by,function($query) use ($sort_by){
          return $query->orderBy($sort_by['columna'],$sort_by['orden']);
        })
        ->where($reglas)
        ->paginate($req->page_size);

      return response()->json(['paginas' => $resultados]);
    }
    // Utiles
    private function get_pagina($url){
      $parsedUrl = parse_url($url, PHP_URL_HOST);
      if (strpos($parsedUrl, 'facebook.com') !== false) {
          return 'Facebook';
      } elseif (strpos($parsedUrl, 'instagram.com') !== false) {
          return 'Instagram';
      } elseif (strpos($parsedUrl, 'twitter.com') !== false) {
          return 'Twitter';
      } else {
          return 'Otra plataforma';
      }
      return $url;
    }

    // Vistas
    public function index(Request $req){
      $denuncias = Pagina::all();
      return view('Denuncias.index', ['denuncias' => $denuncias]);
    }
}