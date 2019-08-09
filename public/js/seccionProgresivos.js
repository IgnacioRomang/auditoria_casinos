/****************EVENTOS DEL DOM***********/


$(document).ready(function(){
  $('#barraMaquinas').attr('aria-expanded','true');
  $('#maquinas').removeClass();
  $('#maquinas').addClass('subMenu1 collapse in');
  $('#gestionarMTM').removeClass();
  $('#gestionarMTM').addClass('subMenu2 collapse in');

  $('#gestionarMTM').siblings('div.opcionesHover').attr('aria-expanded','true');

  $('.tituloSeccionPantalla').text('Progresivos');
  $('#gestionarMaquinas').attr('style','border-left: 6px solid #3F51B5;');
  $('#opcProgresivos').attr('style','border-left: 6px solid #25306b; background-color: #131836;');
  $('#opcProgresivos').addClass('opcionesSeleccionado');



  limpiarModal();

  $('#btn-buscar').trigger('click');
});


//Busqueda
$('#btn-buscar').click(function(e,pagina,page_size,columna,orden){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });

    e.preventDefault();

    //Fix error cuando librería saca los selectores
    if(isNaN($('#herramientasPaginacion').getPageSize())){
      var size = 10; // por defecto
    }else {
      var size = $('#herramientasPaginacion').getPageSize();
    }

    var page_size = (page_size == null || isNaN(page_size)) ? size : page_size;
    // var page_size = (page_size != null) ? page_size : $('#herramientasPaginacion').getPageSize();
    var page_number = (pagina != null) ? pagina : $('#herramientasPaginacion').getCurrentPage();

    var formData = {
      nombre_progresivo: $('#B_nombre_progresivo').val(),
      id_casino: $('#busqueda_casino').val(),
      page: page_number,
      sort_by: 'nombre',
      page_size: page_size,
    }

    $.ajax({
        type: 'POST',
        url: 'progresivos/buscarProgresivos',
        data: formData,
        dataType: 'json',
        success: function (resultados) {
            console.log(resultados);
            $('#herramientasPaginacion').generarTitulo(page_number,page_size,resultados.total,clickIndice);
            $('#cuerpoTabla tr').remove();
            for (var i = 0; i < resultados.data.length; i++){
                //console.log(resultados.data[i]);
                var filaProgresivo = generarFilaTabla(resultados.data[i]);
                $('#cuerpoTabla')
                    .append(filaProgresivo)
            }
            $('#herramientasPaginacion').generarIndices(page_number,page_size,resultados.total,clickIndice);

        },
        error: function (data) {
            console.log('Error:', data);
        }
      });
      $.ajax({
          type: 'GET',
          url: 'progresivos/buscarMaquinas/'+formData.id_casino,
          success: function (resultados) {
              let maquinas_lista = $('#maquinas_lista');
              maquinas_lista.empty();
              let option = $('<option></option>');
              for (var i=0; i < resultados.length; i++){
                let fila = option.clone().attr('value',resultados[i].nombre)
                .attr('data-id',resultados[i].id)
                .attr('data-isla',resultados[i].isla)
                .attr('data-sector',resultados[i].sector)
                .attr('data-nro_admin',resultados[i].nro_admin)
                .attr('data-marca_juego',resultados[i].marca_juego);
                maquinas_lista.append(fila);
              }
          },
          error: function (data) {
              console.log('Error:', data);
          }
      });
});

$('#btn-ayuda').click(function(e){
  e.preventDefault();

  $('.modal-title').text('| PROGRESIVOS');
  $('.modal-header').attr('style','font-family: Roboto-Black; background-color: #aaa; color: #fff');

	$('#modalAyuda').modal('show');

});

//Mostrar modal para agregar nuevo Progresivo
$('#btn-nuevo').click(function(e){
    $('#mensajeExito').hide();
    e.preventDefault();
    limpiarModal();
    habilitarControles(true);
    $('.btn-agregarNivelProgresivo').show();
    $('#btn-cancelar').text('CANCELAR');
    $('#btn-guardar').val("nuevo");
    $('#btn-guardar').removeClass();
    $('#btn-guardar').addClass('btn btn-successAceptar');
    $('.modal-title').text('| NUEVO PROGRESIVO');
    $('.modal-header').attr('style','font-family: Roboto-Black; background-color: #6dc7be; color: #fff');
    $('#modalProgresivo').modal('show');
});

// Modal crear nuevo progresivo individual
$('#btn-nuevo-ind').click(function(e){
  e.preventDefault();
  $('#modalProgInd').modal('show');
  $('.modal-header').attr('style','font-family: Roboto-Black; background-color: #6dc7be; color: #fff');
  $('#inputIslaInd').generarDataList("islas/buscarIslaPorCasinoYNro/" + 0,'islas','id_isla','nro_isla',2,true);
  $('#inputMtmInd').generarDataList("maquinas/obtenerMTMEnCasino/" + 0, 'maquinas','id_maquina','nro_admin',1,true);
  $('#inputIslaInd').setearElementoSeleccionado(0,"");
  $('#inputMtmInd').setearElementoSeleccionado(0,"");

});


// Modal crear nuevo progresivo linkeado
$('#btn-nuevo-link').click(function(e){
  e.preventDefault();
  $('#modalProgLink').modal('show');
  $('.modal-header').attr('style','font-family: Roboto-Black; background-color: #6dc7be; color: #fff');
  $('#inputIslaLink').generarDataList("islas/buscarIslaPorCasinoYNro/" + 0,'islas','id_isla','nro_isla',2,true);
  $('#inputMtmLink').generarDataList("maquinas/obtenerMTMEnCasino/" + 0, 'maquinas','id_maquina','nro_admin',1,true);
  $('#inputIslaLink').setearElementoSeleccionado(0,"");
  $('#inputMtmLink').setearElementoSeleccionado(0,"");

});

// Modal aceptar nuevo progresivo linkeado

