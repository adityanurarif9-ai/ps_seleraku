(() => {
 const root=document.querySelector('#rs-live-rental');if(!root||root.dataset.alertOwner!=='true')return;
 const enable=root.querySelector('[data-alarm-enable]'),trySound=root.querySelector('[data-alarm-test]'),volume=root.querySelector('[data-alarm-volume]'),status=root.querySelector('[data-alarm-status]'),dialog=document.querySelector('#rs-timeup-dialog');
 if(!enable||!dialog)return;
 const tr=window.psT||(v=>v);let context=null,enabled=false,repeat=null,voiceTimer=null,rounds=0,alarm=false;const oscillators=new Set(),seen=new Set();
 const level=()=>Math.max(.1,Math.min(.9,Number(volume.value)||.7));
 const sound=()=>{if(!enabled||!context||context.state!=='running')return;[660,880,660].forEach((hz,i)=>{const o=context.createOscillator(),g=context.createGain(),t=context.currentTime+i*.32;o.type='sine';o.frequency.value=hz;g.gain.setValueAtTime(0,t);g.gain.linearRampToValueAtTime(level()*.26,t+.035);g.gain.exponentialRampToValueAtTime(.001,t+.25);o.connect(g);g.connect(context.destination);oscillators.add(o);o.onended=()=>{oscillators.delete(o);o.disconnect();g.disconnect()};o.start(t);o.stop(t+.28)});
  clearTimeout(voiceTimer);voiceTimer=setTimeout(()=>{if(!enabled||!('speechSynthesis'in window))return;window.speechSynthesis.cancel();const speech=new SpeechSynthesisUtterance(tr('Waktu bermainmu habis.'));speech.lang=document.documentElement.lang==='en'?'en-US':'id-ID';speech.volume=level();speech.rate=.95;window.speechSynthesis.speak(speech)},1100);
 };
 const stop=()=>{alarm=false;clearInterval(repeat);repeat=null;clearTimeout(voiceTimer);oscillators.forEach(o=>{try{o.stop()}catch{}});window.speechSynthesis?.cancel();if(dialog.open)dialog.close()};
 enable.addEventListener('click',async()=>{
  if(enabled){enabled=false;stop();enable.textContent=tr('Aktifkan suara & notifikasi');enable.setAttribute('aria-pressed','false');status.textContent=tr('Suara dinonaktifkan. Pesan waktu habis tetap tampil.');return}
  try{const Audio=window.AudioContext||window.webkitAudioContext;if(!Audio)throw new Error('audio');context=context||new Audio();const resume=context.resume();const permission=('Notification'in window&&Notification.permission==='default')?Notification.requestPermission():Promise.resolve();await Promise.allSettled([resume,permission]);enabled=context.state==='running';enable.setAttribute('aria-pressed',String(enabled));enable.textContent=enabled?tr('Suara aktif — matikan'):tr('Aktifkan suara & notifikasi');status.textContent=enabled?tr('Suara aktif selama halaman rental terbuka. Volume mengikuti perangkat.'):tr('Suara belum aktif. Coba tekan tombol lagi.');if(enabled)sound()}catch{status.textContent=tr('Peramban ini belum mendukung suara. Pesan waktu habis tetap tampil.')}
 });
 trySound?.addEventListener('click',()=>{if(enabled)sound();else status.textContent=tr('Aktifkan suara terlebih dahulu.')});
 dialog.querySelectorAll('[data-alarm-stop]').forEach(b=>b.addEventListener('click',stop));dialog.addEventListener('cancel',e=>{e.preventDefault();stop()});
 root.addEventListener('rental:timer',async event=>{
  const d=event.detail;if(d.status!=='playing'||!d.endsAt)return;
  if(d.remaining>0){if(alarm)stop();return}
  const key='ps-timeup:'+root.dataset.reservationId+':'+d.endsAt;
  if(seen.has(key))return;try{if(sessionStorage.getItem(key))return;sessionStorage.setItem(key,'1')}catch{}seen.add(key);alarm=true;rounds=0;if(!dialog.open)dialog.showModal();
  if(enabled){sound();repeat=setInterval(()=>{if(!alarm||++rounds>=8){clearInterval(repeat);return}sound()},8000)}
  if('Notification'in window&&Notification.permission==='granted'){try{const registration=await navigator.serviceWorker?.getRegistration();if(registration)await registration.showNotification(tr('Waktu bermainmu habis.'),{body:tr('Hubungi pengelola untuk menyelesaikan sesi atau menanyakan tambahan waktu.'),icon:'/icons/brand-v2-192.png',tag:key,requireInteraction:true});else new Notification(tr('Waktu bermainmu habis.'),{tag:key})}catch{}}
 });
 window.addEventListener('pagehide',stop);
})();
