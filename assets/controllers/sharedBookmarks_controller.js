import { Controller } from '@hotwired/stimulus';

/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="hello" attribute will cause
 * this controller to be executed. The name "hello" comes from the filename:
 * hello_controller.js -> "hello"
 *
 * Delete this file or adapt it for your use!
 */

export default class extends Controller {

    connect() {
        this.idObject = document.getElementById('objectPage').dataset.idObject;
    }

    displayForm() {
        document.querySelector('#SharedBookmarksForm').style.display = "flex";

        //Ajout ou modification du nom d'un favori partagé
        const form = document.querySelector('#shared_bookmarks_form');
        form.addEventListener('submit', e => {
            e.preventDefault();
            const formData = new FormData(form); // récupère les données du formulaire

            fetch(form.action, {
                method: form.method,
                body: formData,
            })
                .then( response => response.text())
                .then(data => {
                    this.reloadSharedBookmarks();
                });
        })

    }
    removeForm() {
        document.querySelector('#SharedBookmarksForm').style.display = "none";
    }

    registerSharedBookmark(e) {
        this.addLoader(e);

        fetch(e.currentTarget.dataset.url).then( (response) => {
            this.reloadSharedBookmarks();
            this.removeLoader(e);
            return response.blob();
        })
    }

    addObjectSharedBookmark(e) {
        this.addLoader(e);

        fetch(e.currentTarget.dataset.url).then((response) => {
            this.reloadSharedBookmarks();
            this.removeLoader(e);
            return response.blob();
        })
    }


    //Recharger les items lors de l'ajout d'un nouveau et de la modification d'un
    reloadSharedBookmarks() {
        const sharedBookmarksItems = document.querySelector('#SharedBookmarksItems');
        // Créer un objet URLSearchParams
        const params = new URLSearchParams();
        // Ajouter le paramètre idObject à l'objet URLSearchParams
        params.append("idObject", this.idObject);
        let url = '/shared-bookmarks'
        // Ajouter la chaîne de paramètres à l'URL
        const urlWithParams = `${url}?${params.toString()}`;
        fetch(urlWithParams)
            .then(response => response.text())
            .then(templateContent => {
                sharedBookmarksItems.innerHTML = templateContent;
            });
    }

    //LOADER
    addLoader(e) {
        let scalingDots = document.createElement('div');
        scalingDots.classList.add('scaling-dots');

        for (let i = 0; i < 5; i++) {
            let div = document.createElement('div');
            scalingDots.appendChild(div);
        }
        e.currentTarget.appendChild(scalingDots);
    }

    removeLoader(e) {
        if(e.currentTarget !== null) {
            let scalingDots = e.currentTarget.querySelector('.scalingDots');
            if (scalingDots) e.currentTarget.removeChild();
        }
    }
}
