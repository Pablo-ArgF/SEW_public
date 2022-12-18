<!DOCTYPE HTML>

<html lang="es">

<head>
    <!-- Datos que describen el documento -->
    <meta charset="UTF-8" />
    <title>Simulador de una calculadora basica con PHP</title>
    <link rel="stylesheet" type="text/css" href="CalculadoraMilan.css" />
    <meta name="author" content="Pablo Argallero Fernández" />

    <meta name="description" content="Simulador de una calculadora básica hecho con php" />

    <meta name="keywords" content="calculadora,simulador" />

    <meta name="viewport" content="width=device-width, initial-scale=1.0" />


    <?php
    //iniciamos la sesion si esta no esta iniciada
    session_start();

    //creamos la clase calculadora
    "use strict";
    class Calculadora
    {
        private $pantalla;
        private $memoria;
        private $operacionActual;
        private $acumuladorGuardado;
        private $operadorOFuncionInsertado;
        private $previaStringInsertada;
        private $igualPresionado;

        public function __construct()
        {
            $this->pantalla = "";
            $this->memoria = 0;
            $this->operacionActual = "";
            $this->acumuladorGuardado = "";
            $this->operadorOFuncionInsertado = false;
            $this->previaStringInsertada = ""; //usado para las funciones
            $this->igualPresionado = false;
        }

        public function digitos($digito)
        {
            if ($this->igualPresionado) {
                $this->igualPresionado = false;
                $this->pantalla = "";
            }
            $this->pantalla .= $digito;
            
            $this->operadorOFuncionInsertado = false;
        }
        public function punto()
        {
            $this->pantalla = $this->pantalla . ".";
        }
        public function suma()
        {
            $this->insertarOperador("+");
        }
        public function resta()
        {
            $this->insertarOperador("-");
        }
        public function multiplicacion()
        {
            $this->insertarOperador("*");
        }
        public function division()
        {
            $this->insertarOperador("/");
        }
        public function porcentaje()
        {
            
            $funcion = $this->pantalla . "%";
            
            $this->insertarFuncion($funcion);
        }
        public function raiz()
        {
            $valorPantalla = floatval($this->pantalla);
            if ($this->pantalla == "")
                $valorPantalla = 0;
            $raizCalculada = sqrt($valorPantalla);
            $this->pantalla = $raizCalculada;
            

        }
        public function insertarOperador($operador)
        {
            //guardamos el valor en pantalla
            $this->limpiarPantallaGuardandoValor();

            if ($this->operadorOFuncionInsertado) {
                //eliminamos el operador que se acaba de insertar y ponemos el que nos pasan
                $this->acumuladorGuardado = substr($this->acumuladorGuardado,0,strlen($this->acumuladorGuardado) - 2) . $operador;

            } else
                $this->acumuladorGuardado .= $operador;
            $this->operadorOFuncionInsertado = true;

            
        }

        public function insertarFuncion($funcion)
        { // entendemos como funcion cualquier operador que ocupe mas de un caracter
    
            if ($this->operadorOFuncionInsertado) {
                //eliminamos la funcion que se acaba de insertar y ponemos el que nos pasan
                $this->acumuladorGuardado =  substr($this->acumuladorGuardado, 0,strlen($this->acumuladorGuardado) - 2 - strlen($this->previaStringInsertada) - 1) . $funcion;
            } else
                $this->acumuladorGuardado .= $funcion . $this->previaStringInsertada;

            $this->operadorOFuncionInsertado = true;
            $this->previaStringInsertada = $funcion . $this->previaStringInsertada;
            print_r($this->acumuladorGuardado);
        }
        public function c()
        {
            //elimina toda la operacion 
            $this->limpiarPantalla();
            $this->operacionActual = "";
            $this->acumuladorGuardado = "";
            $this->previaStringInsertada = "";

        }
        public function ce()
        {
            //limpia el número en pantalla sin guardar su valor
            $this->limpiarPantalla();
        }
        public function mrc()
        {
            $this->pantalla = $this->memoria;
        }
        public function mMenos()
        {
            $this->memoria -= floatval($this->pantalla);
        }
        public function mMas()
        {
            $this->memoria += floatval($this->pantalla);
        }
        public function igual()
        {

            if (strlen($this->pantalla) > 0)
                $this->acumuladorGuardado .= $this->pantalla;
            //si lo ultimo introducido es una operacion o funcion
            try {
                $this->pantalla = eval("return $this->acumuladorGuardado;");
            } catch (error) {
                $this->pantalla = "Error";
                $this->acumuladorGuardado = "";
            }
            if ($this->pantalla == "NaN")
                $this->pantalla = "Error";

            $this->acumuladorGuardado = "";
            $this->igualPresionado = true;

        }
        public function cambioDeSigno()
        {
            if ($this->pantalla == "") //si no hay un valor en la pantalla no cambiamos el signo
                return;
            if (substr($this->pantalla ,0,1) == "-")
                $this->pantalla = substr($this->pantalla ,1);
            else
                $this->pantalla = "-" .+ $this->pantalla;

        }

        public function limpiarPantallaGuardandoValor()
        {

            $valorPantalla =floatval($this->pantalla);
            $this->previaStringInsertada = $this->pantalla;
            $this->acumuladorGuardado .=strval($valorPantalla);

            $this->limpiarPantalla();
        }
        public function limpiarPantalla()
        {
            $this->pantalla = "";
        }

        public function getPantalla()
        {
            return $this->pantalla;
        }


    }
    

    //manejo de la sesión
    //cargamos de la sesion el objeto de calculadora usado
    if (isset($_SESSION['calculadora'])) {
        $calculadora = $_SESSION['calculadora'] ; //si ya teniamos una cogemos la ultima
    } else {
        $calculadora = new Calculadora();
        $_SESSION['calculadora'] = $calculadora; //si no teniamos calculadora empezamos una
    }

    //manejo de los inputs del usuario
    if (count($_POST) > 0) { //si el usuario ha presinado algo
        if (isset($_POST['C']))
            $calculadora->c();
        if (isset($_POST['CE']))
            $calculadora->ce();
        if (isset($_POST['+/-']))
            $calculadora->cambioDeSigno();
        if (isset($_POST['√']))
            $calculadora->raiz();
        if (isset($_POST['%']))
            $calculadora->porcentaje();
        if (isset($_POST['0']))
            $calculadora->digitos('0');
        if (isset($_POST['1']))
            $calculadora->digitos('1');
        if (isset($_POST['2']))
            $calculadora->digitos('2');
        if (isset($_POST['3']))
            $calculadora->digitos('3');
        if (isset($_POST['4']))
            $calculadora->digitos('4');
        if (isset($_POST['5']))
            $calculadora->digitos('5');
        if (isset($_POST['6']))
            $calculadora->digitos('6');
        if (isset($_POST['7']))
            $calculadora->digitos('7');
        if (isset($_POST['8']))
            $calculadora->digitos('8');
        if (isset($_POST['9']))
            $calculadora->digitos('9');
        if (isset($_POST['x']))
            $calculadora->multiplicacion();
        if (isset($_POST['/']))
            $calculadora->division();
        if (isset($_POST['-']))
            $calculadora->resta();
        if (isset($_POST['M']))
            $calculadora->mrc();
        if (isset($_POST['+']))
            $calculadora->suma();
        if (isset($_POST['M+']))
            $calculadora->mMas();
        if (isset($_POST['punto']))
            $calculadora->punto();
        if (isset($_POST['=']))
            $calculadora->igual();
        if (isset($_POST['M-']))
            $calculadora->mMenos();

        //default es guardar la calculadora como estado en la sesion
        $_SESSION['calculadora'] = $calculadora;
    }

    //guardamos la pantalla en una variable para poder ser enseñada por el html
    $pantalla = $calculadora->getPantalla();

    ?>
