"use strict";
class Meteorologia {
    constructor() {

        this.ciudades = new Array("Oviedo","Gijón","Avilés","Langreo","Blimea");

        this.apikey = "05aa6d06d5d420e33f845d95737e3552";
        this.codigoPais = "ES";
        this.unidades = "&units=metric";
        this.idioma = "&lang=es";
        this.tipo = "&mode=xml";
        this.urlP1 = "https://api.openweathermap.org/data/2.5/weather?q=";
        this.urlP2 = "," + this.codigoPais +this.tipo + this.unidades + this.idioma + "&APPID=" + this.apikey;

    }

    //carga los xmls y los envia para ser formateados
    cargarDatos(){
        //borramos las secciones del main que pudiera haber anteriormente
        $("main section").remove();
        //lanzamos una peticion por cada una de las ciudades
        var i;
        for(i = 0 ; i < this.ciudades.length ; i++){
            $.ajax({
                dataType: "xml",
                url: this.urlP1 + this.ciudades[i]+ this.urlP2,
                method: 'GET',
                success: datos => this.representarCiudad(datos),
                error:function(){
                    $("h3").html("A ocurrido un error al conectarse a <a href='http://openweathermap.org'>OpenWeatherMap</a>"); 
                }
            });
        }
    }
    representarCiudad(datos){
        //escribimos la seccion
        $("main").append("<section></section>");

        var str = "";
        str +="<img src ='https://openweathermap.org/img/w/"+$("weather",datos).attr("icon")+".png' alt='Icono representacion del clima en "+$('city',datos).attr("name")+"'></img>";
        str +="<h2>" +$('city',datos).attr("name")+" ("+$('coord',datos).attr("lat")+","+$('coord',datos).attr("lon")+")</h2>";
        str +="<p>" +$("weather",datos).attr("value")+"</p>";
        str +="<p>Hay una temperatura de " +$('temperature',datos).attr("value") +"ºC con minimos de "+$('temperature',datos).attr("min")   +"ºC y máximas de "+$('temperature',datos).attr("max")+"ºC</p>";
        str +="<p>La sensación térmica es de " + $('temperature',datos).attr("feels_like") +"ºC</p>";
        str +="<p>Ocurrencia de nubes del " + $('clouds',datos).attr("all")+"%</p>";
        str +="<p>La presión es de " + $('pressure',datos).attr("value") +"Pa</p>";
        str +="<p>Humedad del " + $('humidity',datos).attr("value") +"%</p>";
        str +="<p>Visibilidad de " + $('visibility',datos).attr("value") +"m</p>";
        str +="<p>Vientos de " + $('wind speed',datos).attr("value") +"m/s  y direccion de "+$('direction',datos).attr("value")+"º "+$('direction',datos).attr("name")+"</p>"; 
        str +="<p>Salida del sol a las " + new Date($('sun',datos).attr("rise") * 1000).toLocaleTimeString() +"</p>";
        str +="<p>Puesta del sol a las " + new Date($('sun',datos).attr("set") * 1000).toLocaleTimeString() +"</p>";

        //insertamos el codigo dentro de la ultima de las secciones
        $("section:last").html(str);

    }

}
let meteo = new Meteorologia();