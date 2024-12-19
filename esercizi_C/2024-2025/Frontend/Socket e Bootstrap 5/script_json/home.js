// Funzione per caricare il file JSON e aggiornare il contenuto
fetch('./json/intro.json')
    .then(response => response.json())  // Converte la risposta in formato JSON
    .then(data => {
        // Aggiornamento del contenuto introduttivo
        // Imposta il titolo del sito
        document.getElementById('site-title').textContent = data.intro.title;

        // Imposta la descrizione introduttiva
        document.getElementById('site-description').textContent = data.intro.description;

        // Aggiornamento della galleria di immagini
        const gallery = document.getElementById('image-gallery');
        
        // Per ogni immagine nel JSON, crea un nuovo elemento nella galleria
        data.gallery.forEach(image => {
            // Crea un elemento contenitore per ogni immagine
            const imageItem = document.createElement('div');
            imageItem.classList.add('image-item');
            
            // Aggiungi il markup HTML per ogni immagine, con overlay di informazioni
            imageItem.innerHTML = `
                <img src="${image.src}" alt="${image.alt}">
                <div class="image-overlay">
                    <h5>${image.title}</h5>
                    <p>${image.description}</p>
                    <a href="${image.link}" class="btn btn-primary">Vai alla pagina</a>
                </div>
            `;
            
            // Aggiungi l'elemento dell'immagine al contenitore della galleria
            gallery.appendChild(imageItem);
        });
    })
    .catch(error => {
        // Se si verifica un errore durante il caricamento del JSON
        console.error('Errore nel caricamento del file JSON:', error);
        
        // Mostra un messaggio di errore nell'elemento del titolo
        document.getElementById('site-title').textContent = 'Errore nel caricamento del contenuto';

        // Mostra un messaggio di errore nella descrizione
        document.getElementById('site-description').textContent = 'Si è verificato un errore durante il caricamento dei dati.';

        // Mostra un messaggio di errore nella galleria
        const gallery = document.getElementById('image-gallery');
        gallery.innerHTML = '<p>Errore nel caricamento delle immagini.</p>';
    });
