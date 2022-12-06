"use strict";
class Meteorologia {
    constructor() {

        this.ciudades = new Array("Oviedo","Gijón","Avilés","Langreo","Blimea");

        this.apikey = "05aa6d06d5d420e33f845d95737e3552";
        this.codigoPais = "ES";
        this.unidades = "&units=metric";
        this.idioma = "&lang=es";
        this.urlP1 = "http://api.openweathermap.org/data/2.5/weather?q=";
        this.urlP2 = "," + this.codigoPais + this.unidades + this.idioma + "&APPID=" + this.apikey;

    }

    //carga los jsons y los envia para ser formateados
    cargarDatos(){
        //lanzamos una peticion por cada una de las ciudades
        var i;
        for(i = 0 ; i < this.ciudades.length ; i++){
            $.ajax({
                dataType: "json",
                url: this.urlP1 + this.ciudades[i]+ this.urlP2,
                method: 'GET',
                success: datos => this.representarCiudad(datos),
                error:function(){
                    $("h3").html("A ocurrido un error al conectarse a <a href='http://openweathermap.org'>OpenWeatherMap</a>"); 
                }
            });
        }
    }
    representarCiudad(json){
        //escribimos la seccion
        $("main").append("<section></section>")

        var str = "";
        str +="<img src ='https://openweathermap.org/img/w/"+json.weather[0].icon+".png' alt='Icono representacion del clima en "+json.name+"'></img>";
        str +="<h2>" +json.name+" ("+json.coord.lat+","+json.coord.lon+")</h2>";
        str +="<p>" +json.weather[0].description+"</p>";
        // str += "<h3>Temperatura</h3>"
        str +="<p>Hay una temperatura de " + json.main.temp +"ºC con minimos de "+json.main.temp_min   +"ºC y máximas de "+json.main.temp_max+"ºC</p>";
        str +="<p>La sensación térmica es de " + json.main.feels_like +"ºC</p>";
        str +="<p>Ocurrencia de nubes del " + json.clouds.all+"%</p>";
        str +="<p>La presión es de " + json.main.pressure +"Pa</p>";
        str +="<p>Humedad del " + json.main.humidity +"%</p>";
        str +="<p>Visibilidad de " + json.visibility +"m</p>";
        str +="<p>Vientos de " + json.wind.speed +"m/s con ráfagas de "+json.wind.gust+"m/s y direccion de "+json.wind.deg+"º</p>"; 
        
        str +="<p>Salida del sol a las " + new Date(json.sys.sunrise * 1000).toLocaleTimeString() +"</p>";
        
        str +="<p>Puesta del sol a las " + new Date( json.sys.sunset* 1000).toLocaleTimeString() +"</p>";
        //insertamos el codigo dentro de la ultima de las secciones
        $("section:last").html(str);

    }

}
let meteo = new Meteorologia();