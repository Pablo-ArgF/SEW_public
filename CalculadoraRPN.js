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
            stringPila += (this.pila.length - i - 1) + ":\t\t" + this.pila[i] + "\n";
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



"use strict";
class CalculadoraRPN {
    constructor() {
        this.pantalla = "";
        this.pila = new Pila();

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
        }
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
        var res = new Array();
        for (var i = 0; i < n; i++) {
            var poped = this.pila.pop();
            if (Number.isNaN(poped)) {
                this.showError();
                return;
            }
            res.push(new Number(poped));
        }
        return res;
    }
    insertarValor(valor) {
        this.pila.push(valor + "");
        this.actualizarPila();
    }

    suma() {
        var operands = this.solicitarOperandos(2);
        if (this.pantalla != "Error") {
            var o1 = operands[0];
            var o2 = operands[1];
            this.insertarValor(o1 + o2);
        }

    }
    resta() {
        var operands = this.solicitarOperandos(2);
        if (this.pantalla != "Error") {
            var o1 = operands[0];
            var o2 = operands[1];
            this.insertarValor(o2 - o1);
        }
    }
    multiplicacion() {
        var operands = this.solicitarOperandos(2);
        if (this.pantalla != "Error") {
            var o1 = operands[0];
            var o2 = operands[1];
            this.insertarValor(o1 * o2);
        }
    }
    division() {
        var operands = this.solicitarOperandos(2);
        if (this.pantalla != "Error") {
            var o1 = operands[0];
            var o2 = operands[1];
            this.insertarValor(o2 / o1);
        }
    }
    porcentaje() {
        var operands = this.solicitarOperandos(2);
        if (this.pantalla != "Error") {
            var o1 = operands[0];
            var o2 = operands[1];
            //para calcular el porcentaje calcularemos el o1% de o2
            this.insertarValor(o1 * (o2 / 100));
        }
    }
    raiz() {
        var operands = this.solicitarOperandos(1);
        if (this.pantalla != "Error") {
            this.insertarValor(Math.sqrt(operands[0]) + "");
        }
    }

    /**
     * Computa el cuadrado del elemento en pantalla
     */
    pow2() {
        var operandos = this.solicitarOperandos(1);
        if (this.pantalla != "Error")
            this.insertarValor(Math.pow(operandos[0], 2));
    }

    /**
     * Computa x elevado a y
     */
    powY() {
        var operandos = this.solicitarOperandos(2);
        if (this.pantalla != "Error")
            this.insertarValor(Math.pow(operandos[1], operandos[0]));
    }

    /**
     * Recibe un valor en grados y lo convierte en radianes
     * @param {Number} value que va a ser convertido
     * @returns el numero en radianes
     */
    convertToRadians(value){
        return value / (180 /Math.PI);
    }
    sin() {
        var operandos = this.solicitarOperandos(1);
        if (this.pantalla != "Error") {
            operandos[0] = this.convertToRadians(operandos[0]);
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
            operandos[0] = this.convertToRadians(operandos[0]);
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
            operandos[0] = this.convertToRadians(operandos[0]);
            var t = Math.tan(operandos[0]);
            var insertar;
            if (this.altPressed) //cotang
                insertar = 1 / t;
            else //tang
                insertar = t;
            this.insertarValor(insertar);
        } P
    }
    sqrt() {
        var operandos = this.solicitarOperandos(1);
        if (this.pantalla != "Error")
            this.insertarValor(Math.sqrt(operandos[0]));
    }
    diezPow() {
        var operandos = this.solicitarOperandos(1);
        if (this.pantalla != "Error")
            this.insertarValor(Math.pow(10, operandos[0]));
    }
    log() {
        var operandos = this.solicitarOperandos(1);
        if (this.pantalla != "Error")
            this.insertarValor(Math.log(operandos[0]));
    }
    exp() {
        var operandos = this.solicitarOperandos(1);
        if (this.pantalla != "Error")
            this.insertarValor(Math.exp(operandos[0]));
    }
    mod() {
        var operandos = this.solicitarOperandos(2);
        if (this.pantalla != "Error"){
            if(operandos[0] == 0){ 
                //modulo de 0 da NaN y no queremos eso en la pila
                this.showError(); //enseñamos error
                return; //no hacemos el calculo
            }
            this.insertarValor(operandos[1] % operandos[0]);
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
            this.insertarValor(this.factorialRecursivo(operandos[0]));
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
        this.pila.vaciar();
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
            this.pila.push(new Number(this.pantalla));

        this.actualizarPila();
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
        var stringPila = this.pila.getRepresentacion();
        //actualizamos el elemento textarea del documento para contener la
        //representacion de la pila
        document.querySelector("textarea").value = stringPila + "";
    }
    limpiarPantalla() {
        this.pantalla = "";
        this.actualizarPantalla();
    }


}
let calculadora = new CalculadoraRPN();
