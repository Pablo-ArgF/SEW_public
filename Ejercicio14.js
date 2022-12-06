"use strict";
class Grupo {
    constructor() {
        //cargamos la informacion del formulario en este objeto
        this.nombre = $("input[type='text']:eq(4)").val();
        this.descr = $("textarea:eq(0)").val();

        //vaciamos los campos despues de ser obtenidos los datos
        $("input[type='text']:eq(4)").val('');
        $("textarea:eq(0)").val('');
    }
    getRepresentacion() {
        return { nombre: this.nombre, descripcion: this.descr };
    }
}

"use strict";
class Usuario {
    constructor() {
        //cargamos la informacion del formulario en este objeto
        this.dni = $("input[type='text']:eq(0)").val();
        this.nombre = $("input[type='text']:eq(1)").val();
        this.telefono = $("input[type='text']:eq(2)").val();
        this.correo = $("input[type='text']:eq(3)").val();

        //vaciamos los campos despues de ser obtenidos los datos
        $("input[type='text']:eq(0)").val('');
        $("input[type='text']:eq(1)").val('');
        $("input[type='text']:eq(2)").val('');
        $("input[type='text']:eq(3)").val('');
    }
    getRepresentacion() {
        return {
            dni: this.dni,
            nombreU: this.nombre,
            telefono: this.telefono,
            correo: this.correo,
            grupo: null
        };
    }
}


"use strict";
class GestorGrupos {
    constructor() {

        //manejamos los grupos y usuarios cargados localmente con dos listas
        this.listaGrupos = new Array();
        this.listaUsuarios = new Array();


        //abrimos nuestra base de datos 
        var DBOpenRequest = window.indexedDB.open('gestor', 10);
        // Register two event handlers to act on the database being opened successfully, or not
        DBOpenRequest.onerror = (event) => {
            this.showError("error al cargar la base de datos");
        };
        DBOpenRequest.onsuccess = (event) => {

            // Guardamos la referencia a la base de datos correctamente creada en el objeto gestorBiblioteca
            this.db = event.target.result;

            //dentro de la carga se encarga de repintarlos
            //cargamos los usuarios que haya en la bd
            this.cargarUsuariosBD();
            //cargamos los datos que tenemos en la db
            this.cargarGruposBD();



        };
        //en caso de que no haya bd o si la versión ha sido modificada
        DBOpenRequest.onupgradeneeded = (event) => {
            this.db = event.target.result;

            this.db.onerror = (event) => {
                this.showError("error al cargar la base de datos. Intentelo de nuevo más tarde");
            };

            // creamos el objectstore para los grupos------------------------------
            const objectStore = this.db.createObjectStore('grupos', { keyPath: 'nombre' });
            // definimos los data items que la bd va  a tener
            objectStore.createIndex('descripcion', 'descripcion', { unique: false });


            // creamos el objectstore para los usuarios------------------------------
            const objectStoreUsuarios = this.db.createObjectStore('usuarios', { keyPath: 'dni' });
            objectStoreUsuarios.createIndex('nombreU', 'nombreU', { unique: false });
            objectStoreUsuarios.createIndex('telefono', 'telefono', { unique: false });
            objectStoreUsuarios.createIndex('correo', 'correo', { unique: false });
            objectStoreUsuarios.createIndex('grupo', 'grupo', { unique: false });
        };
    }

    showError(error) {
        var textarea = $("main > section > section:eq(2) > textarea");
        textarea.append(error + "\n");
    }

