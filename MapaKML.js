"use strict";
class RepresentadorKML {
    constructor() {
        this.markers = new Array();
    }

    printError(errorStr) {
        //borramos la ultima seccion en caso de que el usuario presione mas de una vez en el boton
        $("main section").remove();
        //insertamos una seccion y metemos los datos dentro de ella
        $("main").append("<section></section>");
        $("section:last").html("<p>ERROR -> " + errorStr + "</p>");
    }

    initMap() {
        //preparamos la seccion en la que va a ir el mapa
        $("main").append("<article></article>");

        var oviedo = { lat: 43.3672702, lng: -5.8502461 };

        //insertamos el mapa
        this.map = new google.maps.Map(document.querySelector("main article"), {
            center: oviedo,
            zoom: 2,
        });
    }

    leerArchivoKML(archivo) {
        //eliminamos todos los markers del mapa desvinculandolos del mismo
        for(var i = 0 ; i < this.markers.length;i++){
            this.markers[i].setMap(null);
        }
        //vamos a cargar el contenido del archivo y procesarlo con jquery
        var lector = new FileReader();
        //generamos la seccion para el contenido
        lector.readAsText(archivo);
        lector.onload = function (evento) {
            var points = new Array();
            $("Placemark", lector.result).each(function () {
                var placemark = $(this);

                //vamos a obtener la informacion de la placemark
                var nombre = $("name", placemark).text();
                var descripcion = $("description", placemark).text();
                var coords = $("point coordinates", placemark).text().split(",");
                var lat = parseFloat(coords[0]);
                var long = parseFloat(coords[1]);

                points.push({nombre : nombre , lat : lat, long: long})
            });

            var i;
            for(i = 0 ;  i< points.length ; i++){
                this.generarMarker(points[i]);
            }
        }.bind(this);
    };
    generarMarker(point) {
        console.log(point.lat +","+point.long)
        //creamos el marker en el mapa
        var marker = new google.maps.Marker({
            position: { lat: point.lat, lng: point.long },
            map: this.map,
            title: point.nombre
        });
        //guardamos los marcadores creados
        this.markers.push(marker);
    }
}
var representadorKML = new RepresentadorKML();