$('#btn-guardar-link').on('click', function(e){
    $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
      }
  });
  var niveles = [];
  var pozos = [];

  // Carga de niveles
  $('#niveles_link').find(".columna").children().each(function(indexNivel){
    var nivel = {
      id_nivel: $(this).attr('id'),
      nro_nivel : $(this).find(".nro_nivel").val(),
      nombre_nivel: $(this).find('.nombre_nivel').val(),
      //porc_oculto : $(this).find(".porc_oculto").val(), Se quita hasta formalizar su utilidad
      porc_visible: $(this).find(".porc_visible").val(),
      base: $(this).find(".base").val(),
    }
    niveles.push(nivel);

  });

  // carga de pozos
  $('#contenedorPozosLink').children().each(function(indexPozo){
    var maquinas= [];
    $(this).find(".listaMaquinas").children().each(function(indexMaquina){
      var maquina;
      maquina = {
          id_maquina : $(this).val(),
      }
      maquinas.push(maquina);
    });

    var pozo = {
      maquinas: maquinas,
    };
    pozos.push(pozo);

  });

  var formData = {
     id_progresivo : $('#id_progresivo_link').val(),
     nombre:$('#nombre_progresivo_link').val() ,
     tipo: "LINKEADO", //$('#selectTipoProgresivos').val(), se cambia el modal, solo puede ser link
     pozos: pozos , //si es individual manda un solo pozo
     maximo: $('#maximo_link').val(),
     niveles: niveles,
     //porc_recuperacion : $('#porcentaje_recuperacion').val(), se elimina este valor hasta formalizar utilidad
  }

  var state = $('#btn-guardar').val();
  var type = "POST";
  var url = ((state == "modificar") ? 'progresivos/modificarProgresivo':'progresivos/guardarProgresivo');

  $.ajax({
      type: type,
      url: url,
      data: formData,
      dataType: 'json',
      success: function (data) {

          $('.modal').modal('hide');

          $('#mensajeExito').show();

          var pageNumber = $('#herramientasPaginacion').getCurrentPage();
          var tam = $('#herramientasPaginacion').getPageSize();
          var columna = $('#tablaLayouts .activa').attr('value');
          var orden = $('#tablaLayouts .activa').attr('estado');

          $('#btn-buscar').trigger('click',[pageNumber,tam,columna,orden]);
      },
      error: function (data) {
  //         //console.log('Error:', data);
  //         var response = JSON.parse(data.responseText);
  //
  //         limpiarAlertas();
  //
  //         if(typeof response.nombre_progresivo !== 'undefined'){
  //           $('#nombre_progresivo').addClass('alerta');
  //           $('#alerta-nombre-progresivo').text(response.nombre_progresivo[0]);
  //           $('#alerta-nombre-progresivo').show();
  //         }
  //
  //         var i=0;
  //         $('#columna .NivelProgresivo').each(function(){
  //           var error=' ';
  //           if(typeof response['niveles.'+ i +'.nro_nivel'] !== 'undefined'){
  //             error+=response['niveles.'+ i +'.nro_nivel']+'<br>';
  //             $(this).find('#nro_nivel').addClass('alerta');
  //           }
  //           if(typeof response['niveles.'+ i +'.nombre_nivel'] !== 'undefined'){
  //             error+=response['niveles.'+ i +'.nombre_nivel']+'<br>';
  //             $(this).find('#nombre_nivel').addClass('alerta');
  //           }
  //           if(typeof response['niveles.'+ i +'.porc_oculto'] !== 'undefined'){
  //             error+=response['niveles.'+ i +'.porc_oculto']+'<br>';
  //             $(this).find('#porc_oculto').addClass('alerta');
  //           }
  //           if(typeof response['niveles.'+ i +'.porc_visible'] !== 'undefined'){
  //             error+=response['niveles.'+ i +'.porc_visible']+'<br>';
  //             $(this).find('#porc_visible').addClass('alerta');
  //           }
  //           if(typeof response['niveles.'+ i +'.base'] !== 'undefined'){
  //             error+=response['niveles.'+ i +'.base']+'<br>';
  //             $(this).find('#base').addClass('alerta');
  //           }
  //           if(typeof response['niveles.'+ i +'.maximo'] !== 'undefined'){
  //             error+=response['niveles.'+ i +'.maximo']+'<br>';
  //             $(this).find('#maximo').addClass('alerta');
  //           }
  //           if(error != ' '){
  //           var alerta='<div class="col-xs-12"><span class="alertaTabla alertaSpan">'+error+'</span></div>';
  //             $(this).append(alerta);
  //           }
  //           i++;
  //         })

      }
  });

});

//Mostrar modal con los datos del Log
$(document).on('click','.detalle',function(){
      limpiarModal();
      $('.modal-title').text('| VER MÁS');
      $('.modal-header').attr('style','font-family: Roboto-Black; background: #4FC3F7');
      $('.btn-agregarNivelProgresivo').hide();
      $('#btn-cancelar').text('SALIR');

      var id_progresivo = $(this).val();

      $.get("progresivos/obtenerProgresivo/" + id_progresivo, function(data){
          console.log(data);
          mostrarProgresivo(data.progresivo,data.pozos,data.maquinas,false);
          habilitarControles(false);
          $('#modalProgresivo').modal('show');
      });
});

//Mostrar modal con los datos del Juego cargado
$(document).on('click','.modificar',function(){
      $('#mensajeExito').hide();
      limpiarModal();
      habilitarControles(true);
      $('#btn-cancelar').text('CANCELAR');
      $('.btn-agregarNivelProgresivo').show();
      $('.modal-title').text('| MODIFICAR PROGRESIVO');
      $('.modal-header').attr('style','font-family: Roboto-Black; background: #ff9d2d');
      $('#btn-guardar').removeClass();
      $('#btn-guardar').addClass('btn btn-warningModificar');

      var id_progresivo = $(this).val();

      $.get("progresivos/obtenerProgresivo/" + id_progresivo, function(data){
          mostrarProgresivo(data.progresivo,data.pozos,data.maquinas,true);
          console.log('niveles' , data.niveles);

          // habilitarControles(true);
          $('#btn-guardar').val("modificar");
          $('#modalProgresivo').modal('show');
      });
});

//Borrar Progresivo y remover de la tabla
$(document).on('click','.eliminar',function(){
      //Cambiar colores modal
      $('.modal-title').text('ADVERTENCIA');
      $('.modal-header').removeAttr('style');
      $('.modal-header').attr('style','font-family: Roboto-Black; color: #EF5350');

      var id_progresivo = $(this).val();
      $('#btn-eliminarModal').val(id_progresivo);
      $('#modalEliminar').modal('show');
});

