"use strict";
class Calculadora {
    constructor() {
        this.pantalla = "";
        this.memoria = 0;
        this.operacionActual = "";
        this.acumuladorGuardado = "";
        this.operadorOFuncionInsertado = false;
        this.previaStringInsertada = "";//usado para las funciones
        this.igualPresionado = false;
    }
    digitos(digito) {
        if (this.igualPresionado) {
            this.igualPresionado = false;
            this.pantalla = "";
        }
        this.pantalla += digito;
        this.actualizarPantalla();
        this.operadorOFuncionInsertado = false;
    }
    punto() {
        this.pantalla += ".";
        this.actualizarPantalla();
    }
    suma() {
        this.insertarOperador("+");
    }
    resta() {
        this.insertarOperador("-");
    }
    multiplicacion() {
        this.insertarOperador("*");
    }
    division() {
        this.insertarOperador("/");
    }
    porcentaje() {

        //this.limpiarPantallaGuardandoValor();
        this.insertarFuncion(this.pantalla + "%");
    }
    raiz() {
        var valorPantalla = new Number(this.pantalla);
        if (this.pantalla == "")
            valorPantalla = 0;
        var raizCalculada = Math.sqrt(valorPantalla);
        this.pantalla = raizCalculada.toString();
        this.actualizarPantalla();

    }
    insertarOperador(operador) {
        //guardamos el valor en pantalla
        this.limpiarPantallaGuardandoValor();

        if (this.operadorOFuncionInsertado) {
            //eliminamos el operador que se acaba de insertar y ponemos el que nos pasan
            this.acumuladorGuardado = this.acumuladorGuardado.substring(0, this.acumuladorGuardado.length - 2) + operador;
        }
        else
            this.acumuladorGuardado += operador;
        this.operadorOFuncionInsertado = true;

        this.actualizarPantalla();
    }

    insertarFuncion(funcion) { // entendemos como funcion cualquier operador que ocupe mas de un caracter

        if (this.operadorOFuncionInsertado) {
            //eliminamos la funcion que se acaba de insertar y ponemos el que nos pasan
            this.acumuladorGuardado = this.acumuladorGuardado.substring(0, this.acumuladorGuardado.length - 2 - this.previaStringInsertada.length - 1) + funcion;
        }
        else
            this.acumuladorGuardado += funcion + this.previaStringInsertada;
        this.operadorOFuncionInsertado = true;
        this.previaStringInsertada = funcion + this.previaStringInsertada;
    }
    c() {
        //elimina toda la operacion 
        this.limpiarPantalla();
        this.operacionActual = "";
        this.acumuladorGuardado = "";
        this.previaStringInsertada = "";

    }
    ce() {
        //limpia el número en pantalla sin guardar su valor
        this.limpiarPantalla();
    }
    mrc() {
        this.pantalla = this.memoria;
        this.actualizarPantalla();
    }
    mMenos() {
        this.memoria = new Number(eval(this.memoria - new Number(this.pantalla)));
    }
    mMas() {
        this.memoria = new Number(eval(this.memoria + new Number(this.pantalla)));
    }
    igual() {

        if (this.pantalla != "")
            this.acumuladorGuardado += new Number(this.pantalla);
        //si lo ultimo introducido es una operacion o funcion
        try {
            this.pantalla = new Number(eval(this.acumuladorGuardado)).toString();
        }
        catch (error) {
            this.pantalla = "Error";
            this.acumuladorGuardado = "";
        }
        if (this.pantalla == "NaN")
            this.pantalla = "Error";
        this.actualizarPantalla();
        this.acumuladorGuardado = "";
        this.igualPresionado = true;

    }
    cambioDeSigno() {
        if (this.pantalla == "") //si no hay un valor en la pantalla no cambiamos el signo
            return;
        if (this.pantalla.charAt(0) == "-")
            this.pantalla = this.pantalla.substring(1);
        else
            this.pantalla = "-" + this.pantalla;

        this.actualizarPantalla();
    }

    actualizarPantalla() {
        document
            .querySelector("input[type='text']")
            .setAttribute("value", this.pantalla + "");
    }

