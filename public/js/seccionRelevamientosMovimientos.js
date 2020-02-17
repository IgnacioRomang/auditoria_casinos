var es_cargaT2RelMov=0;
$(document).ready(function(){
  var t= $('#tablaRelevamientosMovimientos tbody > tr .fechaRelMov');
  $.each(t, function(index, value){
    console.log($(this));
    $(this).text(convertirDate($(this).text()));
  });

  $('#barraMaquinas').attr('aria-expanded','true');
  $('#maquinas').removeClass();
  $('#maquinas').addClass('subMenu1 collapse in');
  $('#procedimientos').removeClass();
  $('#procedimientos').addClass('subMenu2 collapse in');
  $('#movimientos').removeClass();
  $('#movimientos').addClass('subMenu3 collapse in');

  $('.tituloSeccionPantalla').text('Relevamientos');
  $('#opcRelevamientosMovimientos').attr('style','border-left: 6px solid #673AB7; background-color: #131836;');
  $('#opcRelevamientosMovimientos').addClass('opcionesSeleccionado');

  $('#relFecha').datetimepicker({
    todayBtn:  1,
    language:  'es',
    autoclose: 1,
    todayHighlight: 1,
    format: 'dd MM yyyy, HH:ii',
    pickerPosition: "bottom-left",
    startView: 2,
    minView: 0,
    ignoreReadonly: true,
    minuteStep: 5,
    container:$('#modalCargarRelMov'),
  });

  $('#dtpFechaRM').datetimepicker({
    language:  'es',
    todayBtn:  1,
    autoclose: 1,
    todayHighlight: 1,
    format: 'MM yyyy',
    pickerPosition: "bottom-left",
    startView: 3,
    minView: 3,
    ignoreReadonly: true,
    container:$('main section'),
  });

  $('#B_TipoMovimientoRel').val("");
  $('#busqueda_maquina').val("");
  $('#btn-buscarRelMov').click();
  $('#herramientasPaginacion').generarTitulo(1,10,10,clickIndice);
});

$('#fechaRel').on('change', function (e) {
  $(this).trigger('focusin');
})

$('#fechaRelMov').on('change', function (e) {
  $(this).trigger('focusin');
})

//SELECCIONA EL BOTÓN QUE ABRE EL MODAL DE CARGA
$(document).on('click','.btn-generarRelMov, .btn-imprimirRelMov',function(e){
  es_cargaT2RelMov=0;
  e.preventDefault();
  const id_fiscalizacion= $(this).val();
  window.open('movimientos/generarPlanillasRelevamientoMovimiento/' + id_fiscalizacion,'_blank');
});

$(document).on('click','.btn-cargarRelMov',function(e){
    es_cargaT2RelMov=0;
  e.preventDefault();

  const id_fiscalizacion = $(this).val();

  $('#tablaCargarContadores tbody tr').remove();
  $('#tablaMaquinasFiscalizacion tbody tr').remove();
  $('.modal-title').text('CARGAR RELEVAMIENTOS');
  $('.modal-header').attr('style','background: #4FC3F7');
  $('#form1').trigger("reset");
  $('#fechaRel').val('');
  limpiarDatosToma();
  $('#guardarRel').prop('disabled', true);
  $('#modalCargarRelMov #detalless').hide();
  $('#mensajeExitoCarga').hide();
  $('#mensajeErrorCarga').hide();
  $('#modalCargarRelMov').modal('show');

  $.get('movimientos/obtenerRelevamientosFiscalizacion/' + id_fiscalizacion, function(data){
    $('#modalCargarRelMov').find('#casinoId').val(data.casino);
    $('#fiscaCarga').attr('data-id',data.cargador.id_usuario);
    $('#fiscaCarga').val(data.cargador.nombre);
    $('#fiscaCarga').prop('readonly',true);
    data.relevamientos.forEach(r => {
      let fila = $('<tr>');
      fila.append($('<td>')
        .addClass('col-xs-5')
        .text(r.nro_admin)
      );
      fila.append($('<td>')
        .addClass('col-xs-3')
        .append($('<button>')
          .append($('<i>')
            .addClass('fa').addClass('fa-fw').addClass('fa-upload')
          ).attr('type','button')
          .addClass('btn btn-info cargarMaq')
          .attr('value', r.id_maquina)
          .attr('data-fisc', id_fiscalizacion))
      );
      fila.append($('<td>')
        .addClass('col-xs-3')
        .append($('<i>').addClass('fa fa-fw fa-check faFinalizado').addClass('listo')
          .attr('value', r.id_maquina))
      );
      fila.find('.listo').toggle(r.id_estado_relevamiento == 3);
      $('#tablaMaquinasFiscalizacion tbody').append(fila);
    });

    $('#fiscaToma').generarDataList("usuarios/buscarUsuariosPorNombreYCasino/" + data.casino,'usuarios' ,'id_usuario','nombre',1,false);
    $('#fiscaToma').setearElementoSeleccionado(0,"");
    if(data.usuario_fiscalizador){
      $('#fiscaToma').setearElementoSeleccionado(data.usuario_fiscalizador.id_usuario,data.usuario_fiscalizador.nombre);
    }
  })
});


