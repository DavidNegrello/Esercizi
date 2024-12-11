document.addEventListener("DOMContentLoaded", function() {
    fetch('../json/info.json')
        .then(response => response.json())
        .then(data => {
            // Carica il titolo
            document.getElementById('title').textContent = data.title;

            // Carica l'immagine
            document.getElementById('image').src = data.image;

            // Carica l'indice
            let indiceList = document.getElementById('indice-list');
            data.sections.forEach(section => {
                let listItem = document.createElement('li');
                listItem.innerHTML = `<a href="#${section.id}">${section.title}</a>`;
                indiceList.appendChild(listItem);
            });

            // Carica il contenuto delle sezioni
            let contentSections = document.getElementById('content-sections');
            data.sections.forEach(section => {
                let sectionDiv = document.createElement('section');
                sectionDiv.id = section.id;
                sectionDiv.classList.add('content-section');
                
                let sectionTitle = document.createElement('h2');
                sectionTitle.textContent = section.title;
                sectionDiv.appendChild(sectionTitle);
                
                section.content.forEach(paragraph => {
                    let p = document.createElement('p');
                    p.innerHTML = addGlossaryLinks(paragraph);  // Aggiungi i collegamenti ai termini del glossario
                    sectionDiv.appendChild(p);
                });

                contentSections.appendChild(sectionDiv);
            });
        });
});

// Funzione per aggiungere i collegamenti ai termini nel glossario
function addGlossaryLinks(content) {
    // Usa espressioni regolari per sostituire i termini con i collegamenti al glossario se presenti
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
    return content;
}

// Funzione per visualizzare/nascondere l'indice
function toggleIndice() {
    let indiceBox = document.getElementById('indice-box');
    if (indiceBox.style.display === 'none') {
        indiceBox.style.display = 'block';
    } else {
        indiceBox.style.display = 'none';
    }
}