    insertarGrupo() {
        //delegamos en el objeto grupo para que valide todo y se genere
        //validamos los campos del grupo para checkear que todos están completos
        if ($("input[type='text']:eq(4)").val().length == 0
            || $("textarea:eq(0)").val().length == 0) {
            //si no está relleno todo paramos y ponemos un mensaje de error al usuario
            this.showError("Rellene todos los campos del grupo antes de registrarlo");
            return; //paramos la ejecución
        }
        var grupo = new Grupo();
        //ahora que tenemos el grupo cargado correctamente vamos a hacerlo persistente
        this.insertarGrupoDatos(grupo.getRepresentacion());

    }
    insertarGrupoDatos(datosGrupo) {
        //iniciamos la operacion de escritura
        var transaction = this.db.transaction(["grupos"], "readwrite");
        //agregamos los datos en el objectstore
        var objStore = transaction.objectStore("grupos");
        var request = objStore.add(datosGrupo);
        //la adición se va a realizar de forma asincrona por lo que 
        //vamos a decirle que hacer cuando acabe de realizarla
        transaction.oncomplete = function (event) {
            //se ha añadido correctamente por lo que vamos a añadirlo a la lista de grupos
            this.listaGrupos.push(datosGrupo);
            //actualizamos la vista para que contenga a todos los grupos de la lista
            this.representarGrupos();
        }.bind(this);

        transaction.onerror = function (event) {
            //en caso de que un grupo ya exista con ese nombre ponemos una alerta en forma de parrafo
            $("main> section:eq(0) > section:eq(1)").append("<p>ERROR: ese grupo ya esta registrado</p>")
        };
    }

    insertarUsuario() {
        //delegamos en el objeto usuario para que valide todo y se genere
        //validamos los campos del usuario para checkear que todos están completos
        if ($("input[type='text']:eq(0)").val().length == 0
            || $("input[type='text']:eq(1)").val().length == 0
            || $("input[type='text']:eq(2)").val().length == 0
            || $("input[type='text']:eq(3)").val().length == 0) {
            //si no está relleno todo paramos y ponemos un mensaje de error al usuario
            this.showError("Rellene todos los campos del usuario antes de registrarlo");
            return; //paramos la ejecución
        }
        var usuario = new Usuario();
        //ahora que tenemos el usuario cargado correctamente vamos a hacerlo persistente
        this.insertarUsuarioDatos(usuario.getRepresentacion());

    }
    insertarUsuarioDatos(datosUsuario) {
        //iniciamos la operacion de escritura
        var transaction = this.db.transaction(["usuarios"], "readwrite");
        //agregamos los datos en el objectstore
        var objStore = transaction.objectStore("usuarios");
        var request = objStore.add(datosUsuario);
        //la adición se va a realizar de forma asincrona por lo que 
        //vamos a decirle que hacer cuando acabe de realizarla
        transaction.oncomplete = function (event) {
            //se ha añadido correctamente por lo que vamos a añadirlo a la lista de usuarios
            this.listaUsuarios.push(datosUsuario);
            //actualizamos la vista para que contenga a todos los usuarios de la lista
            this.representarUsuarios();
        }.bind(this);

        transaction.onerror = function (event) {
            //en caso de que un grupo ya exista con ese nombre ponemos una alerta en forma de parrafo
            $("main> section:eq(0) > section:eq(0)").append("<p>ERROR: ese usuario ya esta registrado</p>")
        };
    }

    cargarGruposBD() {
        //tenemos que hacer una lectura de la bd

        //iniciamos la operacion de lectura
        var transaction = this.db.transaction(["grupos"], "readonly");
        var objStore = transaction.objectStore("grupos");
        //conseguimos todos los registros
        objStore.getAll().onsuccess = function (event) {
            var resultados = event.target.result;
            this.listaGrupos = this.listaGrupos.concat(resultados);

            this.representarGrupos();
        }.bind(this);
    }

    cargarUsuariosBD() {
        //tenemos que hacer una lectura de la bd

        //iniciamos la operacion de lectura
        var transaction = this.db.transaction(["usuarios"], "readonly");
        var objStore = transaction.objectStore("usuarios");
        //conseguimos todos los registros
        objStore.getAll().onsuccess = function (event) {
            var resultados = event.target.result;
            this.listaUsuarios = this.listaUsuarios.concat(resultados);

            this.representarUsuarios();
        }.bind(this);
    }