$(document).on('click','#tablaResultados thead tr th[value]',function(e){
  $('#tablaResultados th').removeClass('activa');
  if($(e.currentTarget).children('i').hasClass('fa-sort')){
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

/***********EVENTOS DEL MODAL**********/

// $(document).on("keypress" , function(e){
//   if(e.which == 13 && $('#modalProgresivo').is(':visible')) {
//     e.preventDefault();
//     $('#btn-guardar').click();
//   }
// })

$(document).on("keyup " , ".porc_visible" , function() {
  var input = $(this).val();
  var index = $(this).parent().parent().index();
  $('.columna').each(function() {
    $(this).children().eq(index).find('.porc_visible').val(input);
  })
});

$(document).on("keyup " , ".nro_nivel" , function() {
  var input = $(this).val();
  var index = $(this).parent().parent().index();
  $('.columna').each(function() {
    $(this).children().eq(index).find('.nro_nivel').val(input);
  })
});

$(document).on("keyup " , ".porc_oculto" , function() {
  var input = $(this).val();
  var index = $(this).parent().parent().index();
  $('.columna').each(function() {
    $(this).children().eq(index).find('.porc_oculto').val(input);
  })
});

$(document).on("keyup " , ".nombre_nivel" , function() {
    var input = $(this).val();
    var index = $(this).parent().parent().index();
    $('.columna').each(function() {
      $(this).children().eq(index).find('.nombre_nivel').val(input);
    })
});

$(document).on('click','.btn-agregarNivelProgresivo',function(){
    $('#tablaNivelesProgresivoEncabezado').show();
    var columna =  $(this).parent().parent().find('.columna');
    agregarNivelProgresivo(null,true,-1);//-1 significa a todos las collumnas
});

//borrar un nivel progresivo
$(document).on('click','.borrarNivelProgresivo',function(){
    var index = $(this).parent().parent().index();

    $('.columna').each(function() {
      $(this).children().eq(index).remove();
    })

});

$('#selectTipoProgresivos').on('change' , function(){
  switch ($(this).val()) {
    case 'LINKEADO':
    $('#cuerpo_linkeado').show();
    $('#cuerpo_individual').hide();
      break;
    case 'INDIVIDUAL':
    $('#cuerpo_individual').show();
    $('#cuerpo_linkeado').hide();
      break;
    case '0':
    $('#cuerpo_individual').hide();
    $('#cuerpo_linkeado').hide();
      break;
    default:
      break;

  }
})


$('#btn-agregarPozo-link').click(function(){
  // solo se agrega un pozo si existen maquinas a quien asignarselo
  var listaMaquinas = $(this).parent().parent().find('.listaMaquinas').find('li').clone();
  if (listaMaquinas.length >0){
    var nro_pozo = $('#contenedorPozosLink').children().length + 1 ;
    var pozo = agregarPozo(nro_pozo);
    //var radio_button_group = clonarRadioButton(nro_pozo);

    $('#contenedorPozosLink').append(pozo);
    if($('#pozo_' + (nro_pozo - 1) + ' .columna').length){
      $("#pozo_" + nro_pozo + " .columna").replaceWith($('#pozo_' + (nro_pozo - 1) + ' .columna').clone());
    }
    //$('#pozo_' + nro_pozo + ' .contenedorBuscadores').prepend(radio_button_group);

    $('#pozo_'+nro_pozo).find('.listaMaquinas').html(listaMaquinas);
    $(this).parent().parent().find('.listaMaquinas').empty();
    }

})

$(document).on('change' , '.radioGroup' , function(){
  var id_casino = $('input:checked' , $(this)).val();
  console.log(id_casino);
  $('.buscadorIsla' , $(this).parent() ).generarDataList("http://" + window.location.host+  "/islas/buscarIslaPorCasinoYNro/" + id_casino,'islas','id_isla','nro_isla',2,true);
  $('.buscadorMaquina' , $(this).parent()).generarDataList("http://" + window.location.host+  "/maquinas/buscarMaquinaPorNumeroMarcaYModelo/" + id_casino ,'resultados','id_maquina','nro_admin',2,true);
  $('.buscadorIsla' ,  $(this).parent()).setearElementoSeleccionado(0,"");
  $('.buscadorMaquina' , $(this).parent()).setearElementoSeleccionado(0,"");
})

function agregarPozo(nro_pozo){
  var retorno =
'<div class="row pozo" id="pozo_'+ nro_pozo +'" data-id="0">'
+   '<div id="seccionAgregarNivelProgresivo'+ nro_pozo +'" style="cursor:pointer;" class="cAgregarProgresivo" data-toggle="collapse" data-target="#collapseAgregarProgresivo'+nro_pozo+'">'
+       '<div class="row" style="border-top: 4px solid #a0968b; padding-top: 15px;">'
+           '<div class="col-xs-10">'
+               '<h4>POZO: <i class="fa fa-fw fa-angle-down"></i></h4>'
+           '</div>'
+       '</div>'
+   '</div>'
+   '<div id="collapseAgregarNivelProgresivo'+nro_pozo+'" class="collapse" data-pozo="'+nro_pozo+'">'
+     '<div class="row">'
+       '<div id="" class="col-md-6 col-lg-6">'
+         '<div class="row">'
+           '<div class="col-md-7 col-lg-7">'
+               '<h5>Niveles:</h5>'
+           '</div>'
+           '<div class="col-md-2 col-lg-2">'
+           '</div>'
+           '<div class="col-md-3 col-lg-3 errorVacio">'
+           '</div>'
+         '</div>'
+         '<div class="row">'
+           '<div class="col-md-12">'
+             '<div class="panel panel-default">'
+                 '<div class="panel-heading">'
+                   '<h4>NIVELES</h4>'
+                 '</div>'
+                 '<div class="panel-body">'
+                  '<table id="tablaNiveles"'+nro_pozo+' class="table table-fixed tablesorter">'
+                    '<thead>'
+                       '<tr>'
+                           '<th class="col-xs-6" value="nivel.nombre" estado="">NOMBRE NIVEL  <i class="fa fa-sort"></i></th>'
+                            '<th class="col-xs-6">ACCIONES</th>'
+                         '</tr>'
+                      '</thead>'
+                      '<tbody id="cuerpoTablaNiveles'+nro_pozo+'" style="height: 350px;">'
+                      '</tbody>'
+                   '</table>'
+                 '</div>'
+             '</div>'
+           '</div>'
+         '</div>'
+     '</div>'
+   '</div>'
+'</div>'
+'<br>'
+'<br>'
+'<button  class="btn btn-danger borrarPozo" type="button" name="button" style="" data-pozo="'+ nro_pozo +'"> <i class="fa fa-fw fa-times" style="position:relative; left:-1px; top:-1px;"></i>BORRAR POZO</button>'
+'</div><br>'
+'</div> </div>';
  return retorno;
}

/*****FUNCIONES*****/
function agregarIsla(id_isla , listaMaquinas , tipo_progresivo){
  $.get("islas/obtenerIsla/" + id_isla , function(data){
    switch (tipo_progresivo) {
      case 'link':
          console.log('agregarIsla-link');
          for (var i = 0; i < data.maquinas.length; i++) {
            if(existeEnDataList(data.maquinas[i].id_maquina,tipo_progresivo)){
              moverAPozo(data.maquinas[i].id_maquina,listaMaquinas);
            }else {
              agregarMaquina(data.maquinas[i].id_maquina ,data.maquinas[i].nro_admin ,data.maquinas[i].marca , data.maquinas[i].modelo , listaMaquinas);
            }
          }
          break;

      case 'individual':
        console.log('agregarIsla-individual');
        for (var i = 0; i < data.maquinas.length; i++) {
          if(!existeEnDataList(data.maquinas[i].id_maquina,tipo_progresivo)){
            agregarMaquina(data.maquinas[i].id_maquina ,data.maquinas[i].nro_admin ,data.maquinas[i].marca , data.maquinas[i].modelo , listaMaquinas);
          }
        }
        break;
      default: break;

    }

  });
}

function borrarPozo(nro_pozo){
  $('#pozo_' + nro_pozo).remove();
}

$(document).on("click " , ".borrarPozo" , function() {
    var nro_pozo = $(this).attr('data-pozo');
    borrarPozo(nro_pozo);
});
/*
function agregarMaquina(id_maquina, nro_admin,nombre,modelo, listaMaquinas){
    listaMaquinas.append($('<li>')
        //Se agrega el id del progresivo de la lista
        .val(id_maquina)
        .addClass('row')
        .css('list-style','none')
        //Columna de NUMERO ADMIN
        .append($('<div>')
            .addClass('col-xs-2').css('margin-top','6px')
            .text(nro_admin)
        )
        //Columna de NOMBRE PROGRESIVO
        .append($('<div>')
            .addClass('col-xs-4').css('margin-top','6px')
            .text(nombre)
        )
        //Columna de TIPO PROGRESIVO
        .append($('<div>')
            .addClass('col-xs-4').css('margin-top','6px')
            .text(modelo)
        )
        //Columna BOTON QUITAR
        .append($('<div>')
            .addClass('col-xs-2')
            .append($('<button>')
                .addClass('btn').addClass('btn-danger').addClass('borrarFila').addClass('borrarMaquina')
                .append($('<i>')
                    .addClass('fa fa-fw fa-trash')
                )
            )
        )
    );
}
*/
function clickIndice(e,pageNumber,tam){
  if(e != null){
    e.preventDefault();
  }
  var tam = (isNaN(tam)) ?  $('#herramientasPaginacion').getPageSize() : tam;
  var columna = $('#tablaResultados .activa').attr('value');
  var orden = $('#tablaResultados .activa').attr('estado');
  $('#btn-buscar').trigger('click',[pageNumber,tam,columna,orden]);
}

function generarFilaTabla(progresivo){
    var fila = $(document.createElement('tr'));
    fila.attr('id','progresivo' + progresivo.id_progresivo)
    .append($('<td>')
            .addClass('col-xs-6')
            .text(progresivo.nombre)
    )
    .append($('<td>')
          .addClass('col-xs-6')
          .append($('<button>')
              .append($('<i>')
                  .addClass('fa').addClass('fa-fw').addClass('fa-search-plus')
              )
              .append($('<span>').text(' VER MÁS'))
              .addClass('btn').addClass('btn-info').addClass('detalle')
              .attr('value',progresivo.id_progresivo)
          )
          .append($('<span>').text(' '))
          .append($('<button>')
              .append($('<i>')
                  .addClass('fa').addClass('fa-fw').addClass('fa-pencil-alt')
              )
              .append($('<span>').text(' MODIFICAR'))
              .addClass('btn').addClass('btn-warning').addClass('modificar')
              .attr('value',progresivo.id_progresivo)
          )
          .append($('<span>').text(' '))
          .append($('<button>')
              .append($('<i>').addClass('fa').addClass('fa-fw').addClass('fa-trash-alt')
              )
              .append($('<span>').text(' ELIMINAR'))
              .addClass('btn').addClass('btn-danger').addClass('eliminar')
              .attr('value',progresivo.id_progresivo)
          )
      )
      return fila;
}

function habilitarControles(valor){
    if(valor){// nuevo y modificar
      $('#nombre_progresivo').prop('readonly',false);
      $('#selectTipoProgresivos').prop('disabled',false);
      $('#porcentaje_recuperacion').prop('readonly',false);
      $('#maximo').prop('readonly',false);
      $('.buscadorIsla').prop('readonly',false);
      $('.buscadorMaquina').prop('readonly',false);
      $('#btn-agregarNivelProgresivo').show();
      $('#btn-guardar').prop('disabled',false).show();
      $('#btn-guardar').css('display','inline-block');
    }
    else{// ver detalle
      $('#modalProgresivo input').prop("readonly" , true);
      $('#nombre_progresivo').prop('readonly',true);
      $('#selectTipoProgresivos').prop('disabled',true);
      $('#btn-agregarNivelProgresivo').hide();
      $('.borrarFila').remove();
      $('#btn-guardar').prop('disabled',true).hide();
      $('#btn-guardar').css('display','none');
      $('#borrarJuego').remove();
    }
}

function limpiarModal(){
    $('#frmProgresivo').trigger('reset');
    $('#columna > .NivelProgresivo').remove();
    $('#id_progresivo').val(0);
    $('#juegosSeleccionados li').remove();
    $('#inputJuego').prop("readonly" , false);
    $('#juegoSeleccionado').text("");
    $('#juegoSeleccionado').val("");
    $('#agregarJuego').css('display' , 'none');
    $('#cancelarJuego').css('display' , 'none');
    limpiarAlertas();
}

function limpiarAlertas(){
    $('#nombre_progresivo').removeClass('alerta');
    $('#alerta-nombre_progresivo').text('').hide();

    $('#columna .NivelProgresivo').each(function(){
      $(this).find('#nro_nivel').removeClass('alerta');
      $(this).find('#nombre_nivel').removeClass('alerta');
      $(this).find('#porc_oculto').removeClass('alerta');
      $(this).find('#porc_visible').removeClass('alerta');
      $(this).find('#base').removeClass('alerta');
      $(this).find('#maximo').removeClass('alerta');
    });
    $('.alertaTabla').remove();
}

function clonarRadioButton(i){
  var div_radios_clonado = $('#modelo_radio').clone();
  var id_casino = 0;
  $('input' , div_radios_clonado).each(function(){
    if($(this).is(':checked'))
      $(this).prop('checked', false);
    id_casino = $(this).val();
    $('label[for="' +  $(this).attr('id') + '"]' , div_radios_clonado).attr('for', 'link_pozo_' + i + '_' + id_casino);
    $(this).attr('id', 'link_pozo_' + i + '_' + id_casino);
    $(this).attr('name' , 'casinos_'  + i);
  })
  div_radios_clonado.removeAttr('id');
  return div_radios_clonado;
}

function crearBoton(icono){
  let btn = $('<button></button>').addClass('btn').addClass('btn-info');
  let i = $('<i></i>').addClass('fa').addClass('fa-fw').addClass(icono);
  btn.append(i);
  return btn;
}

function crearEditable(tipo,
  defecto="",
  min=0,
  max=100,
  step=0.001){
  return $('<input></input>')
  .addClass('editable')
  .addClass('form-control')
  .attr('type',tipo)
  .attr('min',min)
  .attr('max',max)
  .attr('step',step)
  .val(defecto);
}

function filaEjemplo(){
  return $('.tablaPozoDiv.ejemplo').find('.filaEjemplo').clone().removeClass('filaEjemplo').show();
}
function filaEjemploMaquina(){
  return $('.tablaMaquinasDiv.ejemplo').find('.filaEjemplo').clone().removeClass('filaEjemplo').show();
}

function setearValoresFilaNivel(fila,nivel,fila_es_editable=false){
  fila.attr('data-id',nivel.id_nivel_progresivo);

  if(!fila_es_editable){
    fila.find('.cuerpoTablaPozoNumero').text(nivel.nro_nivel);
    fila.find('.cuerpoTablaPozoNombre').text(nivel.nombre_nivel);
    fila.find('.cuerpoTablaPozoBase').text(nivel.base);
    fila.find('.cuerpoTablaPozoMaximo').text(nivel.maximo);
    fila.find('.cuerpoTablaPorcVisible').text(nivel.porc_visible);
    fila.find('.cuerpoTablaPorcOculto').text(nivel.porc_oculto);
  }
  else{
    fila.find('.cuerpoTablaPozoNumero .editable').val(nivel.nro_nivel);
    fila.find('.cuerpoTablaPozoNombre .editable').val(nivel.nombre_nivel);
    fila.find('.cuerpoTablaPozoBase .editable').val(nivel.base);
    fila.find('.cuerpoTablaPozoMaximo .editable').val(nivel.maximo);
    fila.find('.cuerpoTablaPorcVisible .editable').val(nivel.porc_visible);
    fila.find('.cuerpoTablaPorcOculto .editable').val(nivel.porc_oculto);
  }
}
function crearFilaEditableNivel(valores = { id_nivel_progresivo : -1 }){
  let fila = filaEjemplo();

  fila.find('.cuerpoTablaPozoNumero').empty().append(crearEditable('number','',0,null,'any'))
  fila.find('.cuerpoTablaPozoNombre').empty().append(crearEditable("text"));
  fila.find('.cuerpoTablaPozoBase').empty().append(crearEditable("number","0",0,null,"any"));
  fila.find('.cuerpoTablaPozoMaximo').empty().append(crearEditable("number","0",0,null,"any"));
  fila.find('.cuerpoTablaPorcVisible').empty().append(crearEditable("number","0"));
  fila.find('.cuerpoTablaPorcOculto').empty().append(crearEditable("number","0"));
  fila.find('.editar').remove();
  fila.find('.cuerpoTablaPozoAcciones').empty();
  fila.find('.cuerpoTablaPozoAcciones').append(crearBoton('fa-check').addClass('confirmar'));
  fila.find('.cuerpoTablaPozoAcciones').append(crearBoton('fa-times').addClass('cancelar'));

  setearValoresFilaNivel(fila,valores,true);

  fila.find('.confirmar').on('click',function(){
    let numero = fila.find('.cuerpoTablaPozoNumero .editable').val();
    let nombre = fila.find('.cuerpoTablaPozoNombre .editable').val();
    let base = fila.find('.cuerpoTablaPozoBase .editable').val();
    let maximo = fila.find('.cuerpoTablaPozoMaximo .editable').val();
    let porc_visible = fila.find('.cuerpoTablaPorcVisible .editable').val();
    let porc_oculto = fila.find('.cuerpoTablaPorcOculto .editable').val();
    let valido =  numero != '';
    valido = valido && (nombre != '');
    valido = valido && (base >= 0);
    valido = valido && (maximo >= 0);
    valido = valido && (porc_visible >= 0) && (porc_visible <= 100);
    valido = valido && (porc_oculto >= 0) && (porc_oculto <= 100);
    if(valido) modificarNivel(fila);
  });

  fila.find('.cancelar').on('click',function(){
    let nueva_fila = filaEjemplo();
    setearValoresFilaNivel(nueva_fila,valores);

    nueva_fila.find('.cuerpoTablaPozoAcciones .editar').on('click',function(){
      let fila_editable = crearFilaEditableNivel(valores);
      nueva_fila.replaceWith(fila_editable);
    });

    nueva_fila.find('.cuerpoTablaPozoAcciones .borrar').on('click',function(){
      nueva_fila.remove();
    });

    fila.replaceWith(nueva_fila);
  });

  return fila;
}


function modificarNivel(fila){
  fila.find('.editable').each(function(index,child){
    let val = $(child).val();
    $(child).removeClass('editable');
    $(child).replaceWith(val);
  });

  fila.find('.confirmar').replaceWith(crearBoton('fa-pencil-alt').addClass('editar'));
  fila.find('.cancelar').replaceWith(crearBoton('fa-trash-alt').addClass('borrar'));

  fila.find('.editar').on('click',function(){
    let valores = arregloNivel(fila);
    let fila_editable = crearFilaEditableNivel(valores);
    fila.replaceWith(fila_editable);
  });

  fila.find('.cuerpoTablaPozoAcciones .borrar').on('click',function(){
    fila.remove();
  });

  fila.parent().parent().parent().find('.agregar').attr('disabled',false);
}


function mostrarPozo(id_pozo,nombre,editable,niveles = {}){
  let pozo_html = $('.tablaPozoDiv.ejemplo').clone().removeClass('ejemplo');
  pozo_html.find('.nombrePozo').text(nombre);
  $('#contenedorPozos').append(pozo_html);
  pozo_html.show();

  pozo_html.attr('data-id',id_pozo);

  pozo_html.find('.filaEjemplo').remove();

  let fila_ejemplo_pozo = filaEjemplo();
  for (var j = 0; j < niveles.length; j++) {
    let fila = fila_ejemplo_pozo.clone();

    const nivel = niveles[j];

    setearValoresFilaNivel(fila,nivel);

    fila.find('.cuerpoTablaPozoAcciones').children().each(
      function (index,child){
        $(child).attr('disabled',!editable);
    });

    fila.find('.cuerpoTablaPozoAcciones .editar').on('click',function(){
      let fila_editable = crearFilaEditableNivel(nivel);
      fila.replaceWith(fila_editable);
    });

    fila.find('.cuerpoTablaPozoAcciones .borrar').on('click',function(){
      fila.remove();
    });

    pozo_html.find('.cuerpoTablaPozo').append(fila);
  }

  const editarPozoCallback = function(){
    let text_viejo = pozo_html.find('.nombrePozo').text();
    pozo_html.find('.nombrePozo').replaceWith(
      crearEditable('text')
      .addClass('nombrePozo')
      .val(text_viejo)
    );

    let boton = crearBoton('fa-check')
    .addClass('confirmarPozo')
    .removeClass('btn-info')
    .addClass('btn-link');
    pozo_html.find('.editarPozo').replaceWith(boton);

    const confirmarPozoCallback = function(){
      let valorModif = pozo_html.find('.nombrePozo').val();
      let text = $('<b></b>').text(valorModif);

      text.addClass('nombrePozo');
      pozo_html.find('.nombrePozo').replaceWith(text);

      let boton2 = crearBoton('fa-pencil-alt')
      .addClass('editarPozo')
      .removeClass('btn-info')
      .addClass('btn-link');

      pozo_html.find('.confirmarPozo').replaceWith(boton2);
      boton2.on('click',editarPozoCallback);
    };

    boton.on('click',confirmarPozoCallback);
  };

  pozo_html.find('.editarPozo').attr('disabled',!editable);
  pozo_html.find('.editarPozo').on('click',editarPozoCallback);
  pozo_html.find('.eliminarPozo').attr('disabled',!editable);
  pozo_html.find('.eliminarPozo').on('click',function(){
      pozo_html.remove();
  });

  pozo_html.find('.collapse').on('show.bs.collapse',function(){
    let icono = pozo_html.find('.abrirPozo i');
    let icono_nuevo = $('<i></i>').addClass('fa').addClass('fa-fw');
    icono.replaceWith(icono_nuevo.addClass('fa-angle-down'));
  });

  pozo_html.find('.collapse').on('hide.bs.collapse',function(){
    let icono = pozo_html.find('.abrirPozo i');
    let icono_nuevo = $('<i></i>').addClass('fa').addClass('fa-fw');
    icono.replaceWith(icono_nuevo.addClass('fa-angle-up'));
  });

  pozo_html.find('.abrirPozo').on('click',function(){
      let colapsable = pozo_html.find('.collapse');
      colapsable.collapse('toggle');
  });

  pozo_html.find('.agregar').attr('disabled',!editable);
  pozo_html.find('.agregar').on("click",function(){
    let fila = crearFilaEditableNivel();
    pozo_html.find('.cuerpoTablaPozo').append(fila);
    $(this).attr('disabled',true);
  });

}

function arregloNivel(fila){
  let nivel = {
    id_nivel_progresivo : fila.attr('data-id'),
    nro_nivel : fila.find('.cuerpoTablaPozoNumero').text(),
    nombre_nivel : fila.find('.cuerpoTablaPozoNombre').text(),
    base : fila.find('.cuerpoTablaPozoBase').text(),
    porc_oculto : fila.find('.cuerpoTablaPorcOculto').text(),
    porc_visible : fila.find('.cuerpoTablaPorcVisible').text(),
    maximo : fila.find('.cuerpoTablaPozoMaximo').text()
  };
  return nivel;
}

function arregloPozos(){
  const pozos_html = $('.tablaPozoDiv').not('.ejemplo');

  let ret = [];

  for(i = 0;i<pozos_html.length;i++){
    const pozo_html = $(pozos_html[i]);
    const id_pozo = pozo_html.attr('data-id');
    const descripcion = pozo_html.find('.nombrePozo').text();

    let filas = [];

    pozo_html.find('tbody tr').each(function(idx,c){
      filas.push(arregloNivel($(c)));
    });

    const data = {
      id_pozo : id_pozo,
      descripcion : descripcion,
      niveles : filas
    };

    ret.push(data);
  }

  return ret;
}
function arregloMaquinas(){
  const maq_html = $($('.tablaMaquinasDiv').not('.ejemplo').first());
  let ret = [];

  maq_html.find('tbody tr').each(function(idx,c){
    let fila = $(c);
    ret.push({
      id_maquina : fila.attr('data-id'),
      nro_admin : fila.find('.cuerpoTablaNroAdmin').text(),
      nro_isla :  fila.find('.cuerpoTablaIsla').text(),
      sector_descripcion : fila.find('.cuerpoTablaSector').text(),
      marca_juego : fila.find('.cuerpoTablaMarcaJuego').text()
    });
  });

  return ret;
}

function mostrarProgresivo(progresivo,pozos,maquinas,editable){
    $('#id_progresivo').val(progresivo.id_progresivo);
    $('#nombre_progresivo').val(progresivo.nombre);
    $('#nombre_progresivo').attr('disabled',!editable);
    $('#porc_recup').val(progresivo.porc_recup);
    $('#porc_recup').attr('disabled',!editable);
    $('#contenedorPozos').empty();
    $('#contenedorMaquinas').empty();
    $('#btn-agregarPozo').attr('disabled',!editable).off();
    $('#btn-agregarPozo').on('click',function(){
        mostrarPozo(-1,'Pozo',editable);
    });

    for (var i = 0; i < pozos.length; i++){
      mostrarPozo(pozos[i].id_pozo,pozos[i].descripcion,editable,pozos[i].niveles);
    }

    $('.abrirPozo').first().trigger('click');

    llenarTablaMaquinas(maquinas,editable);

    $('#btn-guardar').attr('disabled',!editable).off();
    $('#btn-guardar').on('click',function(){
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
          }
      })

      let url = 'progresivos/modificarProgresivo/'+progresivo.id_progresivo;

      let formData = {
        id_progresivo : progresivo.id_progresivo,
        nombre : $('#nombre_progresivo').val(),
        porc_recup :  $('#porc_recup').val(),
        pozos : arregloPozos(),
        maquinas : arregloMaquinas(),
      };

      $.ajax({
          type: 'POST',
          data: formData,
          url: url,
          success: function(data){
            console.log(data)
          },
          error: function(err){
            console.log(err)
          }
      });
    });

}

