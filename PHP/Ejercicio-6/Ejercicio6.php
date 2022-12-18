<!DOCTYPE HTML>

<html lang="es">

<head>
    <!-- Datos que describen el documento -->
    <meta charset="UTF-8" />
    <title>Manejo bases de datos con PHP</title>
    <link rel="stylesheet" type="text/css" href="Ejercicio6.css" />
    <meta name="author" content="Pablo Argallero Fernández" />

    <meta name="description" content="Manejo bases de datos con PHP" />

    <meta name="keywords" content="php,bd" />

    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <?php
    class BaseDatos
    {

        private $personaCargada;
        public $estadisticasCorrectas;
        public $avg_edad;
        public $per_hombres;
        public $per_mujeres;
        public $per_otros;
        public $avg_pericia;
        public $avg_tiempo;
        public $per_completado;
        public $avg_puntuacion;

        public function crearBaseDeDatos()
        {
            //creamos una conexxion a lab ase de datos local 
            $db = new mysqli("localhost", "DBUSER2022", "DBPSWD2022");
            //comprobamos que la conexion pudo ser creada
            if ($db->connect_errno) {
                return false; //en caso de que no informamos de que no se pudo llevar a cabo
            }

            //we drop the database if it already exists
            $drop = "DROP DATABASE IF EXISTS prueba";
            $db->query($drop);

            //Creamos la base de datos con el nombre dado
            $cadenaSQL = "CREATE DATABASE IF NOT EXISTS prueba COLLATE utf8_spanish_ci";
            $respuesta = $db->query($cadenaSQL);
            //cerramos la conexion
            $db->close();
            //devolvemos si se ha podido ejecutar o no
            return $respuesta;
        }

        public function crearUnaTabla()
        {
            //abrimos una conexion a la base de datos de prueba
            $db = new mysqli("localhost", "DBUSER2022", "DBPSWD2022");
            if (!$db->select_db("prueba")) //si hay error = no hay base de datos
                return false;
            $drop = "DROP TABLE IF EXISTS PruebasUsabilidad";
            $db->query($drop);

            $crearTabla = "CREATE TABLE IF NOT EXISTS PruebasUsabilidad 
                        (dni VARCHAR(9) NOT NULL ,
                        nombre VARCHAR(100) NOT NULL,
                        apellidos VARCHAR(100) NOT NULL,
                        email VARCHAR(100) NOT NULL,
                        telefono VARCHAR(100) NOT NULL,
                        edad INT(3) NOT NULL,
                        sexo VARCHAR(100) NOT NULL,
                        pericia INT(2) NOT NULL,
                        tiempo FLOAT NOT NULL,
                        completada BOOLEAN NOT NULL,
                        comentarios VARCHAR(500),
                        propuestas VARCHAR(500),
                        valoracion INT(2) ,

                        PRIMARY KEY (dni))";
            //creamos la tabla en la base de datos
            $respuesta = $db->query($crearTabla);

            //cerramos la conexion
            $db->close();
            return $respuesta;
        }

        public function insertarDatos()
        {
            $db = new mysqli("localhost", "DBUSER2022", "DBPSWD2022");
            $db->select_db("prueba");
            //comporbamos la conexion
            if ($db->connect_error) {
                return false;
            }
            //preparamos la sentencia de insercion de datos
            $prepared = $db->prepare("INSERT INTO PruebasUsabilidad (dni,nombre,apellidos,email,telefono,edad,sexo, pericia,tiempo,completada,comentarios,propuestas,valoracion) values (?,?,?,?,?,?,?,?,?,?,?,?,?)");

            // Valida que todos los campos estén rellenos exceptuando comentarios y propuestas que pueden estar vacios
            if (
                empty($_POST["dni"]) || empty($_POST["nombre"]) || empty($_POST["apellidos"]) ||
                empty($_POST["email"]) || empty($_POST["telefono"]) || empty($_POST["edad"]) ||
                empty($_POST["sexo"]) || empty($_POST["pericia"]) || empty($_POST["tiempo"]) ||
                empty($_POST["completada"]) ||
                empty($_POST["valoracion"])
            ) {
                return false;
            } 
            //agregamos los parametros de post 
            $tareaCompletada = isset($_POST["completada"]);
            $prepared->bind_param(
                'sssssisidissi',
                $_POST["dni"], $_POST["nombre"], $_POST["apellidos"], $_POST["email"], $_POST["telefono"],
                $_POST["edad"], $_POST["sexo"], $_POST["pericia"],
                $_POST["tiempo"],
                $tareaCompletada, $_POST["comentarios"], $_POST["propuestas"], $_POST["valoracion"]
            );

            //ejecutamos la consulta
            $resultado = $prepared->execute();

            //cerramos la query
            $prepared->close();

            //preparamos la query de insercion
            //cerramos la conexion
            $db->close();
            return $resultado;
        }

        public function buscar($dni)
        {
            $db = new mysqli("localhost", "DBUSER2022", "DBPSWD2022");
            $db->select_db("prueba");
            //comporbamos la conexion
            if ($db->connect_error) {
                return false;
            }
            //preparamos la sentencia de insercion de datos
            $prepared = $db->prepare("SELECT * FROM PruebasUsabilidad WHERE dni = ?");

            //agregamos el dni introducido por el usuario
            $prepared->bind_param(
                's',
                $dni
            );

            //ejecutamos la consulta
            $prepared->execute();

            $resultado = $prepared->get_result();
            //procesamos y guardamos el resultado
            if ($resultado->fetch_assoc() != NULL) {
                $resultado->data_seek(0); //Se posiciona al inicio del resultado de búsqueda
                $fila = $resultado->fetch_assoc();
                //guardamos en la variable el objeto cargado
                $this->personaCargada = $fila;

            } else {
                $this->personaCargada = null;
            }

            //cerramos la query
            $prepared->close();

            //preparamos la query de insercion
            //cerramos la conexion
            $db->close();
        }
        public function computarEstadisticas()
        {
            $db = new mysqli("localhost", "DBUSER2022", "DBPSWD2022");
            $db->select_db("prueba");
            //comporbamos la conexion
            if ($db->connect_error) {
                return false;
            }
            //preparamos la sentencia de insercion de datos
            $resultado = $db->query("SELECT * FROM PruebasUsabilidad");

            //inicializamos los acumuladores que nos van a servir para obtener las estadisticas
            $acc_edad = 0;
            $acc_hombres = 0;
            $acc_mujeres = 0;
            $acc_otros = 0;
            $acc_pericia = 0;
            $acc_tiempo = 0;
            $acc_completado = 0;
            $acc_puntuacion = 0;

            //procesamos los datos de la query
            if ($resultado->fetch_assoc() != NULL) {
                $resultado->data_seek(0); //Se posiciona al inicio del resultado de búsqueda
                while ($row = $resultado->fetch_assoc()) {
                    $acc_edad += $row["edad"];
                    switch ($row["sexo"]) {
                        case "hombre":
                            $acc_hombres++;
                            break;
                        case "mujer":
                            $acc_mujeres++;
                            break;
                        case "otro":
                            $acc_otros++;
                            break;
                    }
                    $acc_pericia += $row["pericia"];
                    $acc_tiempo += $row["tiempo"];
                    if ($row["completada"]) {
                        $acc_completado++;
                    }
                    $acc_puntuacion += $row["valoracion"];
                }
                //computamos las estadisticas
                $this->avg_edad = $acc_edad / $resultado->num_rows;
                $this->per_hombres = ($acc_hombres / $resultado->num_rows) * 100;
                $this->per_mujeres = ($acc_mujeres / $resultado->num_rows) * 100;
                $this->per_otros = ($acc_otros / $resultado->num_rows) * 100;
                $this->avg_pericia = $acc_pericia / $resultado->num_rows;
                $this->avg_tiempo = $acc_tiempo / $resultado->num_rows;
                $this->per_completado = ($acc_completado / $resultado->num_rows) * 100;
                $this->avg_puntuacion = $acc_puntuacion / $resultado->num_rows;

                $this->estadisticasCorrectas = true;
            } else {
                $this->estadisticasCorrectas = false;
            }
            //cerramos la conexion
            $db->close();
        }

        public function update()
        {
            $db = new mysqli("localhost", "DBUSER2022", "DBPSWD2022");
            $db->select_db("prueba");
            //comporbamos la conexion
            if ($db->connect_error) {
                return false;
            }
            //preparamos la sentencia de insercion de datos
            $prepared = $db->prepare("UPDATE PruebasUsabilidad SET 
                nombre = ?, apellidos = ?,
                email = ?, telefono = ?, edad = ?, sexo = ?, pericia = ?, tiempo = ?,
                completada = ?, comentarios = ?, propuestas = ?, valoracion = ? 
                WHERE dni = ?");

            //agregamos el dni introducido por el usuario
            $tareaCompletada = isset($_POST["completada-update"]);
            $prepared->bind_param(
                'ssssisidissis',
                $_POST["nombre-update"], $_POST["apellidos-update"], $_POST["email-update"], $_POST["telefono-update"],
                $_POST["edad-update"], $_POST["sexo-update"], $_POST["pericia-update"],
                $_POST["tiempo-update"],
                $tareaCompletada, $_POST["comentarios-update"],
                $_POST["propuestas-update"], $_POST["valoracion-update"], $_POST["dni-update"]
            );

            //ejecutamos la consulta
            $resultado = $prepared->execute();

            $prepared->close();
            $db->close();
            return $resultado;
        }

        public function borrar()
        {
            $db = new mysqli("localhost", "DBUSER2022", "DBPSWD2022");
            $db->select_db("prueba");
            //comporbamos la conexion
            if ($db->connect_error) {
                return false;
            }
            $prepared = $db->prepare("DELETE FROM PruebasUsabilidad WHERE dni = ?");

            //agregamos el dni introducido por el usuario
            $prepared->bind_param(
                's',
                $_POST["buscador-borrar"]
            );
            //ejecutamos la consulta
            $resultado = $prepared->execute();

            $prepared->close();
            $db->close();
            return $resultado;

        }

        public function cargarCSV()
        {
            $db = new mysqli("localhost", "DBUSER2022", "DBPSWD2022");
            $db->select_db("prueba");
            //comporbamos la conexion
            if ($db->connect_error) {
                return false;
            }

            $archivo = $_POST["archivoCSV"];
            //confirmamos que hay algo seleccionado mirando el nombre
            if (strlen($archivo) < 5) //como minimo tenemos X.csv donde x sea un caracter
                return false;
            $handle = fopen($archivo, "r");
            $resultado = true;
            $q = $db->prepare("INSERT INTO PruebasUsabilidad (dni,nombre,apellidos,email,telefono,edad,
                sexo, pericia,tiempo,completada,comentarios,propuestas,valoracion) 
                values (?,?,?,?,?,?,?,?,?,?,?,?,?)");

            while (($data = fgetcsv($handle, null, ",")) !== FALSE) {
                $q->bind_param(
                    'sssssisidissi',
                    $data[0], $data[1], $data[2], $data[3], $data[4],
                    $data[5], $data[6], $data[7], $data[8],
                    $data[9], $data[10], $data[11], $data[12]
                );
                try {
                    $resultado = $resultado && $q->execute();
                } catch (Exception $e) {
                    //en caso de que se quiera cargar a algun usuario ya cargado lo saltamos y continuamos
                }
            }

            fclose($handle);

            return $resultado;
        }

        public function guardarDatosACSV()
        {
            $db = new mysqli("localhost", "DBUSER2022", "DBPSWD2022");
            $db->select_db("prueba");
            //comporbamos la conexion
            if ($db->connect_error) {
                return false;
            }
            // Crea una sentencia SELECT para obtener todos los datos de la tabla
            $query = "SELECT * FROM PruebasUsabilidad";
            $result = $db->query($query);
            $file = fopen($_POST["nombreDestinoCSV"] . ".csv", "w");

            while ($row = mysqli_fetch_assoc($result)) {
                fputcsv($file, $row);
            }

            fclose($file);
            $db->close();
            return true;

        }

        public function getUsuarioBuscado()
        {
            return $this->personaCargada;
        }
    }
    $baseDatos = new BaseDatos();
    ?>
</head>

<body>

    <h1>Gestión de bases de datos</h1>
    <nav>
        <a href="#1">Crear Base de Datos</a>
        <a href="#2">Crear una tabla</a>
        <a href="#3">Insertar datos en una tabla</a>
        <a href="#4">Buscar datos en una tabla</a>
        <a href="#5">Modificar datos en una tabla</a>
        <a href="#6">Eliminar datos de una tabla</a>
        <a href="#7">Generar informe</a>
        <a href="#8">Cargar datos desde un archivo CSV</a>
        <a href="#9">Exportar datos a un archivo en formato CSV</a>
    </nav>

    <main>
        <section id="1">
            <h2>Creación de base de datos</h2>
            <form action="#" method="POST">
                <p>Para crear la base de datos en blanco presione el botón. ADVERTENCIA: si la base de datos ya ha sido
                    creada esta se verá sobre escrita y se perderán los datos.</p>
                <input type="submit" name="crearBaseDeDatos" value="Crear base de datos"></input>
            </form>
            <?php
            if (isset($_POST['crearBaseDeDatos'])) {
                $correcto = $baseDatos->crearBaseDeDatos();

                if (!$correcto) {
                    echo "<p>Ha ocurrido un error inicializando la base de datos, intentelo de nuevo.</p>";
                } else {
                    echo "<p>La base de datos ha sido correctamente creada.</p>";
                }
            }
            ?>
        </section>

        <section id="2">
            <h2>Creación de una tabla</h2>
            <form action="#" method="POST">
                <p>Pulse el botón para crear la tabla de pruebas para la usabilidad. ADVERTENCIA: si la tabla ya habia
                    sido creada esta será sobre escrita y se perderán los datos.</p>
                <input type="submit" name="crearTabla" value="Crear tabla"></input>
            </form>
            <?php
            if (isset($_POST['crearTabla'])) {
                $correcto = $baseDatos->crearUnaTabla();

                if (!$correcto) {
                    echo "<p>Ha ocurrido un error creando la tabla, intentelo de nuevo.</p>";
                } else {
                    echo "<p>La tabla ha sido correctamente creada.</p>";
                }
            }
            ?>
        </section>

        <section id="4">
            <h2>Busqueda de datos en la tabla</h2>
            <form action="#" method="POST">
                <p><label for="buscador">Introduce el DNI del usuario: <input id="buscador" type="text"
                            name="buscador"></input></label></p>
                <input type="submit" name="buscarUsuario" value="Buscar usuario"></input>
            </form>

            <?php
            //si se ha buscado a alguien vamos a presentar la informacion del usuario devuelto por la bd
            if (isset($_POST['buscarUsuario'])) {
                $baseDatos->buscar($_POST["buscador"]);
                //comprobamos si ha encontrado a alguien -> guardado en variable dentro de la base de datos
                $usuario = $baseDatos->getUsuarioBuscado();
                if ($usuario != null) {
                    echo "<p>A continuación se muestran los datos del usuario.</p>";
                    //presentamos los datos personales
                    echo "<h3>Datos personales</h3>
                    <p>DNI: " . $usuario['dni'] . " </p>
                    <p>Nombre: " . $usuario['nombre'] . "</p>
                    <p>Apellidos: " . $usuario['apellidos'] . " </p>
                    <p>E-mail: " . $usuario['email'] . " </p>
                    <p>Telefono: " . $usuario['telefono'] . " </p>
                    <p>Sexo: " . $usuario['sexo'] . " </p>
                    <p>Nivel de pericia informática: " . $usuario['pericia'] . " </p>
                    ";
                    //presentamos los datos de la prueba
                    if ($usuario['completada'])
                        $completada = "SI";
                    else
                        $completada = "NO";
                    echo "<h3>Datos de la prueba</h3>
                    <p>Tiempo tardado: " . $usuario['tiempo'] . "</p>
                    <p>Tarea completada: " . $completada . "</p>
                    <p>Comentarios: \"" . $usuario['comentarios'] . "\"</p>
                    <p>Propuestas: \"" . $usuario['propuestas'] . "\"</p>
                    <p>Valoración de la aplicación: " . $usuario['valoracion'] . "</p>
                    ";
                } else {
                    echo "<p>El usuario no ha podido ser encontrado.</p>";
                }
            }
            ?>


        </section>

        <section id="5">
            <h2>Modificación de datos de la tabla</h2>
            <form action="#" method="POST">
                <p><label for="buscador-modificar">Introduce el DNI del usuario: <input id="buscador-modificar"
                            type="text" name="buscador-modificar"></input></label></p>
                <input type="submit" name="cargarUsuario" value="Cargar usuario"></input>
            </form>
            <?php
            //si se ha cargado a alguien para ser modificado exponemos el formulario con los datos
            if (isset($_POST['cargarUsuario'])) {
                $baseDatos->buscar($_POST["buscador-modificar"]);
                //comprobamos si ha encontrado a alguien -> guardado en variable dentro de la base de datos
                $usuario = $baseDatos->getUsuarioBuscado();
                if ($usuario != null) {
                    //cargamos los datos en un formulario poniendo el dni como NO modificable 
                    //solo se puede cambiar el resto de campos no la Key
                    if ($usuario['nombre'])
                        $completada = "SI";
                    else
                        $completada = "NO";
                    $hombre = false;
                    $mujer = false;
                    $otro = false;
                    switch ($usuario["sexo"]) {
                        case "hombre":
                            $hombre = true;
                            break;
                        case "mujer":
                            $mujer = true;
                            break;
                        case "otro":
                            $otro = true;
                            break;
                    }
                    ;
                    echo "
                        <form action='#' method='POST'>
                            <fieldset>
                                <legend>Datos personales</legend>
                                <p><label for='dni-update'>DNI (no modificable): <input id='dni-update' type='text' name='dni-update' readonly value ='" . $usuario["dni"] . "' ></label></p>
                                <p><label for='nombre-update'>Nombre: <input id='nombre-update' type='text' name='nombre-update' value='" . $usuario["nombre"] . "'></label></p>
                                <p><label for='apellidos-update'>Apellidos: <input id='apellidos-update' type='text' name='apellidos-update' value ='" . $usuario["apellidos"] . "'></label></p>
                                <p><label for='email-update'>E-mail: <input id='email-update' type='email' name='email-update'value ='" . $usuario["email"] . "'></label></p>
                                <p><label for='telefono-update'>Teléfono: <input id='telefono-update' type='tel' name='telefono-update' value ='" . $usuario["telefono"] . "'></label></p>
                                <p><label for='edad-update'>Edad: <input id='edad-update' type='number' name='edad-update' value ='" . $usuario["edad"] . "'></label></p>
                                <p><label for='sexo-update'>Sexo:
                                        <select id='sexo-update' name='sexo-update'>
                                            <option value='hombre' " . ($hombre ? "selected" : "") . ">Hombre</option>
                                            <option value='mujer' " . ($mujer ? "selected" : "") . ">Mujer</option>
                                            <option value='otro' " . ($otro ? "selected" : "") . ">Otro</option>
                                        </select>
                                    </label></p>
                                <p><label for='pericia-update'>Nivel de pericia informática: <input id='pericia-update' type='number'
                                            name='pericia-update' min='0' max='10' value ='" . $usuario["pericia"] . "' ></label></p>
                            </fieldset>

                            <fieldset>
                                <legend>Datos de la prueba</legend>
                                <p><label for='tiempo-update'>Tiempo en realizar la tarea (en segundos): <input id='tiempo-update' type='number'
                                            name='tiempo-update' value ='" . $usuario["tiempo"] . "' min='0'  ></label></p>
                                <p><label for='completada-update'>Tarea completada:<input type='checkbox' id='completada-update' name='completada-update'
                                            value='completada' " . ($usuario["completada"] ? "checked" : "") . " ></label></p>

                                <p><label for='comentario-updates'>Comentarios sobre problemas encontrados: <textarea id='comentarios-update'
                                            name='comentarios-update'>" . $usuario["comentarios"] . " </textarea></label></p>
                                <p><label for='propuestas-update'>Propuestas de mejora: <textarea id='propuestas-update'
                                            name='propuestas-update'> " . $usuario["propuestas"] . "  </textarea> </label></p>
                                <p><label for='valoracion-update'>Valoración de la aplicación por parte del usuario: <input id='valoracion-update'
                                            type='number' name='valoracion-update' min='0' max='10' value ='" . $usuario["valoracion"] . "' ></label></p>
                            </fieldset>
                            <input type='submit' name='modificarDatos' value='Confirmar modificación'></input>
                        </form>
                    ";
                } else {
                    echo "<p>El usuario no ha podido ser encontrado.</p>";
                }
            }
            ?>

            <?php
            //si se ha confirmado la modificación de los datos de un usuario
            if (isset($_POST['modificarDatos'])) {
                //llamamos al metodo update de la bd 
                $completado = $baseDatos->update();

                if ($completado) {
                    echo "<p>Datos actualizados correctamente.</p>";
                } else {
                    echo "<p>Ha habido un error en la actualización, intentelo de nuevo.</p>";
                }
            }
            ?>


        </section>

        <section id="6">
            <h2>Borrado de datos de la tabla</h2>
            <form action="#" method="POST">
                <p><label for="buscador-borrar">Introduce el DNI del usuario a ser borrado: <input id="buscador-borrar"
                            type="text" name="buscador-borrar"></input></label></p>
                <input type="submit" name="borrarUsuario" value="Borrar usuario usuario"></input>
            </form>

            <?php
            //si se ha confirmado el borrado de un usuario ejecutamos la llamada de borrado
            if (isset($_POST['borrarUsuario'])) {
                $completado = $baseDatos->borrar();

                if ($completado) //se elimino correctamente
                {
                    echo "<p>Usuario eliminado correctamente</p>";
                } else
                    echo "<p>Error al eliminar al usuario, intentelo de nuevo más tarde.</p>";
            }
            ?>
        </section>

        <section id="7">
            <h2>Informe estadístico</h2>
            <form action="#" method="POST">
                <p>Pulse el boton para generar un informe estadístico de la prueba.</p>
                <input type="submit" name="generarInforme" value="Generar informe"></input>
            </form>
            <?php
            //si se ha mandado generar un informe
            if (isset($_POST['generarInforme'])) {
                //llamamos que genera el informe
                $baseDatos->computarEstadisticas();

                if ($baseDatos->estadisticasCorrectas) { //si los datos pudieron cargarse correctamente
                    echo "
                    <p>Edad media de los usuarios: " . (number_format($baseDatos->avg_edad, 2)) . " años.</p>
                    <p>Frecuencia del % de cada tipo de sexo entre los usuarios: " . (number_format($baseDatos->per_hombres, 2)) . "% de los usuarios son hombres, " . (number_format($baseDatos->per_mujeres, 2)) . "% son mujeres y un " . (number_format($baseDatos->per_otros, 2)) . "% no se clasifican ni como mujeres ni como hombres.</p>
                    <p>Valor medio del nivel o pericia informática de los usuarios: " . (number_format($baseDatos->avg_pericia, 2)) . ".</p>
                    <p>Tiempo medio para la tarea: " . (number_format($baseDatos->avg_tiempo, 2)) . " segundos.</p>
                    <p>Porcentaje de usuarios que han realizado la tarea correctamente: " . (number_format($baseDatos->per_completado, 2)) . "%.</p>
                    <p>Valor medio de la puntuación de los usuarios sobre la aplicación: " . (number_format($baseDatos->avg_puntuacion, 2)) . " sobre 10.</p>
                    ";
                } else {
                    echo "<p>Ha habido un error la generación del informe estadístico, intentelo de nuevo.</p>";
                }
            }
            ?>


        </section>

        <section id="8">
            <h2>Carga de datos desde archivo CSV</h2>
            <form action="#" method="POST">
                <label for="inputFile">Seleccione el archivo<input id="inputFile" type="file" accept=".csv"
                        name="archivoCSV"></label>
                <input type="submit" name="cargarCSV" value="Cargar CSV"></input>
            </form>

            <?php
            if (isset($_POST['cargarCSV'])) {
                $completado = $baseDatos->cargarCSV();

                if ($completado) //se elimino correctamente
                {
                    echo "<p>CSV cargado correctamente</p>";
                } else
                    echo "<p>Error al cargar el archivo CSV, intentelo de nuevo más tarde.</p>";
            }
            ?>
        </section>

        <section id="9">
            <h2>Exportar datos a un archivo CSV</h2>
            <form action="#" method="POST">
                <p><label for="nombreDestinoCSV">Introduce el nombre para el archivo CSV: <input id="nombreDestinoCSV"
                            type="text" name="nombreDestinoCSV"></input></label></p>
                <input type="submit" name="guardarDatosACSV" value="Descargar datos"></input>
            </form>

            <?php
            if (isset($_POST['guardarDatosACSV'])) {
                $completado = $baseDatos->guardarDatosACSV();

                if ($completado) //se elimino correctamente
                {
                    echo "<p>CSV generado correctamente</p>";
                } else
                    echo "<p>Error al generar el archivo CSV, intentelo de nuevo más tarde.</p>";
            }
            ?>
        </section>

        <section id="3">
            <h2>Insertar datos en una tabla</h2>
            <p>Rellene el siguiente formulario para introducir los datos en la tabla.</p>
            <form action="#" method="POST">
                <fieldset>
                    <legend>Datos personales</legend>
                    <p><label for="dni">DNI: <input id="dni" type="text" name="dni"></label></p>
                    <p><label for="nombre">Nombre: <input id="nombre" type="text" name="nombre"></label></p>
                    <p><label for="apellidos">Apellidos: <input id="apellidos" type="text" name="apellidos"></label></p>
                    <p><label for="email">E-mail: <input id="email" type="email" name="email"></label></p>
                    <p><label for="telefono">Teléfono: <input id="telefono" type="tel" name="telefono"></label></p>
                    <p><label for="edad">Edad: <input id="edad" type="number" name="edad"></label></p>
                    <p><label for="sexo">Sexo:
                            <select id="sexo" name="sexo">
                                <option value="hombre">Hombre</option>
                                <option value="mujer">Mujer</option>
                                <option value="otro">Otro</option>
                            </select>
                        </label></p>
                    <p><label for="pericia">Nivel de pericia informática: <input id="pericia" type="number"
                                name="pericia" min="0" max="10"></label></p>
                </fieldset>
                <fieldset>
                    <legend>Datos de la prueba</legend>
                    <p><label for="tiempo">Tiempo en realizar la tarea (en segundos): <input id="tiempo" type="number"
                                name="tiempo" min='0'></label></p>
                    <p><label for="completada">Tarea completada:<input type="checkbox" id="completada" name="completada"
                                value="completada"></label></p>

                    <p><label for="comentarios">Comentarios sobre problemas encontrados: <textarea id="comentarios"
                                name="comentarios"></textarea></label></p>
                    <p><label for="propuestas">Propuestas de mejora: <textarea id="propuestas"
                                name="propuestas"> </textarea> </label></p>
                    <p><label for="valoracion">Valoración de la aplicación por parte del usuario: <input id="valoracion"
                                type="number" name="valoracion" min="0" max="10"></label></p>
                </fieldset>
                <input type="submit" name="insertarDatos" value="Confirmar datos"></input>
            </form>

            <?php
            //validamos si nos ha llegado la señal de que el boton de confirmar datos ha sido pulsado
            if (isset($_POST['insertarDatos'])) {
                //en caso de haber sido pulsado vamos a hacer llamada al metodo del gestor de base de datos
                $completado = $baseDatos->insertarDatos();

                if ($completado) //se elimino correctamente
                {
                    echo "<p>Usuario insertado correctamente</p>";
                } else
                    echo "<p>Error al insertar al usuario, intentelo de nuevo más tarde.</p>";
            }
            ?>


        </section>
    </main>
    <footer>
        <p>Pablo Argallero Fernández - UO283216</p>
        <img src="multimedia/HTML5.png" alt="HTML válido" />
        <img src="multimedia/CSS3.png" alt="CSS válido" />
    </footer>
</body>

</html>