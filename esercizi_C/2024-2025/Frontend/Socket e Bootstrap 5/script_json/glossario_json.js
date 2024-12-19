document.addEventListener("DOMContentLoaded", () => {
    // Carica il file JSON contenente i termini del glossario
    fetch("../json/glossario.json")
        .then(response => response.json())  // Converte la risposta in formato JSON
        .then(data => {
            // Trova l'elemento dell'accordion
            const accordion = document.getElementById("glossarioAccordion");

            // Imposta la descrizione generica del glossario
            document.querySelector(".text-center.text-muted").textContent = data.description;

            // Cicla su ogni termine nel glossario
            data.terms.forEach(term => {
                // Crea un nuovo elemento dell'accordion per ogni termine
                const accordionItem = document.createElement("div");
                accordionItem.className = "accordion-item";  // Aggiungi la classe 'accordion-item'

                // Imposta il contenuto HTML dell'accordion per ogni termine
                accordionItem.innerHTML = `
                    <h2 class="accordion-header" id="${term.id}">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#desc${term.id}" aria-expanded="false" aria-controls="desc${term.id}">
                            ${term.title}
                        </button>
                    </h2>
                    <div id="desc${term.id}" class="accordion-collapse collapse" aria-labelledby="${term.id}" data-bs-parent="#glossarioAccordion">
                        <div class="accordion-body">
                            ${term.definition}  <!-- Mostra la definizione del termine -->
                        </div>
                    </div>
                `;
                // Aggiungi l'elemento appena creato all'accordion
                accordion.appendChild(accordionItem);
            });
        })
        .catch(error => console.error("Errore nel caricamento del glossario:", error));  // Gestisce gli errori di caricamento
});
