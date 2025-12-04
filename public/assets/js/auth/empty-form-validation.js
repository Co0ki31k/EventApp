// Globalna walidacja formularza rejestracji przed wysłaniem
document.addEventListener('DOMContentLoaded', function() {
  const form = document.querySelector('.form-register');
  
  if (!form) return;
  
  // Pobierz wszystkie pola formularza
  const usernameInput = document.getElementById('username');
  const emailInput = document.getElementById('email');
  const passwordInput = document.getElementById('password');
  const submitButton = form.querySelector('button[type="submit"]');
  
  // Funkcja sprawdzająca czy pole jest puste
  function isEmpty(value) {
    return !value || value.trim().length === 0;
  }
  
  // Funkcja sprawdzająca czy wszystkie pola są prawidłowe
  function areAllFieldsValid() {
    let isValid = true;
    
    // Sprawdź username
    if (!usernameInput || isEmpty(usernameInput.value) || usernameInput.classList.contains('invalid')) {
      isValid = false;
    }
    
    // Sprawdź email
    if (!emailInput || isEmpty(emailInput.value) || emailInput.classList.contains('invalid')) {
      isValid = false;
    }
    
    // Sprawdź hasło - czy wszystkie reguły są spełnione
    if (!passwordInput || isEmpty(passwordInput.value)) {
      isValid = false;
    } else {
      const invalidRules = document.querySelectorAll('.checklist-item:not(.password-strength):not(.valid)');
      if (invalidRules.length > 0) {
        isValid = false;
      }
    }
    
    return isValid;
  }
  
  // Funkcja aktualizująca stan przycisku submit
  function updateSubmitButton() {
    if (!submitButton) return;
    
    if (areAllFieldsValid()) {
      submitButton.disabled = false;
      submitButton.style.opacity = '1';
      submitButton.style.cursor = 'pointer';
      submitButton.title = '';
    } else {
      submitButton.disabled = true;
      submitButton.style.opacity = '0.5';
      submitButton.style.cursor = 'not-allowed';
      submitButton.title = 'Wypełnij poprawnie wszystkie pola formularza';
    }
  }
  
  // Inicjalizacja - zablokuj przycisk na start
  if (submitButton) {
    submitButton.disabled = true;
    submitButton.style.opacity = '0.5';
    submitButton.style.cursor = 'not-allowed';
    submitButton.title = 'Wypełnij poprawnie wszystkie pola formularza';
  }
  
  // Nasłuchuj zmian we wszystkich polach
  [usernameInput, emailInput, passwordInput].forEach(input => {
    if (input) {
      input.addEventListener('input', updateSubmitButton);
      input.addEventListener('keyup', updateSubmitButton);
      input.addEventListener('blur', updateSubmitButton);
    }
  });
  
  // Nasłuchuj zmian w checkliście hasła
  const observer = new MutationObserver(updateSubmitButton);
  const passwordChecklist = document.querySelector('.password-checklist');
  if (passwordChecklist) {
    observer.observe(passwordChecklist, {
      attributes: true,
      childList: true,
      subtree: true,
      attributeFilter: ['class']
    });
  }
  
  // Walidacja przy wysłaniu formularza (dodatkowe zabezpieczenie)
  form.addEventListener('submit', function(e) {
    if (!areAllFieldsValid()) {
      e.preventDefault();
      alert('Wypełnij poprawnie wszystkie pola formularza!');
      
      // Znajdź pierwsze nieprawidłowe pole i ustaw focus
      if (usernameInput && (isEmpty(usernameInput.value) || usernameInput.classList.contains('invalid'))) {
        usernameInput.focus();
      } else if (emailInput && (isEmpty(emailInput.value) || emailInput.classList.contains('invalid'))) {
        emailInput.focus();
      } else if (passwordInput) {
        passwordInput.focus();
      }
      
      return false;
    }
  });
});