//cargar toma2
$(document).on('click','.btn-cargarT2RelMov',function(e){
  es_cargaT2RelMov=1;
  e.preventDefault();

  const id_fiscalizacion = $(this).val();

  $('#tablaCargarContadores tbody tr').remove();
  $('#tablaMaquinasFiscalizacion tbody tr').remove();
  $('.modal-title').text('CARGAR RELEVAMIENTOS');
  $('.modal-header').attr('style','background: #4FC3F7');
  $('#form1').trigger("reset");
  $('#fechaRel').val('');
  $('#guardarRel').prop('disabled', true);
  $('#modalCargarRelMov #detalless').hide();
  $('#mensajeExitoCarga').hide();
  $('#mensajeErrorCarga').hide();
  $('#modalCargarRelMov').modal('show');

  $.get('movimientos/obtenerRelevamientosFiscalizacion/' + id_fiscalizacion, function(data){
    $('#modalCargarRelMov').find('#casinoId').val(data.casino);
    $('#fiscaCarga').attr('data-id',data.cargador.id_usuario);
    $('#fiscaCarga').val(data.cargador.nombre);
    $('#fiscaCarga').prop('readonly',true);
    data.relevamientos.forEach(r => {
      let fila = $('<tr>');
      fila.append($('<td>')
        .addClass('col-xs-5')
        .text(r.nro_admin)
      );
      fila.append($('<td>')
        .addClass('col-xs-3')
        .append($('<button>')
          .append($('<i>')
            .addClass('fa').addClass('fa-fw').addClass('fa-upload')
          ).attr('type','button')
          .addClass('btn btn-info cargarMaq')
          .attr('value', r.id_maquina)
          .attr('data-fisc', id_fiscalizacion))
      );
      fila.append($('<td>')
        .addClass('col-xs-3')
        .append($('<i>').addClass('fa fa-fw fa-check faFinalizado').addClass('listo')
          .attr('value', r.id_maquina))
      );

      fila.find('.listo').toggle(r.id_estado_relevamiento == 7);
      $('#tablaMaquinasFiscalizacion tbody').append(fila);
    });

    $('#fiscaToma').generarDataList("usuarios/buscarUsuariosPorNombreYCasino/" + data.casino,'usuarios' ,'id_usuario','nombre',1,false);
    $('#fiscaToma').setearElementoSeleccionado(0,"");
    if(data.usuario_fiscalizador){
      $('#fiscaToma').setearElementoSeleccionado(data.usuario_fiscalizador.id_usuario,data.usuario_fiscalizador.nombre);
    }
  })

});

//SELECCIONA UNA MÁQUINA PARA VER SU DETALLE
$(document).on('click','.cargarMaq',function(){
  $('#mensajeExitoCarga').hide();
  const id_maquina= $(this).val();
  const id_fiscalizacion= $(this).attr('data-fisc');
  $('#modalCargarRelMov').find('#id_fiscalizac').val(id_fiscalizacion);
  $('#modalCargarRelMov').find('#maquina').val(id_maquina);

  $.get('movimientos/obtenerMTMFiscalizacion/' + id_maquina + '/' + id_fiscalizacion, function(data){
    cargarRelMov(data);
    $('#modalCargarRelMov #detalless').show();
  });
});

