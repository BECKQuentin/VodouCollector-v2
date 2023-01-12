let idObject = document.querySelector('.content_box');

if (idObject.dataset) {
    idObject = idObject.dataset.idObject
    let idsSearchObjs = localStorage.getItem('idsSearchObjs').split(',');

    //Si object inclut dans la recherche
    if (idsSearchObjs.includes(idObject)) {


        let indexOfObject = idsSearchObjs.indexOf(idObject);
        let idPrevElement = idsSearchObjs.at(indexOfObject-1);
        let idNextElement = idsSearchObjs.at(indexOfObject+1);

        //Si dernier element du tableau
        if (indexOfObject === idsSearchObjs.length-1) {
            idNextElement = idsSearchObjs[0];
        }

        //Changement des url de navigation des fiches suivantes
        let prevObject = document.getElementById('prev_object');
        let nextObject = document.getElementById('next_object');

        prevUrl = window.location.pathname.replace('/'+idObject, '/'+idPrevElement);
        nextUrl = window.location.pathname.replace('/'+idObject, '/'+idNextElement);

        prevObject.setAttribute('href', prevUrl);
        nextObject.setAttribute('href', nextUrl);

        //Affichage de l'index de la recherche dans la vue
        const indexSearchObject = document.getElementById('indexSearchObject');
        indexSearchObject.innerText = indexOfObject+1 + '/' + idsSearchObjs.length;

    } else {
        //n'est pas dans la liste, désafficher les icons de navigation suivant et précedent des fiches objets
    }
}


//Message POPUP pour avertir lorsqu'un utilisateur quitte le form Object sans sauvegarder
let formModified = false;
let inputObjectForm = document.querySelectorAll('input');

inputObjectForm.forEach((input) => {
    input.addEventListener('change', () => {
        formModified = true;
    })
})

window.addEventListener('beforeunload', (event) => {
    // Cancel the event as stated by the standard.
    event.preventDefault();

    if (formModified) {
        // Chrome requires returnValue to be set.
        event.returnValue = '';
    }

});


//Affichage des images selon leur format
let images = document.querySelectorAll('.object_img_thumb_container img');
for (let i = 0; i < images.length; i++) {
    let aspectRatio = images[i].naturalWidth / images[i].naturalHeight;
    if (aspectRatio > 1) {
        images[i].style.width = 100 + '%';
    } else {
        images[i].style.height = 100 + '%';
    }
}


