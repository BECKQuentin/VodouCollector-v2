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

        const form = document.querySelector('#inventoried_at_form');
        form.addEventListener('submit', e => {
            e.preventDefault();

            const formData = new FormData(form); // récupère les données du formulaire

            this.addLoader(form.querySelector('#inventoried_at_form_submit'));
            fetch(form.action, {
                method: form.method,
                body: formData,
            })
                .then( response => response.text())
                .then(data => {
                    this.reloadInventoriedAt();
                })
                .then(() => {
                    form.querySelector('input').value = '';
                })
                .then(() => {
                    this.removeLoader(form.querySelector('#inventoried_at_form_submit'));
                });

        })
    }

    removeInventoriedAt(e)
    {
        fetch(e.currentTarget.dataset.urlRemove)
            .then(() => {
                this.reloadInventoriedAt();
            })
    }

    //Recharger les items lors de l'ajout d'un nouveau et de la modification d'un
    reloadInventoriedAt()
    {
        const inventoriedAtItems = document.querySelector('#InventoriedAtItems');
        // Créer un objet URLSearchParams
        const params = new URLSearchParams();
        // Ajouter le paramètre idObject à l'objet URLSearchParams
        let url = '/objects/'+this.idObject+'/inventoried-at/'
        // Ajouter la chaîne de paramètres à l'URL
        fetch(url)
            .then(response => response.text())
            .then(templateContent => {
                inventoriedAtItems.innerHTML = templateContent;
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
        e.appendChild(scalingDots);
    }

    removeLoader(e) {
        if(e !== 'undefined') {
            let scalingDots = e.querySelector('.scaling-dots');
            if (scalingDots) scalingDots.remove();
        }
    }

}