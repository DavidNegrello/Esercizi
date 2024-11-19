let timeLeft = 600; // 10 minuti in secondi
const countdownElement = document.getElementById('countdown');

function updateTimer() {
    let minutes = Math.floor(timeLeft / 60);
    let seconds = timeLeft % 60;
    if (seconds < 10) {
        seconds = '0' + seconds;
    }
    countdownElement.textContent = `${minutes}:${seconds}`;
    if (timeLeft > 0) {
        timeLeft--;
    }
}

setInterval(updateTimer, 1000);
