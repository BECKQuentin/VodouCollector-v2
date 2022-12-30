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
        this.sharedBookmarksFormObjects = document.getElementById('shared_bookmarks_form_objects');

        // this.arrIdObject =
        fetch('/objects/all-code')
            .then( response => response.json())
            .then(data => {this.allObjectCode = data;})
            .then(() => {
                this.transformBadge()
            });
    }

    //Lors d'un keypress dans le champ
    findObjectsCode(e) {
        this.transformBadge()
    }

    transformBadge(){
        const elements = this.sharedBookmarksFormObjects.value.split(',');

        const container = document.createElement('div');

        //Ajout du badge pour delete lors de l'actualisation
        container.classList.add('badge-vodou-code');
        document.querySelectorAll('.badge-vodou-code').forEach((badge) => {
            badge.remove();
        })

        elements.forEach(element => {
            const span = document.createElement('span');
            span.classList.add('badge_vodou');
            span.classList.add('badge_success');
            if (!this.allObjectCode.includes(element)) {
                span.classList.add('badge_error');
            }
            span.textContent = '#' + element;
            container.appendChild(span);
        });
        this.sharedBookmarksFormObjects.parentNode.insertBefore(container, this.sharedBookmarksFormObjects.nextSibling);
    }


}
