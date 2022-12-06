"use strict";
class Geolocalizacion {
    constructor() {
        if (navigator.geolocation) //si el navegador soporta geolocalizacion
            navigator.geolocation.getCurrentPosition(this.getPosicion.bind(this), this.showErrores.bind(this));
        else {
            this.printError("Tu navegador no soporta geolocalización, por favor, actualicelo.");
        }

        this.currentOpcion = 0;

    }
    getPosicion(posicion) {
        this.longitud = posicion.coords.longitude;
        this.latitud = posicion.coords.latitude;

        //ahora que tenemos los datos, los cargamos al mapa
        var ubic = { lat: this.latitud, lng: this.longitud };
        this.ubicacionDesde = ubic;
        this.cambiarALocalizacion(ubic);
    }
    printError(errorStr) {
        //borramos el contenedor del mapa
        $("main article").remove();
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

        var oviedo = { lat: 43.3672702, lng: -5.8502461 };

        //insertamos el mapa
        this.map = new google.maps.Map(document.querySelector("main article"), {
            center: oviedo,
            zoom: 13,
        });


    }

    cambiarALocalizacion(ubicacion) {
        this.map.setCenter(ubicacion);
        var marker = new google.maps.Marker({
            position: ubicacion,
            map: this.map,
            title: "Usted se encuentra aquí"
        });

        //buscamos las pizzerías cercanas al usuario
        var pizzerias = this.buscarPizzeriasCercaDe(ubicacion);
    }

    buscarPizzeriasCercaDe(ubic) {
        var request = {
            location: ubic,
            radius: 5500,
            keyword: 'farmacia'
            // rankby: 'distance'
        };

        //creamos un placeservice y le pasamos la peticion de la pizzeria
        var service = new google.maps.places.PlacesService(this.map);

        service.textSearch({query: 'pizza'}, (results, status) => {
            if (status === google.maps.places.PlacesServiceStatus.OK) {
                //en ese caso representamos la ruta del primero de los establecimientos
                this.localizaciones = results.map(x => x.geometry.location);

                this.resultados = results;
                
                //llamamos al representarRuta desde aqui
                this.representarRuta(ubic,this.localizaciones[0],  results[0].name );

                //insertamos la posibilidad para moverse entre las opciones
                $("header").append("<label for='siguienteLocalizacion'>Mostrar la siguiente localización<input id='siguienteLocalizacion' type='button' value='Siguiente opción' onclick='geo.siguienteOpcion();'></label>");
            }
            else{
                this.printError("No han podido encontrarse pizzerías cercanas");
                return null;
            }
        });


    }

    representarRuta(deste, hasta , titulo = null){
        var icono = "multimedia/destino.png";
        var objConfigDR = {
            map: this.map,
            suppressMarkers: true
        };

        var objConfigDS = {
            origin: deste,
            destination: hasta,
            travelMode: google.maps.TravelMode.DRIVING
        };

        if(this.dr != null){
            //si ya tenemos una ruta vamos a desvincular
            this.dr.setMap(null);
            
            //borramos el marker del mapa
            this.marker.setMap(null);
        }
        this.ds = new google.maps.DirectionsService();
        this.dr = new google.maps.DirectionsRenderer(objConfigDR);

        this.ds.route(objConfigDS, (resultados, estado) => {
            if (estado == "OK") {
                this.dr.setDirections(resultados);
            } else {
                //ha ocurrido un error
                this.printError("La ruta a la pizzería no ha podido sido computada");
            }
        });

        //ponemos la marca en la destination con la imagen que queremos
        this.marker = new google.maps.Marker({
            map: this.map,
            position: hasta,
            title: titulo
          });
        this.marker.setIcon(icono)
    }

    siguienteOpcion(){
        this.currentOpcion++;
        if(this.currentOpcion >= 20 )
            this.currentOpcion = 0; //evitamos que se salga de las 20 posibilidades
        this.representarRuta(this.ubicacionDesde,
             this.localizaciones[this.currentOpcion],
              this.resultados[this.currentOpcion].name);
        
    }
}
var geo = new Geolocalizacion();