//per l'effetto hover nel menu a tendina
document.addEventListener("DOMContentLoaded", function() {
    const dropdown = document.querySelector(".nav-item.dropdown");
    
    dropdown.addEventListener("mouseenter", function() {
        this.classList.add("show");
        this.querySelector(".dropdown-menu").classList.add("show");
    });

    dropdown.addEventListener("mouseleave", function() {
        this.classList.remove("show");
        this.querySelector(".dropdown-menu").classList.remove("show");
    });
});

