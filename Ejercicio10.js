"use strict";
class PrecioDeLaLuz {
    constructor() {
        this.zone = "?zone=PCB"
        this.url1 = "https://apidatos.ree.es/es/datos/mercados/precios-mercados-tiempo-real?start_date=";
        this.url2 = "&time_trunc=hour&geo_limit=peninsular&geo_ids=8741"

        //configuramos los arrays para que puedan devolvernos el max y el minimo
        Array.prototype.max = function() {
            return Math.max.apply(null, this);
          };
          
        Array.prototype.min = function() {
            return Math.min.apply(null, this);
          };
    }

    //carga el json con la informacion dandole la query que va a hacer al servicio
    cargarDatos() {
        //borramos las secciones del main que pudiera haber anteriormente
        $("main section").remove();
        //actualizamos los datos de fechas dependiendo de la hora actual
        this.inicio = new Date().setDate(new Date().getDate() - 1); //un día hacia atras
        this.inicio = new Date(this.inicio).setMinutes(0); //lo ponemos al inicio de la hora
        this.inicio = new Date(this.inicio).setSeconds(0); //lo ponemos al inicio de segundos
        this.final = new Date().setTime(new Date().getTime() - 1);
        this.final = new Date(this.final).setMinutes(0); //lo ponemos al inicio de la hora
        this.final = new Date(this.final).setSeconds(0); //lo ponemos al inicio de segundos

        //lanzamos la peticion que nos mandan
        $.ajax({
            dataType: "json",
            url: this.url1 + new Date(this.inicio).toISOString() + "&end_date=" + new Date(this.final).toISOString() + this.url2,
            method: 'GET',
            success: datos => this.representarDatos(datos),
            error: function () {
                $("section").append("<p>A ocurrido un error al recopilar los datos. Intentelo de nuevo más tarde.</p>");
            }
        });
    }
    representarDatos(datos) {
        //creamos la seccion que va contener la información
        $("main").append("<section></section>");
        //nos quedamos con la informacion que nos importa
        var valoresHoras = datos.included[0].attributes.values;
        var valores =  valoresHoras.map( x => parseFloat(x.value));

        //obtenemos el mínimo, máximo y medio de los valores para
        var max = valores.max();
        var min = valores.min();
        var avg = valores.reduce((a, b) => a + b, 0) / valores.length;

        //Introducimos un titulo para la seccion que contenga la informacion de las fechas
        var str = "<h2>Precios de la electricidad ("+new Date(this.inicio).toLocaleDateString() +" - "+new Date(this.final).toLocaleDateString()+")</h2>";
        //escribimos la informacion del Minimo, máximo y media
        str+= "<p>Precio mínimo :"+min+" €/MWh</p>"
        str+= "<p>Precio máximo :"+max+" €/MWh</p>"
        str+= "<p>Precio medio :"+avg+" €/MWh</p>"
        // insertamos la seccion que va a contener los precios por hora
        str += "<section> <h3>Precios por horas</h3>";
        valoresHoras.forEach(valor => {
            str += "<p>" + new Date(valor.datetime).getHours() + ":00 -> "+ valor.value+"€/MWh</p>";
        });
        str+="</section>";

        //insertamos en la seccion creada los datos de la luz
        $("section:last").html(str);
    }

}
let precioLuz = new PrecioDeLaLuz();