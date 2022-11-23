"use strict";
class Pila {
    constructor() {
        this.pila = new Array();
    }
    push(valor) {
        this.pila.push(valor);
    }
    pop() {
        var poped;
        if (this.pila.length >= 1)
            poped = this.pila.pop();
        else
            poped = NaN; // si no hay valores devolvemos NaN
        return poped;
    }
    getRepresentacion() {
        var stringPila = "";
        for (var i in this.pila) {
            stringPila += this.pila[i] + "\n";
        }

        //devolvemos la representacion de la pila para que sea enseñada
        //por la calculadora
        return stringPila;
    }
    vaciar() {
        //reiniciamos la pila a una vacia
        this.pila = new Array();
    }
}

/**
 * Clase que modela la pareja de valor - base que permite el paso de datos
 * desde la calculadora hasta el PilaManager que maneja las pilas
 */
"use strict";
class Elemento {
    constructor(base, valor) {
        this.base = base;
        this.valor = valor;
    }

    /**
     * Este metodo devuelve el valor en base 10 para ser usado en los calculos de la
     * calculadora. Despues de ser usado para los calculos se devuelve a su base
     * @returns 
     */
    getValorParaCalculos() {
        if (this.base == 10)
            return this.valor;

        //en caso de que no este en base 10 lo pasamos a base 10
        return new Number(parseInt(this.valor, this.base));

    }

    /**
     * Una vez los cálculos se han realizado, nos devuelven un valor en base 10 que
     * ha de ser devuelto a la base que poseía anteriormente
     * @param {String} valor en base 10 
     */
    setValorPostCalculos(valor) {
        this.valor = parseInt(valor, this.base);
    }

}

"use strict";
class PilaManager {
    constructor() {
        this.pOrden = new Pila();
        this.pBase = new Pila();
        this.pValor = new Pila();

    }

    push(elemento) {
        this.pBase.push(elemento.base)
        this.pValor.push(elemento.valor);
        this.pOrden.push(this.pValor.pila.length - 1);
    }
    pop() {
        var popedValue;
        var popedBase;
        if (this.pValor.pila.length >= 1) //nos da igual cual de las pilas miremos
        {
            popedValue = this.pValor.pop();
            popedBase = this.pBase.pop();
        }
        else {
            popedValue = NaN; // si no hay valores devolvemos NaN
            popedBase = NaN;
        }
        //hacemos pop tambien a la pila del order para extraer el valor, 
        //aunque no nos importa lo que devuelva
        this.pOrden.pop();
        return new Elemento(popedBase, popedValue); //devolvemos el elemento
    }
    vaciar() {
        //reiniciamos las pilas
        this.pValor = new Pila();
        this.pBase = new Pila();
        this.pOrden = new Pila();
    }
    /**
     * Actualiza las 3 textareas para que contengan su representacion en string
     */
    actualizarPilas() {
        //actualizamos la pila del orden
        document.querySelector("textarea:nth-of-type(1)").value = this.pOrden.getRepresentacion();

        //actualizamos la pila de la base
        document.querySelector("textarea:nth-of-type(2)").value = this.pBase.getRepresentacion();

        //actualizamos la pila del valor
        document.querySelector("textarea:nth-of-type(3)").value = this.pValor.getRepresentacion();
    }
}



