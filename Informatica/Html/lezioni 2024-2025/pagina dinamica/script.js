let bottoneBello=document.querySelectorAll('button');

bottoneBello=addEventListener('click',() => {
    let paragrafo=document.createElement('p');
    paragrafo.textContent="ciao bello";
    paragrafo.style.backgroundColor='tomato';
    paragrafo.style.fontSize='100px';
    paragrafo.style.textAlign='center';
    document.body.appendChild(paragrafo);
    document.body.insertAdjacentHTML(paragrafo);
});
