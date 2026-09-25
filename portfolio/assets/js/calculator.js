(() => {
  const root=document.querySelector('.calculator'), display=document.querySelector('#display'), expression=document.querySelector('#expression'), history=document.querySelector('#history');
  let input='', base=10, memory=0, justEvaluated=false;
  const mask=n=>(BigInt(Math.trunc(n))>>>0); const format=n=>{const v=mask(n);return base===16?v.toString(16).toUpperCase():v.toString(base)};
  function render(){display.textContent=input||'0'; expression.textContent=justEvaluated?'Result':''; document.querySelectorAll('[data-base]').forEach(b=>b.classList.toggle('active',+b.dataset.base===base)); document.querySelectorAll('[data-value]').forEach(b=>{const v=b.dataset.value;b.disabled=base===2&&/[2-9]/.test(v)||base===8&&/[89]/.test(v)||base===16&&false;});}
  function val(){let s=input.trim();if(!s)return 0;let radix=base;return parseInt(s,radix)||0}
  function calculate(){try{let s=input.replace(/\b[0-9A-F]+\b/gi,m=>parseInt(m,base));if(!/^[0-9+*/%&|^<>()~.\s-]+$/.test(s))throw Error();let result=Function('return '+s)();if(!Number.isFinite(result))throw Error();let shown=format(result);history.innerHTML='<li>'+input+' = '+shown+' ('+baseName()+')</li>'+history.innerHTML;input=shown;justEvaluated=true;render()}catch(e){display.textContent='Error';}}
  const baseName=()=>({2:'BIN',8:'OCT',10:'DEC',16:'HEX'})[base];
  document.querySelectorAll('[data-value]').forEach(b=>b.addEventListener('click',()=>{if(justEvaluated&&!/[+\-*/&|^<>]/.test(b.dataset.value))input='';justEvaluated=false;input+=b.dataset.value;render()}));
  document.querySelectorAll('[data-base]').forEach(b=>b.addEventListener('click',()=>{if(input)input=format(val());base=+b.dataset.base;render()}));
  document.querySelectorAll('[data-action]').forEach(b=>b.addEventListener('click',()=>{const a=b.dataset.action;if(a==='clear'){input='';justEvaluated=false}if(a==='backspace')input=input.slice(0,-1);if(a==='equals')calculate();if(a==='memory-clear')memory=0;if(a==='memory-recall'){input=format(memory);justEvaluated=false}if(a==='memory-add')memory+=val();if(a==='memory-subtract')memory-=val();render()}));
  document.addEventListener('keydown',e=>{if(e.key==='Enter'){e.preventDefault();calculate()}else if(e.key==='Escape'){input='';render()}else if(/^[0-9a-fA-F+*/%&|^<>()~.\-]$/.test(e.key)){input+=e.key;render()}else if(e.key==='Backspace'){input=input.slice(0,-1);render()}}); render();
})();
