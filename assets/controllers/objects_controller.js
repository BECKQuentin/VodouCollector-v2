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

    // connect() {
    //     // this.element.textContent = 'Hello Stimulus! Edit me in assets/controllers/hello_controller.js';
    //     console.log('ok')
    // }
    // static targets = [
    //     'button'
    // ]
    // static values = {
    //     url: String,
    //     interval: Number,
    //     params: Object,
    // }

    connect() {

        //Vérification des champs requis aux cliques sur les boutons Submit
        document.querySelectorAll('#objects_form_submit, #objects_form_submit2').forEach(button => {
            button.addEventListener('click', () => {
                this.verifyError()
            });
        })

        //Vérification des champs et ajout des badges requis lors de la connection
        let connection = true;
        this.verifyError(connection);
    }

    verifyError(connection = false) {

        const errorElements = [];

        // Pour les champs requis vide
        let form = document.querySelectorAll('#objectsForm input[required], #objectsForm textarea[required], #objectsForm select[required]');

        // Pour parcourir les types radios
        let lastName;
        form.forEach((item) => {
            if (lastName !== item.name) {
                lastName = item.name;
                if (item.getAttribute('type') === 'radio') {
                    if (!document.querySelectorAll('[name="' + lastName + '"]:checked').length) {
                        errorElements.push(item);
                    }
                } else if (item.value === "") {
                    errorElements.push(item);
                }
            }
        });

        // Pour les erreurs détecter (erreur de pattern par exemple) après submission
        let errorInput = document.querySelectorAll('.form-error-message');

        errorInput.forEach((item) => {
            let label = item.closest('label');
            errorElements.push(document.querySelector('#' + label.getAttribute('for')));
        });


        // Ajout des badges lors d'Erreurs
        if (errorElements) {

            // Pour commençer de gauche à droite les erreurs(et finir par afficher la première erreur à gauche)
            errorElements.reverse();
            //Réinitialisation des badges
            document.querySelectorAll('span.error_badge').forEach(badge => badge.remove());

            errorElements.forEach((element) => {

                // Récupération de l'onglet ou se trouve l'erreur
                let tabContent = element.closest('.object_form_elem');
                let regex = /objectElementContent_([a-z]+)/;
                let match = tabContent.id.match(regex);

                //Ajout des badges
                let tab = document.getElementById('object_' + match[1] + '_tab');
                let span = tab.querySelector('span');
                //On verifier si la span existe
                if (span !== null) {
                    span.innerText = parseInt(span.innerText) + 1;
                    if (!connection) tab.click();
                } else {
                    let span = document.createElement('span');
                    span.classList.add('error_badge');
                    span.title = "Champs requis";
                    span.innerText = 1;
                    tab.append(span);
                    if (!connection) tab.click();
                }
            });
        }
    }


    setContentObjectTab(e) {
        e.preventDefault();

        let objectTab = e.currentTarget.dataset.objectTab;

        // Chercher tous les onglets
        let allTabs = document.querySelectorAll('.object_tab_edit');
        // Exclure l'élément currentTarget
        let otherTabs = Array.prototype.filter.call(allTabs, function(element) {
            return element !== e.currentTarget;
        });

        // Reinitialiser les elements
        otherTabs.forEach((item) => {
            clearElem(item)
        })

        changeTabClassActive(e.currentTarget);
        displayFormElem(document.getElementById('objectElementContent_'+objectTab));





        function clearElem(tab) {
            tab.classList.remove('tab-active');
            tab.classList.add('tab');

            let content = document.getElementById('objectElementContent_'+tab.dataset.objectTab);
            content.classList.add('slide-out');

            content.style.display = "none";
            content.classList.remove('slide-out');

        }

        function changeTabClassActive(tab) {
            if (!tab.classList.contains('tab-active')) {
                tab.classList.toggle('tab-active');
                tab.classList.toggle('tab');
            }
        }

        function displayFormElem(elem) {
            elem.style.display = "block";
            elem.classList.add('slide-in');
            setTimeout(() => {
                elem.classList.remove('slide-in');
            }, 200)
        }


        function addInventiryDate(e) {
            let button = e.currentTarget;
            fetch(e.currentTarget.dataset.url).then( (response) => {
                button.querySelector('i').classList.toggle('fa-regular')
                button.querySelector('i').classList.toggle('fa-solid')
                if (button.querySelector('i').classList.contains('fa-regular')) {
                    button.title = 'Ajouter aux favoris';
                } else {
                    button.title = 'Retirer des favoris';
                }
                return response.blob();
            })
        }

    }





}
