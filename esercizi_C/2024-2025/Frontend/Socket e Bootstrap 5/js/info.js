// Rende l'indice visibile
function toggleIndice() {
    var indiceBox = document.getElementById("indice-box");
    if (indiceBox.style.display === "none" || indiceBox.style.display === "") {
        indiceBox.style.display = "block";
    } else {
        indiceBox.style.display = "none";
    }
}