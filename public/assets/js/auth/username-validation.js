// Walidacja długości username w czasie rzeczywistym
document.addEventListener('DOMContentLoaded', function() {
  const usernameInput = document.getElementById('username');
  
  if (!usernameInput) return;
  
  // Sprawdź czy istnieje już komunikat walidacyjny
  let validationMessage = usernameInput.parentElement.querySelector('.username-validation-message');
  
  // Jeśli nie istnieje, stwórz go
  if (!validationMessage) {
    validationMessage = document.createElement('div');
    validationMessage.className = 'username-validation-message';
    usernameInput.parentElement.appendChild(validationMessage);
  }
  
  // Wymagania walidacji
  const MIN_LENGTH = 3;
  const MAX_LENGTH = 50;
  
  // Funkcja walidująca username
  function validateUsername(username) {
    if (!username || username.trim().length === 0) {
      return { valid: null, message: '' };
    }
    
    const trimmedUsername = username.trim();
    const length = trimmedUsername.length;
    
    // Sprawdź minimalną długość
    if (length < MIN_LENGTH) {
      return { valid: false, message: `Nazwa użytkownika musi zawierać minimum ${MIN_LENGTH} znaki` };
    }
    
    // Sprawdź maksymalną długość
    if (length > MAX_LENGTH) {
      return { valid: false, message: `Nazwa użytkownika może zawierać maksymalnie ${MAX_LENGTH} znaków` };
    }
    
    // Sprawdź czy zawiera tylko dozwolone znaki (litery, cyfry, podkreślenia)
    const allowedCharsRegex = /^[a-zA-Z0-9_]+$/;
    if (!allowedCharsRegex.test(trimmedUsername)) {
      return { valid: false, message: 'Nazwa użytkownika może zawierać tylko litery, cyfry i podkreślenia' };
    }
    
    return { valid: true, message: '' };
  }
  
  // Funkcja aktualizująca UI walidacji
  function updateValidationUI() {
    const username = usernameInput.value;
    const result = validateUsername(username);
    
    // Usuń poprzednie klasy
    usernameInput.classList.remove('valid', 'invalid');
    validationMessage.classList.remove('valid', 'invalid', 'hidden');
    
    if (result.valid === null) {
      // Pole puste - ukryj komunikat
      validationMessage.classList.add('hidden');
      validationMessage.textContent = '';
    } else if (result.valid) {
      // Username prawidłowy - tylko zielona obramówka, bez komunikatu
      usernameInput.classList.add('valid');
      validationMessage.classList.add('hidden');
      validationMessage.textContent = '';
    } else {
      // Username nieprawidłowy - czerwona obramówka i komunikat
      usernameInput.classList.add('invalid');
      validationMessage.classList.add('invalid');
      validationMessage.innerHTML = result.message;
    }
    
    // Wyemituj custom event dla głównej walidacji
    usernameInput.dispatchEvent(new Event('validationChange', { bubbles: true }));
  }
  
  // Nasłuchuj zmian w polu username
  usernameInput.addEventListener('input', updateValidationUI);
  usernameInput.addEventListener('blur', updateValidationUI);
});
