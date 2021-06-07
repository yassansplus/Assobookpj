const divText = document.querySelector('.read-more');

if(divText){
    const max = 80;
    const str = divText.textContent;
    if(str.trim().length > max){
        const subStr = str.substring(0, max);
        const hiddenStr = str.substring(max, str.trim().length);
        divText.innerHTML = subStr + '<span>...<a href="#" class="lire-plus" style="text-decoration: underline">Voir plus</a></span>' + `<span class="d-none">${hiddenStr}</span>`
        ;
    }

    const readMore = document.querySelector('.lire-plus');
    readMore.addEventListener('click', (e) => {
        e.preventDefault();
        const parent = readMore.parentNode;
        parent.nextSibling.classList.remove('d-none');
        parent.remove();
    });
}