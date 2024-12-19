// Aggiunge un listener per l'evento DOMContentLoaded, che si attiva quando il DOM è completamente carico
document.addEventListener("DOMContentLoaded", function() {
    // Carica il file JSON 'info.json' tramite fetch
    fetch('../json/info.json')
        .then(response => response.json())  // Converte la risposta in formato JSON
        .then(data => {
            // Carica il titolo dalla proprietà 'title' nel JSON e lo inserisce nell'elemento con id 'title'
            document.getElementById('title').textContent = data.title;

            // Carica l'immagine dalla proprietà 'image' nel JSON e la inserisce nell'elemento con id 'image'
            document.getElementById('image').src = data.image;

            // Carica l'indice (una lista di collegamenti) dalla proprietà 'sections' nel JSON
            let indiceList = document.getElementById('indice-list');
            data.sections.forEach(section => {
                // Crea un elemento <li> per ogni sezione e aggiunge un link che punta alla sezione corrispondente
                let listItem = document.createElement('li');
                listItem.innerHTML = `<a href="#${section.id}">${section.title}</a>`;
                indiceList.appendChild(listItem);
            });

            // Carica il contenuto delle sezioni
            let contentSections = document.getElementById('content-sections');
            data.sections.forEach(section => {
                // Crea una sezione <section> per ogni sezione nel JSON, con un id e una classe
                let sectionDiv = document.createElement('section');
                sectionDiv.id = section.id;
                sectionDiv.classList.add('content-section');
                
                // Crea un titolo per la sezione e lo aggiunge alla sezione
                let sectionTitle = document.createElement('h2');
                sectionTitle.textContent = section.title;
                sectionDiv.appendChild(sectionTitle);
                
                // Aggiunge ogni paragrafo della sezione al contenuto della sezione
                section.content.forEach(paragraph => {
                    let p = document.createElement('p');
                    // Aggiunge i collegamenti al glossario all'interno del paragrafo
                    p.innerHTML = addGlossaryLinks(paragraph); 
                    sectionDiv.appendChild(p);
                });

                // Aggiunge la sezione al contenitore principale delle sezioni
                contentSections.appendChild(sectionDiv);
            });
        });
});

// Funzione per aggiungere i collegamenti ai termini nel glossario
function addGlossaryLinks(content) {
    // Utilizza espressioni regolari per cercare i termini specifici nel contenuto e sostituirli con i collegamenti
    content = content.replace(/IPC/g, "<a href='../pagine/glossario.html#term1' target='_blank'>IPC</a>");
    content = content.replace(/TCP/g, "<a href='../pagine/glossario.html#term2' target='_blank'>TCP</a>");
    content = content.replace(/AF_INET/g, "<a href='../pagine/glossario.html#term3' target='_blank'>AF_INET</a>");
    content = content.replace(/AF_UNIX/g, "<a href='../pagine/glossario.html#term4' target='_blank'>AF_UNIX</a>");
    content = content.replace(/ISO/g, "<a href='../pagine/glossario.html#term5' target='_blank'>ISO</a>");
    content = content.replace(/Interoperabilità/g, "<a href='../pagine/glossario.html#term6' target='_blank'>Interoperabilità</a>");
    content = content.replace(/Layer/g, "<a href='../pagine/glossario.html#term7' target='_blank'>Layer</a>");
    content = content.replace(/TLS/g, "<a href='../pagine/glossario.html#term8' target='_blank'>TLS</a>");
    content = content.replace(/SSL/g, "<a href='../pagine/glossario.html#term9' target='_blank'>SSL</a>");
    content = content.replace(/Privacy/g, "<a href='../pagine/glossario.html#term10' target='_blank'>Privacy</a>");
    content = content.replace(/Man-in-the-Middle/g, "<a href='../pagine/glossario.html#term11' target='_blank'>Man-in-the-Middle</a>");
    return content;  // Restituisce il contenuto con i collegamenti al glossario
}

// Funzione per visualizzare o nascondere l'indice (una lista di collegamenti alle sezioni)
function toggleIndice() {
    let indiceBox = document.getElementById('indice-box');
    // Se l'indice è nascosto, lo mostra, altrimenti lo nasconde
    if (indiceBox.style.display === 'none') {
        indiceBox.style.display = 'block';
    } else {
        indiceBox.style.display = 'none';
    }
}