    limpiarPantallaGuardandoValor() {

        var valorPantalla = new Number(this.pantalla);
        this.previaStringInsertada = this.pantalla;
        this.acumuladorGuardado += valorPantalla;

        this.limpiarPantalla();
    }
    limpiarPantalla() {
        this.pantalla = "";
        this.actualizarPantalla();
    }


}
"use strict";
class CalculadoraCientifica extends Calculadora {
    constructor() {
        super();
        this.altPressed = false;
        this.funcion = "";
        this.funcionDeDosOperandos = false;
        this.radianes = true; /*por default tratamos los angulos con radianes*/
        this.hyperbolic = false;

        document.addEventListener('keydown', (event) => {
            const keyName = event.key;
            this.mapKeyWorkInput(keyName);
        });
    }

    mapKeyWorkInput(keyName) {
        switch (keyName) {
            case "1":
            case "2":
            case "3":
            case "4":
            case "5":
            case "6":
            case "7":
            case "8":
            case "9":
            case "0":
                this.digitos(keyName)
                break;
            case " ":
                this.igual();
                break;
            case "+":
                this.suma();
                break;
            case "-":
                this.resta();
                break;
            case "*":
                this.multiplicacion();
                break;
            case "/":
                this.division();
                break;
            case "%":
                this.porcentaje();
                break;
            case "r":
                this.raiz();
                break;
            case "s":
                this.cambioDeSigno();
                break;
            case "c":
                this.c();
                break;
            case "e":
                this.ce();
                break;
            case "m":
                this.mrc();
                break;
            case "a":
                this.mMas();
                break;
            case "n":
                this.mMenos();
                break;
            case ".":
                this.punto();
                break;
            case ".":
                this.punto();
                break;
            case "w":
                this.pow2();
                break;
            case "y":
                this.powY();
                break;
            case "i":
                this.sin();
                break;
            case "o":
                this.cos();
                break;
            case "t":
                this.tan();
                break;
            case "d":
                this.diezPow();
                break;
            case "l":
                this.log();
                break;
            case "x":
                this.exp();
                break;
            case "u":
                this.mod();
                break;
            case "Alt":
                this.alt();
                break;
            case "Backspace":
                this.del();
                break;
            case "p":
                this.pi();
                break;
            case "f":
                this.fact();
                break;
            case "(":
                this.leftParentesis();
                break;
            case ")":
                this.rightParentesis();
                break;
            case "º":
                this.changeDegRad();
                break;
            case "h":
                this.hyper();
                break;
            case "^":
                this.notacionCientifica();
                break;
        }
    }

    notacionCientifica() {
        this.pantalla = new Number(this.pantalla).toExponential(5);
        this.actualizarPantalla();
    }

    hyper() {
        this.actualizarBotonesHyper();
        this.hyperbolic = !this.hyperbolic;

    }

    actualizarBotonesHyper() {
        if (this.altPressed) {
            if (this.hyperbolic) //buscamos por las strings en hyper para devolverlas a sin
            {
                //modificamos las teclas para que tengan el texto cosec sec cotag
                document.querySelector("input[type='button'][value='invsinh']").setAttribute("value", 'cosec');
                document.querySelector("input[type='button'][value='invcosh']").setAttribute("value", 'sec');
                document.querySelector("input[type='button'][value='invtanh']").setAttribute("value", 'cotag');
            }
            else {
                //modificamos las teclas para que tengan el texto invsinh, invcosh y invtanh
                document.querySelector("input[type='button'][value='cosec']").setAttribute("value", 'invsinh');
                document.querySelector("input[type='button'][value='sec']").setAttribute("value", 'invcosh');
                document.querySelector("input[type='button'][value='cotag']").setAttribute("value", 'invtanh');
            }
        }
        else {
            if (this.hyperbolic) //buscamos por las strings en hyper para devolverlas a sin
            {
                document.querySelector("input[type='button'][value='sinh']").setAttribute("value", 'sin');
                document.querySelector("input[type='button'][value='cosh']").setAttribute("value", 'cos');
                document.querySelector("input[type='button'][value='tanh']").setAttribute("value", 'tan');
            }
            else {
                //modificamos las teclas para que tengan el texto  sinh, cosh y tanh
                document.querySelector("input[type='button'][value='sin']").setAttribute("value", 'sinh');
                document.querySelector("input[type='button'][value='cos']").setAttribute("value", 'cosh');
                document.querySelector("input[type='button'][value='tan']").setAttribute("value", 'tanh');
            }
        }
    }

