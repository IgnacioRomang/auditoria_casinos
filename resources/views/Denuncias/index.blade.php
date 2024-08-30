  @extends('includes.dashboard')
  @section('headerLogo')
      <span class="etiquetaLogoMaquinas">@svg('maquinas', 'iconoMaquinas')</span>
  @endsection

  <?php
  use Illuminate\Http\Request;
  use App\Http\Controllers\UsuarioController;
  use\App\http\Controllers\RelevamientoAmbientalController;
  ?>

  @section('estilos')
      <link rel="stylesheet" href="/css/paginacion.css">
      <link rel="stylesheet" href="/css/lista-datos.css">
      <link rel="stylesheet" href="/css/bootstrap-datetimepicker.min.css">
      <link href="/css/fileinput.css" media="all" rel="stylesheet" type="text/css" />
      <link href="/themes/explorer/theme.css" media="all" rel="stylesheet" type="text/css" />
      <link rel="stylesheet" href="/css/animacionCarga.css">


      <style>
          .page {
              display: none;
          }

          .active {
              display: inherit;
          }

          .easy-autocomplete {
              width: initial !important
          }

          /* Make circles that indicate the steps of the form: */
          .step {
              height: 15px;
              width: 15px;
              margin: 0 2px;
              background-color: #bbbbbb;
              border: none;
              border-radius: 50%;
              display: inline-block;
              opacity: 0.5;
          }

          /* Mark the active step: */
          .step.actived {
              opacity: 1;
          }

          /* Mark the steps that are finished and valid: */
          .step.finish {
              background-color: #4CAF50;
          }

          .smalltext {
              font-size: 97%;
          }

          input[required],
          select[required] {
              background: #f0f6ff
          }
      </style>
  @endsection

  @section('contenidoVista')
      <div>
          <!-- BOTONES -->
          <div class="col-xl-2">
              <div class="row">
                  <div class="col-xl-12 col-md-3">
                      <a href="" id="btn-agregar-pagina" style="text-decoration: none;">
                          <div class="panel panel-default panelBotonNuevo">
                              <center>
                                  <img class="imgNuevo" src="/img/logos/relevamientos_white.png">
                              </center>
                              <div class="backgroundNuevo"></div>
                              <div class="row">
                                  <div class="col-xs-12">
                                      <center>
                                          <h4 class="txtLogo" style="padding-top: 16px;transform: scale(0.65);">+</h4>
                                          <h4 class="txtNuevo">Agregar Pagina</h4>
                                      </center>
                                  </div>
                              </div>
                          </div>
                      </a>
                  </div>
                  <div class="col-xl-12 col-md-3">
                      <a href="" id="btn-agregar-denuncia" style="text-decoration: none;">
                          <div class="panel panel-default panelBotonNuevo">
                              <center>
                                  <img class="imgNuevo" src="/img/logos/relevamientos_white.png">
                              </center>
                              <div class="backgroundNuevo"></div>
                              <div class="row">
                                  <div class="col-xs-12">
                                      <center>
                                          <h4 class="txtLogo" style="padding-top: 16px;transform: scale(0.65);">+</h4>
                                          <h4 class="txtNuevo">Agregar Denuncia</h4>
                                      </center>
                                  </div>
                              </div>
                          </div>
                      </a>
                  </div>
              </div>
          </div>

          <!--BODY-->
          <div class="col-xl-10">
              <!-- FILTROS DE BÚSQUEDA -->
              <div class="row">
                  <div class="col-md-12">
                      <div id="contenedorFiltros" class="panel panel-default">
                          <div class="panel-heading" data-toggle="collapse" href="#collapseFiltros" style="cursor: pointer">
                              <h4>Filtros de Búsqueda <i class="fa fa-fw fa-angle-down"></i></h4>
                          </div>
                          <div id="collapseFiltros" class="panel-collapse collapse">
                              <div class="panel-body">
                                  <div class="row">
                                      <div class="col-md-2">
                                          <h5>Usuario</h5>
                                          <input class="form-control" id="filtro-usuario" value="" />
                                      </div>
                                      <div class="col-md-2">
                                          <h5>URL/LINK</h5>
                                          <input class="form-control" id="filtro-url" value="" />
                                      </div>
                                      <div class="col-md-2">
                                          <h5>DNI</h5>
                                          <input class="form-control" id="buscadorDni" value="" />
                                      </div>
                                      <div class="col-md-2">
                                          <h5>Correo</h5>
                                          <input class="form-control" id="buscadorCorreo" value="" />
                                      </div>
                                      <br>
                                      <div class="row">
                                          <center>
                                              <button id="btn-buscar" class="btn btn-infoBuscar" type="button"><i
                                                      class="fa fa-fw fa-search"></i> BUSCAR</button>
                                          </center>
                                      </div>
                                      <br>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>

              <!-- TABLA DE PAGINAS-->
              <div class="row">
                  <div class="col-md-12">
                      <div class="panel panel-default">
                          <div class="panel-heading">
                              <h4>LISTADO DE PAGINAS</h4>
                          </div>
                          <div class="panel-body">
                              <table id="table-paginas" class="table table-fixed tablesorter">
                                  <thead>
                                      <tr>
                                          <th class="col-xs-2" value="paginas-usuario" estado="">USUARIO<i
                                                  class="fa fa-sort"></i></th>
                                          <th class="col-xs-2" value="paginas-pagina" estado="">PAGINA<i
                                                  class="fa fa-sort"></i>
                                          </th>
                                          <th class="col-xs-2" value="paginas-estado" estado="">ESTADO<i
                                                  class="fa fa-sort"></i></th>
                                          <th class="col-xs-2" value="paginas-creado" estado="">F. AGREGADO<i
                                                  class="fa fa-sort"></i></th>
                                      </tr>
                                  </thead>
                                  <tbody id="cuerpoTabla" style="height: 350px;">
                                      <tr class="filaTabla" style="display: none">
                                          <td class="col-xs-2 paginas-usuario"></td>
                                          <td class="col-xs-2 paginas-pagina"></td>
                                          <td class="col-xs-2 paginas-estado"></td>
                                          <td class="col-xs-2 paginas-creado"></td>
                                          <td class="col-xs-3 acciones">
                                              <button id="btnVerMas" class="btn btn-info info" type="button"
                                                  value="" title="VER MÁS" data-toggle="tooltip"
                                                  data-placement="top" data-delay="{'show':'300', 'hide':'100'}">
                                                  <i class="fa fa-fw fa-search-plus"></i>
                                              </button>
                                              <a tabindex="0" id="btnSubirArchivos" class="btn btn-info info"
                                                  role="button" value="" title="SUBIR ARCHIVOS"
                                                  data-toggle="popover" data-html="true" data-trigger="focus"
                                                  data-content="">
                                                  <i class="fa fa-fw fa-folder-open"></i>
                                              </a>
                                              <button id="btnGenerarSolicitudAutoexclusion" class="btn btn-info info"
                                                  type="button" value="" title="GENERAR SOLICITUD AE"
                                                  data-toggle="tooltip" data-placement="top"
                                                  data-delay="{'show':'300', 'hide':'100'}">
                                                  <i class="far fa-fw fa-file-alt"></i>
                                              </button>
                                              <button id="btnGenerarConstanciaReingreso" class="btn btn-info imprimir"
                                                  type="button" value="" title="GENERAR CONSTANCIA DE REINGRESO"
                                                  data-toggle="tooltip" data-placement="top"
                                                  data-delay="{'show':'300', 'hide':'100'}">
                                                  <i class="fa fa-fw fa-print"></i>
                                              </button>
                                              <button id="btnGenerarSolicitudFinalizacion" class="btn btn-info imprimir"
                                                  type="button" value="" title="GENERAR SOLICITUD DE FINALIZACION"
                                                  data-toggle="tooltip" data-placement="top"
                                                  data-delay="{'show':'300', 'hide':'100'}">
                                                  <i class="fa fa-fw fa-print"></i>
                                              </button>
                                              <span></span>
                                          </td>
                                      </tr>
                                  </tbody>
                              </table>
                              <div id="herramientasPaginacion" class="row zonaPaginacion"></div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>

      <!-- MODAL AGREGAR PAGINA-->
      <div class="modal fade" id="mdl-agregar-pag" tabindex="-1" role="dialog" aria-hidden="true">
          <div class="modal-dialog modal-lg">
              <div class="modal-content">
                  <div class="modal-header" style="font-family: Roboto-Black; background-color: #6dc7be; color: #fff">
                      <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times"></i></button>
                      <button id="btn-minimizar" type="button" class="close" data-toggle="collapse"
                          data-minimizar="true" data-target="#colapsado"
                          style="position:relative; right:20px; top:5px"><i class="fa fa-minus"></i></button>
                      <h3 class="modal-title" id="myModalLabel">| AGREGAR PAGINA</h3>
                  </div>
                  <div id="colapsado" class="collapse in">
                      <div class="modal-body modal-Cuerpo">
                          <form id="frmAgregarAE" class="form-horizontal" novalidate=" method=" post""
                              enctype="multipart/form-data">
                              <div class="form-group error">
                                  <div class="col-lg-12">
                                      <div id="columna" class="row">
                                          <div class="col-lg-6">
                                              <h5>Usuario</h5>
                                              <input id="ipt-usuario" type="text" class="form-control" placeholder=""
                                                  value="" required alpha data-size="100">
                                          </div>
                                          <div class="col-lg-6">
                                              <h5>URL / LINK</h5>
                                              <input id="ipt-url" type="text" class="form-control" placeholder=""
                                                  value="" required alpha data-size="100">
                                              <small class="form-text text-muted">Introduce la URL o el enlace. Enter para
                                                  visualizar</small>
                                          </div>
                                          <div id="div-prev" class="col-lg-12 hide">
                                              <h1 class="mb-4">Vista Previa</h1>
                                              <p class="lead">La pagina un perfil.</p>
                                              <div class="embed-responsive embed-responsive-16by9">
                                                  <iframe id="ifm" src="/denuncias" class="embed-responsive-item"
                                                      title="YouTube video" allowfullscreen></iframe>
                                              </div>
                                          </div>
                                      </div>
                                      <span id="alerta_sesion" class="alertaSpan"></span>
                                  </div>
                              </div>
                          </form>
                      </div>
                      <div class="modal-footer">
                          <button type="button" class="btn btn-successAceptar" id="btn-guardar-page"
                              value="nuevo">ENVIAR</button>
                          <button type="button" class="btn btn-default" id="btn-cancelar" data-dismiss="modal"
                              aria-label="Close">CANCELAR</button>
                          <input type="hidden" id="id_sesion" value="0">
                      </div>
                  </div>
              </div>
          </div>
      </div>

      <!-- MODAL AGREGAR DENUNCIA-->
      <div class="modal fade" id="mdl-agregar-den" tabindex="-1" role="dialog" aria-hidden="true">
          <div class="modal-dialog modal-lg">
              <div class="modal-content">
                  <div class="modal-header" style="font-family: Roboto-Black; background-color: #6dc7be; color: #fff">
                      <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times"></i></button>
                      <button id="btn-minimizar" type="button" class="close" data-toggle="collapse"
                          data-minimizar="true" data-target="#colapsado"
                          style="position:relative; right:20px; top:5px"><i class="fa fa-minus"></i></button>
                      <h3 class="modal-title" id="myModalLabel">| AGREGAR DENUNCIAS</h3>
                  </div>
                  <div id="colapsado" class="collapse in">
                      <div class="modal-body modal-Cuerpo">
                          <div class="row">
                              <div class="col-md-4">
                                  <table id="table-paginas-no-agregadas" class="table table-fixed">
                                      <thead>
                                          <tr>
                                              <th class="col-md-4" value="paginas-marcado" estado="">MARCADO</th>
                                              <th class="col-md-4" value="paginas-usuario" estado="">USUARIO</th>
                                              <th class="col-md-4" value="paginas-pagina" estado="">PAGINA</th>
                                          </tr>
                                      </thead>
                                      <tbody id="body-paginas-no-agregadas" style="height: 350px;">
                                          <tr class="filaTabla" style="display: none">
                                              <td class="col-md-4 paginas-marcado align-middle" style="text-align: center;">
                                                  <input class="form-check-input" type="checkbox" id="flexCheckDefault">
                                                  <span></span>
                                              </td>
                                              <td class="col-md-4 paginas-usuario"></td>
                                              <td class="col-md-4 paginas-pagina"></td>
                                          </tr>
                                      </tbody>
                                  </table>
                              </div>
                              <div class="col-md-3">
                                  <div class="btn-group open">
                                      <a class="btn btn-default" href="#"><i class="fa fa-arrow-left"></i>
                                          Quitar</a>
                                      <a class="btn btn-success" href="#"> Agregar <i
                                              class="fa fa-arrow-right"></i></a>
                                  </div>
                              </div>
                              <span></span>
                              <div class="col-md-4">
                                  <table id="table-paginas-agregadas" class="table table-fixed tablesorter">
                                      <thead>
                                          <tr>
                                              <th value="paginas-marcado" estado="">MARCADO<i
                                                      class="fa fa-sort"></i></th>
                                              <th value="paginas-usuario" estado="">USUARIO<i
                                                      class="fa fa-sort"></i></th>
                                              <th value="paginas-pagina" estado="">PAGINA<i class="fa fa-sort"></i>
                                              </th>
                                          </tr>
                                      </thead>
                                      <tbody id="body-paginas-agregadas" style="height: 350px;">
                                          <tr class="filaTabla" style="display: none">
                                              <td class="paginas-marcado">
                                                  <input class="form-check-input" type="checkbox" id="flexCheckDefault">
                                              </td>
                                              <td class="paginas-usuario"></td>
                                              <td class="paginas-pagina"></td>
                                          </tr>
                                      </tbody>
                                  </table>
                              </div>
                          </div>
                      </div>
                      <div class="modal-footer">
                          <button type="button" class="btn btn-successAceptar" id="btn-guardar-page"
                              value="nuevo">ENVIAR</button>
                          <button type="button" class="btn btn-default" id="btn-cancelar" data-dismiss="modal"
                              aria-label="Close">CANCELAR</button>
                          <input type="hidden" id="id_sesion" value="0">
                      </div>
                  </div>
              </div>
          </div>
      </div>
      <meta name="_token" content="{!! csrf_token() !!}" />
  @endsection

  @section('tituloDeAyuda')
      <h3 class="modal-title2" style="color: #fff;">| SESIONES</h3>
  @endsection
  @section('contenidoAyuda')
      <div class="col-md-12">
          <h5>Tarjeta de Sesiones</h5>
          <p>
              Agregar nuevos autoexluidos, revocar autoexclusiones, ver listado y estados.
      </div>
  @endsection

  @section('scripts')
      <!-- JavaScript paginacion -->
      <script src="/js/paginacion.js" charset="utf-8"></script>
      <script src="/js/lista-datos.js" type="text/javascript"></script>
      <!-- JavaScript personalizado -->
      <script src="/js/utils.js" charset="utf-8"></script>
      <script src="/js/Denuncias/index.js?2" charset="utf-8"></script>
      <!-- Custom input Bootstrap -->
      <script src="/js/fileinput.min.js" type="text/javascript"></script>
      <script src="/js/locales/es.js" type="text/javascript"></script>
      <script src="/themes/explorer/theme.js" type="text/javascript"></script>
      <!-- DateTimePicker JavaScript -->
      <script type="text/javascript" src="/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
      <script type="text/javascript" src="/js/bootstrap-datetimepicker.es.js" charset="UTF-8"></script>
  @endsection
  <!--
