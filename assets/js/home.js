/*HOME*/
// //Carousel avec Grab
// const slider = document.querySelector('.carousel_grab_items');
// let isDown = false;
// let startX;
// let scrollLeft;
//
// slider.addEventListener('mousedown', (e) => {
//     isDown = true;
//     slider.classList.add('active');
//     startX = e.pageX - slider.offsetLeft;
//     scrollLeft = slider.scrollLeft;
// });
// slider.addEventListener('mouseleave', () => {
//     isDown = false;
//     slider.classList.remove('active');
// });
// slider.addEventListener('mouseup', () => {
//     isDown = false;
//     slider.classList.remove('active');
// });
// slider.addEventListener('mousemove', (e) => {
//     if(!isDown) return;
//     e.preventDefault();
//     const x = e.pageX - slider.offsetLeft;
//     const walk = (x - startX) * 3; //scroll-fast
//     slider.scrollLeft = scrollLeft - walk;
//     console.log(walk);
// });

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