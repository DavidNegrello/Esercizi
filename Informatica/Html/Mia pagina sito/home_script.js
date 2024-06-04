

//======================per cambiare la mappa====================
//funzione che gestisce la singola mappa
function cambiaMappa(index) {
  mapIframe.src = map[index];
  cambiaMapButton.forEach((button, i) => {
    if (i === index) {
      button.style.backgroundColor = '#b6cc54';
      button.disabled = true; //disabilita il bottone se è stato già premuto
    } else {
      button.style.backgroundColor = 'white';
      button.disabled = false;  //riattiva il bottone
    }
  });
}

const cambiaMapButton=[
  document.getElementById("mappa1"),
  document.getElementById("mappa2"),
  document.getElementById("mappa3")
];

const mapIframe = document.getElementById("map-iframe");
const map=["https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2802.6814415337694!2d11.91075697626913!3d45.375422971072624!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x477edb482a4cf2ff%3A0xc699d62aced72213!2sVia%20G.%20Rossa%2C%208%2C%2035020%20Ponte%20San%20Nicol%C3%B2%20PD!5e0!3m2!1sit!2sit!4v1716009672589!5m2!1sit!2sit",
"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2835.436516052606!2d10.592067995984289!3d44.710702008813044!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47801b8c7de9f37b%3A0x1e610262186585ef!2sVia%20Fratelli%20Cervi%2C%20132%2F1%2C%2042124%20Reggio%20Emilia%20RE!5e0!3m2!1sit!2sit!4v1716009195096!5m2!1sit!2sit",
"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2786.8539465141084!2d11.85501707628652!3d45.69390297107873!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4778d6271abfdc01%3A0x555ac5f55df4faa7!2sVia%20Chioggia%2C%2047A%2C%2031030%20Alberone%20TV!5e0!3m2!1sit!2sit!4v1716009349067!5m2!1sit!2sit"
]

//va a creare gli eventi con un for per evitare ripetizioni
cambiaMapButton.forEach((button, index) => {
  button.addEventListener("click", () => cambiaMappa(index));
});