    representarGrupos() {
        //insertamos dentro de la seccion de vista de grupos nuevas secciones, una por grupo
        //con la información formateada
        var seccion = $("main > section:eq(1)");

        //eliminamos todo lo anterior que puede haber
        $("section", seccion).remove();

        for (var i = 0; i < this.listaGrupos.length; i++) {
            var grupo = this.listaGrupos[i];


            //representamos el grupo con una seccion
            var str = "<section ondrop= 'gestor.drop(event)' ondragover='gestor.allowDrop(event)'>";
            str += "<h3>" + grupo.nombre + "</h3>";
            str += "<label for='descrgrupo" + grupo.nombre + "'>Descripcion: </label><textarea readonly id='descrgrupo" + grupo.nombre + "'>" + grupo.descripcion + "</textarea>";
            str += "<input type='button' value='Borrar grupo' onclick='gestor.borrarGrupo(\"" + grupo.nombre.toString() + "\")'>";
            //vamos a generar una string con los participartes en este curso
            var integrantes = "";
            for (var j = 0; j < this.listaUsuarios.length; j++) {
                if (this.listaUsuarios[j].grupo == grupo.nombre) {
                    integrantes += "<li>" + this.listaUsuarios[j].dni + " " + this.listaUsuarios[j].nombreU + "</li>"
                }
            }
            //insertamos en el grupo la lista
            if (integrantes.length > 0)//si no hay lista la insertamos
            {
                str += "<p>Usuarios asignados:</p><ul>" + integrantes + "</ul>";
            }
            str += "</section>";
            seccion.append(str);
        }
    }

    representarUsuarios() {
        //insertamos dentro de la seccion de vista de usuarios nuevas secciones, una por grupo
        //con la información formateada
        var seccion = $("main > section:eq(2)");

        //eliminamos todo lo anterior que puede haber
        $("section", seccion).remove();

        for (var i = 0; i < this.listaUsuarios.length; i++) {
            var usuario = this.listaUsuarios[i];

            //representamos el grupo con una seccion
            var str = "<section draggable='true' ondragstart='gestor.drag(event)'>";
            str += "<h3>" + usuario.nombreU + "</h3>";
            str += "<p>DNI: " + usuario.dni + "</p>";
            str += "<p>Numero de teléfono: " + usuario.telefono + "</p>";
            str += "<p>Correo electrónico: " + usuario.correo + "</p>";
            str += "<input type='button' value='Borrar usuario' onclick='gestor.borrarUsuario(\"" + usuario.dni.toString() + "\")'>";
            str += "</section>";
            seccion.append(str);
        }
    }

    borrarGrupo(nombreGrupo) {
        var request = this.db.transaction(["grupos"], "readwrite")
            .objectStore("grupos")
            .delete(nombreGrupo.toString());
        request.onsuccess = function (event) {
            // se borro exitosamente.
            //lo eliminamos de la lista de grupos
            var filtrada = this.listaGrupos.filter(x =>
                x.nombre.toString() != nombreGrupo
            );

            this.listaGrupos = filtrada;
            this.representarGrupos();
        }.bind(this);

        request.onerror = function (event) {
            // si ocurre un error mientras se borra
            this.showError("Error al eliminar el curso")
        }.bind(this);
    }

    borrarUsuario(dni) {
        var request = this.db.transaction(["usuarios"], "readwrite")
            .objectStore("usuarios")
            .delete(dni.toString());
        request.onsuccess = function (event) {
            // se borro exitosamente.
            //lo eliminamos de la lista de usuarios
            var filtrada = this.listaUsuarios.filter(x =>
                x.dni.toString() != dni.toString()
            );

            this.listaUsuarios = filtrada;
            this.representarUsuarios();
            //actulizamos tambien la lista de cursos para que no contenga al usuario
            //que acabamos de eliminar
            this.representarGrupos();
        }.bind(this);

        request.onerror = function (event) {
            // si ocurre un error mientras se borra
            this.showError("Error al eliminar el usuario")
        }.bind(this);
    }


