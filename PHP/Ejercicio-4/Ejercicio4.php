<!DOCTYPE HTML>

<html lang="es">

<head>
    <!-- Datos que describen el documento -->
    <meta charset="UTF-8" />
    <title>Consumo de servicios Web: petroleo</title>
    <link rel="stylesheet" type="text/css" href="Ejercicio4.css" />
    <meta name="author" content="Pablo Argallero Fernández" />

    <meta name="description" content="Consumo de servicios Web: petroleo" />

    <meta name="keywords" content="php,consumoWeb,precio,petroleo" />

    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <?php
    const KEY = "5nxnzx69urqfnzrev9qkqg27824wi2oya607oqkfc862u0mn1njeicgn41qn";

    class Petroleo
    {
        private $preciosSemana;

        public function __construct()
        {
            //cargamos los datos de la api de los precios de la ultima semana
            //para ser representados
            $this->cargarDatosDeLaSemana();
        }

        public function cargarDatosDeLaSemana()
        {
            $fechaInicio = date("Y-m-d", strtotime("-1 month -1 day"));
            $fechaAyer = date("Y-m-d", strtotime("-1 day"));

            // URL del servicio web que devuelve datos en formato JSON
            $url = "https://commodities-api.com/api/timeseries?start_date=$fechaInicio&symbols=BRENTOIL&end_date=$fechaAyer&access_key=" . KEY;


            // Realizar la petición a la URL y obtener la respuesta en forma de cadena
            $json = file_get_contents($url);

            // Convertir la cadena JSON en un objeto PHP
            $resultados = json_decode($json);
            // var_dump($resultados);
    
            //vamos a procesar el json devuelto para guardar el array de precios-fecha
            $this->preciosSemana = $resultados->data->rates;
        }

        public function getPreciosSemana()
        {
            return $this->preciosSemana;
        }

        public function getEstadisticas()
        {
            //cargamos las secciones de la semana y representamos los precios con imagenes
            $rates = $this->getPreciosSemana();

            //primero de todo sacamos datos estadísticos de los precios
            $valores = array(); //guarda los valores para sacar las estadisticas
    
            foreach ($rates as $date => $values) {
                $valor = $values->USD / $values->BRENTOIL;
                array_push($valores, $valor);
            }

            $max = max($valores);
            $min = min($valores);
            $avg = array_sum($valores) / count($valores);
            return array($max,$min,$avg,$rates);
        }
    }

    $petroleo = new Petroleo();
    ?>
</head>

<body>

    <h1>Precios del petroleo</h1>

    <section>
        <h2>Datos mensuales</h2>

        <?php
        $estadisticas = $petroleo->getEstadisticas();
        $min = $estadisticas[0];
        $max = $estadisticas[1];
        $avg = $estadisticas[2];
        $rates = $estadisticas[3];

        //ahora que tenemos los valores max min y avg vamos a presentarlos en la seccion principal
        echo "<p>Precio mínimo:$min$/barril\tPrecio Máximo:$max$/barril\tPrecio Medio:$avg$/barril</p>";

        //ahora vamos a representar uno a uno cada uno de los precios en una subsección
        //calculamos el rango
        $range = $max - $min;
        // Dividimos el rango en $nMaxBarriles trozos
        $nMaxBarriles = 5;
        $subrange_size = $range / $nMaxBarriles;

        foreach ($rates as $dia => $values) {

            $stringDia = $dia; //conseguimos el dia 2022-12-06
            $precio = round($values->USD / $values->BRENTOIL, 2); //conseguimos el valor para ese dia
        
            //vamos a calcular el en la escala de 1 a $nMaxBarriles barriles el precio de este dia 
            //en base al mínimo y máximos de la semana
            $NBarriles = round(($precio - $min) / $subrange_size);

            //representamos la información para este dia
            echo "<section><h3>$stringDia</h3><p>Precio del dia: $precio$/barril</p>";
            //añadimos los barriles
            $current = 0; //indica el n del barril actual -> usado en las etiquetas alt de la imagen para hacerlo accesible
            for ($i = 0; $i < $NBarriles; $i++) {
                echo "<img src='multimedia/barril.png' alt='Barril $current del dia $stringDia: pintado'/>";
                $current++;
            }
            //añadimos los barriles sin pintar que falten para llegar hasta $nMaxBarriles
            for ($i = 0; $i < $nMaxBarriles - $NBarriles; $i++) {
                echo "<img src='multimedia/barrilClaro.png' alt='Barril $current : sin pintar'/>";
                $current++;
            }
            //cerramos la seccion para este dia
            echo "</section>";
        }
        ?>

    </section>
    <footer>
        <p>Pablo Argallero Fernández - UO283216</p>
        <img src="multimedia/HTML5.png" alt="HTML válido" />
        <img src="multimedia/CSS3.png" alt="CSS válido" />
    </footer>
</body>

</html>