    actualizarBotonesAlt() {
        if (this.hyperbolic) {
            if (this.altPressed) {
                document.querySelector("input[type='button'][value='invsinh']").setAttribute("value", 'sinh');
                document.querySelector("input[type='button'][value='invcosh']").setAttribute("value", 'cosh');
                document.querySelector("input[type='button'][value='invtanh']").setAttribute("value", 'tanh');
            }
            else {
                document.querySelector("input[type='button'][value='sinh']").setAttribute("value", 'invsinh');
                document.querySelector("input[type='button'][value='cosh']").setAttribute("value", 'invcosh');
                document.querySelector("input[type='button'][value='tanh']").setAttribute("value", 'invtanh');
            }
        }
        else {
            if (this.altPressed) {
                document.querySelector("input[type='button'][value='cosec']").setAttribute("value", 'sin');
                document.querySelector("input[type='button'][value='sec']").setAttribute("value", 'cos');
                document.querySelector("input[type='button'][value='cotag']").setAttribute("value", 'tan');

            }
            else {
                document.querySelector("input[type='button'][value='sin']").setAttribute("value", 'cosec');
                document.querySelector("input[type='button'][value='cos']").setAttribute("value", 'sec');
                document.querySelector("input[type='button'][value='tan']").setAttribute("value", 'cotag');
            }
        }
    }

    /**
     * Cambia el tipo de input a decimal o a radianes
     */
    changeDegRad() {
        this.radianes = !this.radianes;
        //cambiamos el boton para que ponga el valor que debe
        this.actualizarBotonDegRad();
    }

    /**
     * Actuliza el boton que permite seleccionar si estamos con degrees o con radianes
     */
    actualizarBotonDegRad() {
        if (this.radianes)
            document.querySelector("input[type='button'][value='deg']").value = "rad";
        else
            document.querySelector("input[type='button'][value='rad']").value = "deg";
    }

    /**
     * Computa el cuadrado del elemento en pantalla
     */
    pow2() {
        this.pantalla = Math.pow(new Number(this.pantalla), 2);
        this.actualizarPantalla();
    }

    /**
     * Computa x elevado a y
     */
    powY() {
        this.ejecutarOperacionDosOperandos("Math.pow(");
    }

    /**
     * Este metodo es usado por las operaciones que requieren dos operandos como 
     * puede ser el mod o la operacion x^y. Este prepara las variables para que tanto 
     * en la introduccion de una operacion como en la presion del igual se precompute
     * la operacion de dos operandos antes de continuar
     * @param {string} comando 
     */
    ejecutarOperacionDosOperandos(comando) {
        this.funcion = comando;
        this.previaStringInsertada = this.pantalla; //guardamos el primer operando
        this.limpiarPantalla(); //limpiamos la pantalla sin guardar a acumulador

        this.funcionDeDosOperandos = true;
    }

    /**
     * Es usada para recivir las operaciones simples como +-* /
     * para meter la lógica de comprobaciond e funciones con dos parametros
     * antes de llamar al método de la clase padre
     * @param {string} operador usado para la operacion
     */
    insertarOperacionSimple(operador) {

        this.procesarPosibleFuncionDosParametros()
        super.insertarOperador(operador);
        this.actualizarCuentaEntera();
    }

    procesarPosibleFuncionDosParametros() {
        //si hay una funcion de dos parametros pendientes hacemos la operacion antes de insertar el operando
        if (this.funcionDeDosOperandos) {
            //calculamos el valor 
            //el uso de eval en este caso es seguro ya que el usuario no introduce comandos, solo 2 números
            //y recibimos del parametro la funcion a hacer
            var value = eval(this.funcion + this.previaStringInsertada + "," + new Number(this.pantalla) + ")");
            //insertamos a la operacion actual el valor calculado
            this.acumuladorGuardado += value;

            this.funcionDeDosOperandos = false; //desmarcamos la flag
            //limpiamos la pantalla del anterior operando
            this.pantalla = "";
            this.actualizarPantalla();
        }
    }

