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

        document.addEventListener('keydown', (event) => {
            var keyName = event.key;
            this.mapKeywordInput(keyName);
        });
    }

    mapKeywordInput(keyName) {
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
                calculadora.digitos(keyName)
                break;
            case ".":
                calculadora.punto();
                break;
            case " ":
                calculadora.igual();
                break;
            case "+":
                calculadora.suma();
                break;
            case "-":
                calculadora.resta();
                break;
            case "*":
                calculadora.multiplicacion();
                break;
            case "/":
                calculadora.division();
                break;
            case "%":
                calculadora.porcentaje();
                break;
            case "r":
                calculadora.raiz();
                break;
            case "s":
                calculadora.cambioDeSigno();
                break;
            case "c":
                calculadora.c();
                break;
            case "e":
                calculadora.ce();
                break;
            case "m":
                calculadora.mrc();
                break;
            case "a":
                calculadora.mMas();
                break;
            case "n":
                calculadora.mMenos();
                break;
        }
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
let calculadora = new Calculadora();
