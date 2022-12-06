"use strict";
class Geolocalizacion {
    constructor() {
        if (navigator.geolocation) //si el navegador soporta geolocalizacion
            navigator.geolocation.getCurrentPosition(this.getPosicion.bind(this), this.showErrores.bind(this));
        else {
            this.printError("Tu navegador no soporta geolocalización, por favor, actualicelo.");
        }

    }
    getPosicion(posicion) {
        this.longitud = posicion.coords.longitude;
        this.latitud = posicion.coords.latitude;

        //ahora que tenemos los datos, los cargamos al mapa
        var ubic = { lat: this.latitud, lng: this.longitud};
        this.cambiarALocalizacion(ubic);
    }
    printError(errorStr) {
        //borramos la ultima seccion en caso de que el usuario presione mas de una vez en el boton
        $("main section").remove();
        //insertamos una seccion y metemos los datos dentro de ella
        $("main").append("<section></section>");
        $("section:last").html("<p>ERROR -> " + errorStr + "</p>");
    }
    showErrores(error) {
        switch (error.code) {
            case error.PERMISSION_DENIED:
                this.printError('Permiso para usar la ubicación denegado por el usuario');
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

    initMap() {
        //preparamos la seccion en la que va a ir el mapa
        $("main").append("<article></article>");

        var oviedo = {lat: 43.3672702, lng: -5.8502461};

        //insertamos el mapa
        this.map = new google.maps.Map(document.querySelector("main article"), {
            center: oviedo,
            zoom: 13,
        });
        
        
    }

    cambiarALocalizacion(ubicacion){
        this.map.setCenter(ubicacion);
        var marker = new google.maps.Marker({
            position: ubicacion,
            map: this.map,
            title: "Usted se encuentra aquí"
        });
    }
}
var geo = new Geolocalizacion();