//COMPLETA INPUTS
function cargarRelMov(data){
  $('#mensajeExitoCarga').hide();
  $('#form1').trigger("reset");
  $('#tablaCargarContadores tbody tr').remove();

  if(data.fecha != null){
    $('#modalCargarRelMov').find('#fechaRel').val(data.fecha);
  }
  else{
    const fecha = $('#fechaRel').val();
    $('#modalCargarRelMov').find('#fechaRel').val(fecha);
  }

  if(data.fiscalizador != null){
    $('#fiscaToma').val(data.fiscalizador.nombre);
  }

  setearDatosMaquina(data.maquina);
  agregarContadores(data.maquina,data.toma);
  limpiarDatosToma();
  agregarJuegosToma(data.nombre_juego,data.juegos);
  setearDatosToma(data.toma);
  agregarProgresivos(data.progresivos);/*movRelProgresivos.blade.php*/

  $('#guardarRel').prop('disabled', false);
};

//BOTÓN GUARDAR
$(document).on('click','#guardarRel',function(){
  $('#mensajeErrorCarga').hide();
  $.ajaxSetup({ headers: {'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')}});

  const y = $('#fiscaToma').obtenerElementoSeleccionado();
  $('#modalCargarRelMov').find('#fiscalizador').val(y);

  const f = $('#modalCargarRelMov').find('#id_fiscalizac').val();
  const cargador = $('#modalCargarRelMov').find('#fiscaCarga').attr('data-id');
  const id_fiscalizador = $('#modalCargarRelMov').find('#fiscalizador').val();
  const fecha = $('#modalCargarRelMov').find('#fecha_ejecucionRel').val();
  const maq = $('#modalCargarRelMov').find('#maquina').val();
  const datosToma = obtenerDatosToma();
  const obs = $('#modalCargarRelMov').find('#observacionesToma').val();
  const datosMaquinaToma = obtenerDatosMaquinaToma();

  const formData={
    id_fiscalizacion_movimiento: f,
    id_cargador: cargador,
    id_fiscalizador: id_fiscalizador,
    estado: 2,
    id_maquina: maq,
    contadores: obtenerDatosContadores(),
    juego: datosToma.juego,
    apuesta_max: datosToma.apuesta,
    cant_lineas: datosToma.lineas,
    porcentaje_devolucion: datosToma.devolucion,
    denominacion: datosToma.denominacion,
    cant_creditos: datosToma.creditos,
    fecha_sala: fecha,
    observaciones: obs,
    mac: datosMaquinaToma.mac,
    isla_relevada:  isla,
    sectorRelevadoCargar: datosMaquinaToma.sector,
    es_cargaT2: es_cargaT2RelMov,
    progresivos: obtenerDatosProgresivos() /*movRelProgresivos.blade.php*/
  }


  $.ajax({
      type: 'POST',
      url: 'movimientos/cargarTomaRelevamiento',
      data: formData,
      dataType: 'json',
      success: function (data) {
        console.log('BIEN');
        console.log(data);
        $('#modalCargarRelMov #detalless').hide();
        $('#modalCargarRelMov #fechaRel').val(' ');
        $('#modalCargarRelMov #fiscaToma').val(' ');
        $('#mensajeErrorCarga').hide();
        //se agrega una tilde en azul a la máq cargada, dentro del mismo modal
        $('#tablaMaquinasFiscalizacion').find('.listo[value="'+maq+'"]').show();
        $('#mensajeExitoCarga').show();
        $('#modalCargarRelMov .cargarMaq').prop('disabled', false);
        $('#guardarRel').prop('disabled', true);
      },

      error: function (data)
      {
        console.log('ERROR');
        console.log(data);

        const response = JSON.parse(data.responseText);
        if(    typeof response.apuesta_max !== 'undefined'
             || typeof response.cant_lineas !== 'undefined'
             || typeof response.cant_creditos !== 'undefined'
             || typeof response.devolucion !== 'undefined'
             || typeof response.denominacion!== 'undefined'
             || typeof response.juego !== 'undefined'
             || typeof response.id_fiscalizador !== 'undefined'
             || typeof response.fecha_sala !== 'undefined')
        {
          $("#modalCargarRelMov").animate({ scrollTop: 0 }, "slow");
        }

        if(typeof response.apuesta_max !== 'undefined'){
          mostrarErrorValidacion($('#apuesta'),response.apuesta_max[0]);
        }
        if(typeof response.cant_lineas !== 'undefined'){
          mostrarErrorValidacion($('#cant_lineas'),response.cant_lineas[0]);
        }
        if(typeof response.cant_creditos !== 'undefined'){
          mostrarErrorValidacion($('#creditos'),response.cant_creditos[0]);
        }
        if(typeof response.porcentaje_devolucion !== 'undefined'){
          mostrarErrorValidacion($('#devolucion'),response.porcentaje_devolucion[0]);
        }
        if(typeof response.denominacion !== 'undefined'){
            mostrarErrorValidacion($('#denominacion'),response.denominacion[0]);
        }
        if(typeof response.juego !== 'undefined'){
          mostrarErrorValidacion($('#juegoRel'),response.juego[0]);
        }
        if(typeof response.id_fiscalizador !== 'undefined'){
          mostrarErrorValidacion($('#fiscaToma'),response.id_fiscalizador[0]);
        }
        if(typeof response.fecha_sala !== 'undefined'){
          mostrarErrorValidacion($('#fechaRel'),response.fecha_sala[0]);
        }
        if(typeof response.contadores !== 'undefined'){
          $('#mensajeErrorCarga').show();
        }

        $('#tablaCargarContadores tbody tr').each(function(index,element){
          if(typeof response['contadores.'+ index +'.valor'] !== 'undefined'){
            mostrarErrorValidacion($(this).find('.valorModif'),response['contadores.'+ index +'.valor'][0]);
        }
      }); //fin del each
    }
  });
});