    /**
     * Recibe un valor en grados y lo convierte en radianes si la calculadora
     * esta en estado radianes
     * @param {Number} value que va a ser convertido si estamo sen radianes
     * @returns el numero en radianes si la calculadora esta en radianes o en degrees
     * si la calculadora no esta en radianes
     */
    convertToDegreesIfNeeded(value) {
        if (this.radianes) //si estamos en radianes pasamos el valor sin mas
            return value;
        else //si estamos en deg vamos a pasar el valor a radianes
            return (value * Math.PI) / 180;
    }

    sin() {
        let s = Math.sin(this.convertToDegreesIfNeeded(new Number(this.pantalla)));
        let sInversed = Math.sinh(this.convertToDegreesIfNeeded(new Number(this.pantalla)));
        if (this.altPressed) { //cosec
            if (this.hyperbolic) {
                this.pantalla = 1 / sInversed;
            }
            else
                this.pantalla = 1 / s;
        }
        else { //seno
            if (this.hyperbolic) {
                this.pantalla = sInversed;
            }
            else
                this.pantalla = s;
        }
        this.actualizarPantalla();
    }
    cos() {
        let c = Math.cos(this.convertToDegreesIfNeeded(new Number(this.pantalla)));
        let cInversed = Math.cosh(this.convertToDegreesIfNeeded(new Number(this.pantalla)));
        if (this.altPressed) //sec
            if (this.hyperbolic) {
                this.pantalla = 1 / cInversed;
            }
            else
                this.pantalla = 1 / c;
        else //cos
            if (this.hyperbolic) {
                this.pantalla = cInversed;
            }
            else
                this.pantalla = c;
        this.actualizarPantalla();
    }
    tan() {
        var t = Math.tan(this.convertToDegreesIfNeeded(new Number(this.pantalla)));
        let tInversed = Math.tanh(this.convertToDegreesIfNeeded(new Number(this.pantalla)));
        if (this.altPressed) //cotang
            if (this.hyperbolic) {
                this.pantalla = 1 / cInversed;
            }
            else
                this.pantalla = 1 / t;
        else //tang
            if (this.hyperbolic) {
                this.pantalla = tInversed;
            }
            else
                this.pantalla = t;
        this.actualizarPantalla();
    }
    sqrt() {
        this.pantalla = Math.sqrt(new Number(this.pantalla));
        this.actualizarPantalla();
    }
    diezPow() {
        this.pantalla = Math.pow(10, new Number(this.pantalla));
        this.actualizarPantalla();
    }
    log() {
        this.pantalla = Math.log(new Number(this.pantalla));
        this.actualizarPantalla();
    }
    exp() {
        this.pantalla = Math.exp(new Number(this.pantalla));
        this.actualizarPantalla();
    }
    mod() {
        //hacemos que al ejecutar la funcion de dos parametros ejecute la funcion propia de modulo
        this.ejecutarOperacionDosOperandos('this.calcularModulo(');
    }
    calcularModulo(a, b) {
        return a % b;
    }
    /**
     * modifica las teclas sin cos tan para representar 
     * cosecante secante y contangente
     */
    alt() {
        //comprobamos si el alt esta presionado
        if (this.altPressed) {
            //cambiamos el simbolo del boton alt
            document.querySelector("input[type='button'][value='▼']").setAttribute("value", '▲');
        }
        else {
            //cambiamos el simbolo del boton alt
            document.querySelector("input[type='button'][value='▲']").setAttribute("value", '▼');
        }
        //actualizamos los botones subsceptibles de ser hyperbolicos y accesibles con alt
        this.actualizarBotonesAlt();
        this.altPressed = !this.altPressed;
    }
    pi() {
        this.pantalla = new Number(Math.PI);
        this.actualizarPantalla();
    }
    fact() {

        this.pantalla = new Number(this.factorialRecursivo(new Number(this.pantalla))) + "";
        this.actualizarPantalla();
    }
    factorialRecursivo(n) {
        if (n == 0) {
            return 1;
        }
        return n * this.factorialRecursivo(n - 1);
    }
    del() {
        this.pantalla = (this.pantalla + "").substring(0, this.pantalla.length - 1);
        this.actualizarPantalla();
    }
    leftParentesis() {
        this.acumuladorGuardado += "(";
        this.limpiarPantallaGuardandoValor();
        this.actualizarCuentaEntera();

    }
    rightParentesis() {
        this.limpiarPantallaGuardandoValor();
        this.acumuladorGuardado += ")";
        this.actualizarCuentaEntera();
    }

