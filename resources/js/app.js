require('./bootstrap');

document.addEventListener("DOMContentLoaded", function() {
  // Logout button listener
  const logoutForm = document.querySelector('form#logout-form')
  const logoutButtons = document.querySelectorAll('.logout-button')
  for (let i = 0; i < logoutButtons.length; i++) {
    const el = logoutButtons[i];
    el.addEventListener('click', () => {
      logoutForm.submit();
    });
  }
});
