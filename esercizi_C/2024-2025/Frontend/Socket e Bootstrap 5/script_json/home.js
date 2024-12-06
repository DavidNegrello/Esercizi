    // Funzione per caricare il file JSON e aggiornare il contenuto
    fetch('./json/intro.json')
        .then(response => response.json())  // Converte la risposta in formato JSON
        .then(data => {
            // Aggiornamento del contenuto introduttivo
            document.getElementById('site-title').textContent = data.intro.title;
            document.getElementById('site-description').textContent = data.intro.description;

            // Aggiornamento della galleria di immagini
            const gallery = document.getElementById('image-gallery');
            data.gallery.forEach(image => {
                const imageItem = document.createElement('div');
                imageItem.classList.add('image-item');
                
                imageItem.innerHTML = `
                    <img src="${image.src}" alt="${image.alt}">
                    <div class="image-overlay">
                        <h5>${image.title}</h5>
                        <p>${image.description}</p>
                        <a href="${image.link}" class="btn btn-primary">Vai alla pagina</a>
                    </div>
                `;
                
                // Aggiungi l'elemento della galleria al contenitore
                gallery.appendChild(imageItem);
            });
        })
        .catch(error => {
            console.error('Errore nel caricamento del file JSON:', error);
            // Gestisci eventuali errori, ad esempio, mostrando un messaggio di errore
            document.getElementById('site-title').textContent = 'Errore nel caricamento del contenuto';
            document.getElementById('site-description').textContent = 'Si è verificato un errore durante il caricamento dei dati.';
            const gallery = document.getElementById('image-gallery');
            gallery.innerHTML = '<p>Errore nel caricamento delle immagini.</p>';
        });