    /**
     * Sobreescribo el método de limpiar pantalla para que no compute
     * al limpiar sino que guarde la string con toda la operacion
     */
    limpiarPantallaGuardandoValor() {
        if (this.pantalla != "") {

            this.previaStringInsertada = this.pantalla;
            this.acumuladorGuardado += this.pantalla;

            this.limpiarPantalla();
            this.actualizarCuentaEntera();
        }
    }

    /**
     * Este metodo actualiza la pantalla superior de la calculadora cientifica para
     * que esta enseñe el contenido del acumulador guardado hasta el momento
     */
    actualizarCuentaEntera() {
        document
            .querySelector("input[type='text']:first-of-type")
            .setAttribute("value", this.acumuladorGuardado);
    }

    /**
     * El limpiar pantalla ha de ser tambien overrideado ya que ha de llamar al actualizar 
     * pantalla de la cientifica
     */
    limpiarPantalla() {
        this.pantalla = "";
        this.actualizarPantalla();
    }

    /**
     * Sobreescribimos tambien el actualizar de la pantalla con los numeros introducidos
     * por el usuario ya que el selector ha de coger ahora el ultimo de los campos txt del html
     */
    actualizarPantalla() {
        document
            .querySelector("label:nth-of-type(2) input[type='text']")
            .setAttribute("value", this.pantalla + "");
    }

    /**
     * Overrideamos el igual para que actualize la pantalla que contiene toda la operacion
     */
    igual() {
        //primero acabamos con una posible funcion de dos parametros
        this.procesarPosibleFuncionDosParametros();
        if (this.pantalla != "")
            this.acumuladorGuardado += new Number(this.pantalla);
        //si lo ultimo introducido es una operacion o funcion
        if (this.operadorOFuncionInsertado) {
            //lo eliminamos del acumulador
            this.acumuladorGuardado = this.acumuladorGuardado.substring(0, this.acumuladorGuardado.length - 1 - this.previaStringInsertada.length);
        }
        try {
            this.pantalla = new Number(eval(this.acumuladorGuardado)).toString();
        }
        catch (error) {
            this.pantalla = "Error";
            this.acumuladorGuardado = "";
        }
        if (this.pantalla == "NaN") {
            this.pantalla = "Error";
            this.acumuladorGuardado = "";
        }
        this.actualizarPantalla();
        //ponemos la operacion entera en la pantalla superior
        this.actualizarCuentaEntera();
        this.acumuladorGuardado = "";
        this.igualPresionado = true;
    }

    /**
     * Overrideamos el C para que actualice la pantalla con toda la cuenta
     */
    c() {
        super.c();
        this.funcion = "";
        this.funcionDeDosOperandos = false;
        //actualizamos la pantalla
        this.actualizarCuentaEntera();
    }

    /**
     * Limpia la memoria de la calculadora
     */
    mc() {
        this.memoria = 0;
    }

    /**
     * Guarda el valor en pantalla en la memoria
     * sobrescribe el valor anterior de la memoria
     */
    mStore() {
        this.memoria = new Number(this.pantalla);
        this.limpiarPantalla();
    }

    /**
     * Overrideamos la insercion de operador para que actualice tambien la pantalla 
     * con la cuenta entera
     */
    insertarOperador(operador) {
        super.insertarOperador(operador);
        this.actualizarCuentaEntera();
    }


}


let calculadora = new CalculadoraCientifica();


