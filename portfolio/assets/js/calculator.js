(() => {
  const display = document.querySelector('#display');
  const expression = document.querySelector('#expression');
  const history = document.querySelector('#history');
  let input = '';
  let base = 10;
  let memory = 0;
  let justEvaluated = false;

  const baseNames = { 2: 'BIN', 8: 'OCT', 10: 'DEC', 16: 'HEX' };
  const digitPattern = { 2: /[^01]/i, 8: /[^0-7]/i, 10: /[^0-9]/i, 16: /[^0-9a-f]/i };
  const toUint32 = value => Number(BigInt.asUintN(32, BigInt(Math.trunc(value))));
  const format = value => toUint32(value).toString(base).toUpperCase();
  const isValidNumber = value => value !== '' && !digitPattern[base].test(value);

  function render() {
    display.textContent = input || '0';
    expression.textContent = justEvaluated ? 'Result' : `${baseNames[base]} mode`;
    document.querySelectorAll('[data-base]').forEach(button => {
      button.classList.toggle('active', Number(button.dataset.base) === base);
    });
    document.querySelectorAll('[data-value]').forEach(button => {
      const value = button.dataset.value;
      button.disabled = /^[0-9A-F]$/i.test(value) &&
        digitPattern[base].test(value);
    });
  }

  function currentValue() {
    const number = input.match(/[0-9A-F]+$/i)?.[0] || '';
    return isValidNumber(number) ? parseInt(number, base) : 0;
  }

  function evaluate() {
    if (!input.trim()) return;
    try {
      // Convert the displayed base into decimal literals, then allow only
      // calculator operators. Function is never called with unsanitized input.
      const decimalExpression = input.replace(/[0-9A-F]+/gi, token => {
        if (!isValidNumber(token)) throw new Error('Invalid digit');
        return String(parseInt(token, base));
      });
      if (!/^[0-9+*/%&|^<>()~.\s-]+$/.test(decimalExpression)) throw new Error('Invalid expression');
      const result = Function(`"use strict"; return (${decimalExpression})`)();
      if (!Number.isFinite(result)) throw new Error('Invalid result');
      const shown = format(result);
      const item = document.createElement('li');
      item.textContent = `${input} = ${shown} (${baseNames[base]})`;
      history.prepend(item);
      input = shown;
      justEvaluated = true;
    } catch {
      display.textContent = 'Error';
      expression.textContent = 'Invalid expression or digit';
    }
    render();
  }

  document.querySelectorAll('[data-value]').forEach(button => {
    button.addEventListener('click', () => {
      const value = button.dataset.value;
      if (justEvaluated && !/[+\-*/&|^<>]/.test(value)) input = '';
      justEvaluated = false;
      input += value;
      render();
    });
  });

  document.querySelectorAll('[data-base]').forEach(button => {
    button.addEventListener('click', () => {
      if (input && !/[+\-*/&|^<>]/.test(input)) input = format(currentValue());
      base = Number(button.dataset.base);
      render();
    });
  });

  document.querySelectorAll('[data-action]').forEach(button => {
    button.addEventListener('click', () => {
      switch (button.dataset.action) {
        case 'clear': input = ''; justEvaluated = false; break;
        case 'backspace': input = input.slice(0, -1); break;
        case 'equals': evaluate(); return;
        case 'memory-clear': memory = 0; break;
        case 'memory-recall': input = format(memory); justEvaluated = false; break;
        case 'memory-add': memory = toUint32(memory + currentValue()); break;
        case 'memory-subtract': memory = toUint32(memory - currentValue()); break;
      }
      render();
    });
  });

  document.addEventListener('keydown', event => {
    if (event.key === 'Enter') { event.preventDefault(); evaluate(); return; }
    if (event.key === 'Escape') { input = ''; justEvaluated = false; render(); return; }
    if (event.key === 'Backspace') { input = input.slice(0, -1); render(); return; }
    if (/^[0-9A-F+*/%&|^<>()~.\-]$/i.test(event.key)) {
      const value = event.key.toUpperCase();
      if (/^[0-9A-F]$/.test(value) && digitPattern[base].test(value)) return;
      input += value;
      justEvaluated = false;
      render();
    }
  });

  render();
})();
