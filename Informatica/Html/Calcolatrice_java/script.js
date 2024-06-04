const buttons = document.querySelectorAll('.calculator-grid button');
const display = document.getElementById('display');

  buttons.forEach(button => {
    button.addEventListener('click', () => {
      const buttonText = button.textContent;
      //fa tutti gli inserimenti tranne quelli nella condizione
      if (buttonText!== '=' && buttonText!== 'Canc') {
        display.value += buttonText;
      }
      //cancella l'input precedente utilizzando le stringhe
      else if(buttonText=='Canc'){
        display.value = display.value.substring(0, display.value.length - 1);
      }
    });
  });