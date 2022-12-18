<!DOCTYPE HTML>

<html lang="es">

<head>
    <!-- Datos que describen el documento -->
    <meta charset="UTF-8" />
    <title>Simulador de una calculadora científica</title>
    <link rel="stylesheet" type="text/css" href="CalculadoraCientifica.css" />
    <meta name="author" content="Pablo Argallero Fernández" />

    <meta name="description" content="Simulador de una calculadora científica" />

    <meta name="keywords" content="calculadora,cientifica,simulador" />

    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <?php
    //iniciamos la sesion si esta no esta iniciada
    session_start();

    //creamos la clase calculadora
    "use strict";
    class Calculadora
    {
        public $pantalla;
        public $memoria;
        public $operacionActual;
        public $acumuladorGuardado;
        public $operadorOFuncionInsertado;
        public $previaStringInsertada;
        public $igualPresionado;

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
                $this->acumuladorGuardado = substr($this->acumuladorGuardado, 0, strlen($this->acumuladorGuardado) - 1) . $operador;

            } else
                $this->acumuladorGuardado .= $operador;
            $this->operadorOFuncionInsertado = true;


        }

        public function insertarFuncion($funcion)
        { // entendemos como funcion cualquier operador que ocupe mas de un caracter
    
            if ($this->operadorOFuncionInsertado) {
                //eliminamos la funcion que se acaba de insertar y ponemos el que nos pasan
                $this->acumuladorGuardado = substr($this->acumuladorGuardado, 0, strlen($this->acumuladorGuardado) - 2 - strlen($this->previaStringInsertada) - 1) . $funcion;
            } else
                $this->acumuladorGuardado .= $funcion . $this->previaStringInsertada;

            $this->operadorOFuncionInsertado = true;
            $this->previaStringInsertada = $funcion . $this->previaStringInsertada;
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
            if (substr($this->pantalla, 0, 1) == "-")
                $this->pantalla = substr($this->pantalla, 1);
            else
                $this->pantalla = "-" . +$this->pantalla;

        }

        public function limpiarPantallaGuardandoValor()
        {
            $valorPantalla = floatval($this->pantalla);
            $this->previaStringInsertada = $this->pantalla;
            $this->acumuladorGuardado .= strval($valorPantalla);

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
    "use strict";
    class CalculadoraCientifica extends Calculadora
    {
        private $altPressed;
        private $funcion;
        private $funcionDeDosOperandos;
        private $radianes;
        private $hyperbolic;
        private $acumuladorAEnseñar;

        public function __construct()
        {
            parent::__construct();
            $this->altPressed = false;
            $this->funcion = "";
            $this->funcionDeDosOperandos = false;
            $this->radianes = true; /*por default tratamos los angulos con radianes*/
            $this->hyperbolic = false;
            $this->acumuladorAEnseñar = "";
        }

        public function notacionCientifica()
        {
            $this->pantalla = sprintf("%.5e", floatval($this->pantalla));
        }

        public function hyper()
        {
            $this->hyperbolic = !$this->hyperbolic;
        }


        /**
         * Cambia el tipo de input a decimal o a radianes
         */
        public function changeDegRad()
        {
            $this->radianes = !$this->radianes;
        }

        /**
         * Computa el cuadrado del elemento en pantalla
         */
        public function pow2()
        {
            $this->pantalla = pow(floatval($this->pantalla), 2);
        }

        /**
         * Computa x elevado a y
         */
        public function powY()
        {
            $this->ejecutarOperacionDosOperandos("pow(");
        }

        /**
         * Este metodo es usado por las operaciones que requieren dos operandos como 
         * puede ser el mod o la operacion x^y. Este prepara las variables para que tanto 
         * en la introduccion de una operacion como en la presion del igual se precompute
         * la operacion de dos operandos antes de continuar
         * @param {string} comando 
         */
        public function ejecutarOperacionDosOperandos($comando)
        {
            $this->funcion = $comando;
            $this->previaStringInsertada = $this->pantalla; //guardamos el primer operando
            $this->limpiarPantalla(); //limpiamos la pantalla sin guardar a acumulador
    
            $this->funcionDeDosOperandos = true;
        }

        /**
         * Es usada para recivir las operaciones simples como +-* /
         * para meter la lógica de comprobaciond e funciones con dos parametros
         * antes de llamar al método de la clase padre
         * @param {string} operador usado para la operacion
         */
        public function insertarOperacionSimple($operador)
        {
            if (preg_match('/[a-zA-Z]/', $this->pantalla)) { //si hay letras en la pantalla
                //si hay letras son parte del mensaje de error
                $this->pantalla = ""; //limpiamos la pantalla
                return; //no insertamos el valor en el acumulador 
            }

            $this->procesarPosibleFuncionDosParametros();
            $this->insertarOperador($operador);
            //actulizamos la string a ser enseñada
            $this->guardarAcumuladorAEnseñar();
            $this->previaStringInsertada = $operador;
        }

        public function procesarPosibleFuncionDosParametros()
        {
            //si hay una funcion de dos parametros pendientes hacemos la operacion antes de insertar el operando
            if ($this->funcionDeDosOperandos) {
                //calculamos el valor 
                //el uso de eval en este caso es seguro ya que el usuario no introduce comandos, solo 2 números
                //y recibimos del parametro la funcion a hacer
                $expr = "return " . $this->funcion . $this->previaStringInsertada . "," . $this->pantalla . ");";
                $value = eval($expr);
                //insertamos a la operacion actual el valor calculado
                $this->acumuladorGuardado .= $value;

                $this->funcionDeDosOperandos = false; //desmarcamos la flag
                //limpiamos la pantalla del anterior operando
                $this->pantalla = "";
            }
        }

        /**
         * Recibe un valor en grados y lo convierte en radianes si la calculadora
         * esta en estado radianes
         * @param {Number} value que va a ser convertido si estamos en radianes
         * @return Number numero en radianes si la calculadora esta en radianes o en degrees
         * si la calculadora no esta en radianes
         */
        public function convertToDegreesIfNeeded($value)
        {
            if ($this->radianes) //si estamos en radianes pasamos el valor sin mas
                return $value;
            else //si estamos en deg vamos a pasar el valor a radianes
                return ($value * pi()) / 180;
        }

        public function sin()
        {
            $s = sin($this->convertToDegreesIfNeeded(floatval($this->pantalla)));
            $sInversed = sinh($this->convertToDegreesIfNeeded(floatval($this->pantalla)));
            if ($this->altPressed) { //cosec
                if ($this->hyperbolic) {
                    $this->pantalla = 1 / $sInversed;
                } else
                    $this->pantalla = 1 / $s;
            } else { //seno
                if ($this->hyperbolic) {
                    $this->pantalla = $sInversed;
                } else
                    $this->pantalla = $s;
            }
        }
        public function cos()
        {
            $c = cos($this->convertToDegreesIfNeeded(floatval($this->pantalla)));
            $cInversed = cosh($this->convertToDegreesIfNeeded(floatval($this->pantalla)));
            if ($this->altPressed) //sec
                if ($this->hyperbolic) {
                    $this->pantalla = 1 / $cInversed;
                } else
                    $this->pantalla = 1 / $c;
            else //cos
                if ($this->hyperbolic) {
                    $this->pantalla = $cInversed;
                } else
                    $this->pantalla = $c;
        }
        public function tan()
        {
            $t = tan($this->convertToDegreesIfNeeded(floatval($this->pantalla)));
            $tInversed = tanh($this->convertToDegreesIfNeeded(floatval($this->pantalla)));
            if ($this->altPressed) //cotang
                if ($this->hyperbolic) {
                    $this->pantalla = 1 / $tInversed;
                } else
                    $this->pantalla = 1 / $t;
            else //tang
                if ($this->hyperbolic) {
                    $this->pantalla = $tInversed;
                } else
                    $this->pantalla = $t;
        }
        public function sqrt()
        {
            $this->pantalla = sqrt(floatval($this->pantalla));
        }
        public function diezPow()
        {
            $this->pantalla = pow(10, floatval($this->pantalla));
        }
        public function log()
        {
            $this->pantalla = log10(floatval($this->pantalla));
        }
        public function exp()
        {
            $this->pantalla = exp(floatval($this->pantalla));
        }
        public function mod()
        {
            $this->insertarOperacionSimple('%');//modulo
        }

        public function alt()
        {
            $this->altPressed = !$this->altPressed;
        }
        public function pi()
        {
            $this->pantalla = floatval(pi());

        }
        public function fact()
        {

            $this->pantalla = strval($this->factorialRecursivo(floatval($this->pantalla)));

        }
        public function factorialRecursivo($n)
        {
            if ($n == 0) {
                return 1;
            }
            return $n * $this->factorialRecursivo($n - 1);
        }
        public function del()
        {
            $this->pantalla = substr($this->pantalla, 0, strlen($this->pantalla) - 1);
        }
        public function leftParentesis()
        {
            $this->acumuladorGuardado .= "(";
            $this->limpiarPantallaGuardandoValor();
            $this->guardarAcumuladorAEnseñar();

        }
        public function rightParentesis()
        {
            $this->limpiarPantallaGuardandoValor();
            $this->acumuladorGuardado .= ")";
            $this->guardarAcumuladorAEnseñar();
        }

        /**
         * Sobreescribo el método de limpiar pantalla para que no compute
         * al limpiar sino que guarde la string con toda la operacion
         */
        public function limpiarPantallaGuardandoValor()
        {
            if ($this->pantalla != "") {

                $this->previaStringInsertada = $this->pantalla;
                $this->acumuladorGuardado .= $this->pantalla;

                $this->limpiarPantalla();
            }
        }

        /**
         * El limpiar pantalla ha de ser tambien overrideado ya que ha de llamar al actualizar 
         * pantalla de la cientifica
         */
        public function limpiarPantalla()
        {
            $this->pantalla = "";
        }


        /**
         * Overrideamos el igual para que actualize la pantalla que contiene toda la operacion
         */
        public function igual()
        {
            //primero acabamos con una posible funcion de dos parametros
            $this->procesarPosibleFuncionDosParametros();
            if ($this->pantalla != "")
                $this->acumuladorGuardado .= $this->pantalla;
            //si lo ultimo introducido es una operacion o funcion
            if ($this->operadorOFuncionInsertado) {
                //lo eliminamos del acumulador
                $this->acumuladorGuardado = substr($this->acumuladorGuardado, 0, strlen($this->acumuladorGuardado)- strlen($this->previaStringInsertada));
            }
            try {
                $this->pantalla = strval(eval("return $this->acumuladorGuardado;"));
            } catch (error) {
                $this->pantalla = "Error";
                $this->acumuladorGuardado = "";
            }
            if ($this->pantalla == "NaN") {
                $this->pantalla = "Error";
                $this->acumuladorGuardado = "";
            }
            $this->guardarAcumuladorAEnseñar();
            $this->acumuladorGuardado = "";
            $this->igualPresionado = true;
        }

        /**
         * Overrideamos el C para que actualice la pantalla con toda la cuenta
         */
        public function c()
        {
            parent::c();
            $this->funcion = "";
            $this->funcionDeDosOperandos = false;
            $this->acumuladorAEnseñar = "";
        }

        /**
         * Limpia la memoria de la calculadora
         */
        public function mc()
        {
            $this->memoria = 0;
        }

        /**
         * Guarda el valor en pantalla en la memoria
         * sobrescribe el valor anterior de la memoria
         */
        public function mStore()
        {
            $this->memoria = floatval($this->pantalla);
            $this->limpiarPantalla();
        }


        /**
         * Devuelve el acumulador guardado para poder ser representado
         */
        public function getAcumuladorGuardado()
        {
            return $this->acumuladorAEnseñar;
        }

        /**
         * Devuelve si el alt esta presionado o no
         */
        public function getAltPressed()
        {
            return $this->altPressed;
        }

        /**
         * Devuelve si esta en modo radianes o decimal
         */
        public function getRadianes()
        {
            return $this->radianes;
        }

        /**
         * Devuelve si esta en hyperbolico o no
         */
        public function getHyperbolic()
        {
            return $this->hyperbolic;
        }

        /**
         * Guarda una copia del acumulador de la cuenta en la variable
         * acumuladorAEnseñar para poder representar en pantalla la ultima operación
         * dado que al dar al = reiniciamos el acumulador guardado
         * @return void
         */
        public function guardarAcumuladorAEnseñar()
        {
            $this->acumuladorAEnseñar = $this->acumuladorGuardado;
        }

    }

    //manejo de la sesión
    //cargamos de la sesion el objeto de calculadora usado
    if (isset($_SESSION['calculadoraCientifica'])) {
        $calculadora = $_SESSION['calculadoraCientifica']; //si ya teniamos una cogemos la ultima
    } else {
        $calculadora = new CalculadoraCientifica();
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
            $calculadora->insertarOperacionSimple('*');
        if (isset($_POST['/']))
            $calculadora->insertarOperacionSimple('/');
        if (isset($_POST['-']))
            $calculadora->insertarOperacionSimple('-');
        if (isset($_POST['MC']))
            $calculadora->mc();
        if (isset($_POST['+']))
            $calculadora->insertarOperacionSimple('+');
        if (isset($_POST['M+']))
            $calculadora->mMas();
        if (isset($_POST['MS']))
            $calculadora->mStore();
        if (isset($_POST['MR']))
            $calculadora->mrc();
        if (isset($_POST['punto']))
            $calculadora->punto();
        if (isset($_POST['=']))
            $calculadora->igual();
        if (isset($_POST['M-']))
            $calculadora->mMenos();
        //especificas de la calculadora científica
        if (isset($_POST['rad']))
            $calculadora->changeDegRad();
        if (isset($_POST['hyp']))
            $calculadora->hyper();
        if (isset($_POST['F-E']))
            $calculadora->notacionCientifica();
        if (isset($_POST['x^2']))
            $calculadora->pow2();
        if (isset($_POST['x^y']))
            $calculadora->powY();
        if (isset($_POST['sin']))
            $calculadora->sin();
        if (isset($_POST['cos']))
            $calculadora->cos();
        if (isset($_POST['tan']))
            $calculadora->tan();
        if (isset($_POST['10^x']))
            $calculadora->diezPow();
        if (isset($_POST['log']))
            $calculadora->log();
        if (isset($_POST['exp']))
            $calculadora->exp();
        if (isset($_POST['mod']))
            $calculadora->mod();
        if (isset($_POST['alt']))
            $calculadora->alt();
        if (isset($_POST['del']))
            $calculadora->del();
        if (isset($_POST['pi']))
            $calculadora->pi();
        if (isset($_POST['n!']))
            $calculadora->fact();
        if (isset($_POST['(']))
            $calculadora->leftParentesis();
        if (isset($_POST[')']))
            $calculadora->rightParentesis();

        //default es guardar la calculadora como estado en la sesion
        $_SESSION['calculadoraCientifica'] = $calculadora;

    }

    //guardamos la pantalla en una variable para poder ser enseñada por el html
    
    $pantalla = $calculadora->getPantalla();
    $acumuladorCuenta = $calculadora->getAcumuladorGuardado();
    $altPressed = $calculadora->getAltPressed();
    $hyperbolic = $calculadora->getHyperbolic();
    $radianes = $calculadora->getRadianes();

    ?>
</head>

<body>

    <h1>Simulador de calculadora científica en PHP</h1>

    <section>
        <h2>Calculadora Científica</h2>
        <form action="#" method="POST">
            <!-- Contiene la cuenta entera hasta el momento -->
            <?php
            echo
                "
            <label for='acumuladorCuenta'>Operacion actual<input type='text' value = '$acumuladorCuenta'
            id='acumuladorCuenta' readonly></input> </label>
            "
                ?>

            <!-- Contiene los valores que el usuario acaba de introducir -->
            <?php
            echo
                "
            <label for='pantalla'>Pantalla calculadora científica<input type='text' value = '$pantalla'
             id='pantalla' readonly></input></label>
            "
                ?>

            <!--Introducimos los botones deg  hyp y F-E -->
            <?php
            if ($radianes)
                echo "<input type='submit' value='rad' name = 'rad'></input>";
            else
                echo "<input type='submit' value='deg' name = 'rad'></input>";
            ?>

            <input type="submit" value="hyp" name="hyp"></input>
            <input type="submit" value="F-E" name="F-E"></input>

            <input type="submit" value="MC" name="MC"></input>
            <input type="submit" value="MR" name="MR"></input>
            <input type="submit" value="M+" name="M+"></input>
            <input type="submit" value="M-" name="M-"></input>
            <input type="submit" value="MS" name="MS"></input>


            <input type="submit" value="x^2" name="x^2"></input>
            <input type="submit" value="x^y" name="x^y"></input>
            <!-- los valores de los botones dependen del estado de la calculadora -->
            <?php
            if ($hyperbolic) {
                if (!$altPressed) {
                    echo
                        "
                        <input type='submit' value='sinh' name = 'sin'></input>
                        <input type='submit' value='cosh' name = 'cos'></input>
                        <input type='submit' value='tanh' name = 'tan'></input>
                    ";
                } else { //el alt esta presionado
                    echo
                        "
                        <input type='submit' value='invsinh' name ='sin'></input>
                        <input type='submit' value='invcosh' name ='cos'></input>
                        <input type='submit' value='invtanh' name = 'tan'></input>
                    ";
                }
            } else {
                if (!$altPressed) {
                    echo
                        "
                        <input type='submit' value='sin' name ='sin'></input>
                        <input type='submit' value='cos' name = 'cos'></input>
                        <input type='submit' value='tan' name = 'tan'></input>
                    ";
                } else { //el alt esta presionado
                    echo
                        "
                        <input type='submit' value='cosec' name = 'sin'></input>
                        <input type='submit' value='sec' name = 'cos'></input>
                        <input type='submit' value='cotag' name = 'tan'></input>
                    ";
                }
            }
            ?>

            <input type="submit" value="√" name="√"></input>
            <input type="submit" value="10^x" name="10^x"></input>
            <input type="submit" value="log" name="log"></input>
            <input type="submit" value="exp" name="exp"></input>
            <input type="submit" value="mod" name="mod"></input>

            <?php
            if ($altPressed) {
                echo "<input type='submit' value='▼' name = 'alt'></input>";
            } else {
                echo "<input type='submit' value='▲' name = 'alt'></input>";
            }
            ?>
            <input type="submit" value="CE" name="CE"></input>
            <input type="submit" value="C" name="C"></input>
            <input type="submit" value="del" name="del"></input>
            <input type="submit" value="/" name="/"></input>

            <input type="submit" value="pi" name="pi"></input>
            <input type="submit" value="7" name="7"></input>
            <input type="submit" value="8" name="8"></input>
            <input type="submit" value="9" name="9"></input>
            <input type="submit" value="x" name="x"></input>

            <input type="submit" value="n!" name="n!"></input>
            <input type="submit" value="4" name="4"></input>
            <input type="submit" value="5" name="5"></input>
            <input type="submit" value="6" name="6"></input>
            <input type="submit" value="-" name="-"></input>

            <input type="submit" value="+/-" name="+/-"></input>
            <input type="submit" value="1" name="1"></input>
            <input type="submit" value="2" name="2"></input>
            <input type="submit" value="3" name="3"></input>
            <input type="submit" value="+" name="+"></input>

            <input type="submit" value="(" name="("></input>
            <input type="submit" value=")" name=")"></input>
            <input type="submit" value="0" name="0"></input>
            <input type="submit" value="." name="punto"></input>
            <input type="submit" value="=" name="="></input>
        </form>
    </section>
    <footer>
        <p>Pablo Argallero Fernández - UO283216</p>
        <img src="multimedia/HTML5.png" alt="HTML válido" />
        <img src="multimedia/CSS3.png" alt="CSS válido" />
    </footer>
</body>

</html>