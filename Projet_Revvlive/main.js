const form = document.querySelector('.registration-form form');  
const errorMessage = document.querySelector('.error-message');
const createAccountButton = document.querySelector('#create-account');

createAccountButton.addEventListener('click', (e) => {
  e.preventDefault(); 
  const errors = validateForm();
  if (errors) {
    const errorList = document.createElement('ul'); //create the error list
    errors.forEach((error) => {
      const errorListItem = document.createElement('li');
      errorListItem.textContent = error;
      errorList.appendChild(errorListItem);  //add each error to the list with appendChild

    });
    errorMessage.textContent = ''; //remove the error
    errorMessage.appendChild(errorList);
    errorMessage.classList.add('show');  //show the error

  } else {
    //no error executing the else
  }
});

function validateForm() {
  const username = document.getElementById('username').value;
  const email = document.getElementById('email').value;
  const password = document.getElementById('password').value;
  const password2 = document.getElementById('password2').value;

  const errors = [];

  if (username.length < 5) errors.push('Le nom d\'utilisateur doit contenir au moins 5 caractères.');
  if (!email.match(/^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]{2,}\.[a-z]{2,4}$/)) errors.push('L\'adresse email est invalide.');
  if (password.length < 8) errors.push('Le mot de passe doit contenir au moins 8 caractères.');
  if (password !== password2) errors.push('Les mots de passe ne correspondent pas.');

  return errors.length > 0 ? errors : null;
}