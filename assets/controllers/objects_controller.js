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

        verifyError();

        function verifyError() {
            //récupère la div des erreurs eventuelles
            let div = document.querySelector('div[id^="objects_form_"][id$="_errors"]');
            // Une erreur
            if (div) {
                //recupérer le champ du form
                let divId = div.id;  // "objects_form_images_errors"
                let regex = /_([a-z]+)_errors/;
                let match = divId.match(regex);
                let word = match[1];  // "images"

                //Défini les champs et donc erreur eventuel par onglet
                const constants = {
                    identification: ['code', 'title' , 'memo', 'gods'],
                    images: ['images'],
                    files: ['files'],
                };

                let constantName = null;
                //On regarde si l'erreur est présent dans un des onglets
                for (const key in constants) {
                    const value = constants[key];
                    //Si on trouve un onglet avec le champ de l'erreur
                    if (Array.isArray(value) && value.includes('images')) {
                        constantName = key; //images
                        //On ajoute 1 au badge erreur
                        let tab = document.getElementById('object_'+key+'_tab');
                        let span = tab.querySelector('span');
                        //On verifier si la span existe
                        if (span !== null) {
                            span.innerText += 1;
                            tab.click();
                        } else {
                            let span = document.createElement('span');
                            span.classList.add('error_badge');
                            span.setAttribute('title', 'Erreur dans le champ ' + key);
                            span.innerText += 1;
                            tab.append(span);
                            tab.click();
                        }
                        break;
                    }
                }
            }
        }


    }


    setContentObjectTab(e) {

        e.preventDefault();

        let objectTab = e.currentTarget.dataset.objectTab;

        changeClassActive(e.currentTarget);
        displayFormElem(document.getElementById('objectElementContent_'+objectTab));

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

        function clearElem(tab) {
            tab.classList.remove('tab-active');
            tab.classList.add('tab');

            let content = document.getElementById('objectElementContent_'+tab.dataset.objectTab);
            content.classList.add('slide-out');
            setTimeout(() => {
                content.style.display = "none";
                content.classList.remove('slide-out');
            }, 200)
        }

        function changeClassActive(tab) {
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

    }





}
