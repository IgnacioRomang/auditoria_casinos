var pag_denuncia_envio = undefined;
var paginas = undefined;

$(document).ready(function () {
  //INICIO
  pag_denuncia_envio = new Map();
  paginas = new Map();

  $("#btn-buscar-pagina").trigger("click");
  $("#btn-buscar-denuncia").trigger("click");
});

//  funciones
const convertir_fecha = function (fecha) {
  [date, hour] = fecha.split(" ");
  [yyyy, mm, dd] = date.split("-");
  return yyyy + "/" + mm + "/" + dd ;
};

// SI SON DOS METODOS IGUALES PERO COMO generarIndices NO SE DONDE SE ENCUENTRA
// COMO PARA ADAPATARLO A UN METODO GENERALIZADO QUE RECIBA A QUE TABLA Y BOTON Y HERRAMINETA DE PAG ESPESIFICOS
// ME VEO OBLIGADO A HACER DOS METODOS
function clickIndicePagina(e, pageNumber, tam) {
  if (e != null) {
    e.preventDefault();
  }
  var tam = tam != null ? tam : $("#herramientasPaginacion-paginas").getPageSize();
  var columna = $("#table-paginas .activa").attr("value");
  var orden = $("#table-paginas .activa").attr("estado");
  $("#btn-buscar-pagina").trigger("click", [pageNumber, tam, columna, orden]);
}

function clickIndiceDenuncia(e, pageNumber, tam, tabla) {
  if (e != null) {
    e.preventDefault();
  }
  var tam = tam != null ? tam : $("#herramientasPaginacion-denuncia").getPageSize();
  var columna = $("#table-denuncia .activa").attr("value");
  var orden = $("#table-denuncia .activa").attr("estado");
  $("#btn-buscar-denuncia").trigger("click", [pageNumber, tam, columna, orden]);
}

function generarFilaTablaPaginas(datos, tablabody) {
  // agrega los datos en una fila, sirve para las tablas pequeñas y grandes
  let fila = $(tablabody + " .filaTabla")
    .clone()
    .removeClass("filaTabla")
    .show();
  fila.attr("data-id", datos.id_pagina);
  fila
    .find(".paginas-usuario")
    .text(datos.usuario)
    .attr("title", datos.usuario);
  fila.find(".paginas-pagina").text(datos.pagina).attr("title", datos.pagina);
  fila
    .find(".paginas-estado")
    .text(datos.descripcion)
    .attr("title", datos.descripcion);
  fila.find(".paginas-marcado .form-check-input").prop("checked", datos.check)
  fila.find(".paginas-marcado .form-check-input").on("change", function () {
    let isChecked = $(this).prop("checked");
    let pagina = paginas.get(datos.id_pagina);
    if (pagina) {
      pagina.check = isChecked;
    } else {
      pagina = pag_denuncia_envio.get(datos.id_pagina);
      pagina.check = isChecked;
    }
  });
  fila
    .find(".paginas-creado")
    .text(convertir_fecha(datos.created_at))
    .attr("title", convertir_fecha(datos.created_at));
  fila.css("display", "flow-root");
  return fila;
}

function generarFilaTablaDenuncias(datos, tablabody) {
  // agrega los datos en una fila, sirve para las tablas pequeñas y grandes
  let fila = $(tablabody + " .filaTabla")
    .clone()
    .removeClass("filaTabla")
    .show();
  fila.attr("data-id", datos.id_denuncia);

  fila
    .find(".denuncia-id")
    .text(datos.id_denuncia)
    .attr("title", datos.id_denuncia);
  fila.find(".denuncia-pagina").text(datos.paginas_count).attr("title", datos.paginas_count);
  fila
    .find(".denuncia-estado")
    .text(datos.estado_descripcion)
    .attr("title", datos.estado_descripcion);
  fila
    .find(".denuncia-creado")
    .text(convertir_fecha(datos.created_at))
    .attr("title", convertir_fecha(datos.created_at));
  fila.css("display", "flow-root");
  return fila;
}

function verificar_url(url) {
  const regex =
    /^(https?:\/\/)?(www\.)?(facebook\.com|fb\.com|instagram\.com)\/[a-zA-Z0-9(\.\?)?]/;
  return regex.test(url);
}

function consultar_paginas(formData) {
  $.ajaxSetup({
    headers: {
      "X-CSRF-TOKEN": $('meta[name="_token"]').attr("content"),
    },
  });
  return $.ajax({
    type: "GET",
    url: "/paginas/list",
    data: formData,
    dataType: "json",
  });
}

function consultar_denuncias(formData) {
  $.ajaxSetup({
    headers: {
      "X-CSRF-TOKEN": $('meta[name="_token"]').attr("content"),
    },
  });
  return $.ajax({
    type: "GET",
    url: "/denuncias/list",
    data: formData,
    dataType: "json",
  });
}