function setearFilaMaquinas(fila,id,nro_admin,sector,isla,marca_juego){
  fila.find('.cuerpoTablaNroAdmin').text(nro_admin);
  fila.find('.cuerpoTablaSector').text(sector)
  fila.find('.cuerpoTablaIsla').text(isla);
  fila.find('.cuerpoTablaMarcaJuego').text(marca_juego);
  fila.attr('data-id',id);
  fila.find('.unlink').on('click',function(){fila.remove()});
}

function filaEditableMaquina(){
  let fila = filaEjemploMaquina();
  let input = $('<input></input>')
  .addClass('form-control')
  .addClass('editable')
  .attr('list','maquinas_lista');

  setearFilaMaquinas(fila,'','','','','')

  fila.find('.cuerpoTablaNroAdmin').replaceWith(input);
  fila.find('.cuerpoTablaAcciones').empty();
  fila.find('.cuerpoTablaAcciones').append(crearBoton('fa-check').addClass('confirmar'));
  fila.find('.cuerpoTablaAcciones').append(crearBoton('fa-times').addClass('cancelar'));

  fila.find('.cancelar').on('click',function(){
    fila.remove();
  });

  fila.find('.confirmar').on('click',function(){
    let filaCompleta = filaEjemploMaquina();
    let value = input.val();
    let data =  $('#maquinas_lista')
    .find('option[value='+input.val()+']');
    let data_id = data.attr('data-id');
    let nro_admin = data.attr('data-nro_admin');
    let sector = data.attr('data-sector');
    let isla = data.attr('data-isla');
    let marca_juego = data.attr('data-marca_juego');

    setearFilaMaquinas(filaCompleta,data_id,nro_admin,sector,isla,marca_juego);

    fila.replaceWith(filaCompleta);
  });

  return fila;
}