<div class="page step1">
                                              <div class="col-lg-6">
                                                  <h5>NÚMERO DE DOCUMENTO</h5>
                                                  <input id="nro_dni" type="text" class="form-control"
                                                      placeholder="" value="" required>
                                              </div>
                                          </div>
                                          <div class="page">
                                              <div class="col-lg-12">
                                                  <h6>Datos Personales</h6>
                                              </div>
                                              <div class="step2">
                                                  <div class="col-lg-6">
                                                      <h5>APELLIDO</h5>
                                                      <input id="apellido" type="text" class="form-control"
                                                          placeholder="" value="" required alpha data-size="100">
                                                  </div>
                                                  <div class="col-lg-6">
                                                      <h5>NOMBRES</h5>
                                                      <input id="nombres" type="text" class="form-control"
                                                          placeholder="" value="" required alpha data-size="100">
                                                  </div>
                                                  <div class="col-lg-6">
                                                      <h5>FECHA DE NACIMIENTO</h5>
                                                      <div class="input-group date" id="dtpFechaNacimiento"
                                                          data-date-format="yyyy-mm-dd" data-link-format="yyyy-mm-dd">
                                                          <input type="text" class="form-control"
                                                              placeholder="Fecha de nacimiento" id="fecha_nacimiento"
                                                              autocomplete="off" data-original-title="" title=""
                                                              required>
                                                          <span id="input-times-nacimiento" class="input-group-addon"
                                                              style="border-left:none;cursor:pointer;"><i
                                                                  class="fa fa-times"></i></span>
                                                          <span id="input-calendar-nacimiento" class="input-group-addon"
                                                              style="cursor:pointer;"><i
                                                                  class="fa fa-calendar"></i></span>
                                                      </div>
                                                  </div>
                                                  <div class="col-lg-3">
                                                      <h5>SEXO</h5>
                                                      <select id="id_sexo" class="form-control" required>
                                                          <option selected="" value="">Seleccionar Valor</option>
                                                          <option value="0">Masculino</option>
                                                          <option value="1">Femenino</option>
                                                          <option value="-1">Otro</option>
                                                      </select>
                                                  </div>
                                                  <div class="col-lg-6">
                                                      <h5>DOMICILIO</h5>
                                                      <input id="domicilio" type="text" class="form-control"
                                                          placeholder="" value="" required>
                                                  </div>
                                                  <div class="col-lg-6">
                                                      <h5>NRO. DOMICILIO</h5>
                                                      <input id="nro_domicilio" type="text" class="form-control"
                                                          placeholder="" value="" required numeric data-size="100">
                                                  </div>
                                                  <div class="col-lg-6">
                                                      <h5>PISO</h5>
                                                      <input id="piso" type="text" class="form-control"
                                                          placeholder="" value="" data-size="5">
                                                  </div>
                                                  <div class="col-lg-6">
                                                      <h5>DEPARTAMENTO</h5>
                                                      <input id="dpto" type="text" class="form-control"
                                                          placeholder="" value="" data-size="5">
                                                  </div>
                                                  <div class="col-lg-6">
                                                      <h5>PROVINCIA</h5>
                                                      <input id="nombre_provincia" type="text" class="form-control"
                                                          placeholder="" value="" required data-size="200">
                                                  </div>
                                                  <div class="col-lg-6">
                                                      <h5>LOCALIDAD</h5>
                                                      <input id="nombre_localidad" class="form-control" type="text"
                                                          class="form-control" placeholder="" value="" required
                                                          data-size="200">
                                                  </div>
                                                  <div class="col-lg-6">
                                                      <h5>CÓDIGO POSTAL</h5>
                                                      <input id="codigo_postal" type="text" class="form-control"
                                                          placeholder="" value="" data-size="10">
                                                  </div>
                                                  <div class="col-lg-6">
                                                      <h5>TELEFONO</h5>
                                                      <input id="telefono" type="text" class="form-control"
                                                          placeholder="" value="" required numeric data-size="100">
                                                  </div>
                                                  <div class="col-lg-6">
                                                      <h5>EMAIL</h5>
                                                      <input id="correo" type="text" class="form-control"
                                                          placeholder="" value="" email data-size="100">
                                                  </div>
                                              </div>
                                              <div class="col-lg-12">
                                                  <h6>Datos Persona de Contacto</h6>
                                              </div>
                                              <div class="col-lg-6">
                                                  <h5>NOMBRE Y APELLIDO</h5>
                                                  <input id="nombre_apellido" type="text" class="form-control"
                                                      placeholder="" value="" data-size="200">
                                              </div>
                                              <div class="col-lg-6">
                                                  <h5>DOMICILIO</h5>
                                                  <input id="domicilio_vinculo" type="text" class="form-control"
                                                      placeholder="" value="" data-size="200">
                                              </div>
                                              <div class="col-lg-6">
                                                  <h5>PROVINCIA</h5>
                                                  <input id="nombre_provincia_vinculo" type="text"
                                                      class="form-control" placeholder="" value=""
                                                      data-size="200">
                                              </div>
                                              <div class="col-lg-6">
                                                  <h5>LOCALIDAD</h5>
                                                  <input id="nombre_localidad_vinculo" type="text"
                                                      class="form-control" placeholder="" value=""
                                                      data-size="200">
                                              </div>
                                              <div class="col-lg-6">
                                                  <h5>TELEFONO</h5>
                                                  <input id="telefono_vinculo" type="text" class="form-control"
                                                      placeholder="" value="" data-size="200">
                                              </div>
                                              <div class="col-lg-6">
                                                  <h5>VINCULO</h5>
                                                  <input id="vinculo" type="text" class="form-control"
                                                      placeholder="" value="" data-size="200">
                                              </div>
                                          </div>
                                          <div class="page">
                                              <div class="step3">
                                                  <div class="col-lg-6">
                                                      <h5>FECHA AUTOEXCLUSIÓN</h5>
                                                      <div class="input-group date" id="dtpFechaAutoexclusionEstado"
                                                          data-date-format="yyyy-mm-dd" data-link-format="yyyy-mm-dd">
                                                          <input type="text" class="form-control"
                                                              placeholder="Fecha de autoexclusion" id="fecha_autoexlusion"
                                                              autocomplete="off" data-original-title="" title=""
                                                              required>
                                                          <span id="input-times" class="input-group-addon"
                                                              style="border-left:none;cursor:pointer;"><i
                                                                  class="fa fa-times"></i></span>
                                                          <span id="input-calendar" class="input-group-addon"
                                                              style="cursor:pointer;"><i
                                                                  class="fa fa-calendar"></i></span>
                                                      </div>
                                                  </div>
                                                  <div class="col-lg-6">
                                                      <h5>VENCIMIENTO 1° PERIODO</h5>
                                                      <input id="fecha_vencimiento_periodo" type="text"
                                                          class="form-control" placeholder="" value=""
                                                          disabled="" required>
                                                  </div>
                                                  <div class="col-lg-6">
                                                      <h5>PERMITIR RENOVACIÓN DESDE</h5>
                                                      <input id="fecha_renovacion" type="text" class="form-control"
                                                          placeholder="" value="" disabled="" required>
                                                  </div>
                                                  <div class="col-lg-6">
                                                      <h5>FECHA CIERRE DEFINITIVO</h5>
                                                      <input id="fecha_cierre_definitivo" type="text"
                                                          class="form-control" placeholder="" value=""
                                                          disabled="" required>
                                                  </div>
                                              </div>
                                              <div class="row">
                                                  <div class="col-lg-6">
                                                      <h5>FOTO #1</h5>
                                                      <div>
                                                          <a href="" target="_blank">FOTO1.PDF</a>
                                                          <button type="button" class="sacarArchivo btn btn-link"><i
                                                                  class="fa fa-times"></i></button>
                                                      </div>
                                                      <input id="foto1" type="file">
                                                  </div>
                                                  <div class="col-lg-6">
                                                      <h5>FOTO #2</h5>
                                                      <div>
                                                          <a href="" target="_blank">FOTO2.PDF</a>
                                                          <button type="button" class="sacarArchivo btn btn-link"><i
                                                                  class="fa fa-times"></i></button>
                                                      </div>
                                                      <input id="foto2" data-borrado="false" type="file">
                                                  </div>
                                              </div>
                                              <div class="row">
                                                  <div class="col-lg-6">
                                                      <h5>SCAN DNI</h5>
                                                      <div>
                                                          <a href="" target="_blank">DNI.PDF</a>
                                                          <button type="button" class="sacarArchivo btn btn-link"><i
                                                                  class="fa fa-times"></i></button>
                                                      </div>
                                                      <input id="scan_dni" data-borrado="false" type="file">
                                                  </div>
                                                  <div class="col-lg-6">
                                                      <h5>SOLICITUD AUTOEXCLUSIÓN</h5>
                                                      <div>
                                                          <a href="" target="_blank">SOLAE.PDF</a>
                                                          <button type="button" class="sacarArchivo btn btn-link"><i
                                                                  class="fa fa-times"></i></button>
                                                      </div>
                                                      <input id="solicitud_autoexclusion" data-borrado="false"
                                                          type="file">
                                                  </div>
                                              </div>
                                              <div class="row">
                                                  <div class="col-lg-6">
                                                      <h5>SOLICITUD DE FINALIZACIÓN</h5>
                                                      <div>
                                                          <a href="" target="_blank">SOLFIN.PDF</a>
                                                          <button type="button" class="sacarArchivo btn btn-link"><i
                                                                  class="fa fa-times"></i></button>
                                                      </div>
                                                      <input id="solicitud_revocacion" data-borrado="false"
                                                          type="file">
                                                  </div>
                                                  <div class="col-lg-6">
                                                      <h5>CARATULA</h5>
                                                      <div>
                                                          <a href="" target="_blank">CARATULA.PDF</a>
                                                          <button type="button" class="sacarArchivo btn btn-link"><i
                                                                  class="fa fa-times"></i></button>
                                                      </div>
                                                      <input id="caratula" data-borrado="false" type="file">
                                                  </div>
                                              </div>
                                          </div>

                                          <div class="page">

                                              <div class="col-lg-3">
                                                  <h5>VECES</h5>
                                                  <input id="veces" type="text" class="form-control"
                                                      placeholder="" value="">
                                              </div>
                                              <div class="col-lg-3">
                                                  <h5>TIEMPO JUGANDO (HS)</h5>
                                                  <input id="tiempo_jugado" type="text" class="form-control"
                                                      placeholder="" value="">
                                              </div>
                                              <div class="col-lg-6">
                                                  <h5>¿ES SOCIO DEL CLUB DE JUGADORES?</h5>
                                                  <select id="socio_club_jugadores" class="form-control">
                                                      <option selected="" value="">- Seleccione una opción -
                                                      </option>
                                                      <option value="SI">SI</option>
                                                      <option value="NO">NO</option>
                                                  </select>
                                              </div>
                                              <div class="col-lg-6">
                                                  <h5>¿CONOCE EL PROGRAMA JUEGO RESPONSABLE?</h5>
                                                  <select id="juego_responsable" class="form-control">
                                                      <option selected="" value="">- Seleccione una opción -
                                                      </option>
                                                      <option value="SI">SI</option>
                                                      <option value="NO">NO</option>
                                                  </select>
                                              </div>
                                              <div class="col-lg-6">
                                                  <h5>¿DECISIÓN POR PROBLEMAS DE AUTOCONTROL?</h5>
                                                  <select id="autocontrol_juego" class="form-control">
                                                      <option selected="" value="">- Seleccione una opción -
                                                      </option>
                                                      <option value="SI">SI</option>
                                                      <option value="NO">NO</option>
                                                  </select>
                                              </div>
                                              <div class="col-lg-6">
                                                  <h5>¿CÓMO ASISTE?</h5>
                                                  <select id="como_asiste" class="form-control">
                                                      <option selected="" value="">- Seleccione una opción -
                                                      </option>
                                                      <option value="0">SOLO</option>
                                                      <option value="1">ACOMPAÑADO</option>
                                                  </select>
                                              </div>
                                              <div class="col-lg-6">
                                                  <h5>¿DESEA RECIBIR INFORMACIÓN SOBRE JR?</h5>
                                                  <select id="recibir_informacion" class="form-control">
                                                      <option selected="" value="">- Seleccione una opción -
                                                      </option>
                                                      <option value="SI">SI</option>
                                                      <option value="NO">NO</option>
                                                  </select>
                                              </div>
                                              <div class="col-lg-6">
                                                  <h5>¿MEDIO DE RECEPCIÓN?</h5>
                                                  <input id="medio_recepcion" type="text" class="form-control"
                                                      placeholder="" value="" data-size="100">
                                              </div>
                                              <div class="col-lg-12">
                                                  <h5>OBSERVACIONES</h5>
                                                  <textarea id="observaciones" class="form-control" placeholder="" value="" data-size="200"></textarea>
                                              </div>
                                          </div>
                                      
