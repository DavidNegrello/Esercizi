document.addEventListener("DOMContentLoaded", function () {
    fetch("./contenuti/home.json")
        .then(response => response.json())
        .then(data => {
            // Navbar
            const navLinks = document.getElementById("nav-links");
            data.navbar.forEach(item => {
                const li = document.createElement("li");
                li.className = "nav-item";
                li.innerHTML = `<a class="nav-link" href="${item.link}">${item.name}</a>`;
                navLinks.appendChild(li);
            });

            // Contenuto
            const contentDiv = document.getElementById("content");
            data.content.forEach(section => {
                const div = document.createElement("div");
                div.className = "mb-4";
                div.innerHTML = `<h2>${section.title}</h2><p>${section.text}</p>`;
                contentDiv.appendChild(div);
            });

            // Footer
            document.getElementById("footer-text").textContent = data.footer;
        })
        .catch(error => console.error("Errore nel caricamento del JSON:", error));
});
