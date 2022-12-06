"use strict";
class Geolocalizacion {
    constructor() {
        navigator.geolocation.getCurrentPosition(this.getPosicion.bind(this));
    }
    getPosicion(posicion){
        this.longitud         = posicion.coords.longitude; 
        this.latitud          = posicion.coords.latitude;  
        this.precision        = posicion.coords.accuracy;
        this.altitud          = posicion.coords.altitude;
        this.precisionAltitud = posicion.coords.altitudeAccuracy;
        this.rumbo            = posicion.coords.heading;
        this.velocidad        = posicion.coords.speed;       
    }
    representarDatos() {
        //borramos la ultima seccion en caso de que el usuario presione mas de una vez en el boton
        $("main section").remove();
        //insertamos una seccion y metemos los datos dentro de ella
        $("main").append("<section><h2>Datos recopilados de tu ubicación</h2></section>")
        var datos=''; 
        datos+= this.representarDato('Longitud: ',this.longitud ,' grados'); 
        datos+= this.representarDato('Latitud: ',this.latitud ,' grados');
        datos+= this.representarDato('Precisión de la latitud y longitud: ', this.precision ,' metros');
        datos+= this.representarDato('Altitud: ', this.altitude ,' metros');
        datos+= this.representarDato('Precisión de la altitud: ', this.precisionAltitud ,' metros'); 
        datos+= this.representarDato('Rumbo: ', this.rumbo ,' grados'); 
        datos+= this.representarDato('Velocidad: ', this.velocidad ,' metros/segundo');

        //TODO comprobar que los parametros funcionan en otro lado que no sea el ordenador

        $("section:last").append(datos);
    }

    representarDato(inicio,dato,unidades)
    {
        if(dato == null) //si no disponemos del dato
            return "<p>"+inicio+" El dato no ha podido ser recogido</p>";
        return "<p>"+inicio+dato+unidades+ "</p>";
    }
}
let geo = new Geolocalizacion();