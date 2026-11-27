(() => {
 const form=document.querySelector('[data-demo-checkout]');if(!form)return;
 const tr=window.psT||(x=>x),methods=form.querySelector('[data-pay-methods]'),instructions=form.querySelector('[data-pay-instructions]'),next=form.querySelector('[data-pay-next]'),back=form.querySelector('[data-pay-back]'),confirm=form.querySelector('[data-pay-confirm]'),consent=form.querySelector('[data-pay-consent]'),status=form.querySelector('[data-pay-status]');
 instructions.hidden=true;next.hidden=false;back.hidden=false;confirm.disabled=true;
 next.addEventListener('click',()=>{const selected=form.querySelector('[name="method"]:checked');if(!selected)return;form.querySelector('[data-pay-method-title]').textContent=selected.parentElement.querySelector('span').textContent;form.querySelector('[data-pay-qr]').hidden=selected.value!=='qris';form.querySelector('[data-pay-wallet]').hidden=selected.value==='qris';methods.hidden=true;instructions.hidden=false;document.querySelector('[data-pay-step="2"]').classList.add('is-active');back.focus();});
 back.addEventListener('click',()=>{methods.hidden=false;instructions.hidden=true;document.querySelector('[data-pay-step="2"]').classList.remove('is-active');next.focus()});
 consent.addEventListener('change',()=>{confirm.disabled=!consent.checked});
 form.addEventListener('submit',event=>{if(event.submitter===confirm&&!consent.checked){event.preventDefault();status.textContent=tr('Centang persetujuan simulasi terlebih dahulu.');return}form.setAttribute('aria-busy','true');status.textContent=tr('Memproses simulasi…');});
 form.querySelector('[data-pay-copy]').addEventListener('click',async()=>{try{await navigator.clipboard.writeText(form.querySelector('[data-pay-code]').textContent);status.textContent=tr('Kode demo disalin. Jangan dipakai untuk transfer sungguhan.')}catch{status.textContent=tr('Salin kode demo yang tertera secara manual.')}});
})();
