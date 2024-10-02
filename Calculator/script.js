const result = document.querySelector('#result');
const numbers = document.querySelectorAll('.numbers');
const operations = document.querySelectorAll('.operations');
const clear = document.querySelector('#clear');
const backspace = document.querySelector('#backspace');
const equals = document.querySelector('#equals');
const dot = document.querySelector('#dot');



numbers.forEach( function(number) {
    number.addEventListener('click', function(event){
        result.value = result.value + number.value;
    });
});

operations.forEach( function(operation) {
    operation.addEventListener('click', function(event) {
        let operators = ['+', '-', '*', '/', '%', '**']
        if ((result.value == '') && (operation.value != '-')) {
            return;
        }
        else if (result.value.includes('+') || 
        result.value.includes('-') || 
        result.value.includes('*') ||
        result.value.includes('/') || 
        result.value.includes('%') || 
        result.value.includes('**') ) {
            let lengthminusone = result.value.length - 1;
            if(result.value[lengthminusone] === '+' || 
            result.value[lengthminusone] === '-' ||  
            result.value[lengthminusone] === '/' || 
            result.value[lengthminusone] === '%' ) {

                result.value = result.value.slice(0,-1);
                result.value = result.value + operation.value;

            }
            else if (result.value.slice(-2) === '**') {
                result.value = result.value.slice(0,-2);
                result.value = result.value + operation.value;
            }

            else if (result.value.slice(-1) === '*' ) {
                result.value = result.value.slice(0,-1);
                result.value = result.value + operation.value;
            }
            else {
                result.value = eval(result.value);
                result.value = result.value + operation.value;
            }
        }
        else {
            result.value = result.value + operation.value;
        }

        if (result.value.length === 1 && result.value != '-')
        {
            result.value = '-';
        }
        
    });
});

dot.addEventListener('click', function(event){
    result.value = result.value + '.';
});

clear.addEventListener('click', function(event){
    result.value = '';
});

backspace.addEventListener('click', function(event){
    result.value = result.value.slice(0,-1);
});

equals.addEventListener('click', function(event) {
    if (result.value.length != 0) {
        let answer = eval(result.value);
        result.value = answer;
    }
});