//
$("#btn-minimizar").click(function () {
  if ($(this).data("minimizar") == true) {
    $(".modal-backdrop").css("opacity", "0.1");
    $(this).data("minimizar", false);
  } else {
    $(".modal-backdrop").css("opacity", "0.5");
    $(this).data("minimizar", true);
  }
});

$("#btn-agregar-pagina").click(function (e) {
  e.preventDefault();
  $("#mdl-agregar-pag").modal("show");
});

$("#btn-agregar-denuncia").click(function (e) {
  e.preventDefault();
  $("#mdl-agregar-den").modal("show");
  $("#body-paginas-no-agregadas tr").not(".filaTabla").remove();
  paginas.forEach((value, key) => {
    if(value.id_estado <= 1){
      $("#table-paginas-no-agregadas").append(
        generarFilaTablaPaginas(value, "#body-paginas-no-agregadas")
      );
    }
  });
});

$("#btn-buscar-pagina").click(function (e, pagina, page_size, columna, orden) {
  e.preventDefault();
  const deflt_size = isNaN($("#herramientasPaginacion-paginas").getPageSize())
  ? 10
    : $("#herramientasPaginacion-paginas").getPageSize();
  const sort_by =
    columna != null
      ? { columna: columna, orden: orden }
      : {
          columna: $("#table-paginas .activa").attr("value"),
          orden: $("#table-paginas .activa").attr("estado"),
        };

  const formData = {
    usuario: $("#filtro-usuario").val(),
    page_url: $("#filtro-url").val(),
    //fecha_cierre_definitivo_h: isoDate($("#dtpFechaCierreDefinitivoH")),
    page:
      pagina != null ? pagina : $("#herramientasPaginacion-paginas").getCurrentPage(),
    sort_by: sort_by,
    page_size: page_size == null || isNaN(page_size) ? deflt_size : page_size,
  };

  let respuesta = consultar_paginas(formData);
  respuesta
    .success(function (resultados) {
      let paginas_list = resultados.paginas;
      $("#herramientasPaginacion-paginas").generarTitulo(
        formData.page,
        formData.page_size,
        paginas_list.total,
        clickIndicePagina
      );
      $("#herramientasPaginacion-paginas").generarIndices(
        formData.page,
        formData.page_size,
        paginas_list.total,
        clickIndicePagina
      );

      $("#body-tabla-paginas tr").not(".filaTabla").remove();
      for (var i = 0; i < paginas_list.data.length; i++) {
        paginas.set(paginas_list.data[i].id_pagina, {
          ...paginas_list.data[i],
          check: false,
        });
        $("#table-paginas tbody").append(
          generarFilaTablaPaginas(paginas_list.data[i], "#body-tabla-paginas")
        );
      }
    })
    .error(function (data) {
      console.error("Error:", data);
    });
});

$("#btn-buscar-denuncia").click(function (e, pagina, page_size, columna, orden) {
  e.preventDefault();
  const deflt_size = isNaN($("#herramientasPaginacion-denuncias").getPageSize())
  ? 10
    : $("#herramientasPaginacion-denuncias").getPageSize();
  const sort_by =
    columna != null
      ? { columna: columna, orden: orden }
      : {
          columna: $("#table-denuncias .activa").attr("value"),
          orden: $("#table-denuncias .activa").attr("estado"),
        };

  const formData = {
    //usuario: $("#filtro-usuario").val(),
    //page_url: $("#filtro-url").val(),
    //fecha_cierre_definitivo_h: isoDate($("#dtpFechaCierreDefinitivoH")),
    page:
      pagina != null ? pagina : $("#herramientasPaginacion-denuncias").getCurrentPage(),
    sort_by: sort_by,
    page_size: page_size == null || isNaN(page_size) ? deflt_size : page_size,
  };

  let respuesta = consultar_denuncias(formData);
  respuesta
    .success(function (resultados) {
      let denuncias_list = resultados.denuncias;
      $("#herramientasPaginacion-denuncias").generarTitulo(
        formData.page,
        formData.page_size,
        denuncias_list.total,
        clickIndicePagina
      );
      $("#herramientasPaginacion-denuncias").generarIndices(
        formData.page,
        formData.page_size,
        denuncias_list.total,
        clickIndicePagina
      );

      $("#body-tabla-denuncias tr").not(".filaTabla").remove();
      for (var i = 0; i < denuncias_list.data.length; i++) {
        $("#table-denuncias tbody").append(
          generarFilaTablaDenuncias(denuncias_list.data[i], "#body-tabla-denuncias")
        );
      }
    })
    .error(function (data) {
      console.error("Error:", data);
    });
});

