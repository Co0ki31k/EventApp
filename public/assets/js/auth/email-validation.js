// Walidacja email w czasie rzeczywistym
document.addEventListener('DOMContentLoaded', function() {
  const emailInput = document.getElementById('email');
  
  if (!emailInput) return;
  
  // Sprawdź czy istnieje już komunikat walidacyjny
  let validationMessage = emailInput.parentElement.querySelector('.email-validation-message');
  
  // Jeśli nie istnieje, stwórz go
  if (!validationMessage) {
    validationMessage = document.createElement('div');
    validationMessage.className = 'email-validation-message';
    emailInput.parentElement.appendChild(validationMessage);
  }
  
  // Regex dla walidacji email (zgodny ze standardem RFC 5322)
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  
  // Bardziej restrykcyjny regex dla lepszej walidacji
  const strictEmailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
  
  // Funkcja walidująca format email
  function validateEmail(email) {
    if (!email || email.trim().length === 0) {
      return { valid: null, message: '' };
    }
    
    // Sprawdź podstawową strukturę
    if (!emailRegex.test(email)) {
      return { valid: false, message: 'Nieprawidłowy format adresu email' };
    }
    
    // Sprawdź dodatkowe warunki
    if (email.length > 254) {
      return { valid: false, message: 'Adres email jest za długi' };
    }
    
    // Sprawdź część przed @
    const parts = email.split('@');
    if (parts[0].length > 64) {
      return { valid: false, message: 'Nazwa użytkownika jest za długa' };
    }
    
    // Sprawdź czy nie zaczyna się ani nie kończy kropką
    if (parts[0].startsWith('.') || parts[0].endsWith('.')) {
      return { valid: false, message: 'Nieprawidłowy format adresu email' };
    }
    
    // Sprawdź czy nie ma podwójnych kropek
    if (email.includes('..')) {
      return { valid: false, message: 'Nieprawidłowy format adresu email' };
    }
    
    // Sprawdź restrykcyjniejszą walidację
    if (!strictEmailRegex.test(email)) {
      return { valid: false, message: 'Nieprawidłowy format adresu email' };
    }
    
    return { valid: true, message: 'Prawidłowy adres email' };
  }
  
  // Funkcja aktualizująca UI walidacji
  function updateValidationUI() {
    const email = emailInput.value.trim();
    const result = validateEmail(email);
    
    // Usuń poprzednie klasy
    emailInput.classList.remove('valid', 'invalid');
    validationMessage.classList.remove('valid', 'invalid', 'hidden');
    
    if (result.valid === null) {
      // Pole puste - ukryj komunikat
      validationMessage.classList.add('hidden');
      validationMessage.textContent = '';
    } else if (result.valid) {
      // Email prawidłowy - tylko zielona obramówka, bez komunikatu
      emailInput.classList.add('valid');
      validationMessage.classList.add('hidden');
      validationMessage.textContent = '';
    } else {
      // Email nieprawidłowy - czerwona obramówka i komunikat
      emailInput.classList.add('invalid');
      validationMessage.classList.add('invalid');
      validationMessage.innerHTML = result.message;
    }
    
    // Wyemituj custom event dla głównej walidacji
    emailInput.dispatchEvent(new Event('validationChange', { bubbles: true }));
  }
  
  // Nasłuchuj zmian w polu email
  emailInput.addEventListener('input', updateValidationUI);
  emailInput.addEventListener('blur', updateValidationUI);
});
