$(document).ready(function(){
  $('#barraInformes').attr('aria-expanded','true');
  $('#informes').removeClass();
  $('#informes').addClass('subMenu1 collapse in');
  $('.tituloSeccionPantalla').text('Informes de tragamonedas');
  $('#opcInformesMTM').attr('style','border-left: 6px solid #185891; background-color: #131836;');
  $('#opcInformesMTM').addClass('opcionesSeleccionado');
});

//MUESTRA LA PLANILLA
$(document).on('click','.planilla',function(){
    $('#alertaArchivo').hide();
    const anio   = $(this).attr('data-anio');
    const mes    = $(this).attr('data-mes');
    const casino = $(this).attr('data-casino');
    const moneda = $(this).attr('data-moneda');
    let url = 'informesMTM/';
    const maq1 = $('#maquinasMenor').val();
    const maq2 = $('#maquinasMayor').val();
    if(maq1.length > 0 || maq2.length > 0){
      const m1 = maq1.length > 0? maq1 : -1;
      const m2 = maq2.length > 0? maq2 : -1;
      url += `generarPlanillaMaquinas/${anio}/${mes}/${casino}/${moneda}/${m1}/${m2}`;
    }
    else{
      url += `generarPlanilla/${anio}/${mes}/${casino}/${moneda}`;
    }
    window.open(url,'_blank');
});
