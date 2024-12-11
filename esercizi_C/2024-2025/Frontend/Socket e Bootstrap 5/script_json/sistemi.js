// Funzione per caricare il file JSON e iniettare il contenuto nella pagina
function loadOSIContent() {
    fetch('../json/sistemi.json')
      .then(response => response.json())
      .then(data => {
        renderOSIContent(data);
      })
      .catch(error => console.error('Errore nel caricamento del contenuto JSON:', error));
  }

  // Funzione per renderizzare il contenuto JSON nella pagina HTML
  function renderOSIContent(content) {
    const container = document.getElementById('osi-model-container');

    // Titolo principale
    const titleElement = document.createElement('h1');
    titleElement.classList.add('text-center', 'section-title', 'mt-5', 'text-primary');
    titleElement.innerHTML = content.title;
    container.appendChild(titleElement);

    // Sezione Introduzione
    const introBox = document.createElement('div');
    introBox.classList.add('container', 'text-center', 'mt-4');
    introBox.innerHTML = `
      <div class="intro-box bg-light rounded-3 p-5 shadow-lg">
        <p class="lead text-justify mb-4">${content.introduction.paragraphs.join('</p><p class="lead text-justify mb-4">')}</p>
      </div>
    `;
    container.appendChild(introBox);

    // Sezioni principali
    content.sections.forEach(section => {
      const sectionCard = document.createElement('div');
      sectionCard.classList.add('card', 'shadow-sm', 'mb-4');
      sectionCard.innerHTML = `
        <div class="card-body">
          <h3 class="text-center text-success">${section.title}</h3>
          <ul class="text-start mb-4">
            ${section.content.map(item => `<li>${item}</li>`).join('')}
          </ul>
        </div>
      `;
      container.appendChild(sectionCard);
    });

    // Bottone di approfondimento
    const buttonDiv = document.createElement('div');
    buttonDiv.classList.add('text-center', 'mb-5');
    buttonDiv.innerHTML = `
      <a href="${content.button.link}" target="_blank" class="btn btn-outline-primary btn-lg">
        ${content.button.text}
      </a>
    `;
    container.appendChild(buttonDiv);
  }

  // Esegui la funzione per caricare e renderizzare il contenuto al caricamento della pagina
  document.addEventListener('DOMContentLoaded', function () {
    loadOSIContent();
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