function llenarTablaMaquinas(maquinas,editable){
  let maq_html = $('.tablaMaquinasDiv.ejemplo').clone().removeClass('ejemplo');
  $('#contenedorMaquinas').append(maq_html);
  maq_html.show();

  $('#btn-agregarMaquina').attr('disabled',!editable).off();
  $('#btn-agregarMaquina').on('click', function() {
    maq_html.find('.cuerpoTabla').append(filaEditableMaquina());
  });


  var fila_ejemplo_maq = filaEjemploMaquina();
  maq_html.find('.filaEjemplo').remove();
  for (var j = 0; j < maquinas.length; j++) {
    let fila = fila_ejemplo_maq.clone();

    setearFilaMaquinas(fila,
      maquinas[j].id_maquina,maquinas[j].nro_admin,
      maquinas[j].sector,maquinas[j].isla,
      maquinas[j].marca_juego);

    fila.find('.cuerpoTablaAcciones').children().each(
      function (index,child){
        $(child).attr('disabled',!editable);
    });

    maq_html.find('.cuerpoTabla').append(fila);
  }
}

function moverAPozo(id_maquina, listaMaquinas){
  var listas = $('#cuerpo_linkeado .listaMaquinas').not(listaMaquinas);
  $('li' , listas).each(function(){
     if(parseInt($(this).val()) == parseInt(id_maquina)){
        var maquina_clon = $(this).clone();
        listaMaquinas.append(maquina_clon);
        $(this).remove();
     }
  })
}


