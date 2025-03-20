//===================CARRELLO_NAVBAR====================
// Funzione per aggiornare il contatore del carrello nella navbar
function aggiornaContatoreCarrello() {
    fetch('../api/cart_actions.php?action=count')
        .then(response => response.json())
        .then(data => {
            const counterElement = document.getElementById("carrello-counter");
            if (counterElement) {
                counterElement.textContent = data.count > 0 ? data.count : "";
            }
        })
        .catch(error => console.error("Errore nell'aggiornamento del contatore:", error));
}

// Aggiorna il contatore quando la pagina viene caricata
document.addEventListener("DOMContentLoaded", function() {
    aggiornaContatoreCarrello();
    
    //====================FOOTER======================
    fetch("../api/get_footer.php")
        .then(response => response.json())
        .then(data => {
            // Social media
            let socialHtml = "";
            data.social.forEach(social => {
                socialHtml += `<a href="${social.link}" class="text-light me-3"><i class="${social.icon} fa-lg"></i></a>`;
            });
            
            const footerSocial = document.getElementById("footer-social");
            if (footerSocial) {
                footerSocial.innerHTML = socialHtml;
            }

            // Email
            const footerEmail = document.getElementById("footer-email");
            if (footerEmail) {
                footerEmail.textContent = data.email;
                footerEmail.href = `mailto:${data.email}`;
            }

            // Copyright
            const footerCopyright = document.getElementById("footer-copyright");
            if (footerCopyright) {
                footerCopyright.innerHTML = data.copyright;
            }
        })
        .catch(error => console.error("Errore nel caricamento del footer:", error));
});