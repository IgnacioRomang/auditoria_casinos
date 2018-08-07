<!DOCTYPE html>

<html>
  <head>
    <meta charset="utf-8">
    <title></title>

    <!-- <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/> -->

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- <link href="css/bootstrap.min.css" rel="stylesheet"> -->

    <link href="css/estiloPlanillaLandscape.css" rel="stylesheet">
  </head>
  <body>


        <div class="encabezadoImg">
              <img src="img/logos/banner_loteria_landscape2_f.png" width="900">
              <h2><span>PMTM06 | Control de valores de progresivos (MTM)</span></h2>
        </div>
              <div class="camposTab titulo">CASINO | FECHA PLANILLA</div><div class="camposTab titulo" style="right:196px;">| SECTOR</div>
              <div class="camposInfo"><span>{{$relevamiento_progresivos->casino}} | </span>
                                      <span>{{$relevamiento_progresivos->fecha}} |</span>
                                      </div>
                                      <div class="campoSector"><span style="position: fixed; top:28px;">{{$relevamiento_progresivos->sector}}</span></div>
              <div class="horaEjecucion">
                    <span class="titulo">HORA DE EJECUCIÓN</span>
                <div class="campo titulo">
                  <span>{{$relevamiento_progresivos->fecha}}</span>
                </div>
              </div>

        <div class="relevamientos">

            @foreach ($detalles as $detalle)
            <div id="contenedorPlanilla">

              <div class="filaProgresivo">
                <div class="mtm"><span>ISLA</span>
                </div><div class="nro_mtm"><span>{{$detalle->isla}}</span>
                </div><div class="isla"><span>MAQUINA</span>
                </div><div class="nro_isla"><span>{{$detalle->maquina}}</span>
                </div><div class="marca"><span>MARCA</span>
                </div><div class="nombre_marca"><span>{{$detalle->marca}}</span>
                </div><div class="mtm"><span>TIPO</span>
                </div><div class="nro_mtm"><span>{{$detalle->tipo}}</span>
                </div>
              </div>

              <div class="filaContadores">
                <div class="contador"> @if($detalle->formula->cont1 != null) {{$detalle->formula->cont1}} @endif
                </div><div class="contador"> @if($detalle->formula->cont2 != null) {{$detalle->formula->cont2}} @endif
                </div><div class="contador"> @if($detalle->formula->cont3 != null) {{$detalle->formula->cont3}} @endif
                </div><div class="contador"> @if($detalle->formula->cont4 != null) {{$detalle->formula->cont4}} @endif
                </div><div class="contador"> @if($detalle->formula->cont5 != null) {{$detalle->formula->cont5}} @endif
                </div><div class="contador"> @if($detalle->formula->cont6 != null) {{$detalle->formula->cont6}} @endif
                </div><div class="contador"> @if($detalle->formula->cont7 != null) {{$detalle->formula->cont7}} @endif
                </div><div class="contador"> @if($detalle->formula->cont8 != null) {{$detalle->formula->cont8}} @endif
                </div>
              </div>

              <div class="filaRelleno">
                <div class="rellenoContador"> @if($detalle->cont1 != null) {{$detalle->cont1}} @endif
                </div><div class="rellenoContador"> @if($detalle->cont2 != null) {{$detalle->cont2}} @endif
                </div><div class="rellenoContador"> @if($detalle->cont3 != null) {{$detalle->cont3}} @endif
                </div><div class="rellenoContador"> @if($detalle->cont4 != null) {{$detalle->cont4}} @endif
                </div><div class="rellenoContador"> @if($detalle->cont5 != null) {{$detalle->cont5}} @endif
                </div><div class="rellenoContador"> @if($detalle->cont6 != null) {{$detalle->cont6}} @endif
                </div><div class="rellenoContador"> @if($detalle->cont7 != null) {{$detalle->cont7}} @endif
                </div><div class="rellenoContador"> @if($detalle->cont8 != null) {{$detalle->cont8}} @endif
                </div>
              </div>
            </div>
            @endforeach

        </div>


  </body>
</html>
