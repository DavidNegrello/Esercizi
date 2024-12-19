// Aggiunge un listener per l'evento DOMContentLoaded, che si attiva quando il DOM è completamente carico
document.addEventListener("DOMContentLoaded", () => {
    // Carica il contenuto JSON dal file 'sistemi.json'
    fetch("../json/sistemi.json")
        .then(response => response.json())  // Converte la risposta in formato JSON
        .then(data => {
            // Inserisce il titolo nella sezione .section-title
            document.querySelector(".section-title").innerHTML = data.title;
  
            // Inserisce i paragrafi di introduzione nella sezione .intro-box
            const introBox = document.querySelector(".intro-box");
            data.introduction.paragraphs.forEach(paragraph => {
                const p = document.createElement("p");  // Crea un nuovo elemento <p> per ogni paragrafo
                p.classList.add("lead", "text-justify", "mb-4");  // Aggiunge le classi di stile
                p.innerHTML = paragraph;  // Imposta il contenuto del paragrafo
                introBox.appendChild(p);  // Aggiunge il paragrafo alla sezione
            });
  
            // Inserisce le sezioni principali nella pagina
            const container = document.querySelector(".container .mt-5");  // Seleziona il contenitore dove aggiungere le sezioni
            data.sections.forEach(section => {
                // Crea il markup HTML per ogni sezione
                const sectionHTML = `
                    <h3 class="text-center text-${getColor()}">${section.title}</h3> 
                    <div class="card shadow-sm mb-4">  
                        <div class="card-body">
                            <p class="lead mb-4">${section.content}</p>  
                        </div>
                    </div>`;
                // Aggiunge la sezione al contenitore
                container.insertAdjacentHTML("beforeend", sectionHTML);
            });
  
            // Inserisce un bottone alla fine della sezione
            const buttonHTML = `
                <div class="text-center mb-5">  
                    <a href="${data.button.link}" target="_blank" class="btn btn-outline-primary btn-lg">
                        ${data.button.text}
                    </a>
                </div>`;
            // Aggiunge il bottone al contenitore
            container.insertAdjacentHTML("beforeend", buttonHTML);
        })
        .catch(error => console.error("Errore nel caricamento del JSON:", error));  // Gestisce gli errori nel caso di un problema nel caricamento
  });
  
  // Funzione per generare un colore dinamico per le sezioni
  function getColor() {
    // Array con i colori disponibili per la sezione
    const colors = ["success", "warning", "info", "danger"];
    // Restituisce un colore casuale dall'array
    return colors[Math.floor(Math.random() * colors.length)];
  }
  