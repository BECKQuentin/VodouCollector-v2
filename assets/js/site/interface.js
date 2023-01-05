let root = document.documentElement;

window.onload = () => {
    let mainColorInput = document.getElementById('color_main_choice')
    let secondColorInput = document.getElementById('color_second_choice')
    let thirdColorInput = document.getElementById('color_third_choice')
    let textColorInput = document.getElementById('color_text_choice')

    if (localStorage.getItem('main-color') || localStorage.getItem('second-color') || localStorage.getItem('third-color') || localStorage.getItem('text-color')) {
        //recuperation et utilisation des valuers stockées dans le dépôt local
        document.querySelector(':root').style.setProperty('--main', localStorage.getItem('main-color'));
        document.querySelector(':root').style.setProperty('--second', localStorage.getItem('second-color'));
        document.querySelector(':root').style.setProperty('--third', localStorage.getItem('second-color'));
        document.querySelector(':root').style.setProperty('--text', localStorage.getItem('text-color'));
        if ( mainColorInput != null) {
            //Affichage des couleurs récupérés dans l'input de choix des couleurs
            mainColorInput.value = localStorage.getItem('main-color');
            secondColorInput.value = localStorage.getItem('second-color');
            thirdColorInput.value = localStorage.getItem('third-color');
            textColorInput.value = localStorage.getItem('text-color');
        }
    } else {
        //charger les valeurs des variables css définies dans l'input de choix des couleurs
        let mainColor = getComputedStyle(root).getPropertyValue('--main')
        let secondColor = getComputedStyle(root).getPropertyValue('--second')
        let thirdColor = getComputedStyle(root).getPropertyValue('--third')
        let textColor = getComputedStyle(root).getPropertyValue('--text')
        mainColorInput.value = mainColor.trim()
        secondColorInput.value = secondColor.trim()
        thirdColorInput.value = thirdColor.trim()
        textColorInput.value = textColor.trim()
    }
}

let toolbar = document.getElementById('toolbar');
let page = document.getElementById('page');
let spanNavLink = document.querySelectorAll('.nav-link span');
let hideToolbar = document.getElementById('hideToolbar');

toolbar.addEventListener('mouseenter', () => {
    extendToolbar();
})
toolbar.addEventListener('mouseleave', () => {
    setTimeout(() => {
        retractToolbar();
    }, 150)
})

function extendToolbar() {
    toolbar.classList.remove('toolbar_retract');
    page.classList.remove('toolbar_retract_page');
    // hideToolbar.querySelector('i').classList.toggle('fa-chevron-right')
    // hideToolbar.querySelector('i').classList.toggle('fa-chevron-left')
    toolbar.classList.add('col-md-2');
    page.classList.add('offset-md-2');
    page.classList.add('col-md-10');

    setTimeout(() => {
        spanNavLink.forEach((item) => {
            item.classList.add('d-md-inline');
        })
    }, 150)
}

function retractToolbar() {
    toolbar.classList.add('toolbar_retract');
    page.classList.add('toolbar_retract_page');
    // hideToolbar.querySelector('i').classList.toggle('fa-chevron-right')
    // hideToolbar.querySelector('i').classList.toggle('fa-chevron-left')
    toolbar.classList.remove('col-md-2');
    page.classList.remove('offset-md-2');
    page.classList.remove('col-md-10');

    spanNavLink.forEach((item) => {
        item.classList.remove('d-md-inline');
    })
}






