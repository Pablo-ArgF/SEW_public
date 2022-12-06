"use strict";
class Geolocalizacion {
    constructor() {
        if (navigator.geolocation) //si el navegador soporta geolocalizacion
            navigator.geolocation.getCurrentPosition(this.getPosicion.bind(this),this.showErrores.bind(this));
        else {
            this.printError("Tu navegador no soporta geolocalización, por favor, actualicelo.");
        }

    }
    getPosicion(posicion) {
        this.longitud = posicion.coords.longitude;
        this.latitud = posicion.coords.latitude;
        this.precision = posicion.coords.accuracy;
        this.altitud = posicion.coords.altitude;
        this.precisionAltitud = posicion.coords.altitudeAccuracy;
        this.rumbo = posicion.coords.heading;
        this.velocidad = posicion.coords.speed;
    }
    printError(errorStr){
        //borramos la ultima seccion en caso de que el usuario presione mas de una vez en el boton
        $("main section").remove();
        //insertamos una seccion y metemos los datos dentro de ella
        $("main").append("<section></section>");
        $("section:last").html("<p>ERROR -> "+errorStr+"</p>");
        //eliminamos tambien el boton
        $("input[type='button']").remove();
    }
    showErrores(error){
        switch (error.code) {
            case error.PERMISSION_DENIED:
                this.printError('Permiso denegado por el usuario'); 
                break;
            case error.POSITION_UNAVAILABLE:
                this.printError('Posición no disponible');
                break; 
            case error.TIMEOUT:
                this.printError('Tiempo de espera agotado');
                break;
            default:
                this.printError('Error de Geolocalización desconocido :' + error.code);
        }
    }
    
    representarDatos() {
        //borramos la ultima seccion en caso de que el usuario presione mas de una vez en el boton
        $("main section").remove();
        //insertamos una seccion y metemos los datos dentro de ella
        $("main").append("<section></section>");
        var datos = '';
        datos += this.representarDato('Longitud: ', this.longitud, ' grados');
        datos += this.representarDato('Latitud: ', this.latitud, ' grados');
        datos += this.representarDato('Precisión de la latitud y longitud: ', this.precision, ' metros');
        datos += this.representarDato('Altitud: ', this.altitude, ' metros');
        datos += this.representarDato('Precisión de la altitud: ', this.precisionAltitud, ' metros');
        datos += this.representarDato('Rumbo: ', this.rumbo, ' grados');
        datos += this.representarDato('Velocidad: ', this.velocidad, ' metros/segundo');

        //TODO comprobar que los parametros funcionan en otro lado que no sea el ordenador

        $("section:last").html(datos);

        //insertamos la imagen de google maps
        this.insertarImagenGoogleMaps();
    }

    insertarImagenGoogleMaps(){
        var apiKey = "&key=AIzaSyCcNnYs4xiRLh_1TH71HU_oDm8hGXS2wXs";
        var url = "https://maps.googleapis.com/maps/api/staticmap?";

        //Parámetros
        var centro = "center=" + this.latitud + "," + this.longitud;
        var zoom ="&zoom=15";
        var tamaño= "&size=500x450";
        var marcador = "&markers=color:red%7Clabel:S%7C" + this.latitud + "," + this.longitud;
        var sensor = "&sensor=false"; 
        var maptype = "&maptype=hybrid"
        
        this.imagenMapa = url + centro + zoom + tamaño + marcador + sensor + apiKey + maptype;
        $("section:last").append("<img src='"+this.imagenMapa+"' alt='mapa estático google' />");
    }

    representarDato(inicio, dato, unidades) {
        if (dato == null) //si no disponemos del dato
            return "<p>" + inicio + " El dato no ha podido ser recogido</p>";
        return "<p>" + inicio + dato + unidades + "</p>";
    }
}
let geo = new Geolocalizacion();