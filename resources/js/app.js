require('./bootstrap');

require('alpinejs');

document.addEventListener("DOMContentLoaded", function() {
  // Logout Button
  const logoutButton = document.querySelector('.logout-button')
  if (logoutButton) {
    const logoutForm = document.querySelector('form#logout-form')
    logoutButton.addEventListener('click', () => {
      logoutForm.submit();
    });
  }
});
