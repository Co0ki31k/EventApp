(function(){
  // Kod do obsługi pola 6-cyfrowego kodu weryfikacyjnego
  const inputs = Array.from(document.querySelectorAll('.code-inputs .digit'));
  const hidden = document.getElementById('codeInput');
  const form = document.getElementById('verifyForm');
  if (!inputs.length) return;

  inputs[0].focus();
  inputs.forEach((input, idx) => {
    input.addEventListener('input', (e) => {
      const v = input.value.replace(/[^0-9]/g,'');
      input.value = v;
      if (v.length === 1 && idx < inputs.length -1) inputs[idx+1].focus();
      updateHidden();
    });
    input.addEventListener('keydown', (e) => {
      if (e.key === 'Backspace' && input.value === '' && idx > 0) {
        inputs[idx-1].focus();
      }
    });
    input.addEventListener('paste', (e) => {
      e.preventDefault();
      const paste = (e.clipboardData || window.clipboardData).getData('text') || '';
      const digits = paste.replace(/\D/g,'').slice(0, inputs.length).split('');
      digits.forEach((d,i)=>{ if (inputs[i]) inputs[i].value=d; });
      updateHidden();
    });
  });

  function updateHidden(){
    if (!hidden) return;
    hidden.value = inputs.map(i=>i.value||'').join('');
  }

  if (form) {
    form.addEventListener('submit', (e) => {
      updateHidden();
      if (!hidden || hidden.value.length !== inputs.length) {
        e.preventDefault();
        alert('Proszę wpisać pełny 6-cyfrowy kod.');
      }
    });
  }

  // Countdown
  const countdownEl = document.getElementById('verify-countdown');
  if (countdownEl) {
    let secs = parseInt(countdownEl.textContent,10) || 0;
    const t = setInterval(()=>{
      secs -= 1; if (secs < 0) { clearInterval(t); countdownEl.textContent = '0'; return; }
      countdownEl.textContent = secs;
    }, 1000);
  }
})();