$(document).on('click','.btn-eliminarFiscal',function(){
  es_cargaT2RelMov=0;
  const id=$(this).val();
  console.log(id);

  $.get('relevamientos_movimientos/eliminarFiscalizacion/' + id,function(data){
    if(data==1){
      $('#mensajeExito h3').text('ÉXITO DE ELIMINACIÓN');
      $('#mensajeExito p').text(' ');
      $('#mensajeExito').show();
      $('#btn-buscarRelMov').trigger('click');
    }
  });
});

function noTieneValor(val){
  const es_null = val === null;
  const es_undefined = typeof val === 'undefined';
  return es_null || es_undefined;
}

//Busqueda de eventos
$('#btn-buscarRelMov').click(function(e,pagina,tam,columna,orden){
  es_cargaT2RelMov=0;
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
    }
  });
  e.preventDefault();

  let sort_by = {
    columna: noTieneValor(columna)? $('#tablaRelevamientosMovimientos .activa').attr('value') : columna, 
    orden: noTieneValor(orden)?  $('#tablaRelevamientosMovimientos .activa').attr('estado') : orden
  };
  if(noTieneValor(sort_by.columna)){
    sort_by.columna = 'fiscalizacion_movimiento.id_fiscalizacion_movimiento';
  }
  if(noTieneValor(sort_by.orden)){
    sort_by.orden = 'desc';
  }
  console.log(sort_by);
  const page = noTieneValor(pagina)? $('#herramientasPaginacion').getCurrentPage() : pagina;
  const page_size = noTieneValor(tam)? $('#herramientasPaginacion').getPageSize() : tam;

  const formData = {
    id_tipo_movimiento: $('#B_TipoMovimientoRel').val(),
    fecha: $('#fechaRelMov').val(),
    nro_admin: $('#busqueda_maquina').val(),
    id_casino: $('#B_Casino').val(),
    id_log_movimiento: $('#busqueda_numero_movimiento').val(),
    page: noTieneValor(page)? 1 : page,
    sort_by: sort_by,
    page_size: noTieneValor(page_size)? 10 : page_size,
  }

  $.ajax({
    type: 'POST',
    url: 'relevamientos_movimientos/buscarFiscalizaciones',
    data: formData,
    dataType: 'json',

    success: function (response) {
      const fiscalizaciones = response.fiscalizaciones.data;
      console.log('success rel:', response);
      $('#herramientasPaginacion').generarTitulo(page,page_size,response.fiscalizaciones.total,clickIndice);
      $('#herramientasPaginacion').generarIndices(page,page_size,response.fiscalizaciones.total,clickIndice);
      $('#tablaRelevamientosMovimientos #cuerpoTablaRel tr').remove();
      fiscalizaciones.forEach(f => {
        $('#cuerpoTablaRel').append(generarFilaTabla(f));
      });
    },
    error: function (data) {
      console.log('Error:', data);
    }
  });
});

