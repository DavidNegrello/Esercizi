document.addEventListener("DOMContentLoaded", () => {
    fetch("../json/glossario.json") // Carica il file JSON
        .then(response => response.json())
        .then(data => {
            const accordion = document.getElementById("glossarioAccordion");
            document.querySelector(".text-center.text-muted").textContent = data.description;

            data.terms.forEach(term => {
                const accordionItem = document.createElement("div");
                accordionItem.className = "accordion-item";

                accordionItem.innerHTML = `
                    <h2 class="accordion-header" id="${term.id}">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#desc${term.id}" aria-expanded="false" aria-controls="desc${term.id}">
                            ${term.title}
                        </button>
                    </h2>
                    <div id="desc${term.id}" class="accordion-collapse collapse" aria-labelledby="${term.id}" data-bs-parent="#glossarioAccordion">
                        <div class="accordion-body">
                            ${term.definition}
                        </div>
                    </div>
                `;
                accordion.appendChild(accordionItem);
            });
        })
        .catch(error => console.error("Errore nel caricamento del glossario:", error));
});
