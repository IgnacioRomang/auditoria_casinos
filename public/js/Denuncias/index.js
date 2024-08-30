$(document).ready(function () {
  //INICIO
  $("#btn-buscar").trigger("click");
});

//  funciones
function clickIndice(e, pageNumber, tam) {
  if (e != null) {
    e.preventDefault();
  }
  var tam = tam != null ? tam : $("#herramientasPaginacion").getPageSize();
  var columna = $("#table-denuncias .activa").attr("value");
  var orden = $("#table-denuncias .activa").attr("estado");
  $("#btn-buscar").trigger("click", [pageNumber, tam, columna, orden]);
}

function generarFilaTabla(d) {
  const convertir_fecha = function (fecha) {
    [date, hour] = fecha.split(" ");
    [yyyy, mm, dd] = date.split("-");
    return yyyy + "/" + mm + "/" + dd + " " + hour;
  };

  let fila = $("#cuerpoTabla .filaTabla")
    .clone()
    .removeClass("filaTabla")
    .show();

  fila.attr("data-id", d.id_pagina);
  fila.find(".paginas-usuario").text(d.usuario).attr("title", d.usuario);
  fila.find(".paginas-pagina").text(d.pagina).attr("title", d.pagina);
  fila.find(".paginas-estado").text(d.descripcion).attr("title", d.descripcion);
  fila
    .find(".paginas-creado")
    .text(convertir_fecha(d.created_at))
    .attr("title", convertir_fecha(d.created_at));
  fila.css("display", "flow-root");
  return fila;
}

function generarFilaTablaMini(d) {
  const convertir_fecha = function (fecha) {
    [date, hour] = fecha.split(" ");
    [yyyy, mm, dd] = date.split("-");
    return yyyy + "/" + mm + "/" + dd + " " + hour;
  };

  let fila = $("#body-paginas-no-agregadas .filaTabla")
    .clone()
    .removeClass("filaTabla")
    .show();

  fila.attr("data-id", d.id_pagina);
  fila.find(".paginas-usuario").text(d.usuario).attr("title", d.usuario);
  fila.find(".paginas-pagina").text(d.pagina).attr("title", d.pagina);
  fila.find(".paginas-estado").text(d.descripcion).attr("title", d.descripcion);
  fila
    .find(".paginas-creado")
    .text(convertir_fecha(d.created_at))
    .attr("title", convertir_fecha(d.created_at));
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

$("#btn-buscar").click(function (e, pagina, page_size, columna, orden) {
  e.preventDefault();
  const deflt_size = isNaN($("#herramientasPaginacion").getPageSize())
    ? 10
    : $("#herramientasPaginacion").getPageSize();
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
      pagina != null ? pagina : $("#herramientasPaginacion").getCurrentPage(),
    sort_by: sort_by,
    page_size: page_size == null || isNaN(page_size) ? deflt_size : page_size,
  };

  let respuesta = consultar_paginas(formData);
  respuesta
    .success(function (resultados) {
      console.log(resultados);
      let paginas_list = resultados.paginas;
      $("#herramientasPaginacion").generarTitulo(
        formData.page,
        formData.page_size,
        paginas_list.total,
        clickIndice
      );
      $("#herramientasPaginacion").generarIndices(
        formData.page,
        formData.page_size,
        paginas_list.total,
        clickIndice
      );

      $("#cuerpoTabla tr").not(".filaTabla").remove();
      for (var i = 0; i < paginas_list.data.length; i++) {
        $("#table-paginas tbody").append(
          generarFilaTabla(paginas_list.data[i])
        );
      }
    })
    .error(function (data) {
      console.log("Error:", data);
    });
});

$("#ipt-url").on("change", function () {
  var url = $("#ipt-url").val();
  if (verificar_url(url)) {
    if (url.length > 0) {
      $("#ifm").attr("src", url);
      $("#div-prev").removeClass("hide");
    } else {
      $("#div-prev").addClass("hide");
    }
  } else {
    $("#div-prev").addClass("hide");
  }
});

$("#btn-guardar-page").on("click", function () {
  var formData = new FormData();
  var usuario = $("#ipt-usuario").val();
  var url_pagina = $("#ipt-url").val();
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
      console.log("Exito:", resultados);
      $("#mensajeExito h3").text("ÉXITO");
      $("#mensajeExito p").text("La Pagina se cargo correctamente");
      $("#mdl-agregar-pag").modal("hide");
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

$("#btn-agregar-denuncia").click(function (e) {
  e.preventDefault();
  $("#mdl-agregar-den").modal("show");

  const formData = {
    usuario: $("#filtro-usuario").val(),
    page_url: $("#filtro-url").val(),
    //fecha_cierre_definitivo_h: isoDate($("#dtpFechaCierreDefinitivoH")),
    page: 1,
    page_size: 999999,
  };

  let respuesta = consultar_paginas(formData);
  respuesta
    .success(function (resultados) {
      console.log(resultados);
      let paginas_list = resultados.paginas;

      $("#body-paginas-no-agregadas tr").not(".filaTabla").remove();
      for (var i = 0; i < paginas_list.data.length; i++) {
        $("#table-paginas-no-agregadas").append(
          generarFilaTablaMini(paginas_list.data[i])
        );
      }
    })
    .error(function (data) {
      console.log("Error:", data);
    });
});