$("#ipt-url").on("input", function () {
  let url = $("#ipt-url").val();
  if (verificar_url(url)) {
    if (url.length > 0) {
      let formData = new FormData();
      formData.append("pag_url", url);
      $.ajaxSetup({
        headers: {
          "X-CSRF-TOKEN": $('meta[name="_token"]').attr("content"),
        },
      });
      $.ajax({
        type: "POST",
        url: "/paginas/verificar",
        data: formData,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function (resultados) {
          console.log("Exito:", resultados);
          let pags = resultados.paginas
          if(pags.length>0){
            $("#mensajeError h3").text("ERROR");
            $("#mensajeError p").text("La pagina ya existe, verifique si fue denunciada o no");
            //$("#mdl-agregar-pag").modal("hide");
            $("#mensajeError").show();
          }
        },
        error: function (data) {
          console.error("Error:", data);
        },
      });
      $("#ifm").attr("src", "https://api.thumbalizr.com/api/v1/embed/EMBED_API_KEY/TOKEN/?url="+url);
      $("#div-prev").removeClass("hide");
    } else {
      $("#div-prev").addClass("hide");
    }
  } else {
    $("#div-prev").addClass("hide");
  }
});

$("#btn-agregar-pagina-denuncia").click(function (e) {
  e.preventDefault();
  $("#body-paginas-no-agregadas tr").not(".filaTabla").remove();
  //$("#body-paginas-agregadas tr").not(".filaTabla").remove();
  paginas.forEach((value, key) => {
    if (value.check) {
      value.check = false;
      pag_denuncia_envio.set(key, value);
      paginas.delete(key);
      $("#table-paginas-agregadas").append(
        generarFilaTablaPaginas(value, "#body-paginas-agregadas")
      );
    } else {
      $("#table-paginas-no-agregadas").append(
        generarFilaTablaPaginas(value, "#body-paginas-no-agregadas")
      );
    }
  });
});

$("#btn-quitar-pagina-denuncia").click(function (e) {
  e.preventDefault();
  //$("#body-paginas-no-agregadas tr").not(".filaTabla").remove();
  $("#body-paginas-agregadas tr").not(".filaTabla").remove();
  pag_denuncia_envio.forEach((value, key) => {
    if (value.check) {
      value.check = false;
      paginas.set(key, value);
      pag_denuncia_envio.delete(key);
      $("#table-paginas-no-agregadas").append(
        generarFilaTablaPaginas(value, "#body-paginas-no-agregadas")
      );
    } else {
      $("#table-paginas-agregadas").append(
        generarFilaTablaPaginas(value, "#body-paginas-agregadas")
      );
    }
  });
});

$("#btn-guardar-page").on("click", function () {
  let formData = new FormData();
  let usuario = $("#ipt-usuario").val();
  let url_pagina = $("#ipt-url").val();
  if (usuario.length > 0) {
    formData.append("usuario", usuario);
  }
  if (url_pagina.length > 0) {
    formData.append("pag_url", url_pagina);
  }

  $.ajaxSetup({
    headers: {
      "X-CSRF-TOKEN": $('meta[name="_token"]').attr("content"),
    },
  });
  $.ajax({
    type: "POST",
    url: "/paginas/agregar",
    data: formData,
    contentType: false,
    processData: false,
    dataType: "json",
    success: function (resultados) {
      //console.log("Exito:", resultados);
      $("#mensajeExito h3").text("ÉXITO");
      $("#mensajeExito p").text("La Pagina se cargo correctamente");
      $("#mdl-agregar-pag").modal("hide");
      $("#btn-buscar-pagina").trigger("click");
      $("#mensajeExito").show();
    },
    error: function (data) {
      console.log("Error:", data);
      mostrarErrorValidacion(
        $("#mdl-agregar-pag"),
        "Verifique que completo todos los campos",
        true
      );
    },
  });
  $("#mdl-agregar-pag").modal("show");
});

$("#btn-guardar-den").click(function (e) {
  e.preventDefault();
  let formData = new FormData();
  let paginas_ids = pag_denuncia_envio.keys();
  for (let id of paginas_ids) {
      formData.append("paginas_id[]", id);
  }
  $.ajaxSetup({
    headers: {
      "X-CSRF-TOKEN": $('meta[name="_token"]').attr("content"),
    },
  });
  $.ajax({
    type: "POST",
    url: "/denuncias/agregar",
    data: formData,
    contentType: false,
    processData: false,
    dataType: "json",
    success: function (resultados) {
      console.log("Exito:", resultados);
      $("#mensajeExito h3").text("ÉXITO");
      $("#mensajeExito p").text("La Denuncia se cargo correctamente");
      $("#mdl-agregar-den").modal("hide");
      //actualizo las tablas de denuncias y de paginas por el cmabio de estado
      $("#btn-buscar-pagina").trigger("click");
      $("#btn-buscar-denuncia").trigger("click");
      $("#mensajeExito").show();
    },
    error: function (data) {
      console.log("Error:", data);
      mostrarErrorValidacion(
        $("#mdl-agregar-den"),
        "Verifique que la denuncia tenga al menos una pagina",
        true
      );
    },
  });
})

