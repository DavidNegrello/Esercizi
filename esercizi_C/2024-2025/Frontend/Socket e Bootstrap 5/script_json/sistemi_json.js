document.addEventListener("DOMContentLoaded", () => {
  // Carica il contenuto JSON
  fetch("../json/sistemi.json")
      .then(response => response.json())
      .then(data => {
          // Inserisce il titolo
          document.querySelector(".section-title").innerHTML = data.title;

          // Inserisce i paragrafi di introduzione
          const introBox = document.querySelector(".intro-box");
          data.introduction.paragraphs.forEach(paragraph => {
              const p = document.createElement("p");
              p.classList.add("lead", "text-justify", "mb-4");
              p.innerHTML = paragraph;
              introBox.appendChild(p);
          });

          // Inserisce le sezioni
          const container = document.querySelector(".container .mt-5");
          data.sections.forEach(section => {
              const sectionHTML = `
                  <h3 class="text-center text-${getColor()}">${section.title}</h3>
                  <div class="card shadow-sm mb-4">
                      <div class="card-body">
                          <p class="lead mb-4">${section.content}</p>
                      </div>
                  </div>`;
              container.insertAdjacentHTML("beforeend", sectionHTML);
          });

          // Inserisce il bottone
          const buttonHTML = `
              <div class="text-center mb-5">
                  <a href="${data.button.link}" target="_blank" class="btn btn-outline-primary btn-lg">
                      ${data.button.text}
                  </a>
              </div>`;
          container.insertAdjacentHTML("beforeend", buttonHTML);
      })
      .catch(error => console.error("Errore nel caricamento del JSON:", error));
});

// Funzione per colori dinamici
function getColor() {
  const colors = ["success", "warning", "info", "danger"];
  return colors[Math.floor(Math.random() * colors.length)];
}
