const test=require('node:test'),assert=require('node:assert/strict'),vm=require('node:vm'),fs=require('node:fs');
function fixture(owner=true){
 const listeners={},buttons={},timers=new Map(),storage=new Map();let shown=0,tones=0,notifications=0;
 function node(){return {value:'.7',addEventListener(t,f){this[t]=f},setAttribute(){},textContent:''}}
 const enable=node(),status=node(),volume=node(),trySound=node(),stop=node();
 const dialog={open:false,showModal(){this.open=true;shown++},close(){this.open=false},querySelectorAll(){return [stop]},addEventListener(){}};
 const root={dataset:{alertOwner:String(owner),reservationId:'90'},querySelector(s){return {'[data-alarm-enable]':enable,'[data-alarm-status]':status,'[data-alarm-volume]':volume,'[data-alarm-test]':trySound}[s]},addEventListener(t,f){listeners[t]=f}};
 class Audio {state='running';currentTime=0;resume(){return Promise.resolve()}createOscillator(){return {frequency:{},connect(){},disconnect(){},start(){tones++},stop(){}}}createGain(){return {gain:{setValueAtTime(){},linearRampToValueAtTime(){},exponentialRampToValueAtTime(){}},connect(){},disconnect(){}}}}
 const window={AudioContext:Audio,addEventListener(){}};
 const ctx={window,document:{querySelector:s=>s==='#rs-live-rental'?root:dialog,documentElement:{lang:'id'}},sessionStorage:{getItem:k=>storage.get(k),setItem:(k,v)=>storage.set(k,v)},navigator:{},setInterval(f,ms){timers.set(ms,f);return ms},clearInterval:id=>timers.delete(id),setTimeout(){return 1},clearTimeout(){},Promise,Set,Number,Math};
 vm.runInNewContext(fs.readFileSync('public/rental-alert.js','utf8'),ctx);
 return {enable,stop,dialog,get shown(){return shown},get tones(){return tones},timers,async emit(status,remaining,endsAt='end1'){await listeners['rental:timer']?.({detail:{status,remaining,endsAt}})}};
}
test('expiry shows once per end time and is silent until explicitly enabled',async()=>{const f=fixture();await f.emit('pending',0);await f.emit('confirmed',0);await f.emit('playing',90);assert.equal(f.shown,0);await f.emit('playing',0);assert.equal(f.shown,1);assert.equal(f.tones,0);f.stop.click();await f.emit('playing',0);assert.equal(f.shown,1);await f.emit('playing',0,'end2');assert.equal(f.shown,2)});
test('sound requires gesture, repeats for a bounded time, and stops on acknowledgement or extension',async()=>{const f=fixture();await f.enable.click();assert.equal(f.tones,3);await f.emit('playing',0);assert.equal(f.tones,6);f.timers.get(8000)();assert.equal(f.tones,9);f.stop.click();assert.equal(f.timers.size,0);assert.equal(f.dialog.open,false);await f.emit('playing',0,'end2');await f.emit('playing',300,'end3');assert.equal(f.dialog.open,false);assert.equal(f.timers.size,0)});
test('nonowners never initialize alarm controls',async()=>{const f=fixture(false);assert.equal(f.enable.click,undefined);await f.emit('playing',0);assert.equal(f.shown,0)});
