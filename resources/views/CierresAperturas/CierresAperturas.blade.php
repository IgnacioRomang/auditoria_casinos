<?php
use App\Http\Controllers\AuthenticationController;
?>
@extends('includes.dashboard')

@section('headerLogo')

@endsection
@section('estilos')
<link href="/css/fileinput.css" media="all" rel="stylesheet" type="text/css"/>
<link href="css/bootstrap-datetimepicker.css" rel="stylesheet"/>
<link href="css/bootstrap-datetimepicker.min.css" rel="stylesheet"/>
<link href="css/bootstrap.min.css" rel="stylesheet"/>
<link href="/themes/explorer/theme.css" media="all" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" href="/css/lista-datos.css">
<link rel="stylesheet" href="/js/jquery-ui-1.12.1.custom/jquery-ui.css">
<link rel="stylesheet" href="/css/paginacion.css">
<style>
  table.tablaResultados thead tr th {
    font-size:14px;
    text-align:center !important;
  }
  tr.filaResultado td {
    text-align:center !important;
  }
  section {
    padding: 0 !important;
  }
  .contenedor > nav {
    display: none;
  }
  .tabs {
    width: 100%;
    display: flex;
    margin-bottom: 10px;
  }
  .tabs > div {
    flex: 1;
    margin: 0;
    padding: 0;
  }
  .tabs a {
    padding: 15px 10px;
    font-family:Roboto-condensed;
    font-size:20px;
    background: white;
    display: inline-block;
    width: 100%;
    height: 100%;
    text-align: center;
    text-decoration: none;
    border: 1px solid transparent;
    border-top-left-radius: 4px;
    border-top-right-radius: 4px
  }
  .tabs a.active {
    color: #555;
    cursor: default;
    border-color: rgb(221, 221, 221);
  }
</style>
@endsection

@section('contenidoVista')

