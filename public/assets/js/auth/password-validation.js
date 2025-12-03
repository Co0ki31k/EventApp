// Walidacja hasła w czasie rzeczywistym
document.addEventListener('DOMContentLoaded', function() {
  const passwordInput = document.getElementById('password');
  
  if (!passwordInput) return;
  
  const checklistItems = document.querySelectorAll('.checklist-item');
  const strengthItem = document.querySelector('.password-strength');
  const strengthValue = document.querySelector('.strength-value');
  const submitButton = document.querySelector('.form-register button[type="submit"]');
  
  // Reguły walidacji
  const rules = {
    length: (password) => password.length >= 8,
    uppercase: (password) => /[A-Z]/.test(password),
    lowercase: (password) => /[a-z]/.test(password),
    number: (password) => /[0-9]/.test(password),
    specialcase: (password) => /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)
  };
  
  // Funkcja obliczająca siłę hasła
  function calculateStrength(password) {
    if (!password || password.length === 0) return { text: 'słabe', class: 'weak' };
    
    let score = 0;
    if (password.length >= 8) score++;
    if (password.length >= 12) score++;
    if (/[a-z]/.test(password)) score++;
    if (/[A-Z]/.test(password)) score++;
    if (/[0-9]/.test(password)) score++;
    if (/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)) score++;
    
    if (score <= 3) return { text: 'słabe', class: 'weak' };
    if (score <= 5) return { text: 'średnie', class: 'medium' };
    return { text: 'silne', class: 'strong' };
  }
  
  // Funkcja sprawdzająca czy wszystkie wymagania są spełnione
  function areAllRequirementsMet(password) {
    return rules.length(password) &&
           rules.uppercase(password) &&
           rules.lowercase(password) &&
           rules.number(password) &&
           rules.specialcase(password);
  }
  
  // Funkcja aktualizująca przycisk submit
  function updateSubmitButton(password) {
    if (!submitButton) return;
    
    if (areAllRequirementsMet(password)) {
      submitButton.disabled = false;
      submitButton.style.opacity = '1';
      submitButton.style.cursor = 'pointer';
      submitButton.title = '';
    } else {
      submitButton.disabled = true;
      submitButton.style.opacity = '0.5';
      submitButton.style.cursor = 'not-allowed';
      submitButton.title = 'Hasło musi spełniać wszystkie wymagania';
    }
  }
  
  // Funkcja aktualizująca checklistę
  function updateChecklist() {
    const password = passwordInput.value;
    const strength = calculateStrength(password);
    
    // Aktualizuj siłę hasła
    if (strengthValue && strengthItem) {
      strengthValue.textContent = strength.text;
      strengthItem.classList.remove('weak', 'medium', 'strong');
      strengthItem.classList.add(strength.class);
      
      const strengthIcon = strengthItem.querySelector('.check-icon');
      if (strengthIcon) {
        if (strength.class === 'strong') {
          strengthIcon.textContent = '✓';
        } else {
          strengthIcon.textContent = 'X';
        }
      }
    }
    
    // Aktualizuj checklistę
    checklistItems.forEach(item => {
      if (item.classList.contains('password-strength')) return;
      
      const rule = item.getAttribute('data-rule');
      const isValid = rules[rule] && rules[rule](password);
      const icon = item.querySelector('.check-icon');
      
      if (isValid) {
        item.classList.add('valid');
        icon.textContent = '✓';
      } else {
        item.classList.remove('valid');
        icon.textContent = 'X';
      }
    });
    
    // Aktualizuj przycisk submit
    updateSubmitButton(password);
  }
  
  // Inicjalizacja - zablokuj przycisk na start
  if (submitButton) {
    submitButton.disabled = true;
    submitButton.style.opacity = '0.5';
    submitButton.style.cursor = 'not-allowed';
    submitButton.title = 'Hasło musi spełniać wszystkie wymagania';
  }
  
  // Nasłuchuj zmian w polu hasła
  passwordInput.addEventListener('input', updateChecklist);
  passwordInput.addEventListener('keyup', updateChecklist);
  
  // Dodatkowa walidacja przed wysłaniem formularza
  const form = document.querySelector('.form-register');
  if (form) {
    form.addEventListener('submit', function(e) {
      const password = passwordInput.value;
      if (!areAllRequirementsMet(password)) {
        e.preventDefault();
        alert('Hasło musi spełniać wszystkie wymagania z listy!');
        return false;
      }
    });
  }
});

