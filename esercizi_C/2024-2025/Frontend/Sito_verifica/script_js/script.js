

// Funzione per avviare il timer
function startTimer(duration, display) {
    let timer = duration, minutes, seconds;
    setInterval(function () {
        minutes = parseInt(timer / 60, 10);
        seconds = parseInt(timer % 60, 10);
        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;

        display.textContent = minutes + ":" + seconds;

        if (--timer < 0) {
            timer = duration;
        }
    }, 1000);
}

// Timer iniziale
document.addEventListener('DOMContentLoaded', function () {
    let timeLimit = 10 * 60; // 10 minuti
    let display = document.querySelector('#countdown');
    startTimer(timeLimit, display);
});