/****************TODOS EVENTOS DE BUSCADORES*****************/

//Agregar Máquina
$(document).on("click",  ".agregarMaquina" , function(){
  //Crear un item de la lista
  var input = $(this).parent().parent().find('input');
  var id = input.obtenerElementoSeleccionado();
  var listaMaquinas = $(this).parent().parent().parent().parent().parent().find('.listaMaquinas');
  if(id != 0){
    if(!existeEnDataList(id,tipoProgresivo())){
      $.get('http://' + window.location.host +"/maquinas/obtenerConfiguracionMaquina/" + id, function(data){
        agregarMaquina( data.maquina.id_maquina,data.maquina.nro_admin,data.maquina.marca,data.maquina.modelo,listaMaquinas);
        input.setearElementoSeleccionado(0,"");
      });

    }else {
      if(tipoProgresivo() == 'link'){
        moverAPozo(id,listaMaquinas);
      }
      input.setearElementoSeleccionado(0,"");
    }
  }
});

//Agregar Isla
$(document).on("click", ".agregarIsla" ,function(){
  var listaMaquinas =  $(this).parent().parent().parent().parent().parent().find('.listaMaquinas');
  var input = $(this).parent().parent().find('input');
  var id = input.obtenerElementoSeleccionado();
  if(id != 0){
    console.log('agregarIsla-click');
    agregarIsla(id,listaMaquinas,tipoProgresivo());
    input.setearElementoSeleccionado(0,"")
  }
});

