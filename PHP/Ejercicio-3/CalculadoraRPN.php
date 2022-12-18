<!DOCTYPE HTML>

<html lang="es">

<head>
    <!-- Datos que describen el documento -->
    <meta charset="UTF-8" />
    <title>Simulador de una calculadora RPN en PHP</title>
    <link rel="stylesheet" type="text/css" href="CalculadoraRPN.css" />
    <meta name="author" content="Pablo Argallero Fernández" />

    <meta name="description" content="Simulador de una calculadora RPN hecha en PHP" />

    <meta name="keywords" content="calculadora,polaca,RPN,simulador" />

    <meta name="viewport" content="width=device-width, initial-scale=1.0" />


    <?php
    session_start();

    "use strict";
    class Pila
    {
        private $pila;
        public function __construct()
        {
            $this->pila = array();
        }
        public function push($valor)
        {
            array_push($this->pila, $valor);
        }
        public function pop()
        {
            $poped = NAN; // si no hay valores devolvemos NaN
            if (count($this->pila) >= 1)
                $poped = array_pop($this->pila);
            return $poped;
        }
        public function getRepresentacion()
        {
            $stringPila = "";
            for ($i = 0; $i < count($this->pila); $i++) {
                $stringPila .= (count($this->pila) - $i - 1) . ":\t\t" . $this->pila[$i] . "\n";
            }

            //devolvemos la representacion de la pila para que sea enseñada
            //por la calculadora
            return $stringPila;
        }
        public function vaciar()
        {
            //reiniciamos la pila a una vacia
            $this->pila = array();
        }
    }



    "use strict";
    class CalculadoraRPN
    {
        private $pila;
        private $pantalla;
        private $altPressed;
        private $hyperbolic;
        private $radianes;
        private $representacionPila;


        public function __construct()
        {
            $this->pantalla = "";
            $this->pila = new Pila();
            $this->altPressed = false;
            $this->hyperbolic = false;
            $this->radianes = false;
            $this->representacionPila = "";
        }

        public function digitos($digito)
        {
            $this->validarSiErrorParaLimpiar();

            $this->pantalla .= strval($digito);
        }
        public function validarSiErrorParaLimpiar()
        {
            if ($this->pantalla == "Error") {
                //si hay error, reiniciamos la calculadora
                $this->c();
            }
        }
        public function punto()
        {
            $this->validarSiErrorParaLimpiar();

            $this->pantalla .= ".";

        }

        public function showError($msg)
        {
            //enseñamos el error en la pila
            $texto = "Error: " . $msg;
            $this->pila->push($texto);
            //enseñamos el error en la pantalla
            $this->pantalla = "Error";

        }

        public function solicitarOperandos($n)
        {
            $res = array();
            for ($i = 0; $i < $n; $i++) {
                $poped = $this->pila->pop();
                if (is_nan($poped)) {
                    $this->showError("No hay suficientes operandos en la pila");
                    return;
                }
                array_push($res, floatval($poped));
            }
            return $res;
        }
        public function insertarValor($valor)
        {
            $this->pila->push($valor . "");
        }

        public function suma()
        {
            $operands = $this->solicitarOperandos(2);
            if ($this->pantalla != "Error") {
                $o1 = $operands[0];
                $o2 = $operands[1];
                $this->insertarValor($o1 + $o2);
            }

        }
        public function resta()
        {
            $operands = $this->solicitarOperandos(2);
            if ($this->pantalla != "Error") {
                $o1 = $operands[0];
                $o2 = $operands[1];
                $this->insertarValor($o2 - $o1);
            }
        }
        public function multiplicacion()
        {
            $operands = $this->solicitarOperandos(2);
            if ($this->pantalla != "Error") {
                $o1 = $operands[0];
                $o2 = $operands[1];
                $this->insertarValor($o1 * $o2);
            }
        }
        public function division()
        {
            $operands = $this->solicitarOperandos(2);
            if ($this->pantalla != "Error") {
                $o1 = $operands[0];
                $o2 = $operands[1];
                $this->insertarValor($o2 / $o1);
            }
        }
        public function porcentaje()
        {
            $operands = $this->solicitarOperandos(2);
            if ($this->pantalla != "Error") {
                $o1 = $operands[0];
                $o2 = $operands[1];
                //para calcular el porcentaje calcularemos el o1% de o2
                $this->insertarValor($o1 * ($o2 / 100));
            }
        }
        public function raiz()
        {
            $operands = $this->solicitarOperandos(1);
            if ($this->pantalla != "Error") {
                $this->insertarValor(strval(sqrt($operands[0])));
            }
        }

        /**
         * Computa el cuadrado del elemento en pantalla
         */
        public function pow2()
        {
            $operandos = $this->solicitarOperandos(1);
            if ($this->pantalla != "Error")
                $this->insertarValor(pow($operandos[0], 2));
        }

        /**
         * Computa x elevado a y
         */
        public function powY()
        {
            $operandos = $this->solicitarOperandos(2);
            if ($this->pantalla != "Error")
                $this->insertarValor(pow($operandos[1], $operandos[0]));
        }

        /**
         * Recibe un valor en grados y lo convierte en radianes
         * @param {Number} value que va a ser convertido
         * @return Number el numero en radianes
         */
        public function convertToRadians($value)
        {
            return $value / (180 / pi());
        }
        public function sin()
        {
            $operandos = $this->solicitarOperandos(1);
            if ($this->pantalla != "Error") {
                $operandos[0] = $this->convertToRadians($operandos[0]);
                $s = sin($operandos[0]);
                $insertar = 0;
                if ($this->altPressed) //cosec
                    $insertar = 1 / $s;
                else //tang
                    $insertar = $s;
                $this->insertarValor($insertar);
            }
        }
        public function cos()
        {
            $operandos = $this->solicitarOperandos(1);
            if ($this->pantalla != "Error") {
                $operandos[0] = $this->convertToRadians($operandos[0]);
                $c = cos($operandos[0]);
                $insertar = 0;
                if ($this->altPressed) //sec
                    $insertar = 1 / $c;
                else //tang
                    $insertar = $c;
                $this->insertarValor($insertar);
            }
        }
        public function tan()
        {
            $operandos = $this->solicitarOperandos(1);
            if ($this->pantalla != "Error") {
                $operandos[0] = $this->convertToRadians($operandos[0]);
                $t = tan($operandos[0]);
                $insertar = 0;
                if ($this->altPressed) //cotang
                    $insertar = 1 / $t;
                else //tang
                    $insertar = $t;
                $this->insertarValor($insertar);
            }
        }
        public function sqrt()
        {
            $operandos = $this->solicitarOperandos(1);
            if ($this->pantalla != "Error")
                $this->insertarValor(sqrt($operandos[0]));
        }
        public function diezPow()
        {
            $operandos = $this->solicitarOperandos(1);
            if ($this->pantalla != "Error")
                $this->insertarValor(pow(10, $operandos[0]));
        }
        public function log()
        {
            $operandos = $this->solicitarOperandos(1);
            if ($this->pantalla != "Error")
                $this->insertarValor(log($operandos[0]));
        }
        public function exp()
        {
            $operandos = $this->solicitarOperandos(1);
            if ($this->pantalla != "Error")
                $this->insertarValor(exp($operandos[0]));
        }
        public function mod()
        {
            $operandos = $this->solicitarOperandos(2);
            if ($this->pantalla != "Error") {
                if ($operandos[0] == 0) {
                    //modulo de 0 da NaN y no queremos eso en la pila
                    $this->showError("Modulo de 0 no es posible"); //enseñamos error
                    return; //no hacemos el calculo
                }
                $this->insertarValor($operandos[1] % $operandos[0]);
            }
        }
        public function pi()
        {
            $this->insertarValor(pi());
        }
        public function fact()
        {
            $operandos = $this->solicitarOperandos(1);
            if ($this->pantalla != "Error")
                $this->insertarValor($this->factorialRecursivo($operandos[0]));
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
            //si tenemos mensaje de error lo borramos entero
            if ($this->pantalla == "Error") {
                $this->pantalla = "";

                return;
            }
            $this->pantalla = substr($this->pantalla, 0, strlen($this->pantalla) - 1);

        }

        public function alt(){
            $this->altPressed = !$this->altPressed;
        }


        //-----------------------------------------
    

        public function c()
        {
            //elimina toda la operacion 
            $this->limpiarPantalla();
            //limpiamos la pila
            $this->pila->vaciar();
        }
        public function ce()
        {
            //limpia el número en pantalla sin guardar su valor
            $this->limpiarPantalla();
        }
        public function enter()
        {
            //si tenemos error en pantalla no insertamos nada
            if ($this->pantalla == "Error")
                return;
            //si tenemos un valor en pantalla lo pusheamos
            if ($this->pantalla != "")
                $this->pila->push(floatval($this->pantalla));

            $this->limpiarPantalla();
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

        public function limpiarPantalla()
        {
            $this->pantalla = "";

        }
        public function getPantalla()
        {
            return $this->pantalla;
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

        public function getRepresentacionPila()
        {
            $stringPila = $this->pila->getRepresentacion();
            $this->representacionPila = $stringPila;
            return $this->representacionPila;
        }


    }
    //manejo de la sesión
    //cargamos de la sesion el objeto de calculadora usado
    if (isset($_SESSION['calculadoraRPN'])) {
        $calculadora = $_SESSION['calculadoraRPN']; //si ya teniamos una cogemos la ultima
    } else {
        $calculadora = new calculadoraRPN();
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
        if (isset($_POST['+']))
            $calculadora->suma();
        if (isset($_POST['punto']))
            $calculadora->punto();
        if (isset($_POST['=']))
            $calculadora->igual();
        //especificas de la calculadora científica
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
        if (isset($_POST['Enter']))
            $calculadora->enter();

        //default es guardar la calculadora como estado en la sesion
        $_SESSION['calculadoraRPN'] = $calculadora;

    }

    //guardamos la pantalla en una variable para poder ser enseñada por el html
    
    $pantalla = $calculadora->getPantalla();
    $pila = $calculadora->getRepresentacionPila();
    $altPressed = $calculadora->getAltPressed();
    $hyperbolic = $calculadora->getHyperbolic();
    $radianes = $calculadora->getRadianes();
    $valorPila = $calculadora->getRepresentacionPila();



    ?>

</head>

<body>

    <h1>Simulador de calculadora RPN en PHP</h1>

    <section>

        <h2>Calculadora RPN</h2> 
        <!-- Representa la pila -->

        <form action="#" method="POST">
            <!-- Contiene la cuenta entera hasta el momento -->
            <?php
            echo
                "
                <label for='pila'>Pila<textarea readonly id='pila'>$valorPila</textarea></label>
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

            <input type="submit" value="%" name="%"></input>
            <input type="submit" value="." name="punto"></input>
            <input type="submit" value="0" name="0"></input>
            <input type="submit" value="Enter" name="Enter"></input>
        </form>
    </section>

    <section>
        <h2>Manual de uso</h2>
        <p>La calculadora RPN funciona basándose en el uso de la pila (pantalla superior de la calculadora). Para
            realizar una operación, cada uno de los operandos han de ser insertados en la pila, para ello introduzca
            los valores en la pantalla inferior ("Pantalla calculadora RPN") y pulsando el botón ENTER.
            Una vez los operandos han sido insertados, presione el botón de la correspondiente función u operación,
            esta tomará los últimos N (los necesarios para esa operación) operandos insertados y devolverá a la pila el
            resultado.</p>

        <p>Las calculadoras RPN no disponen de sistema de paréntesis, para disponer de prioridad dentro de las
            operaciones han de usarse las operaciones en un orden concreto que permita acabar obteniendo los operandos
            ordenados en la pila</p>


        <p>A continuación un ejemplo para la operación (3+1)*2:</p>
        <figure>
            <img src="multimedia/ejemploOperacion_1.png"
                alt="Primer paso del ejemplo de operación - introduccion del 3 y 1 en pila" />
            <figcaption>
                Insertamos el 3 (presionando en el 3 y en ENTER) y el 1 (presionando el 1 y ENTER)
            </figcaption>
        </figure>
        <figure>
            <img src="multimedia/ejemploOperacion_2.png"
                alt="Segundo paso del ejemplo de operación - suma de 3 y 1 para obtener la prioridad" />
            <figcaption>
                Para aplicar la suma (3+1) apretamos el botón +. En la pila los dos operandos (3 y 1) serán sumados y se
                insertará el resultado, en este caso, 4. Para multiplicar el resultado por 2 insertamos este número en
                la pila.
            </figcaption>
        </figure>
        <figure>
            <img src="multimedia/ejemploOperacion_3.png"
                alt="Tercer paso del ejemplo de operación - aplicar multiplicación" />
            <figcaption>
                Por último, aplicamos la multiplicación con los dos operandos existentes en pila (4 y 2). Los dos
                operandos son multiplicados y se inserta en la pila el valor 8.
            </figcaption>
        </figure>

    </section>

    <footer>
        <p>Pablo Argallero Fernández - UO283216</p>
        <img src="multimedia/HTML5.png" alt="HTML válido" />
        <img src="multimedia/CSS3.png" alt="CSS válido" />
    </footer>
</body>

</html>