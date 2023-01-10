import 'bootstrap/dist/css/bootstrap.min.css';

import '../styles/base/login.scss';
import '../styles/app.scss';

import 'bootstrap/dist/js/bootstrap.bundle.min';


console.log('connected');

let login_password_show =  document.querySelector('.login_password_show');
let passwordInput = document.getElementById('password');

login_password_show.addEventListener('click', () => {
    togglePasswordType();
})

function togglePasswordType() {
    // Si le type actuel est "password", le changer en "text"
    if (passwordInput.type === "password") {
        passwordInput.type = "text";
    }
    // Sinon, le changer en "password"
    else {
        passwordInput.type = "password";
    }
}
