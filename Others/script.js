const display = document.getElementById("display");

function clearDisplay(){
  display.value = '';
}

function appendToDisplay(value) {
  display.value += value;
}

function calculate() {
  try {
    display.value = eval(display.value);
  } catch {
    display.value = "Error";
  }
}