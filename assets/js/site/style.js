//Changer les valeurs de couleur au cliques
let color_choice_button = document.querySelectorAll('#color_choice button');

color_choice_button.forEach( (button) =>  {
    button.addEventListener('click', () => {
        //recuperation des valeurs des inputs color
        let inputMainColor = document.getElementById("color_main_choice").value;
        let inputSecondColor = document.getElementById("color_second_choice").value;
        let inputThirdColor = document.getElementById("color_third_choice").value;
        let inputTextColor = document.getElementById("color_text_choice").value;
        //changement des variables avec les valeurs récupérées
        root.style.setProperty('--main', inputMainColor)
        root.style.setProperty('--second', inputSecondColor)
        root.style.setProperty('--third', inputThirdColor)
        root.style.setProperty('--text', inputTextColor)
        //stockage dans le dépôt local
        localStorage.setItem('main-color', inputMainColor);
        localStorage.setItem('second-color', inputSecondColor);
        localStorage.setItem('third-color', inputThirdColor);
        localStorage.setItem('text-color', inputTextColor);
    });
});

//function reset color
let reset_color = document.getElementById('reset_color');

reset_color.addEventListener('click', () => {
    localStorage.removeItem('main-color')
    localStorage.removeItem('second-color')
    localStorage.removeItem('third-color')
    localStorage.removeItem('text-color')

    location.reload(true)
})


