<!DOCTYPE HTML>

<html lang="es">

<head>
    <!-- Datos que describen el documento -->
    <meta charset="UTF-8" />
    <title>MonkeyCode</title>
    <link rel="stylesheet" type="text/css" href="Ejercicio7.css" />
    <meta name="author" content="Pablo Argallero Fernández" />

    <meta name="description" content="Página para subir preguntas y respuestas sobre programación" />

    <meta name="keywords" content="php,bd,programación" />

    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <?php
    //iniciamos la sesion
    session_start();

    class Pregunta
    {
        private $id;
        private $titulo;
        private $descripcion;
        private $code;
        private $lenguaje;
        private $categoria;
        private $nombre_usuario;

        public function __construct($id, $titulo, $descripcion, $code, $lenguaje, $categoria, $nombre_usuario)
        {
            $this->id = $id;
            $this->titulo = $titulo;
            $this->descripcion = $descripcion;
            $this->code = $code;
            $this->lenguaje = $lenguaje;
            $this->categoria = $categoria;
            $this->nombre_usuario = $nombre_usuario;
        }

        public function getId()
        {
            return $this->id;
        }

        public function setId($id)
        {
            $this->id = $id;
        }

        public function getTitulo()
        {
            return $this->titulo;
        }

        public function setTitulo($titulo)
        {
            $this->titulo = $titulo;
        }

        public function getDescripcion()
        {
            return $this->descripcion;
        }

        public function setDescripcion($descripcion)
        {
            $this->descripcion = $descripcion;
        }

        public function getCode()
        {
            return $this->code;
        }

        public function setCode($code)
        {
            $this->code = $code;
        }

        public function getLenguaje()
        {
            return $this->lenguaje;
        }

        public function setLenguaje($lenguaje)
        {
            $this->lenguaje = $lenguaje;
        }

        public function getCategoria()
        {
            return $this->categoria;
        }

        public function setCategoria($categoria)
        {
            $this->categoria = $categoria;
        }

        public function getNombreUsuario()
        {
            return $this->nombre_usuario;
        }

        public function setNombreUsuario($nombre_usuario)
        {
            $this->nombre_usuario = $nombre_usuario;
        }
    }
    class Respuesta
    {
        public $id;
        public $contenido;
        public $code;
        public $nombre_usuario;
        public $pregunta_id;

        public function __construct($id, $contenido, $code, $nombre_usuario, $pregunta_id)
        {
            $this->id = $id;
            $this->contenido = $contenido;
            $this->code = $code;
            $this->nombre_usuario = $nombre_usuario;
            $this->pregunta_id = $pregunta_id;
        }

        public function getRepresentacion($user)
        {
            $rep = "<section>";
            $rep .= ($user == $this->nombre_usuario ? "<h3>Tu respuesta</h3>" : "<h3>Respuesta de $this->nombre_usuario</h3>");
            $rep .= "<p>$this->contenido</p>";
            if($this->code != null && strlen(trim($this->code))> 0)
                //convertimos el codigo en una string para ser presentada evitando asi la ejecución de cualquier tipo de codigo
                $rep .= "<pre>".htmlentities($this->code)."</pre>";
            $rep .= "</section>";
            return $rep;
        }
    }



    class BaseDatos
    {
        public function crearBaseDeDatos()
        {
            //creamos una conexxion a lab ase de datos local 
            $db = new mysqli("localhost", "DBUSER2022", "DBPSWD2022");
            //comprobamos que la conexion pudo ser creada
            if ($db->connect_errno) {
                return false; //en caso de que no informamos de que no se pudo llevar a cabo
            }

            //we drop the database if it already exists
            $drop = "DROP DATABASE IF EXISTS monkeycode";
            $db->query($drop);

            //Creamos la base de datos con el nombre dado
            $cadenaSQL = "CREATE DATABASE IF NOT EXISTS monkeycode COLLATE utf8_spanish_ci";
            $respuesta = $db->query($cadenaSQL);
            if ($respuesta == false) {
                $db->close();
                return false;
            }

            //seleccionamos la bd
            if (!$db->select_db("monkeycode")) //si hay error = no hay base de datos
                return false;

            $drops = [
                "DROP TABLE IF EXISTS Usuarios;",
                "DROP TABLE IF EXISTS Preguntas;",
                "DROP TABLE IF EXISTS Respuestas;",
                "DROP TABLE IF EXISTS Lenguajes;",
                "DROP TABLE IF EXISTS Categorias;"
            ];
            foreach ($drops as $drop)
                $db->query($drop);

            $creates = ["CREATE TABLE Lenguajes (
                nombre VARCHAR(255) NOT NULL,
                descripcion TEXT NOT NULL,
                PRIMARY KEY (nombre)
            );"

                ,
                "CREATE TABLE Categorias (
                nombre VARCHAR(255) NOT NULL,
                descripcion TEXT NOT NULL,
                PRIMARY KEY (nombre)
            );",

                "CREATE TABLE Usuarios (
                nombre VARCHAR(255) NOT NULL,
                correo VARCHAR(255) NOT NULL,
                contraseña VARCHAR(255) NOT NULL,
                PRIMARY KEY (nombre)
            );"

                ,
                "CREATE TABLE Preguntas (
                id INT NOT NULL AUTO_INCREMENT,
                titulo VARCHAR(255) NOT NULL,
                descripcion TEXT NOT NULL,
                code TEXT ,
                lenguaje VARCHAR(255) NOT NULL,
                categoria VARCHAR(255) NOT NULL,
                nombre_usuario VARCHAR(255) NOT NULL,
                PRIMARY KEY (id),
                FOREIGN KEY (lenguaje) REFERENCES Lenguajes(nombre),
                FOREIGN KEY (categoria) REFERENCES Categorias(nombre),
                FOREIGN KEY (nombre_usuario) REFERENCES Usuarios(nombre)
            );"

                ,
                "CREATE TABLE Respuestas (
                id INT NOT NULL AUTO_INCREMENT,
                contenido TEXT NOT NULL,
                code TEXT ,
                nombre_usuario VARCHAR(255) NOT NULL,
                pregunta_id INT NOT NULL,
                PRIMARY KEY (id),
                FOREIGN KEY (nombre_usuario) REFERENCES Usuarios(nombre),
                FOREIGN KEY (pregunta_id) REFERENCES Preguntas(id)
            );"

            ]; //creamos la tabla en la base de datos
            foreach ($creates as $create)
                $respuesta = $respuesta && $db->query($create);

            //cerramos la conexion
            $db->close();
            //devolvemos si se ha podido ejecutar o no insertando tambien los datos iniciales
            return $respuesta && $this->cargarDatosIniciales();
        }

        public function cargarDatosIniciales()
        {
            //creamos una conexxion a lab ase de datos local 
            $db = new mysqli("localhost", "DBUSER2022", "DBPSWD2022");
            //comprobamos que la conexion pudo ser creada
            if ($db->connect_errno) {
                return false; //en caso de que no informamos de que no se pudo llevar a cabo
            }

            //seleccionamos la bd
            if (!$db->select_db("monkeycode")) //si hay error = no hay base de datos
                return false;

            $inserts = ["INSERT INTO Lenguajes (nombre, descripcion) VALUES
                    ('Java', 'Lenguaje de programación orientado a objetos'),
                    ('Python', 'Lenguaje de programación interpretado y de alto nivel'),
                    ('C++', 'Lenguaje de programación de propósito general'),
                    ('JavaScript', 'Lenguaje de programación interpretado utilizado principalmente en la web'),
                    ('PHP', 'Lenguaje de programación de propósito general y de código del lado del servidor'),
                    ('Ruby', 'Lenguaje de programación interpretado y orientado a objetos'),
                    ('C#', 'Lenguaje de programación de propósito general y orientado a objetos');"

                ,
                "INSERT INTO Categorias (nombre, descripcion) VALUES
                ('Desarrollo web', 'Categoría dedicada a preguntas y respuestas relacionadas con el desarrollo de aplicaciones web'),
                ('Machine learning', 'Categoría dedicada a preguntas y respuestas relacionadas con el aprendizaje automático y el análisis de datos'),
                ('Base de datos', 'Categoría dedicada a preguntas y respuestas relacionadas con el diseño y administración de bases de datos'),
                ('Programación concurrente', 'Categoría dedicada a preguntas y respuestas relacionadas con la programación de múltiples tareas al mismo tiempo'),
                ('Desarrollo móvil', 'Categoría dedicada a preguntas y respuestas relacionadas con el desarrollo de aplicaciones móviles'),
                ('Seguridad informática', 'Categoría dedicada a preguntas y respuestas relacionadas con la seguridad de sistemas y redes informáticas');"

                ,
                "INSERT INTO Usuarios (nombre, correo, contraseña) VALUES
                ('Juan Pérez', 'juan.perez@gmail.com', 'MiContraseña123'),
                ('Ana Martínez', 'ana.martinez@gmail.com', 'SuContraseña456'),
                ('Pedro García', 'pedro.garcia@gmail.com', 'SuContraseña789'),
                ('Sara Rodríguez', 'sara.rodriguez@gmail.com', 'MiContraseña246'),
                ('Mario Moreno', 'mario.moreno@gmail.com', 'SuContraseña369'),
                ('Laura Martín', 'laura.martin@gmail.com', 'MiContraseña159'),
                ('Pablo Sánchez', 'pablo.sanchez@gmail.com', 'SuContraseña753');"

                ,
                " INSERT INTO Preguntas (id, titulo, descripcion, code, lenguaje, categoria, nombre_usuario) VALUES
                (1, '¿Cómo conectar una base de datos MySQL a una aplicación Java?', 'Tengo una aplicación Java y quiero conectarla a una base de datos MySQL. ¿Cuáles son los pasos a seguir?',null, 'Java', 'Desarrollo web', 'Juan Pérez'),
                (2, '¿Cómo implementar un algoritmo de machine learning en Python?', 'Estoy interesado en utilizar machine learning en un proyecto de Python. ¿Cuáles son los pasos a seguir para implementar un algoritmo de machine learning?',null, 'Python', 'Machine learning', 'Ana Martínez'),
                (3, '¿Cómo optimizar el rendimiento de una aplicación Java?', 'Tengo una aplicación Java que está funcionando lentamente. ¿Qué puedo hacer para optimizar su rendimiento?',null, 'Java', 'Desarrollo web', 'Pedro García'),
                (4, '¿Cómo realizar una consulta en una base de datos MySQL desde PHP?', 'Tengo una base de datos MySQL y quiero realizar una consulta desde una aplicación PHP. ¿Cuál es la forma correcta de hacerlo?',null,'PHP', 'Desarrollo web', 'Sara Rodríguez'),
                (5, '¿Cómo resolver un problema de concurrencia en Java?', 'Tengo un problema de concurrencia en mi aplicación Java. ¿Cuáles son algunas formas de resolverlo?',null,'Java', 'Programación concurrente', 'Mario Moreno'),
                (6, '¿Cómo proteger una aplicación web contra ataques de inyección SQL?', 'Tengo una aplicación web y quiero protegerla contra ataques de inyección SQL. ¿Cuáles son algunas medidas que puedo tomar para evitar estos ataques? Inserto a continuación mi código actual:',' \$db = new mysqli(\'localhost\', \'user\', \'password\');\n\$db->select_db(\'monkeycode\');\n//comporbamos la conexion\nif (\$db->connect_error) {\n    return \'ERROR\';\n}\n//preparamos la query\n\$q = \'SELEC...`\';\n\$resultado = \'\';\n\$datos = \$db->query(\$q);','PHP', 'Seguridad informática', 'Laura Martín'),
                (7, '¿Cómo desarrollar una aplicación móvil híbrida con Ionic?', 'Estoy interesado en desarrollar una aplicación móvil híbrida con Ionic. ¿Cuáles son los pasos a seguir para hacerlo?',null,'JavaScript', 'Desarrollo móvil', 'Pablo Sánchez');"

                ,
                "INSERT INTO Respuestas (id, contenido, code, nombre_usuario, pregunta_id) VALUES
                (1, 'Hay varias formas de optimizar el rendimiento de una aplicación Java. Algunas posibles acciones a considerar son:
                
                Utilizar la última versión del JDK y asegurarse de que tienes suficiente memoria y CPU disponibles.
                Evitar el uso de recursos de red y disco duro en exceso, ya que pueden ser costosos en términos de rendimiento.
                Utilizar estructuras de datos y algoritmos eficientes, y optimizar el uso de memoria y los accesos a la memoria caché.
                Usar herramientas de perfilado y depuración para identificar y solucionar cuellos de botella en el rendimiento.', null, 'Pedro García', 3),

                (2, 'Para realizar una consulta a una base de datos MySQL desde PHP, puedes utilizar la función mysql_query(). Esta función te permite ejecutar una consulta SQL y devuelve un resource que puedes utilizar para acceder a los resultados de la consulta.
                Aquí tienes un ejemplo de cómo realizar una consulta simple a una base de datos MySQL desde PHP:', 
                '\n<?php\n Conecta a la base de datosº\n \$conn = mysql_connect(\'localhost\', \'username\', \'password\');º\n mysql_select_db(\'database_name\', \$conn);º\n º\n Realiza la consultaº\n \$result = mysql_query(\'SELEC.....);º\n º\n Recorre los resultados de la consultaº\n while (\$row = mysql_fetch_array(\$result)) {º\n   echo \$row[\'column_name\'] . \' \' . \$row[\'column_name\'];º\n }º\n º\n Cierra la conexión a la base de datosº\n mysql_close(\$conn);
                ?>', 'Sara Rodríguez', 4),
                
                (3, 'Una forma de proteger una aplicación web contra ataques de inyección SQL es validar y sanitizar todos los datos de entrada. Esto incluye verificar que los datos cumplan con ciertos requisitos (por ejemplo, que sean del tipo y formato adecuado) y eliminar o escapar cualquier carácter peligroso que pueda ser utilizado para realizar un ataque de inyección.
                También es importante utilizar consultas preparadas y parámetros en lugar de concatenar manualmente las consultas SQL, ya que esto ayuda a evitar ataques de inyección.
                Aquí tienes un ejemplo de cómo utilizar consultas preparadas y parámetros en PHP para protegerse contra ataques de inyección:',
                 '
                 // Conecta a la base de datos \n\$conn = mysqli_connect(\'localhost\', \'username\', \'password\', \'database_name\'); \n \n// Prepara la consulta \n\$stmt = mysqli_prepare(\$conn, \'SELEC......\'); \n \n// Vincula los parámetros a la consulta \nmysqli_stmt_bind_param(\$stmt, \'s\', \$param); \n \n// Establece el valor del parámetro \n\$param = \'value\'; \n \n// Ejecuta la consulta \nmysqli_stmt_execute(\$stmt); \n \n// Obtiene el resultado de la consulta \n\$result = mysqli_stmt_get_result(\$stmt); \n \n// Recorre los resultados \nwhile (\$row = mysqli_fetch_array(\$result)) { \n    echo \$row[\'column_name\'] . \' \' . \$row[\'column_name\']; \n} \n \n// Cierra la conexión a la base de datos \nmysqli_close(\$conn);'
                 ,'Mario Moreno', 6)
                "
            ];
            $resultado = true;
            foreach ($inserts as $insert)
                $resultado = $resultado && $db->query($insert);

            $db->close();

            return $resultado;
        }

        /**
         * genera un array de strings para ser insertada dentro de la seccion de las categorias 
         * esta array contiene los nombres de todas las categorias registradas
         * @return array conteniendo los nombres de las categorias
         * existentes
         */
        public function getAllCategorias()
        {
            $db = new mysqli("localhost", "DBUSER2022", "DBPSWD2022");
            $db->select_db("monkeycode");
            //comporbamos la conexion
            if ($db->connect_error) {
                return array();
            }
            //preparamos la query
            $q = "SELECT * FROM `categorias`";
            $resultado = array();
            $datos = $db->query($q);
            while ($categoria = $datos->fetch_assoc()) {
                $resultado[] = $categoria["nombre"];
            }


            return $resultado;
        }

        /**
         * genera un array de strings para ser insertada dentro de la seccion de los lenguajes 
         * esta array contiene los nombres de todas los lenguajes registradas
         * @return array conteniendo los nombres de los lenguajes
         * existentes
         */
        public function getAllLenguajes()
        {
            $db = new mysqli("localhost", "DBUSER2022", "DBPSWD2022");
            $db->select_db("monkeycode");
            //comporbamos la conexion
            if ($db->connect_error) {
                return array();
            }
            //preparamos la query
            $q = "SELECT * FROM `lenguajes`";
            $resultado = array();
            $datos = $db->query($q);
            while ($lenguaje = $datos->fetch_assoc()) {
                $resultado[] = $lenguaje["nombre"];
            }

            return $resultado;
        }

        public function generarPreguntasFiltradas()
        {
            $db = new mysqli("localhost", "DBUSER2022", "DBPSWD2022");
            $db->select_db("monkeycode");
            //comporbamos la conexion
            if ($db->connect_error) {
                return "<p>ERROR</p>";
            }
            //preparamos la query
            $q = "SELECT * FROM `preguntas`";
            $resultado = array();
            $datos = $db->query($q);
            while ($pregunta = $datos->fetch_assoc()) {
                $resultado[] = new Pregunta(
                    $pregunta["id"], $pregunta["titulo"], $pregunta["descripcion"],
                    $pregunta["code"], $pregunta["lenguaje"], $pregunta["categoria"], $pregunta["nombre_usuario"]
                );
            }

            $db->close();

            return $resultado;
        }

        public function insertarPregunta($usr)
        {

            $db = new mysqli("localhost", "DBUSER2022", "DBPSWD2022");
            $db->select_db("monkeycode");
            //comporbamos la conexion
            if ($db->connect_error) {
                return false;
            }
            //preparamos la query para insertar a un usuario
            $prepared = $db->prepare("INSERT INTO preguntas (titulo,descripcion,code,lenguaje,categoria,nombre_usuario) VALUES (?,?,?,?,?,?)");
            $code = null;
            if (strlen(trim($_POST["codigoPregunta"])) > 0) { //si hay pregunta no vacia
                $code = trim($_POST["codigoPregunta"]);
            }

            $prepared->bind_param(
                "ssssss", $_POST["TituloPregunta"],
                $_POST["descripcionPregunta"], $code,
                $_POST["LenguajePregunta"], $_POST["CategoriaPregunta"],
                $usr
            );

            $resultado = $prepared->execute();

            $prepared->close();
            $db->close();
            return $resultado;
        }
        public function insertarRespuesta($usr, $idpreg)
        {

            $db = new mysqli("localhost", "DBUSER2022", "DBPSWD2022");
            $db->select_db("monkeycode");
            //comporbamos la conexion
            if ($db->connect_error) {
                return false;
            }
            //preparamos la query para insertar a un usuario
            $prepared = $db->prepare("INSERT INTO respuestas (contenido,code,nombre_usuario,pregunta_id) VALUES (?,?,?,?)");
            $code = null;
            if (strlen(trim($_POST["codigoRespuesta"])) > 0) { //si hay codigo no vacio
                $code = trim($_POST["codigoRespuesta"]);
            }
            $idnumericopregunta = intval($idpreg);
            $prepared->bind_param(
                "sssi", $_POST["contenidoRespuesta"],
                $code,
                $usr,
                $idnumericopregunta
            );

            $resultado = $prepared->execute();

            $prepared->close();
            $db->close();
            return $resultado;
        }

        public function crearUsuario()
        {
            //validamos que las contraseñas son iguales, si no, error
            if ($_POST["password"] != $_POST["password2"])
                return false;

            $db = new mysqli("localhost", "DBUSER2022", "DBPSWD2022");
            $db->select_db("monkeycode");
            //comporbamos la conexion
            if ($db->connect_error) {
                return false;
            }
            //preparamos la query para insertar a un usuario
            $prepared = $db->prepare("INSERT INTO Usuarios (nombre,correo,contraseña) VALUES (?,?,?)");

            $prepared->bind_param("sss", $_POST["nombre"], $_POST["email"], $_POST["password"]);

            $resultado = $prepared->execute();

            $prepared->close();
            $db->close();
            return $resultado;
        }

        public function logIn()
        {
            $db = new mysqli("localhost", "DBUSER2022", "DBPSWD2022");
            $db->select_db("monkeycode");
            //comporbamos la conexion
            if ($db->connect_error) {
                return null;
            }

            //intentamos cargar un usuario con el correo y la contraseña iguales
            $prepared = $db->prepare("SELECT * FROM usuarios WHERE nombre = ? and contraseña = ? ");

            $prepared->bind_param("ss", $_POST["usuario-inicioSesion"], $_POST["contraseña-inicioSesion"]);

            $prepared->execute();
            $resultado = $prepared->get_result();

            $fila = null;
            if ($resultado->fetch_assoc() != NULL) {
                $resultado->data_seek(0); //Se posiciona al inicio del resultado de búsqueda
                $fila = $resultado->fetch_assoc();
            }
            $prepared->close();
            $db->close();

            return $fila != null ? $fila["nombre"] : null;
        }

        public function getDescripcionLenguaje($len)
        {
            $db = new mysqli("localhost", "DBUSER2022", "DBPSWD2022");
            $db->select_db("monkeycode");
            //comporbamos la conexion
            if ($db->connect_error) {
                return "No se ha podido cargar la descripción del lenguaje, intentelo más tarde.";
            }

            $prepared = $db->prepare("SELECT descripcion FROM lenguajes WHERE nombre = ?");

            $prepared->bind_param("s", $len);

            $prepared->execute();

            $result = $prepared->get_result();
            $descr = $result->fetch_assoc()["descripcion"];

            $prepared->close();
            $db->close();

            return $descr;
        }
        public function getDescripcionCategoria($cat)
        {
            $db = new mysqli("localhost", "DBUSER2022", "DBPSWD2022");
            $db->select_db("monkeycode");
            //comporbamos la conexion
            if ($db->connect_error) {
                return "No se ha podido cargar la descripción del lenguaje, intentelo más tarde.";
            }
            $prepared = $db->prepare("SELECT descripcion FROM categorias WHERE nombre = ?");

            $prepared->bind_param("s", $cat);

            $prepared->execute();

            $result = $prepared->get_result();
            $descr = $result->fetch_assoc()["descripcion"];

            $prepared->close();
            $db->close();

            return $descr;
        }
        /**
         * 
         * Devuelve un array de objetos Respuesta para la pregunta dada
         * @param string id de la pregunta de la que se quieren sacar las respuestas
         * @return array de objetos Respuesta
         */
        public function getRespuestasPara($idPregunta)
        {
            $db = new mysqli("localhost", "DBUSER2022", "DBPSWD2022");
            $db->select_db("monkeycode");
            //comporbamos la conexion
            if ($db->connect_error) {
                return array();
            }

            $prepared = $db->prepare("SELECT * FROM respuestas WHERE  pregunta_id= ?");

            $prepared->bind_param("s", $idPregunta);

            $prepared->execute();

            $result = $prepared->get_result();

            $respuestas = array();

            // Recorre los resultados de la consulta y crea un nuevo objeto "Respuesta" para cada fila de la tabla.
            while ($row = mysqli_fetch_assoc($result)) {
                $respuesta = new Respuesta(
                    $row['id'],
                    $row['contenido'],
                    $row['code'],
                    $row['nombre_usuario'],
                    $row['pregunta_id']
                );
                $respuestas[] = $respuesta;
            }
            return $respuestas;
        }
    }
    class MonkeyCode
    {
        public $usuario;
        private $baseDatos;
        private $categoria;
        private $lenguaje;
        public $preguntaDesplegada;

        public function __construct()
        {
            $this->baseDatos = new BaseDatos();
            $this->usuario = null;
        }
        public function persist()
        {
            $_SESSION["app"] = $this;
        }

        public function logIn()
        {
            $this->usuario = $this->baseDatos->logIn();
            $this->persist();
        }
        public function logout()
        {
            $this->usuario = null;
            $this->persist();
        }

        public function generarPreguntasFiltradas()
        {
            //conseguimos las preguntas de la base de datos
            $preguntas = $this->baseDatos->generarPreguntasFiltradas();

            //filtramos para devolver las que sean del lenguaje y de la categoría que queremos
            //tenemos las categorias y los lenguajes en el objeto monkeycode
            $categoria = $this->categoria;
            $lenguaje = $this->lenguaje;
            $preguntas = array_filter($preguntas, function ($question) use ($categoria, $lenguaje) {
                $aceptada = true;
                if ($categoria != null) //si tenermos categoría para filtrar
                    $aceptada = $aceptada && $question->getCategoria() == $categoria;
                if ($lenguaje != null)
                    $aceptada = $aceptada && $question->getLenguaje() == $lenguaje;
                return $aceptada;
            });

            //miramos a ver si el usuario ha desplegado las respuestas de alguna pregunta
            $respuestas = null;
            $preguntaDesplegada = null;
            foreach ($_POST as $key => $value) {
                if (preg_match('/^respuestas-/', $key)) {
                    $preguntaDesplegada = str_replace('respuestas-', '', $key);
                    //una vez tenemos el id de la pregunta conseguimos las respuestas para esa pregunta
                    $respuestas = $this->baseDatos->getRespuestasPara($preguntaDesplegada);
                    //guardamos en el objeto monkeycode la pregunta desplegada
                    $this->preguntaDesplegada = $preguntaDesplegada;
                }
            }

            //representamos las preguntas en formato html para ser impresas
            $resultado = "";
            foreach ($preguntas as $pregunta) {
                $resultado .= "<section>";

                $resultado .=
                    "<h3>" . $pregunta->getTitulo() . "</h3>
                    <p>Lenguaje: " . $pregunta->getLenguaje() . "</p>
                    <p>Categoría: " . $pregunta->getCategoria() . "</p>
                    <p>Usuario: " . ($pregunta->getNombreUsuario() == $this->usuario ? "TU" : $pregunta->getNombreUsuario()) . "</p>
                    <p>" . $pregunta->getDescripcion() . "</p>"
                ;

                //si tiene codigo lo metemos dentro de un bloque pre
                if ($pregunta->getCode() != null && strlen($pregunta->getCode()) > 0) {
                    $resultado .=
                        "<pre>" . htmlentities($pregunta->getCode()) . "</pre>"
                    ;
                }
                $resultado .= " <form action='#' method='POST'><input type='submit' name='respuestas-" . $pregunta->getId() . "' value='Ver respuestas'></input></form>";
                //si tenemos las respuestas cargadas para esta pregunta las enseñamos
    
                if ($preguntaDesplegada == $pregunta->getId()) {
                    if (!empty($respuestas)) {
                        $resultado .= "<h4>RESPUESTAS</h4>";
                        foreach ($respuestas as $res) {
                            $resultado .= $res->getRepresentacion($this->usuario);
                        }
                    } else {
                        $resultado .= "<h4>LA PREGUNTA NO TIENE RESPUESTAS</h4>";
                    }
                    //insertamos la seccion para insertar respuestas si hay usuario iniciado
                    if ($this->usuario != null) {
                        $resultado .= "
                            <section>
                                <h3>Deja tu respuesta</h3>
                                <form action='#' method='POST'>
                                    <p><label for='contenidoRespuesta'>Da tu respuesta: <input required id='contenidoRespuesta' type='text'
                                                name='contenidoRespuesta'></label></p>
                                    <p><label for='codigoRespuesta'>Apoya tu respuesta con código: <textarea id='codigoRespuesta'
                                        name='codigoRespuesta'></textarea></label></p>
                                    <input type='submit' name='insertarRespuesta' value='Responder'></input>
                                </form>
                            </section>";
                    }
                }


                $resultado .= "</section>";
            }
            return $resultado;
        }

        public function getBd()
        {
            return $this->baseDatos;
        }

        public function setLenguaje($len)
        {
            $this->lenguaje = $len;
        }
        public function setCategoria($cat)
        {
            $this->categoria = $cat;
        }

        public function getLenguaje()
        {
            return $this->lenguaje;
        }
        public function getCategoria()
        {
            return $this->categoria;
        }

    }
    $monkeyCode;
    if (isset($_SESSION["app"])) {
        $monkeyCode = $_SESSION["app"];
    } else {
        $monkeyCode = new MonkeyCode();
    }

    ?>
</head>

<body>

    <h1>MonkeyCode</h1>
    <main>
        <!-- seccion para el inicio de la base de datos -->
        <section>
            <h2>Creación de base de datos</h2>
            <form action="#" method="POST">
                <p>Para crear la base de datos presione el botón. ADVERTENCIA: si la base de datos ya ha sido
                    creada esta se verá sobre escrita y se perderán los datos.</p>
                <input type="submit" name="crearBaseDeDatos" value="Crear base de datos"></input>
            </form>
            <?php
            if (isset($_POST['crearBaseDeDatos'])) {
                $correcto = $monkeyCode->getBd()->crearBaseDeDatos();

                if (!$correcto) {
                    echo "<p>Ha ocurrido un error inicializando la base de datos, intentelo de nuevo.</p>";
                } else {
                    echo "<p>La base de datos ha sido correctamente creada.</p>";
                }
            }
            ?>
        </section>
        <!-- seccion para el inicio de sesión -->
        <section>

            <h2>Gestión de la sesión</h2>

            <!-- si hay un usuario cargado ponemos el mensaje de bienvenida y y el boton de logIn -->
            <?php
            $errorLogin = false;
            if (isset($_POST['cerrarSesion'])) {
                //llamamos al metodo de logout de la clase monkeyCode
                $monkeyCode->logout();
            }
            ;
            if (isset($_POST['iniciarSesion'])) {
                //llamamos la metodo de log in de la clase monkeycode
                $monkeyCode->logIn();
                //si el usuario no pudo ser cargado == null ponemos un error
                $errorLogin = true;
            }
            ;
            if ($monkeyCode->usuario != null) {
                //insertamos la seccion de bienvenida y de log out
                echo "
                    <section>
                        <h3>Sesión activa</h3>
                        <p>Bienvenido de nuevo " . $monkeyCode->usuario . "</p>
                        <form action='#' method='POST'>
                            <label for='logout'>Pulsa para cerrar tu sesión<input id='logout' type='submit' name='cerrarSesion' value='Cerrar sesión'></input></label>
                        </form>
                    </section>";


            } else {
                echo
                    "
                    <section>
                        <h3>Log in</h3>
                        <form action='#' method='POST'>
                            <p><label for='inicio-sesion-usuario'>Usuario: <input required id='inicio-sesion-usuario' type='text'
                                        name='usuario-inicioSesion'></input></label></p>
                            <p><label for='inicio-sesion-contraseña'>Contraseña: <input required id='inicio-sesion-contraseña'
                                        type='password' name='contraseña-inicioSesion'></input></label></p>
                            <input type='submit' name='iniciarSesion' value='Iniciar sesión'></input>
                        </form>";

                if ($errorLogin) {
                    echo "<p>El usuario no ha sido encontrado</p>";
                }
                echo "</section>";
            }
            ?>

            <section>
                <h3>Sign in</h3>
                <form action="#" method="POST">
                    <p><label for="crearCuenta">¿Aún no tienes cuenta?<input id="crearCuenta" type="submit"
                                name="crearCuenta" value="Crear cuenta"></input></label></p>
                </form>

                <?php
                //si se ha pulsado la creacion de una cuenta nueva
                if (isset($_POST["crearCuenta"])) {
                    //enseñamos el formulario de introduccion de datos para una nueva cuenta
                    echo

                        "<p>Creación de una cuenta nueva:</p>
                    <form action='#' method='POST'>
                        
                            <p><label for='nombre'>Nombre: <input id='nombre' type='text' name='nombre' required></label></p>
                            <p><label for='email'>E-mail: <input id='email' type='email' name='email' required></label></p>
                            <p><label for='password'>Contraseña:<input type='password' id='password' name='password' required></label></p>
                            <p><label for='password2'>Repite la contraseña:<input type='password' id='password2' name='password2' required></label></p>
                            
                        <input type='submit' name='confirmarCreacionCuenta' value='Crear cuenta'></input>
                    </form>";
                }

                //si se ha pulsado al boton de crear cuenta vamos a persistir esa cuenta y a guardarla como la cuenta actual
                if (isset($_POST["confirmarCreacionCuenta"])) {
                    $correcto = $monkeyCode->getBd()->crearUsuario();

                    if ($correcto)
                        echo '<p>Cuenta creada con éxito</p>';
                    else
                        echo '<p>Ha habido un error durante la creación de la cuenta. Intentelo de nuevo</p>';
                }
                ?>
            </section>

        </section>

        <!-- seccion para las categorias generadas en base a lo cargado -->
        <section>
            <h2>Filtros</h2>
            <h3>Categorías</h3>
            <form action="#" method="POST">
                <input type='submit' name='categoria-null' value='Desactivar filtro'></input>
                <?php
                $cats = $monkeyCode->getBd()->getAllCategorias();
                foreach ($cats as $cat) {
                    echo "<input type='submit' name='categoria-" . $cat . "' value='" . $cat . "'></input>";
                } ?>
            </form>
            <?php
            //vamos a iterar por los eventos y si hay uno que empiece por categoria vamos a poner esa categoria
            //como la categoría escogida
            foreach ($_POST as $key => $value) {
                if (preg_match('/^categoria-/', $key)) {
                    $categoria = str_replace('categoria-', '', $key);
                    $categoria = str_replace('_', ' ', $categoria);
                    if ($categoria != "null")
                        $monkeyCode->setCategoria($categoria);
                    else
                        $monkeyCode->setCategoria(null);
                }
            }

            //vamos a presentar la informacion de la categoria actualmente escogida
            if ($monkeyCode->getCategoria() != null) {
                $descripcion = $monkeyCode->getBd()->getDescripcionCategoria($monkeyCode->getCategoria());
                echo "<h4>Categoría seleccionada: " . $monkeyCode->getCategoria() . "</h4>
                        <p>$descripcion</p>";
            }

            ?>

            <h3>Lenguajes</h3>
            <form action="#" method="POST">
                <input type='submit' name='lenguaje-null' value='Desactivar filtro'></input>
                <?php
                $lengs = $monkeyCode->getBd()->getAllLenguajes();
                foreach ($lengs as $len) {
                    echo "<input type='submit' name='lenguaje-" . $len . "' value='" . $len . "'></input>";
                }
                ?>
            </form>
            <?php
            //vamos a iterar por los eventos y si hay uno que empiece por lenguaje- vamos a poner ese lenguaje
            //como la lenguaje de filtrado
            foreach ($_POST as $key => $value) {
                if (preg_match('/^lenguaje-/', $key)) {
                    $lenguaje = str_replace('lenguaje-', '', $key);
                    $lenguaje = str_replace('_', ' ', $lenguaje);
                    if ($lenguaje != "null")
                        $monkeyCode->setLenguaje($lenguaje);
                    else
                        $monkeyCode->setLenguaje(null);
                }
            }

            //vamos a presentar la informacion del Lenguaje actualmente escogido
            if ($monkeyCode->getLenguaje() != null) {
                $descripcion = $monkeyCode->getBd()->getDescripcionLenguaje($monkeyCode->getLenguaje());
                echo "<h4>Lenguaje seleccionado: " . $monkeyCode->getLenguaje() . "</h4>
                        <p>$descripcion</p>";
            }
            ?>
        </section>

       
            <!-- introducimos la seccion para introducir un pregunta si hay un usuario loggeado proponemos hacer una pregunta-->
            <?php
                if($monkeyCode->usuario != null){
                    $hazTuPregunta = "<section>
                        <h3>Haz tu propia pregunta</h3>
                        <form action='#' method='POST'>

                            <p><label for='TituloPregunta'>Titulo de la pregunta: <input required id='TituloPregunta'
                                        type='text' name='TituloPregunta'></label></p>
                            <p><label for='descripcionPregunta'>Aporta más informacion: <input id='descripcionPregunta'
                                        type='text' name='descripcionPregunta'></label></p>
                            <p><label for='codigoPregunta'>Escribe tu código para apoyar tu pregunta: <textarea
                                        id='codigoPregunta' name='codigoPregunta'></textarea></label></p>
                            <fieldset>
                                <legend>Clasificación de la pregunta</legend>
                                <p><label for='LenguajePregunta'>Indica el lenguaje de programacion:
                                        <select id='LenguajePregunta' name='LenguajePregunta'>";
                    //añadimos los lenguajes cargados en base de datos
                    $lengs = $monkeyCode->getBd()->getAllLenguajes();
                    foreach ($lengs as $len){
                        $hazTuPregunta.= "<option value='$len'>$len</option>";
                    }
                    $hazTuPregunta .="
                                        </select>
                                    </label></p>
                                <p><label for='CategoriaPregunta'>Indica la categoria de la pregunta:
                                        <select id='CategoriaPregunta' name='CategoriaPregunta'>";
                    //añadimos las categorias cargadas en base de datos                
                    $cats = $monkeyCode->getBd()->getAllCategorias();
                    foreach ($cats as $cat){
                        $hazTuPregunta.=  "<option value='$cat'>$cat</option>";   
                    }

                    $hazTuPregunta .= "  </select>
                                    </label></p>
                            </fieldset>
                            <input type='submit' name='insertarPregunta' value='Preguntar'></input>
                        </form>
                        </section>";
                    echo $hazTuPregunta;
                };
                    
            ?>
            <!-- gestionamos la insercion de una pregunta nueva -->
            <?php
            if (isset($_POST["insertarPregunta"])) {
                $completado = $monkeyCode->getBd()->insertarPregunta($monkeyCode->usuario);
                if (!$completado)
                    echo "<p>ERROR al registrar tu pregunta, intentelo de nuevo.</p>";
            } ?>

        <!-- seccion principal en la que van a aparecer las preguntas -->
        <section>
            <h2>Preguntas</h2>
            <?php echo $monkeyCode->generarPreguntasFiltradas();
            //si se ha pulsado el boton para subir una respuesta
            if (isset($_POST["insertarRespuesta"])) {
                $completado = $monkeyCode->getBd()->insertarRespuesta($monkeyCode->usuario, $monkeyCode->preguntaDesplegada);
                if (!$completado)
                    echo "<p>ERROR al registrar tu respuesta, intentelo de nuevo.</p>";
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

<?php
//guardamos el estado de la app
$_SESSION["app"] = $monkeyCode;
?>

</html>