"use strict";
class CalculadoraRPNEspecializada {
    constructor() {
        this.pantalla = "";
        this.pManager = new PilaManager();
        this.currentBase = 10;

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
                this.enter();
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
            case "g":
                this.cambiarBaseDeUltimoValor();
                break;
            case "b":
                this.setCurrentBase(2);
                break;
            case "a":
                this.setCurrentBase(8);
                break;
            case "m":
                this.setCurrentBase(10);
                break;
            case "h":
                this.setCurrentBase(16);
                break;
        }
    }

    /**
     * Extrae un número de la pila, lo cambia de base a la base actual
     * y lo vuelve a introducir en la pila
     */
    cambiarBaseDeUltimoValor() {
        var value = this.solicitarOperandos(1)[0];
        //lo sacamos como decimal y lo insertamos en la base que tengamos seleccionada
        this.insertarValor(new Number(value.getValorParaCalculos()));
    }
    setCurrentBase(base) {
        //ponemos en minuscula la base pasada para que el css la pinte sin destacarla
        var str;
        switch (this.currentBase) {
            case 2:
                str = "bin";
                break;
            case 8:
                str = "oct";
                break;
            case 10:
                str = "dec";
                break;
            case 16:
                str = "hex";
                break;
        }
        document.querySelector("section input[type='button'][value='" + str.toUpperCase() + "']")
            .setAttribute("value", str);

        //actualizamos la variable de la base       
        this.currentBase = base;
        //actualizamos la seleccion visualmente cambiando el boton apretado
        //a mayusculas para que el selector css pueda pintarlo diferente
        switch (this.currentBase) {
            case 2:
                str = "bin";
                break;
            case 8:
                str = "oct";
                break;
            case 10:
                str = "dec";
                break;
            case 16:
                str = "hex";
                break;
        }
        document.querySelector("section input[type='button'][value='" + str + "']")
            .setAttribute("value", str.toUpperCase());
    }

    digitos(digito) {
        if (this.pantalla == "Error") {
            //si hay error, reiniciamos la calculadora
            this.c();
        }

        this.pantalla += digito;
        this.actualizarPantalla();
    }
    punto() {
        this.pantalla += ".";
        this.actualizarPantalla();
    }

    showError() {
        this.pantalla = "Error";
        this.actualizarPantalla();
    }

    solicitarOperandos(n) {
        var res = new Array(); //contiene objetos de la clase Elemento
        for (var i = 0; i < n; i++) {
            //extraemos el proximo elemento
            var element = this.pManager.pop();
            if (Number.isNaN(element.valor)) {
                this.showError();
                return;
            }
            //en caso de que si que tengamos valores los añadimos
            res.push(element);
        }

        //anter de devolver el valor vamos a actualizar las pilas
        this.actualizarPila();
        return res;
    }
    insertarValor(valor) {
        //pasamos el valor a la base deseada -> los valores introducidos por el usuario son siempre en base 10
        var valorMutado = this.cambiarABaseCorrecta(valor);
        //metemos al manager la informacion del valor y la base
        var elem = new Elemento(this.currentBase, valorMutado)
        this.pManager.push(elem);
        this.actualizarPila();
    }

    cambiarABaseCorrecta(valor) {
        return valor.toString(this.currentBase);
    }

    suma() {
        var operands = this.solicitarOperandos(2);
        if (this.pantalla != "Error") {
            var o1 = operands[0].getValorParaCalculos();
            var o2 = operands[1].getValorParaCalculos();
            this.insertarValor(new Number(o1) + new Number(o2));
        }

    }
    resta() {
        var operands = this.solicitarOperandos(2);
        if (this.pantalla != "Error") {
            var o1 = operands[0].getValorParaCalculos();
            var o2 = operands[1].getValorParaCalculos();
            this.insertarValor(new Number(o2) - new Number(o1));
        }
    }
    multiplicacion() {
        var operands = this.solicitarOperandos(2);
        if (this.pantalla != "Error") {
            var o1 = operands[0].getValorParaCalculos();
            var o2 = operands[1].getValorParaCalculos();
            this.insertarValor(new Number(o1) * new Number(o2));
        }
    }
    division() {
        var operands = this.solicitarOperandos(2);
        if (this.pantalla != "Error") {
            var o1 = operands[0].getValorParaCalculos();
            var o2 = operands[1].getValorParaCalculos();
            this.insertarValor(new Number(o2) / new Number(o1));
        }
    }
    porcentaje() {
        var operands = this.solicitarOperandos(2);
        if (this.pantalla != "Error") {
            var o1 = operands[0].getValorParaCalculos();
            var o2 = operands[1].getValorParaCalculos();
            //para calcular el porcentaje calcularemos el o1% de o2
            this.insertarValor(new Number(o1) * (new Number(o2) / 100));
        }
    }
    raiz() {
        var operands = this.solicitarOperandos(1);
        if (this.pantalla != "Error") {
            this.insertarValor(Math.sqrt(new Number(operands[0].getValorParaCalculos()) + ""));
        }
    }

    /**
     * Computa el cuadrado del elemento en pantalla
     */
    pow2() {
        var operandos = this.solicitarOperandos(1);
        if (this.pantalla != "Error")
            this.insertarValor(Math.pow(new Number(operandos[0].getValorParaCalculos()), 2));
    }

    /**
     * Computa x elevado a y
     */
    powY() {
        var operandos = this.solicitarOperandos(2);
        if (this.pantalla != "Error")
            this.insertarValor(Math.pow(new Number(operandos[1].getValorParaCalculos()), new Number(operandos[0].getValorParaCalculos())));
    }

    /**
     * Recibe un valor en grados y lo convierte en radianes
     * @param {Number} value que va a ser convertido
     * @returns el numero en radianes
     */
    convertToRadians(value) {
        return value / (180 / Math.PI);
    }
    sin() {
        var operandos = this.solicitarOperandos(1);
        if (this.pantalla != "Error") {
            operandos[0] = this.convertToRadians(new Number(operandos[0].getValorParaCalculos()));
            let s = Math.sin(operandos[0]);
            var insertar;
            if (this.altPressed) //cosec
                insertar = 1 / s;
            else //tang
                insertar = s;
            this.insertarValor(insertar);
        }
    }
    cos() {
        var operandos = this.solicitarOperandos(1);
        if (this.pantalla != "Error") {
            operandos[0] = this.convertToRadians(new Number(operandos[0].getValorParaCalculos()));
            let c = Math.cos(operandos[0]);
            var insertar;
            if (this.altPressed) //sec
                insertar = 1 / c;
            else //tang
                insertar = c;
            this.insertarValor(insertar);
        }
    }
    tan() {
        var operandos = this.solicitarOperandos(1);
        if (this.pantalla != "Error") {
            operandos[0] = this.convertToRadians(new Number(operandos[0].getValorParaCalculos()));
            var t = Math.tan(operandos[0]);
            var insertar;
            if (this.altPressed) //cotang
                insertar = 1 / t;
            else //tang
                insertar = t;
            this.insertarValor(insertar);
        }
    }
    diezPow() {
        var operandos = this.solicitarOperandos(1);
        if (this.pantalla != "Error")
            this.insertarValor(Math.pow(10, new Number(operandos[0].getValorParaCalculos())));
    }
    log() {
        var operandos = this.solicitarOperandos(1);
        if (this.pantalla != "Error")
            this.insertarValor(Math.log(new Number(operandos[0].getValorParaCalculos())));
    }
    exp() {
        var operandos = this.solicitarOperandos(1);
        if (this.pantalla != "Error")
            this.insertarValor(Math.exp(new Number(operandos[0].getValorParaCalculos())));
    }
    mod() {
        var operandos = this.solicitarOperandos(2);
        if (this.pantalla != "Error") {
            if (new Number(operandos[0].getValorParaCalculos()) == 0) {
                //modulo de 0 da NaN y no queremos eso en la pila
                this.showError(); //enseñamos error
                return; //no hacemos el calculo
            }
            this.insertarValor(new Number(operandos[1].getValorParaCalculos()) % new Number(operandos[0].getValorParaCalculos()));
        }
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
            //modificamos las teclas para que tengan el texto sin cos tan
            document.querySelector("input[type='button'][value='cosec']").setAttribute("value", 'sin');
            document.querySelector("input[type='button'][value='sec']").setAttribute("value", 'cos');
            document.querySelector("input[type='button'][value='cotag']").setAttribute("value", 'tan');
        }
        else {
            //cambiamos el simbolo del boton alt
            document.querySelector("input[type='button'][value='▲']").setAttribute("value", '▼');
            //modificamos las teclas para que tengan el texto cosec sec cotag
            document.querySelector("input[type='button'][value='sin']").setAttribute("value", 'cosec');
            document.querySelector("input[type='button'][value='cos']").setAttribute("value", 'sec');
            document.querySelector("input[type='button'][value='tan']").setAttribute("value", 'cotag');
        }
        this.altPressed = !this.altPressed;
    }
    pi() {
        this.insertarValor(Math.PI);
    }
    fact() {
        var operandos = this.solicitarOperandos(1);
        if (this.pantalla != "Error")
            this.insertarValor(this.factorialRecursivo(new Number(operandos[0].getValorParaCalculos())));
    }
    factorialRecursivo(n) {
        if (n == 0) {
            return 1;
        }
        return n * this.factorialRecursivo(n - 1);
    }
    del() {
        //si tenemos mensaje de error lo borramos entero
        if (this.pantalla == "Error") {
            this.pantalla = "";
            this.actualizarPantalla();
            return;
        }
        this.pantalla = this.pantalla.substring(0, this.pantalla.length - 1);
        this.actualizarPantalla();
    }

    //-----------------------------------------


    c() {
        //elimina toda la operacion 
        this.limpiarPantalla();
        //limpiamos la pila
        this.pManager.vaciar();
        //repintamos el text area con la pila vacia
        this.actualizarPila();

    }
    ce() {
        //limpia el número en pantalla sin guardar su valor
        this.limpiarPantalla();
    }
    enter() {
        //si tenemos error en pantalla no insertamos nada
        if (this.pantalla == "Error")
            return
        //si tenemos un valor en pantalla lo pusheamos
        if (this.pantalla != "")
            this.insertarValor(new Number(this.pantalla));

        this.limpiarPantalla();
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
    actualizarPila() {
        this.pManager.actualizarPilas();
    }
    limpiarPantalla() {
        this.pantalla = "";
        this.actualizarPantalla();
    }
}
let calculadora = new CalculadoraRPNEspecializada();