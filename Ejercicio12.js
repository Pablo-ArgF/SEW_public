"use strict";
class LectorArchivos {
    constructor() {
        //comprobamos que la api funciona correctamente
        if (window.File && window.FileReader && window.FileList && window.Blob) {
            //El navegador soporta el API File
            $("section").append("<p>Este navegador soporta el API File </p>");
        }
        else document.write("<p>¡¡¡ Este navegador NO soporta el API File y este programa puede no funcionar correctamente !!!</p>");
    }

    procesarArchivos(files) {
        //eliminamos las secciones de archivos pasados
        $("main section").remove();
        //representamos todos los archivos seleccionados
        var i;
        for (i = 0; i < files.length; i++) {
            this.representarArchivo(files[i],i);
        }
    }


    representarArchivo(archivo,index = null) {
        //sacamos las propiedades comunes a todos los archivos
        var tamaño = "Tamaño del archivo: " + archivo.size + " bytes";
        var tipo = "Tipo del archivo: " + archivo.type;
        var fechaModif = "Fecha de la última modificación: " + archivo.lastModifiedDate;

        //lo escribimos en parrafos usando jquery 
        //insertamos la section
        $("main").append("<section><h2>" + archivo.name + "</h2></section>");
        $("section:last").append("<p>" + tamaño + "</p>");
        if (archivo.type.length > 0)
            $("section:last").append("<p>" + tipo + "</p>");
        $("section:last").append("<p>" + fechaModif + "</p>");

        //Solamente admite archivos de tipo texto
        var tipoTexto = /text.*/;
        var tipoJSON = new RegExp(".*\.json");
        var tipoKML = new RegExp(".*\.kml");
        if (archivo.type.match(tipoTexto) || archivo.name.match(tipoJSON) || archivo.name.match(tipoKML)) {
            var lector = new FileReader();
            //generamos la seccion para el contenido
            $("section:last").append("<section><h3>Contenido del documento</h3> <label for='contenido"+index+"'>Contenido del documento<textarea readonly id='contenido"+index+"'></textarea></label></section>");
            lector.readAsText(archivo);
            lector.onload =function (evento,indice = index) {
                //insertamos el textarea de la seccion de la que el documento era para insertarle el valor
                var selector ="main > section:eq("+indice+") textarea"; 
                $(selector).text(lector.result);
            }
            
        }
    };



}
let lectorArchivos = new LectorArchivos();