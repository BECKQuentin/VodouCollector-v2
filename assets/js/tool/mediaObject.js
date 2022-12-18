let media_form_image = document.getElementById('objects_form_image');
//Affichage des noms des Fichiers ajoutés en temp de l'input
if (media_form_image) {
    media_form_image.onchange = function(e) {
        let media_form_name = document.getElementById('objects_media_input_file_name');
        let files = e.target.files;
        if (files.length > 0) {
            media_form_name.style.display = 'block';
            for (let i = 0; i < files.length; i++) {
                let li = document.createElement('li');
                li.innerText = files[i].name;
                media_form_name.appendChild(li);
            }
        }
    }
}


