// Script per mostrare/nascondere password
document.getElementById('showPassword').addEventListener('click', function() {
    togglePasswordVisibility('password', this);
});

document.getElementById('showConfirmPassword').addEventListener('click', function() {
    togglePasswordVisibility('conferma_password', this);
});

function togglePasswordVisibility(fieldId, button) {
    const passwordField = document.getElementById(fieldId);
    const icon = button.querySelector('i');

    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passwordField.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Controllo forza password
function checkPasswordStrength(password) {
    const strength = document.getElementById('passwordStrength');
    let strengthValue = 0;

    // Criteri di forza
    if (password.length >= 8) strengthValue += 25;
    if (password.match(/[A-Z]/)) strengthValue += 25;
    if (password.match(/[0-9]/)) strengthValue += 25;
    if (password.match(/[^A-Za-z0-9]/)) strengthValue += 25;

    // Colore basato sulla forza
    if (strengthValue <= 25) {
        strength.style.width = '25%';
        strength.style.backgroundColor = '#dc3545'; // Rosso
    } else if (strengthValue <= 50) {
        strength.style.width = '50%';
        strength.style.backgroundColor = '#ffc107'; // Giallo
    } else if (strengthValue <= 75) {
        strength.style.width = '75%';
        strength.style.backgroundColor = '#fd7e14'; // Arancione
    } else {
        strength.style.width = '100%';
        strength.style.backgroundColor = '#198754'; // Verde
    }
}

// Verifica corrispondenza password
function checkPasswordMatch() {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('conferma_password').value;
    const message = document.getElementById('passwordMatchMessage');

    if (confirmPassword === '') {
        message.textContent = '';
    } else if (password === confirmPassword) {
        message.textContent = 'Le password corrispondono';
        message.style.color = '#198754'; // Verde
    } else {
        message.textContent = 'Le password non corrispondono';
        message.style.color = '#dc3545'; // Rosso
    }
}