function clickIndice(e,pageNumber,tam){
  if(e != null){
    e.preventDefault();
  }

  const tam = (tam != null) ? tam : $('#herramientasPaginacion').getPageSize();
  const columna = $('#tablatablaRelevamientosMovimientosResultados .activa').attr('value');
  const orden = $('#tablaRelevamientosMovimientos .activa').attr('estado');
  $('#btn-buscarRelMov').trigger('click',[pageNumber,tam,columna,orden]);
}


//Se generan filas en la tabla principal con las fiscalizaciones encontradas
function generarFilaTabla(rel){
  console.log('generar',rel);
  const fecha = rel.fecha_envio_fiscalizar;
  const tipo_mov = rel.descripcion;
  const casino = rel.nombre;
  const nota = noTieneValor(rel.identificacion_nota)? '---' : rel.identificacion_nota;

  let fila = $('#filaEjemploRelevamiento').clone().attr('id',rel.id_fiscalizacion_movimiento);
  fila.find('.movimiento').text(rel.id_log_movimiento).attr('title',rel.id_log_movimiento);
  fila.find('.fecha').text(fecha).attr('title',fecha);
  fila.find('.nota').text(nota).attr('title',nota);
  fila.find('.tipo').text(tipo_mov).attr('title',tipo_mov);
  fila.find('.casino').text(casino).attr('title',casino);
  fila.find('.maquinas').text(rel.maquinas).attr('title',rel.maquinas);
  fila.find('button').attr('value',rel.id_fiscalizacion_movimiento);

  if(rel.es_controlador != 1){fila.find('.btn-eliminarFiscal').hide();}

  const estado = rel.id_estado_relevamiento;
  if(estado < 3){
    fila.find('.btn-imprimirRelMov').hide();
    fila.find('.btn-cargarT2RelMov').hide();
  }
  if(estado > 2){
    fila.find('.btn-imprimirRelMov').show();
    fila.find('.btn-eliminarFiscal').show();
    fila.find('.btn-generarRelMov').hide();
    fila.find('.btn-cargarRelMov').hide();
    if(estado < 7 && estado != 4 && tipo_mov != 'INGRESO' && tipo_mov != 'EGRESO/REINGRESOS'){
      fila.find('.btn-cargarT2RelMov').show();
    }else{
      fila.find('.btn-cargarT2RelMov').hide();
    }
  }

  return fila;
};


$(document).on('click','#tablaRelevamientosMovimientos thead tr th[value]',function(e){
  $('#tablaResultados th').removeClass('activa');

  if($(e.currentTarget).children('i').hasClass('fa-sort')){
    console.log('1');
    $(e.currentTarget).children('i').removeClass().addClass('fa fa-sort-desc').parent().addClass('activa').attr('estado','desc');
  }
  else{
    if($(e.currentTarget).children('i').hasClass('fa-sort-desc')){
      $(e.currentTarget).children('i').removeClass().addClass('fa fa-sort-asc').parent().addClass('activa').attr('estado','asc');
    }
    else{
      $(e.currentTarget).children('i').removeClass().addClass('fa fa-sort').parent().attr('estado','');
    }
  }
  $('#tablaResultados th:not(.activa) i').removeClass().addClass('fa fa-sort').parent().attr('estado','');
  clickIndice(e,$('#herramientasPaginacion').getCurrentPage(),$('#herramientasPaginacion').getPageSize());
});

$('#collapseFiltros').keypress(function(e){
  if(e.charCode == 13){//Enter
    $('#btn-buscarRelMov').click();
  }
})
