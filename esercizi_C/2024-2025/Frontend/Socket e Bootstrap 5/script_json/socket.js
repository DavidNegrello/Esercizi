// Carica il file JSON 'socket.json' usando fetch e poi elabora i dati
fetch('../json/socket.json')
  .then(response => response.json())  // Converte la risposta JSON
  .then(data => {
    // INTRODUZIONE: Inserisce il contenuto dell'introduzione nel container
    const introContainer = document.getElementById('intro-container');
    introContainer.innerHTML = `
      <h2 class="text-center text-primary my-4">${data.intro.title}</h2>  
      <div class="row align-items-center">
        <div class="col-md-4">
          <img src="${data.intro.image}" alt="${data.intro.imageAlt}" class="img-fluid rounded shadow">  
        </div>
        <div class="col-md-8">
          ${data.intro.text.map(paragraph => `<p class="text-justify">${enhanceWithGlossaryLinks(paragraph)}</p>`).join('')}  
        </div>
      </div>
    `;

    // FAMIGLIE DI SOCKET: Popola la sezione delle famiglie di socket
    const famiglieContainer = document.getElementById('famiglie-container');
    famiglieContainer.innerHTML = `
      <h2 class="text-center text-success my-4">${data.socketFamilies.title}</h2>  
      <p class="text-center">${enhanceWithGlossaryLinks(data.socketFamilies.text)}</p>  
      <ul class="list-group">
        ${data.socketFamilies.families.map(family => `
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <span><strong>${family.name}:</strong> ${enhanceWithGlossaryLinks(family.description)}</span>  
            <a href="${family.link}" class="btn btn-sm btn-outline-primary">Scopri di più</a> 
          </li>
        `).join('')}
      </ul>
    `;

    // TIPI DI SOCKET: Popola la sezione dei tipi di socket
    const tipiContainer = document.getElementById('tipi-container');
    tipiContainer.innerHTML = `
      <h2 class="text-center text-danger my-4">${data.socketTypes.title}</h2>  
      ${data.socketTypes.types.map((type, index) => `
        <div class="row mb-5 align-items-center ${index % 2 === 1 ? 'flex-row-reverse' : ''}">  
          <div class="col-md-6">
            <img src="${type.image}" alt="${type.imageAlt}" class="img-fluid rounded shadow"> 
          </div>
          <div class="col-md-6">
            <h3 class="text-secondary">${type.name}</h3>  
            ${type.description.map(paragraph => `<p>${enhanceWithGlossaryLinks(paragraph)}</p>`).join('')}  
          </div>
        </div>
      `).join('')}
    `;
  })
  .catch(error => console.error('Errore nel caricamento del JSON:', error));  // Gestione degli errori nel caso in cui il file JSON non venga caricato correttamente

// Funzione per arricchire il testo con collegamenti al glossario
function enhanceWithGlossaryLinks(text) {
  // Oggetto che associa i termini del glossario ai loro link
  const glossaryTerms = {
    'Inter-Process Communication': '#term1',
    'IPC': '#term1',
    'TCP': '#term2',
    'AF_INET': '#term3',
    'AF_UNIX': '#term4',
    'ISO': '#term5',
    'Interoperabilità': '#term6',
    'Layer': '#term7',
    'TLS': '#term8',
    'SSL': '#term9',
    'Privacy': '#term10',
    'Man-in-the-Middle': '#term11',
    'MitM': '#term11'
  };

  let enhancedText = text;  // Inizializza il testo arricchito con il testo originale

  // Cicla attraverso i termini del glossario e sostituisce le occorrenze con i link
  for (const [term, link] of Object.entries(glossaryTerms)) {
    const regex = new RegExp(`\\b${term}\\b`, 'g');  // Crea una regex per trovare il termine esatto
    enhancedText = enhancedText.replace(regex, `<a href="../pagine/glossario.html${link}" class="text-decoration-none">${term}</a>`);  // Sostituisce il termine con un link
  }

  return enhancedText;  // Restituisce il testo arricchito
}
