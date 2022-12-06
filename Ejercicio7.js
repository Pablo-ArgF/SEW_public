"use strict";
class Ejercicio {
    constructor() {

    }

    /**
     * Metodo que oculta todas las imágenes del html
     */
    ocultarImagenes() {
        $("img").hide();
    }

    mostrarImagenes() {
        $("img").show();
    }

    primerTituloAMayusculas() {
        var nuevoParrafo = $("body section p:first").text().toUpperCase();
        $("body section p:first").text(nuevoParrafo);
    }

    añadirElementoALista() {
        $("body ul").append("<li>Nuevo elemento a la lista</li>");
    }

    eliminarUltimoElementoDeLista() {
        $("body ul li:last-of-type").remove();
    }

    imprimirArbolDOM() {
        var stringDOM = "";

        $("*", document.body).each(function () {
            var etiquetaPadre = $(this).parent().get(0).tagName;
            var etiquetaActual = $(this).get(0).tagName;
            stringDOM += "ETIQUETA PADRE -> " + etiquetaPadre + " TIPO -> " + etiquetaActual + "\n";
        }) 


        $("textarea").text(stringDOM);
    }

    sumarTabla() {

        //iteramos por las columnas ultimas de las columnas del body 
        //sumando los valores de las dos columnas del medio
        $("tbody tr", document.body).each(function () {
            var celdas = $("td", this);
            var valorCelda1 = celdas[0].innerHTML;
            var valorCelda2 = celdas[1].innerHTML;
            celdas[2].innerHTML = new Number(valorCelda1) * new Number(valorCelda2);
        })
        var suma1 = 0;
        var suma2 = 0;
        var suma3 = 0;

        $("tbody tr:not(:last)").each(function(){
            var celdas = $("td", this);
            suma1 += new Number(celdas[0].innerHTML);
            suma2 += new Number(celdas[1].innerHTML);
            suma3 += new Number(celdas[2].innerHTML);
        });
        //recorremos el ultimo tr y ponemos los valores

        $("tbody tr:last").each(function(){
            var celdas = $("td", this);
            celdas[0].innerHTML = suma1;
            celdas[1].innerHTML = suma2;
            celdas[2].innerHTML = suma3;
        });

    }
}
let ejercicio = new Ejercicio();