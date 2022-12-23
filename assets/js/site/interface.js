let toolbar = document.getElementById('toolbar');
let page = document.getElementById('page');
let textNavLink = document.querySelectorAll('.nav-link span');
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
        textNavLink.forEach((item) => {
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

    textNavLink.forEach((item) => {
        item.classList.remove('d-md-inline');
    })
}






