// Przełączanie widoczności hasła
document.addEventListener('DOMContentLoaded', function() {
  const toggleButtons = document.querySelectorAll('.toggle-password');
  
  toggleButtons.forEach(button => {
    button.addEventListener('click', function() {
      // Znajdź pole hasła w tym samym wrapperze
      const wrapper = this.closest('.password-input-wrapper');
      const passwordInput = wrapper.querySelector('input[type="password"], input[type="text"]');
      const eyeIcon = this.querySelector('.eye-icon');
      
      if (!passwordInput || !eyeIcon) return;
      
      // Przełącz typ pola
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.src = eyeIcon.src.replace('show.png', 'hide.png');
        eyeIcon.alt = 'Ukryj hasło';
        this.setAttribute('aria-label', 'Ukryj hasło');
      } else {
        passwordInput.type = 'password';
        eyeIcon.src = eyeIcon.src.replace('hide.png', 'show.png');
        eyeIcon.alt = 'Pokaż hasło';
        this.setAttribute('aria-label', 'Pokaż hasło');
      }
    });
  });
});
