let slider=document.querySelector(".slider");
let slideSelezionata=0;
let slideTotali=slider.querySelectorAll(".wrapper .left>div").length-1;

slider.querySelector(".controllo .up").addEventListener("click",function(){
    if(slideSelezionata==0){
        return 0;
    }
    slideSelezionata--;
    slider.querySelector(".wrapper .left div").style.marginTop= ` ${slideSelezionata*-100}vh ` ;
    slider.querySelector(".wrapper .right div").style.marginTop= ` ${(slideTotali-slideSelezionata)*-100}vh` ;
});

slider.querySelector(".controllo .down").addEventListener("click",function(){
    if(slideSelezionata==slideTotali){
        return 0;
    }
    slideSelezionata++;
    slider.querySelector(".wrapper .left div").style.marginTop= ` ${slideSelezionata*-100}vh ` ;
    slider.querySelector(".wrapper .right div").style.marginTop= ` ${(slideTotali-slideSelezionata)*-100}vh` ;
});