$(document).on('click','.borrarMaquina',function(e){
  e.preventDefault();
  $(this).parent().parent().remove();
});

//Opacidad del modal al minimizar
$('#btn-minimizar').click(function(){
    if($(this).data("minimizar")==true){
    $('.modal-backdrop').css('opacity','0.1');
    $(this).data("minimizar",false);
  }else{
    $('.modal-backdrop').css('opacity','0.5');
    $(this).data("minimizar",true);
  }
});

//Quitar eventos de la tecla Enter
$("#contenedorFiltros input").on('keypress',function(e){
    if(e.which == 13) {
      e.preventDefault();
      $('#btn-buscar').click();
    }
});

$('#btn-eliminarModal').click(function(e){
      var id_progresivo = $(this).val();

      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
          }
      })

      $.ajax({
          type: "DELETE",
          url: "progresivos/eliminarProgresivo/" + id_progresivo,
          success: function (data) {
            console.log(data);
            $('#progresivo' + id_progresivo).remove();
            $("#tablaResultados").trigger("update");
            $('#modalEliminar').modal('hide');
          },
          error: function (data) {
            console.log('Error: ', data);
          }
      });
});


//Crear nuevo progresivo / actualizar si existe
$('#btn-guardar').click(function (e) {
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
          }
      });

      var niveles = [];

      if($('#selectTipoProgresivos').val()  == 'LINKEADO'){//si es linkeado, capturo y mando informacion de casda pozo
        var pozos = [];

        $('#contenedorPozos').children().each(function(indexPozo){
          var maquinas= [];
          var niveles= [];
          $(this).find(".listaMaquinas").children().each(function(indexMaquina){
            var maquina;
            maquina = {
                id_maquina : $(this).val(),
            }
            maquinas.push(maquina);
          });

          $(this).find(".columna").children().each(function(indexNivel){
            var nivel = {
              id_nivel: $(this).attr('id'),
              nro_nivel : $(this).find(".nro_nivel").val(),
              nombre_nivel: $(this).find('.nombre_nivel').val(),
              porc_oculto : $(this).find(".porc_oculto").val(),
              porc_visible: $(this).find(".porc_visible").val(),
              base: $(this).find(".base").val(),
            }
            niveles.push(nivel);

          });

          var pozo = {
            maquinas: maquinas,
            niveles: niveles,
          };

          pozos.push(pozo);

        })

        var formData = {
           id_progresivo : $('#id_progresivo').val(),
           nombre:$('#nombre_progresivo').val() ,
           tipo: $('#selectTipoProgresivos').val(),
           pozos: pozos , //si es individual manda un solo pozo
           maximo: $('#maximo').val(),
           porc_recuperacion : $('#porcentaje_recuperacion').val(),
        }

      }else { //INDIVIDUAL
        var maquinas = [];
        var  pozos = [];

        $('#cuerpo_individual').find(".columna").children().each(function(indexNivel){
          var nivel = {
            id_nivel: $(this).attr('id'),
            nro_nivel : $(this).find(".nro_nivel").val(),
            nombre_nivel: $(this).find('.nombre_nivel').val(),
            porc_oculto : $(this).find(".porc_oculto").val(),
            porc_visible: $(this).find(".porc_visible").val(),
            base: $(this).find(".base").val(),
          }
          niveles.push(nivel);

        });

        $('#cuerpo_individual').find('.listaMaquinas').children().each(function(indexMaquina){
          var maquina;
          var maquina;
          maquina = {
              id_maquina : $(this).val(),
          };
          maquinas.push(maquina);
        })

        var pozo = {
          maquinas: maquinas,
          niveles: niveles,
        } ;

        var formData = {
           id_progresivo : $('#id_progresivo').val(),
           nombre: $('#nombre_progresivo').val() ,
           tipo: $('#selectTipoProgresivos').val(),
           pozos: pozo, //se manda un solo pozo
           maximo: $('#maximo').val(),
           porc_recuperacion : $('#porcentaje_recuperacion').val(),
        }

      }

      var state = $('#btn-guardar').val();
      var type = "POST";
      var url = ((state == "modificar") ? 'progresivos/modificarProgresivo':'progresivos/guardarProgresivo');


      console.log(formData);
      $.ajax({
          type: type,
          url: url,
          data: formData,
          dataType: 'json',
          success: function (data) {

              $('.modal').modal('hide');

              $('#mensajeExito').show();

              var pageNumber = $('#herramientasPaginacion').getCurrentPage();
              var tam = $('#herramientasPaginacion').getPageSize();
              var columna = $('#tablaLayouts .activa').attr('value');
              var orden = $('#tablaLayouts .activa').attr('estado');

              $('#btn-buscar').trigger('click',[pageNumber,tam,columna,orden]);
          },
          error: function (data) {
      //         //console.log('Error:', data);
      //         var response = JSON.parse(data.responseText);
      //
      //         limpiarAlertas();
      //
      //         if(typeof response.nombre_progresivo !== 'undefined'){
      //           $('#nombre_progresivo').addClass('alerta');
      //           $('#alerta-nombre-progresivo').text(response.nombre_progresivo[0]);
      //           $('#alerta-nombre-progresivo').show();
      //         }
      //
      //         var i=0;
      //         $('#columna .NivelProgresivo').each(function(){
      //           var error=' ';
      //           if(typeof response['niveles.'+ i +'.nro_nivel'] !== 'undefined'){
      //             error+=response['niveles.'+ i +'.nro_nivel']+'<br>';
      //             $(this).find('#nro_nivel').addClass('alerta');
      //           }
      //           if(typeof response['niveles.'+ i +'.nombre_nivel'] !== 'undefined'){
      //             error+=response['niveles.'+ i +'.nombre_nivel']+'<br>';
      //             $(this).find('#nombre_nivel').addClass('alerta');
      //           }
      //           if(typeof response['niveles.'+ i +'.porc_oculto'] !== 'undefined'){
      //             error+=response['niveles.'+ i +'.porc_oculto']+'<br>';
      //             $(this).find('#porc_oculto').addClass('alerta');
      //           }
      //           if(typeof response['niveles.'+ i +'.porc_visible'] !== 'undefined'){
      //             error+=response['niveles.'+ i +'.porc_visible']+'<br>';
      //             $(this).find('#porc_visible').addClass('alerta');
      //           }
      //           if(typeof response['niveles.'+ i +'.base'] !== 'undefined'){
      //             error+=response['niveles.'+ i +'.base']+'<br>';
      //             $(this).find('#base').addClass('alerta');
      //           }
      //           if(typeof response['niveles.'+ i +'.maximo'] !== 'undefined'){
      //             error+=response['niveles.'+ i +'.maximo']+'<br>';
      //             $(this).find('#maximo').addClass('alerta');
      //           }
      //           if(error != ' '){
      //           var alerta='<div class="col-xs-12"><span class="alertaTabla alertaSpan">'+error+'</span></div>';
      //             $(this).append(alerta);
      //           }
      //           i++;
      //         })

          }
      });
});