</head>

<body>

    <h1>Simulador de calculadora básica con PHP</h1>

    <section>
        <h2>Calculadora Milan</h2>
        <form action="#" method="POST">
             <?php echo "
                <label for='pantalla'>Pantalla<input value= '$pantalla'
                    type='text' id='pantalla' readonly></input></label>
            "?>

            <input type="submit" value="C" name="C"></input>
            <input type="submit" value="CE" name="CE"></input>
            <input type="submit" value="+/-" name="+/-"></input>
            <input type="submit" value="√" name="√"></input>
            <input type="submit" value="%" name="%"></input>

            <input type="submit" value="7" name="7"></input>
            <input type="submit" value="8" name="8"></input>
            <input type="submit" value="9" name="9"></input>

            <input type="submit" value="x" name="x"></input>
            <input type="submit" value="/" name="/"></input>

            <input type="submit" value="4" name="4"></input>
            <input type="submit" value="5" name="5"></input>
            <input type="submit" value="6" name="6"></input>

            <input type="submit" value="-" name="-"></input>
            <input type="submit" value="M" name="M"></input>


            <input type="submit" value="1" name="1"></input>
            <input type="submit" value="2" name="2"></input>
            <input type="submit" value="3" name="3"></input>

            <input type="submit" value="+" name="+"></input>
            <input type="submit" value="M+" name="M+"></input>

            <input type="submit" value="0" name="0"></input>

            <input type="submit" value="." name="punto"></input>
            <input type="submit" value="=" name="="></input>
            <input type="submit" value="M-" name="M-"></input>
        </form></input>
    </section>
    <footer>
        <p>Pablo Argallero Fernández - UO283216</p>
        <img src="multimedia/HTML5.png" alt="HTML válido" />
        <img src="multimedia/CSS3.png" alt="CSS válido" />
    </footer>
</body>

</html>