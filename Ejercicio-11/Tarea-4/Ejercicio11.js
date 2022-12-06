"use strict";
class Geolocalizacion {
    constructor() {    }

    initMap() {
        //preparamos la seccion en la que va a ir el mapa
        $("main").append("<article></article>");

        var acuarioGijon = { lat: 43.542194, lng: -5.676875 };

        //insertamos el mapa
        var map = new google.maps.Map(document.querySelector("main article"), {
            center: acuarioGijon,
            zoom: 13,
        });
        
        var marker = new google.maps.Marker({
            position: acuarioGijon,
            map: map,
            title: "Acuario de Gijón"
        });
    }
}
var geo = new Geolocalizacion();