/************************************/
$('.modal').on('hidden.bs.modal', function() {//cuando se cierra el modal
  limpiarCollapseProgresivo(true);
  limpiarProgresivoSeleccionado();
  $('.columna').empty();
  $('.pozo').each(function(index){
      $(this).find('.cAgregarProgresivo').attr('aria-expanded', false);
      $(this).find('.collapse').removeClass('in');
  })

  $('.radioGroup input').prop('checked' , false);

  $('.listaMaquinas').empty();
})

function tipoProgresivo(){
  var bandera = '';
  if($('#cuerpo_individual').is(':visible')) {
    bandera = 'individual';
  }else {
    bandera = 'link';
  }
  return bandera
}

function existeEnDataList( id_maquina, tipo_progresivo){
  var bandera = false;
  switch (tipo_progresivo) {
    case 'link':
      var listas = $('#cuerpo_linkeado .listaMaquinas');
      $('li' , listas).each(function(){
          if(parseInt($(this).val()) == parseInt(id_maquina)){
            bandera=true;
            console.log('existe linkeado');
         }
      })
      break;
    case 'individual':
        var listas = $('#cuerpo_individual .listaMaquinas');
        $('li' , listas).each(function(){
            if(parseInt($(this).val()) == parseInt(id_maquina)){
              bandera=true;
              console.log('existe individual');
           }
        })
      break;

  }
    return bandera;
}

function limpiarCollapseProgresivo(bandera = false){
  //si bandera viene en true mantener input del buscador
  if (bandera != true) {
    console.log('limpia');
    $('#nombre_progresivo').prop("readonly", false).val("");
    $('#nombre_progresivo').setearElementoSeleccionado(0 , "");
    seleccionado_progresivo = 0;
  }
  $('.pozo').remove();
  $('#maximo').val('');
  $('#selectTipoProgresivos').val(0).trigger('change');
  $('#porcentaje_recuperacion').val(""); //Se esconde el botón de agregar
  $('#btn-cancelarProgresivo').hide();
  $('#btn-agregarProgresivo').hide();
  $('#btn-crearProgresivo').hide();
  $('#btn-agregarNivelProgresivo').show();
  $('.columna>.NivelProgresivo').remove();//quita los niveles de progresivo individual y linkeado
}

function limpiarProgresivoSeleccionado(){
  $('#progresivoSeleccionado').text("");
  $('#tipoSeleccionado').text("");
  $('#maximoSeleccionado').text("");
  $('#porc_recuperacionSeleccionado').text("");
  $('#noexiste_progresivo').show();
  limpiarNivelesProgresivos();
  $('#tablaProgresivoSeleccionado').hide();
  $('#tablaNivelesSeleccionados').hide();
}

function limpiarNivelesProgresivos(){
  $('#columna .NivelProgresivo input').each(function(indexMayor){
    if($(this).is('[readonly]')){
      $('#columna').empty();
    }
  });
};

function mostrarBonus(bandera){
  var asdf;
}