    //GESTION DE LOS EVENTOS DRAG AND DROP

    allowDrop(ev) {
        ev.preventDefault();
    }
    drag(ev) {
        //obtenemos el dni del usuario siendo drageado
        var parrafo = $("p:first", ev.target).text();
        //eliminamos la parte de "DNI: "
        var dni = parrafo.substring(5);
        var nombre = $("h3", ev.target).text();
        ev.dataTransfer.setData("text", dni + " " + nombre);
    }

    drop(ev) {
        ev.preventDefault();
        //tenemos en data el dni del usuario
        var data = ev.dataTransfer.getData("text");

        var section = $(ev.target)[0];
        while (section.nodeName != "SECTION") { //no lo depositó en la seccion sino que en un hijo de ella
            section = $(section).parent()[0];
        }

        //insertamos los datos en la bd pasando dni y nombre de grupo para que sean asignados
        this.asignarUsuarioAGrupo(data.split(" ")[0], $("h3", section).text());
    }

    asignarUsuarioAGrupo(dni, nGrupo) {
        //obtenemos los dos objetos desde la base de datos
        //iniciamos la operacion de lectura
        var transaction = this.db.transaction(["grupos"], "readonly");
        var objStore = transaction.objectStore("grupos");
        //conseguimos todos los registros
        objStore.get(nGrupo).onsuccess = function (event) {
            var grupo = event.target.result;
            //una vez tenemos el grupo hacemos la query para el usuario

            var tUsuario = this.db.transaction(["usuarios"], "readwrite");
            var osUsuario = tUsuario.objectStore("usuarios");
            osUsuario.get(dni).onsuccess = function (event) {
                var usuario = event.target.result;

                //unimos el usuario con el grupo
                usuario.grupo = grupo.nombre;
                //actualizamos al usuario para que en la bd este con el nombre
                var updateReq = osUsuario.put(usuario);

                updateReq.onsuccess = () => {
                    //actualizamos las listas locales ya que se ha actualizado correctamente
                    for (var i = 0; i < this.listaUsuarios.length; i++) {
                        var ul = this.listaUsuarios[i];
                        if (ul.dni == dni) {
                            ul.grupo = nGrupo;
                            break;
                        }
                    }
                    //actualizamos los cursos para que se pinten ellos solos
                    this.representarGrupos();
                }

            }.bind(this);

        }.bind(this);
    }

    //CARGA DE ARCHIVO CON DATOS------------------------------

    cargarDeArchivo(file) {
        //leemos el archivo json y añadimos la información
        //vamos a cargar el contenido del archivo y procesarlo con jquery
        var lector = new FileReader();
        //generamos la seccion para el contenido  
        lector.readAsText(file);
        lector.onload = function (evento) {
            var json = JSON.parse(lector.result);

            var usuarios = json.usuarios;
            var grupos = json.grupos;

            var i;
            for (i = 0; i < grupos.length; i++) {
                //si ya tenemos un grupo con ese nombre no lo insertamos
                var skip = false;
                for (var j = 0; j < this.listaGrupos.length; j++) {
                    if (this.listaGrupos[j].nombre == grupos[i].nombre) {
                        skip = true;
                        break;
                    } else skip = false;
                }
                if (!skip)
                    this.insertarGrupoDatos(grupos[i]);
            }
            for (i = 0; i < usuarios.length; i++) {
                //si ya tenemos un usuario con ese dni no lo añadimos
                var skip = false;
                for (var j = 0; j < this.listaUsuarios.length; j++) {
                    if(this.listaUsuarios[j].dni == usuarios[i].dni) {
                        skip = true;
                        break;
                    } else skip = false;
                }
                if (!skip)
                    this.insertarUsuarioDatos(usuarios[i]);
            }
        }.bind(this);
    }


}
var gestor = new GestorGrupos();