<?php 
  $CamelCase_to_TitleCase = function($s){
    $arr = preg_split('/(^[^A-Z]+|[A-Z][^A-Z]+)/',$s,-1,PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
    return implode(' ',$arr);
  };
  
  $tiene_permiso = function($p) use ($usuario){
    if(empty($p)) return true;
    return AuthenticationController::getInstancia()->usuarioTienePermiso($usuario->id_usuario,$p);
  };
  
  $tabs = [
    'aperturas' => [
      'botones' => [
        'Generar Plantilla' => '[data-js-generar-plantilla]',
        'Cargar Apertura' => '[data-js-cargar-apertura]',
        'Apertura A Pedido' => '[data-js-apertura-a-pedido]'
      ],
      'botones_solo_adm' => ['Apertura A Pedido'],
      'buscar' => 'aperturas/filtrosAperturas',
      'resultados' => [
        'fecha' => 'fecha',
        'nro_mesa' => 'nro_mesa',
        'juego' => 'juego_mesa.siglas',
        'hora' => 'hora',
        'moneda' => 'moneda.siglas',
        'casino' => 'casino.nombre',
        'estado' => 'apertura_mesa.id_estado_cierre',
        'acciones' =>  [//attr => icono, permiso, html extra
          'data-js-ver-apertura' => ['fa-search-plus',null,'data-estados="1,2,3,4"'],
          'data-js-desvincular' => ['fa-unlink','m_validar_aperturas','data-estados="2,3,4"'],
          'data-js-modificar-apertura' => ['fa-pencil-alt',null,'data-estados="1"'],
          'data-js-validar-apertura' => ['fa-check','m_validar_aperturas','data-estados="1"'],
          'data-js-eliminar-apertura' => ['fa-trash','m_eliminar_cierres_y_aperturas','data-estados="1"'],
        ],
      ],
    ],
    'cierres' => [
      'botones' => [
        'Cargar Cierre' => '[data-js-cargar-cierre]'
      ],
      'botones_solo_adm' => [],
      'buscar' => 'cierres/filtrosCierres',
      'resultados' => [
        'fecha' => 'fecha',
        'nro_mesa' => 'nro_mesa',
        'juego' => 'juego_mesa.siglas',
        'hora' => 'hora_inicio',
        'moneda' => 'moneda.siglas',
        'casino' => 'casino.nombre',
        'estado' => 'cierre_mesa.id_estado_cierre',
        'acciones' =>  [
          'data-js-ver-cierre' => ['fa-search-plus',null,'data-estados="1,2,3,4"'],
          'data-js-modificar-cierre' => ['fa-pencil-alt',null,'data-estados="1"'],
          'data-js-validar-cierre' => ['fa-check','m_validar_cierres','data-estados="1"'],
          'data-js-eliminar-cierre' => ['fa-trash','m_eliminar_cierres_y_aperturas','data-estados="1,3"'],
        ],
      ],
    ],
  ];
?>
<div class="row">
  <div class="tabs" data-js-tabs>
    <div>
      <a data-js-tab data-tab-target="#pant_aperturas">Aperturas</a>
    </div>
    <div>
      <a data-js-tab data-tab-target="#pant_cierres">Cierres</a>
    </div>
  </div>
</div>

<style>
  .tablaResultados tbody tr .estado i.rojo{
    color: rgb(211, 47, 47);
  }
  .tablaResultados tbody tr .estado i.azul{
    color: rgb(30, 30, 227);
  }
  .tablaResultados tbody tr .estado i.verde{
    color: rgb(76, 175, 80);
  }
  .tablaResultados tbody tr .estado i.naranja{
    color: rgb(189, 133, 1);
  }
</style>

<div id="iconosEstados" hidden>{{-- El manejo de estados es bastante raro... por eso todos estos casos --}}
  <i data-linkeado="0" data-estado="1" class="rojo fas fa-fw fa-times" title="CARGADO"></i>
  <i data-linkeado="0" data-estado="2" class="azul fa fa-fw fa-check" title="VISADO"></i>
  <i data-linkeado="0" data-estado="3" class="azul fa fa-fw fa-check" title="VISADO"></i>
  <i data-linkeado="0" data-estado="4" class="azul fa fa-fw fa-check" title="VISADO"></i>
  <i data-linkeado="0" data-estado=""  class="naranja fas fa-fw fa-question" title="ERROR"></i>
  <i data-linkeado="1" data-estado="1" class="verde fas fa-fw fa-check" title="VALIDADO"></i>
  <i data-linkeado="1" data-estado="2" class="verde fa fa-fw fa-check" title="VALIDADO C/ DIFERENCIAS"></i>
  <i data-linkeado="1" data-estado="3" class="verde fa fa-fw fa-check" title="VALIDADO"></i>
  <i data-linkeado="1" data-estado="4" class="verde fa fa-fw fa-check" title="VALIDADO"></i>
  <i data-linkeado="1" data-estado=""  class="naranja fas fa-fw fa-question" title="ERROR"></i>
</div>
@foreach($tabs as $tab => $tdata)
<div class="col-lg-12 tab_content" id="pant_{{$tab}}" hidden="true">
  <div class="row">
    <div class="col-md-3">
      @foreach($tdata['botones'] as $btn_text => $modal_selector)
      @if(!in_array($btn_text,$tdata['botones_solo_adm']) || $usuario->es_administrador || $usuario->es_superusuario)
      <div class="row">
        <div class="col-md-12">
          <a href="" class="btn-grande" data-js-mostrar="{{$modal_selector}}" dusk="btn-nuevo" style="text-decoration: none;">
            <div class="panel panel-default panelBotonNuevo">
              <center>
                <img class="imgNuevo" src="/img/logos/informes_white.png">
              <center>
              <div class="backgroundNuevo"></div>
              <div class="row">
                <div class="col-xs-12">
                  <center>
                    <h5 class="txtLogo">+</h5>
                    <h4 class="txtNuevo">{{$btn_text}}</h4>
                  </center>
                </div>
              </div>
            </div>
          </a>
        </div>
      </div>
      @endif
      @endforeach
    </div>
    <div class="col-md-9">
      <div class="row">
        <div class="col-md-12">
          <div class="panel panel-default">
            <div class="panel-heading" data-toggle="collapse" href="#pant_{{$tab}} .filtro-busqueda-collapse" style="cursor: pointer">
              <h4>Filtros de búsqueda <i class="fa fa-fw fa-angle-down"></i></h4>
            </div>
            <div class="filtro-busqueda-collapse panel-collapse collapse">
              <div class="panel-body">
                <div class="row">
                  <div class="col-xs-4">
                    <h5>Fecha</h5>
                    <div class="form-group">
                      <div class='input-group date' data-js-fecha data-date-format="MM yyyy">
                        <input name="fecha" type='text' class="form-control" placeholder="aaaa-mm-dd" />
                        <span class="input-group-addon" style="border-left:none;cursor:pointer;"><i class="fa fa-times"></i></span>
                        <span class="input-group-addon" style="cursor:pointer;"><i class="fa fa-calendar"></i></span>
                      </div>
                    </div>
                  </div>
                  <div class="col-xs-4">
                    <h5>Mesa</h5>
                    <div class="input-group">
                      <input name="nro_mesa" class="form-control filtroMesa" type="text" autocomplete="off">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-xs-4">
                    <h5>Casino</h5>
                    <select name="id_casino" class="form-control filtroCas">
                      <option value="" selected>- Seleccione un Casino -</option>
                      @foreach ($casinos as $cas)
                      <option value="{{$cas->id_casino}}">{{$cas->nombre}}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-xs-4">
                    <h5>JUEGO</h5>
                    <select name="id_juego" class="form-control filtroJuego">
                      <option value="" selected>- Seleccione un Juego -</option>
                      @foreach ($juegos as $j)
                      <option value="{{$j->id_juego_mesa}}">{{$j->nombre_juego}} - {{$j->casino->codigo}}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-4" style="padding-top:50px;">
                    <button data-target="{{$tdata['buscar']}}" data-js-buscar class="btn btn-infoBuscar" type="button" style="margin-top:30px">
                      <i class="fa fa-fw fa-search"></i> BUSCAR
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h4>{{$tab}}</h4>
            </div>
            <div class="panel-body">
              <div class="table-responsibe">
                <table class="table tablesorter tablaResultados">
                  <thead>
                    <tr align="center">
                      @foreach($tdata['resultados'] as $class => $key)
                      @php $txt = str_replace('_',' ',strtoupper($class)); @endphp
                      @if(!is_array($key))
                        <th data-js-sortable="{{$key}}">{{$txt}}</th>
                      @else
                        <th>{{$txt}}</th>
                      @endif
                      @endforeach
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              </div>
              <table hidden>
                <tr class="filaResultado moldeFilaResultados">
                  @foreach($tdata['resultados'] as $class => $key)
                  @if(!is_array($key))
                  <td class="{{$class}}">{{$class}}</td>
                  @else
                  <td>
                    @foreach($key as $boton => $icono_perm)
                    @if($tiene_permiso($icono_perm[1]))
                    <button type="button" class="btn" {{$boton}} {!! $icono_perm[2] !!}>
                      <i class="fa fa-fw {{$icono_perm[0]}}"></i>
                    </button>
                    @endif                    
                    @endforeach
                  </td>
                  @endif
                  @endforeach
                </tr>
              </table>
            </div>
            <div class="row zonaPaginacion herramientasPaginacion"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endforeach

<div data-js-generar-plantilla>
  <div class="modal fade" data-js-generar-plantilla-modal  tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header" style="background-color:#1DE9B6;">
          <h3 class="modal-title">| GENERANDO RELEVAMIENTO</h3>
        </div>
        <div class="modal-body modalCuerpo" style="text-align: center;">
          <div class="loading">
            <i class="fa fa-spinner fa-spin" style="font-size:4em;" alt="Cargando"></i>
            <br>
            <h6>Un momento, por favor...</h6>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" data-js-reintente tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content" style="border-radius:5px !important">
       <div class="modal-header" style="font-family: Roboto-Black; background-color:#0D47A1">
         <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
         <h3 class="modal-title">| AVISO</h3>
        </div>
        <div class="modal-body">
          <div class="row">
            <h6 style="text-align:center !important">'Por favor reintente en 15 minutos...'</h6>
            <h6 style="text-align:center !important">GRACIAS</h6>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">ACEPTAR</button>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade .aperturaAPedido" data-js-apertura-a-pedido tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" style="width: 80%">
    <div class="modal-content">
      <div class="modal-header" style="background-color:#4AA89F;">
        <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times"></i></button>
        <button id="btn-minimizar" type="button" class="close" data-toggle="collapse" data-minimizar="true"
                data-target=".aperturaAPedido .collapse" style="position:relative; right:20px; top:5px"><i class="fa fa-minus"></i></button>
        <h3 class="modal-title">| APERTURAS A PEDIDO</h3>
      </div>
      <div class="collapse in">
        <div class="modal-body">
          <div class="row">
            <div class="col-xs-2">
              <h5>JUEGO</h5>
              <select class="form-control" data-js-juego>
                @foreach ($juegos as $j)
                <option value="{{$j->id_juego_mesa}}" data-siglas="{{$j->siglas}}" data-casino="{{$j->casino->nombre}}">
                  {{$j->nombre_juego}} - {{$j->casino->codigo}}
                </option>
                @endforeach
              </select>
            </div>
            <div class="col-xs-2">
              <h5>MESA</h5>
              <input class="form-control" data-js-mesa name="id_mesa_de_panio" data-js-formdata-attr="data-elemento-seleccionado" placeholder="Número de mesa"/>
            </div>
            <div class="col-xs-2">
              <h5>F. INICIO</h5>
              <div class="form-group">
                <div class='input-group date' data-js-fecha data-date-format="aaaa-mm-dd">
                  <input type='text' class="form-control" name="fecha_inicio" placeholder="Fecha de inicio"/>
                  <span class="input-group-addon" style="border-left:none;cursor:pointer;"><i class="fa fa-times"></i></span>
                  <span class="input-group-addon" style="cursor:pointer;"><i class="fa fa-calendar"></i></span>
                </div>
              </div>
            </div>
            <div class="col-xs-2">
              <h5>F. FIN</h5>
              <div class="form-group">
                <div class='input-group date' data-js-fecha data-date-format="aaaa-mm-dd">
                  <input type='text' class="form-control" name="fecha_fin" placeholder="Fecha fin"/>
                  <span class="input-group-addon" style="border-left:none;cursor:pointer;"><i class="fa fa-times"></i></span>
                  <span class="input-group-addon" style="cursor:pointer;"><i class="fa fa-calendar"></i></span>
                </div>
              </div>
            </div>
            <div class="col-xs-2">
              <h5>&nbsp;</h5>
              <button data-js-agregar class="btn btn-success" type="button">
                <i class="fa fa-plus"></i>
              </button>
            </div>
          </div>
          <hr>
          <div class="row" style="max-height: 450px;overflow-y: scroll;">
            <table class="table" data-js-tabla>
              <thead>
                <tr>
                  <th class="col-md-2" style="text-align:center;">CASINO</th>
                  <th class="col-md-2" style="text-align:center;">MONEDA</th>
                  <th class="col-md-2" style="text-align:center;">JUEGO</th>
                  <th class="col-md-2" style="text-align:center;">MESA</th>
                  <th class="col-md-2" style="text-align:center;">FECHA INICIO</th>
                  <th class="col-md-2" style="text-align:center;">FECHA FIN</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
            <table hidden>
              <tr data-js-molde>
                <td class="casino" style="text-align:center;">CASINO</td>
                <td class="moneda" style="text-align:center;">ARS/USD/MULTIMONEDA</td>
                <td class="juego" style="text-align:center;">RA/CR/MJ/ETC</td>
                <td class="nro_mesa" style="text-align:center;">1234</td>
                <td class="fecha_inicio" style="text-align:center;">9999-99-99</td>
                <td class="fecha_fin" style="text-align:center;">9999-99-99</td>
                <td style="text-align:center;">
                  <button type="button" class="btn btn-success" data-js-eliminar-aap>
                    <i class="fa fa-fw fa-trash"></i>
                  </button>
                </td>
              </tr>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">SALIR</button>
      </div>
    </div>
  </div>
</div>

<style>
  .verCierreApertura {
    font-family: Roboto;
  }
  .verCierreApertura .modal-header {
    background-color:#0D47A1;
  }
  .verCierreApertura .modal-header button {
    margin: 2px !important;
  }
  .verCierreApertura .modal-body .titulo_datos {
    margin: 0px;
    text-align: center;
  }
  .verCierreApertura .borde_arriba {
    border-top:1px solid #ccc;
  }
  .verCierreApertura .bordes_columnas > *:not(:last-child) {
    border-right:1px solid #ccc;
  }
  .verCierreApertura .div_icono_texto {
    display: flex;
    flex-wrap: wrap;
    align-content: center;
  }
  .verCierreApertura .div_icono_texto h5 {
    color: #000 !important;
    font-size: 14px;
  }
  .verCierreApertura .tablaFichas thead tr th {
    text-align: center;
    font-size: 1.1em;
  }
  .verCierreApertura .tablaFichas tbody tr td {
    text-align: right;
  }
</style>

<div class="modal fade verCierreApertura" data-js-ver-cierre-apertura tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">
          <i class="fa fa-times"></i>
        </button>
        <button id="btn-minimizar" type="button" class="close" data-toggle="collapse" data-minimizar="true" data-target=".verCierreApertura .collapse">
          <i class="fa fa-window-minimize"></i>
        </button>
        <h3 class="modal-title">DETALLE</h3>
      </div>
      <div class="collapse in">
        <div class="modal-body">
          @foreach(['Cierre','Apertura'] as $tipo)
          <div class="row datos{{$tipo}}">
            <div class="row">
              <h3 class="titulo_datos">{{$tipo}}</h3>
            </div>
            <div class="row datos">
              <div class="col-xs-12 bordes_columnas borde_arriba">
                <div class="col-xs-4">
                  <h6>MESA</h6>
                  <div class="col-xs-12 div_icono_texto">
                    <i class="fas fa-clipboard-check fa-2x"></i>
                    <h5 class="nro_mesa">nro mesa</h5>
                  </div>
                </div>
                <div class="col-xs-4">
                  <h6>JUEGO</h6>
                  <div class="col-xs-12 div_icono_texto">
                    <i class="fas fa-dice fa-2x"></i>
                    <h5 class="nombre_juego"></h5>
                  </div>
                </div>
                <div class="col-xs-4">
                  <h6>FISCALIZADOR</h6>
                  <div class="col-xs-12 div_icono_texto">
                    <i class="far fa-user fa-2x"></i>
                    <h5 class="fiscalizador"></h5>
                  </div>
                </div>
              </div>
            </div>
            <div class="row datos">
              <div class="col-xs-12 bordes_columnas borde_arriba">
                @if($tipo == 'Cierre')
                <div class="col-xs-4">
                  <h6>HORA INICIO</h6>
                  <div class="col-xs-12 div_icono_texto">
                    <i class="far fa-clock fa-2x"></i>
                    <h5 class="hora_inicio">10:20 H</h5>
                  </div>
                </div>
                <div class="col-xs-4">
                  <h6>HORA FIN</h6>
                  <div class="col-xs-12 div_icono_texto">
                    <i class="far fa-clock fa-2x"></i>
                    <h5 class="hora_fin">10:20 H</h5>
                  </div>
                </div>
                @else
                <div class="col-xs-4">
                  <h6>FISCALIZADOR DE CARGA</h6>
                  <div class="col-xs-12 div_icono_texto">
                    <i class="far fa-user fa-2x"></i>
                    <h5 class="cargador"></h5>
                  </div>
                </div>
                <div class="col-xs-4">
                  <h6>HORA</h6>
                  <div class="col-xs-12 div_icono_texto">
                    <i class="far fa-clock fa-2x"></i>
                    <h5 class="hora">10:20 H</h5>
                  </div>
                </div>
                @endif
                <div class="col-xs-4">
                  <h6>FECHA</h6>
                  <div class="col-xs-12 div_icono_texto">
                    <i class="far fa-calendar-alt fa-2x"></i>
                    <h5 class="fecha">10-10-1990</h5>
                  </div>
                </div>
              </div>
            </div>
            <div class="row datos">
              <div class="col-xs-12 bordes_columnas borde_arriba">
                <div class="col-xs-6">
                  <h6>FICHAS</h6>
                  <table class="table table-striped tablaFichas">
                    <thead>
                      <tr class="bordes_columnas">
                        <th>Valor</th>
                        @if($tipo == 'Apertura')
                        <th>Fichas</th>
                        @endif
                        <th>Monto</th>
                      </tr>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
                  <table hidden>
                    <tr class="moldeFila">
                      <td class="valor_ficha">Valor</td>
                      @if($tipo == 'Apertura')
                      <td class="cantidad_ficha">Fichas</td>
                      @endif
                      <td class="monto_ficha">Monto</td>
                    </tr>
                  </table>
                </div>
                <div class="col-xs-6">
                  <div class="row">
                    @if($tipo == 'Cierre')
                    <div class="col-xs-12">
                      <h6>TOTAL</h6>
                      <input type="text" class="total_pesos_fichas_c" readonly="true">
                    </div>
                    <div class="col-xs-12">
                      <h6>TOTAL ANTICIPOS</h6>
                      <input type="text" class="total_anticipos_c" readonly="true">
                    </div>
                    @else
                    <div class="col-xs-12">
                      <h6>TOTAL</h6>
                      <input type="text" class="total_pesos_fichas_a" readonly="true">
                    </div>
                    @endif
                    <div class="col-xs-12">
                      <h6>Observaciones</h6>
                      <p class="observacion"></p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <br>
          @endforeach
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">SALIR</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" data-js-desvincular-modal tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content" style="border-radius:5px !important">
      <div class="modal-header" style="background-color:#0D47A1;">
       <h3 class="modal-title">| ALERTA</h3>
      </div>
      <div  id="colapsadoNuevo" class="collapse in">
        <div class="modal-body modalCuerpo">
          <h6>Esta Apertura fue vinculada a un Cierre determinado mediante la validación,
              puede observarse en los detalles de la misma.</h6>
          <h6>¿Desea deshacer esta validación y desvincular el Cierre?</h6>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-info" data-js-desvincular-boton>DESVINCULAR</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">CANCELAR</button>
      </div>
    </div>
  </div>
</div>

<style>
  .cargarAperturaCierre .modal-lg {
    width: 50%;
  }
  .cargarAperturaCierre .mesa_seleccionada {
    background-color: #E0E0E0;
  }
  .cargarAperturaCierre .tablaFichas th {
    text-align: center;
    font-size: 1.1em;
  }
  .cargarAperturaCierre .align-right {
    text-align: right !important;
  }
  .cargarAperturaCierre .tablaMesas tbody tr i {
    padding: 0.15em;
  }
  .cargarAperturaCierre [name="observacion"] {
    background-color: transparent;
    border: 1px solid #000000;
    height: 100%;
    width: 100%;
    scrollbar-arrow-color: #000066;
    scrollbar-base-color: #000033;
    scrollbar-dark-shadow-color: #336699;
    scrollbar-track-color: #666633;
    scrollbar-face-color: #cc9933;
    scrollbar-shadow-color: #DDDDDD;
    scrollbar-highlight-color: #CCCCCC;
    resize: vertical;
  }
</style>

<input id="quienSoy" value="{{$usuario->nombre}}" data-elemento-seleccionado="{{$usuario->id_usuario}}" class="form-control" name="id_cargador" data-js-formdata-attr="data-elemento-seleccionado" type="text" readonly style="display: none;">

<div data-js-cargar-apertura hidden></div>
<div data-js-cargar-cierre hidden></div>
<div class="modal fade cargarAperturaCierre" data-js-cargar-apertura-cierre tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg" >
    <div class="modal-content">
      <div class="modal-header" style="background-color:#6dc7be;">
        <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times"></i></button>
        <button id="btn-minimizar-carga-cierre" type="button" class="close" data-toggle="collapse" data-minimizar="true" data-target=".cargarAperturaCierre .collapse" style="position:relative; right:20px; top:5px"><i class="fa fa-minus"></i></button>
        <h3 class="modal-title tipo">CARGAR XXXXX</h3>
      </div>
      <div class="collapse in">
        <div class="modal-body" style="font-family: Roboto;">
          <div class="row" style="border-bottom:2px solid #ccc;">
            <div class="col-md-4">
              <h6>FECHA</h6>
              <div class="form-group">
                <div class='input-group date' data-js-fecha>
                  <input type='text' class="form-control" placeholder="aaaa-mm-dd" name="fecha"/>
                  <span class="input-group-addon" style="border-left:none;cursor:pointer;" data-js-campo-cargar><i class="fa fa-times"></i></span>
                  <span class="input-group-addon" style="cursor:pointer;" data-js-campo-cargar><i class="fa fa-calendar"></i></span>
                </div>
              </div>
            </div>
            <div class="col-xs-4">
              <h6>CASINO</h6>
              <select class="form-control" name="id_casino" data-js-casino>
                <option value="" selected>- Seleccione un Casino -</option>
                @foreach ($casinos as $cas)
                <option value="{{$cas->id_casino}}">{{$cas->nombre}}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="row inputMesas" hidden>
            <div class="row">
              <div class="col-md-6">
                <h6 data-js-campo-cargar>Agregar Mesa</h6>
                <div class="row" data-js-campo-cargar>
                  <div class="input-group">
                    <input class="form-control mesa" type="text" autocomplete="off" placeholder="Nro. de Mesa" >
                    <span class="input-group-btn" style="display:block;">
                      <button class="btn btn-default btn-lista-datos" data-js-agregar-mesa type="button"><i class="fa fa-plus"></i></button>
                    </span>
                  </div>
                </div>
              </div> 
              <div class="col-md-4">
                <h6>FISCALIZADOR DE CARGA</h6>
                <input class="form-control" name="id_cargador" readonly>
              </div>
            </div>
            <div class="row">
              <div class="col-xs-4 inputMesas" hidden>
                <h6><b>MESAS</b></h6>
                <table class="table tablaMesas">
                  <thead>
                    <tr>
                      <th class="col-xs-4" style="border-right:2px solid #ccc;">NRO</th>
                      <th class="col-xs-8"></th>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
                <table hidden>
                  <tr class="moldeFila">
                    <td class="nro_mesa">99999999</td>
                    <td>
                      <button data-js-cargar data-js-campo-cargar-modificar>
                        <i class="fas fa-fw fa-pencil-alt"></i>
                      </button>
                      <button data-js-ver data-js-campo-validar>
                        <i class="fas fa-fw fa-eye"></i>
                      </button>
                      <button data-js-borrar data-js-campo-cargar>
                        <i class="fas fa-fw fa-trash"></i>
                      </button>
                    </td>
                  </tr>
                </table>
              </div>
              <div class="col-xs-8 datosCierreApertura" style="border-left:2px solid #ccc; border-right:2px solid #ccc;" hidden>
                <h6 style="border-bottom:1px solid #ccc"><b>DETALLES</b></h6>
                <div>
                    <div class="row">
                      <div class="col-md-4">
                        <h6>MONEDA</h6>
                        <select class="form-control" name="id_moneda" data-js-moneda>
                          <option value="" selected>- Moneda -</option>
                          @foreach ($monedas as $m)
                          <option value="{{$m->id_moneda}}">{{$m->descripcion}}</option>
                          @endforeach
                        </select>
                      </div>
                      <div class="col-md-4" data-js-campo-cierres>
                        <h6>HORA DE APERTURA</h6>
                        <input name="hora_inicio" type="time" class="form-control" format="hh:mm">
                      </div>
                      <div class="col-md-4" data-js-campo-cierres>
                        <h6>HORA CIERRE</h6>
                        <input name="hora_fin" type="time" class="form-control" format="hh:mm">
                      </div>
                      <div class="col-md-4" data-js-campo-aperturas>
                        <h6>HORA</h6>
                        <input name="hora" type="time" class="form-control" format="hh:mm">
                      </div>
                      <div class="col-md-4" data-js-campo-aperturas>
                        <h6>FISCALIZADOR DE TOMA</h6>
                        <input class="form-control" name="id_fiscalizador" type="text" formData-attr="data-elemento-seleccionado">
                      </div>
                    </div>
                  </div>
                  <hr>
                  <h6 align="center">FICHAS</h6>
                  <div class="row">
                    <div class="col-xs-6" >
                      <table class="table tablaFichas">
                        <thead>
                          <tr>
                            <th>VALOR</th>
                            <th data-js-campo-aperturas>CANTIDAD</th>
                            <th data-js-campo-cierres>MONTO</th>
                          </tr>
                        </thead>
                        <tbody>
                        </tbody>
                      </table>
                      <table hidden>
                        <tr class="moldeFichas">
                          <td>
                            <input class="form-control align-right valor_ficha" data-js-cambio-ficha readonly>
                          </td>
                          <td data-js-campo-aperturas>
                            <input class="form-control align-right cantidad_ficha" data-js-cambio-ficha>
                          </td>
                          <td data-js-campo-cierres>
                            <input class="form-control align-right monto_ficha" data-js-cambio-ficha>
                          </td>
                        </tr>
                      </table>
                    </div>
                    <div class="col-xs-6">
                      <h6><b>TOTAL: </b></h6>
                      <input class="form-control align-right" name="total_pesos_fichas_c" readonly data-js-campo-cierres>
                      <input class="form-control align-right" name="total_pesos_fichas_a" readonly data-js-campo-aperturas>
                      <h6 data-js-campo-cierres><b>TOTAL ANTICIPOS ($): </b></h6>
                      <input class="form-control align-right" name="total_anticipos_c" data-js-campo-cierres>
                    </div>
                  </div>
                  <hr>
                  <div class="row">
                    <div class"col-md-12" data-js-campo-validar>
                      <textarea name="observacion"></textarea>
                    </div>
                    <div class="col-md-12" data-js-campo-validar>
                      <div class="col-md-offset-10">
                        <button type="button" class="btn btn-success btn-validar" data-js-validar style="font-family: Roboto-Condensed;">VALIDAR</button>
                      </div>
                    </div>
                    <div class="col-md-12" data-js-campo-cargar-modificar>
                      <div class="col-md-offset-10">
                        <button type="button" class="btn btn-primary btn-guardar" data-js-guardar style="font-family: Roboto-Condensed;">GUARDAR</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default btn-salir" data-js-salir>SALIR</button>
        </div>
      </div>
    </div>
  </div>
</div>
  
<style>
  .validarApertura .borde_abajo {
    border-bottom: 2px solid #ccc;
  }
  .validarApertura .observacion {
    background-color: transparent;
    border: 1px solid #000000;
    height: 100%;
    width: 100%;
    scrollbar-arrow-color: #000066;
    scrollbar-base-color: #000033;
    scrollbar-dark-shadow-color: #336699;
    scrollbar-track-color: #666633;
    scrollbar-face-color: #cc9933;
    scrollbar-shadow-color: #DDDDDD;
    scrollbar-highlight-color: #CCCCCC;
    resize: vertical;
  }
  .validarApertura .tablaFichas th{
    padding-bottom: 8px;
    padding-top: 8px;
    padding-left: 8px;
    padding-right:8px;
    border-right: 1px solid #ccc;
    border-bottom: 1px solid #ccc;
  }
  .validarApertura .tablaFichas th:last_child{
    color: #aaa !important;
    border-right: unset;
  }
  .validarApertura .tablaFichas th h5 {
    font-size: 15px !important;
    color: #aaa !important;
    text-align: center !important;
  }
  .validarApertura .datosA h6,.validarApertura .datosC h6 {
    font-size:17px !important;
    text-align:left !important;
    margin-left:15px;
  }
</style>

<div class="modal fade .validarApertura" data-js-validar-apertura-modal tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg" style="width:70%">
    <div class="modal-content">
      <div class="modal-header" style="background-color:#6dc7be;">
        <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times"></i></button>
        <button id="btn-minimizar" type="button" class="close" data-toggle="collapse" data-minimizar="true" data-target=".validarApertura .collapse" style="position:relative; right:20px; top:5px"><i class="fa fa-minus"></i></button>
        <h3 class="modal-title">VALIDAR APERTURA </h3>
      </div>
      <div class="collapse in">
        <div class="modal-body" style="font-family: Roboto;">
          <div class="row borde_abajo" style="padding-bottom:20px">
            <div class="col-xs-5">
              <h6 display="inline-block" style="font-size:19px !important; padding:0px;margin:0px !important;">Seleccione un Cierre para validar esta Apertura:</h6>
            </div>
            <div class="col-xs-4" >
              <select name="id_cierre_mesa" class="form-control" data-js-validar-apertura-cambio-fecha display="inline-block" style="padding-right:40px;margin:0px !important;padding-left:0px;">
                <option value="" selected>- Seleccione una Fecha -</option>
              </select>
              <select hidden>
                <option data-js-validar-apertura-molde-fecha value="-1"><span class="fecha">YYYY-MM-DD</span> -- <span class="hora_inicio_format">HH:MM</span> a <span class="hora_fin_format">HH:MM</span> -- <span class="siglas">MONEDA</span></option>
              </select>
            </div>
          </div>
          <div class="row">
            <div class="col-xs-6 borde_abajo datosA" style="border-right: 2px solid #aaa">
              <div class="row">
                <h5>APERTURA</h5>
              </div>
              <div class="row" style="background-color:#BDBDBD;">
                <div class="col-md-12">
                  <h6>HORA APERTURA: <span class="hora"></span></h6>
                  <h6>FECHA APERTURA: <span class="fecha_apertura"></span></h6>
                  <h6>FISCALIZADOR DE TOMA: <span class="fiscalizador"></span></h6>
                  <h6>FISCALIZADOR DE CARGA: <span class="cargador"></span></h6>
                  <h6>TIPO MESA: <span class="tipo_mesa"></span></h6>
                  <h6>MONEDA: <span class="moneda"></span></h6>
                </div>
              </div>
            </div>
            <div class="col-xs-6 borde_abajo datosC" hidden>
              <div class="row">
                <h5>CIERRE</h5>
              </div>
              <div class="row" style="background-color:#BDBDBD;">
                <div class="col-xs-12">
                  <h6>MESA: <span class="nro_mesa"></span></h6>
                  <h6>JUEGO: <span class="juego"></span></h6>
                  <h6>CASINO: <span class="casino"></span></h6>
                  <h6>HORA APERTURA: <span class="hora_inicio"></span></h6>
                  <h6>HORA CIERRE: <span class="hora_fin"></span></h6>
                  <h6>FECHA: <span class="fecha_cierre"></span></h6>
                </div>
              </div>
            </div>
          </div>
          <br>
          <div class="row borde_abajo" style="text-align:center;">
            <h3 align="center" style="padding-bottom:20px; display:inline;position:relative;top:-2px;">DATOS GENERALES</h3><i class="fas fa-info-circle" style="font-size:30px;"></i>
            <br>
            <br>
          </div>
          <div class="row borde_abajo">
            <h6 align="center">FICHAS</h6>
            <table style="border-collapse: separate;" class="table table-bordered tablaFichas">
              <thead>
                <tr>
                  <th class="col-xs-3">
                    <h5>VALOR</h5>
                  </th>
                  <th class="col-xs-3">
                    <h5>CANTIDAD CIERRE</h5>
                  </th>
                  <th class="col-xs-3">
                    <h5>CANTIDAD APERTURA</h5>
                  </th>
                  <th class="col-xs-3">
                    <h5>DIFERENCIAS</h5>
                  </th>
                </tr>
              </thead>
              <tbody style="border-spacing: 7px 7px;">
              </tbody>
            </table>
            <table hidden>
              <tr data-js-validar-apertura-molde-ficha style="padding:0px !important;">
                <td class="valor_ficha" style="padding:1px !important;text-align:right !important;"></td>
                <td class="cierre_cantidad_ficha" style="padding:1px !important;text-align:right !important;font-weight: bold"></td>
                <td class="apertura_cantidad_ficha" style="padding:1px !important;text-align:right !important;"></td>
                <td class="diferencia" style="padding:1px !important;text-align:right !important;">
                  <i data-diferencia="0" class="fa fa-fw fa-check" style="color: rgb(102, 187, 106);" hidden></i>
                  <i data-diferencia="1" class="fa fa-fw fa-times" style="color: rgb(211, 47, 47);" hidden></i>
                </td>
              </tr>
            </table>
          </div>
          <div class="row">
            <div class="col-md-4">
              <h6>TOTAL CIERRE</h6>
              <input type="text" class="form-control total_pesos_fichas_c" readonly="true">
            </div>
            <div class="col-md-4" >
              <h6>TOTAL APERTURA</h6>
              <input type="text" class="form-control total_pesos_fichas_a" readonly="true">
            </div>
            <div class="col-md-4" >
              <h6>TOTAL ANTICIPOS</h6>
              <input type="text" class="form-control total_anticipos_c" readonly="true">
            </div>
          </div>
          <br>
          <div class="row">
            <div class="col-md-12">
              <h6>OBSERVACIONES</h6>
              <textarea name="observacion" class="observacion"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button data-js-validar-apertura-validar data-diferencia="0" type="button" class="btn btn-successAceptar" hidden>VALIDAR</button>
            <button data-js-validar-apertura-validar data-diferencia="1" type="button" class="btn btn-successAceptar" hidden>VALIDAR CON DIFERENCIA</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">SALIR</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade alertaBaja" data-js-alerta-baja tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-70%">
    <div class="modal-content">
      <div class="modal-header" style="font-family: Roboto-Black; background-color:#D50000">
        <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times"></i></button>
        <button id="btn-minimizar" type="button" class="close" data-toggle="collapse" data-minimizar="true" data-target=".alertaBaja .collapse" style="position:relative; right:20px; top:5px"><i class="fa fa-minus"></i></button>
        <h3 class="modal-title">ALERTA</h3>
      </div>
      <div class="collapse in">
        <div class="modal-body">
          <h6 class="mensaje" style="color:#000000; font-size: 18px !important; text-align:center !important"></h6>
          <div class="row">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-dangerEliminar" data-js-eliminar>ELIMINAR</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">CANCELAR</button>
      </div>
    </div>
  </div>
</div>

<meta name="_token" content="{!! csrf_token() !!}" />

@endsection


<!-- Comienza modal de ayuda -->
@section('tituloDeAyuda')
<h3 class="modal-title" style="color: #fff;">| AYUDA</h3>
@endsection
@section('contenidoAyuda')
<div class="col-md-12">
  <h6>GESTIÓN DE CIERRES Y APERTURAS</h6>
  <p>
    Desde esta sección se podrán visualizar los cierres y aperturas cargados, ordenados por fecha,
    y generar las planillas de Relevamiento de Aperturas.
    Los datos cargados pueden filtrarse, cargar y editar. Sólo las aperturas se validan, seleccionando
    el cierre con el que se desea realizar dicha acción, para luego poder comparar datos de cada mesa.
    <br><br>

    <h6>CIERRES</h6>
    Desde el botón "Nuevo Cierre", podrán cargarse simultaneamente los Cierres correspondientes a una fecha de producción
    especificada y a un casino especificados en la ventana de carga, de las diferentes mesas que abrieron. Para guardar
    la información cargada para cada mesa, se debe presionar el botón "Guardar", y esta aparecerá con un tilde en el listado
    de mesas a cargar. Una vez que se hayan cargado todos los datos de cierre de cada mesa, se presiona el botón "Finalizar"
    para cerrar la ventana de carga.
    Luego podrán visualizarse en el listado principal, los Cierres cargados hasta el momento, ordenados por fecha y paginados.
    Estos pueden filtrarse por mesa, fecha, juego y casino, desplazando la barra de "FILTROS".
    Además se puede acceder a los detalles de cada cierre, modificarse y eliminar, según los roles y permisos de cada usuario.
    <br><br>
    <h6>APERTURAS</h6>
    Desde el botón "Generar Planilla Apertura", se genera un archivo con cinco planillas en las que se detallan las mesas que
    han sido seleccionadas por sorteo para relevar su apertura.
    Desde el botón "Cargar Apertura, podrán cargarse simultaneamente las Aperturas correspondientes a una fecha de producción
    especificada y a un casino especificados en la ventana de carga, de las mesas relevadas. Para guardar la información
    cargada para cada mesa, se debe presionar el botón "Guardar", y esta aparecerá con un tilde en el listado de mesas a cargar.
    Una vez que se hayan cargado todos los datos de apertura de cada mesa, se presiona el botón "Finalizar" para cerrar la
    ventana de carga.
    Luego podrán visualizarse en el listado principal, las Aperturas cargadas hasta el momento, ordenadas por fecha y paginadas.
    Estas pueden filtrarse por mesa, fecha, juego y casino, desplazando la barra de "FILTROS".
    Además se puede acceder a los detalles de cada Apertura, modificarse, eliminarse y validarse, según los roles y permisos de
    cada usuario.
    Para la validación se debe seleccionar el Cierre que se corresponda con la Apertura a validar, en la selección se detalla
    la hora, la moneda y fecha del cierre.  En caso de haber diferencias, podrá validarse con Observación.  Una vez validada,
    esta apertura aparecerá en el listado principal con una tilde verde en la columna "Estado".
  </p>
</div>
@endsection
<!-- Termina modal de ayuda -->

@section('scripts')

  <!-- JavaScript personalizado -->
  <script src="js/CierresAperturas/CierresAperturas.js?7" type="text/javascript" charset="utf-8"></script>
  <script type="text/javascript" src="js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
  <script type="text/javascript" src="js/bootstrap-datetimepicker.es.js" charset="UTF-8"></script>

  <script src="js/inputSpinner.js" type="text/javascript"></script>
  <script src="/js/lista-datos.js" type="text/javascript"></script>

  <script src="/js/fileinput.min.js" type="text/javascript"></script>
  <script src="/js/locales/es.js" type="text/javascript"></script>
  <script src="/themes/explorer/theme.js" type="text/javascript"></script>
  <script src="/js/jquery-ui.js" type="text/javascript"></script>

  <script src="js/math.min.js" type="text/javascript"></script>

  <!-- JS paginacion -->
  <script src="/js/paginacion.js" charset="utf-8"></script>
@endsection
