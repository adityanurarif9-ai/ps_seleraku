const {test}=require('node:test');
const assert=require('node:assert/strict');
const fs=require('node:fs');
const vm=require('node:vm');
const path=require('node:path');
const root=path.resolve(__dirname,'../..');
const code=fs.readFileSync(path.join(root,'public/pwa.js'),'utf8');
function browser(ua='Android',installed=false){
 const events={}, clicks={}, closeEvents=[];
 const promo={hidden:false}, button={textContent:'Pasang',addEventListener:(name,cb)=>{clicks[name]=cb;}}, steps={children:[],replaceChildren(...nodes){this.children=nodes;}}, intro={textContent:''};
 const dialog={open:false,showModal(){this.open=true;},close(){this.open=false;},querySelector:s=>s==='[data-install-steps]'?steps:intro,querySelectorAll:()=>[{addEventListener:(name,cb)=>closeEvents.push(cb)}]};
 const nav={userAgent:ua,platform:'',maxTouchPoints:0,serviceWorker:{register:()=>Promise.resolve()}};
 const win={matchMedia:()=>({matches:installed,addEventListener(){}}),addEventListener:(name,cb)=>{events[name]=cb;},isSecureContext:true};
 const doc={querySelectorAll:s=>s==='[data-install-promotion]'?[promo]:s==='[data-install-app]'?[button]:s==='[data-close-install]'?[{addEventListener:(name,cb)=>closeEvents.push(cb)}]:[],querySelector:()=>dialog,createElement:()=>({textContent:''})};
 vm.runInNewContext(code,{window:win,document:doc,navigator:nav});
 return {events,clicks,promo,button,dialog,steps,closeEvents};
}
test('iPhone shows Safari home-screen instructions without pretending to install',async()=>{
 const b=browser('iPhone');await b.clicks.click();assert.equal(b.dialog.open,true);assert.ok(b.steps.children.some(n=>n.textContent.includes('Safari')));assert.ok(b.steps.children.some(n=>n.textContent.includes('Tambah ke Layar Utama')));b.closeEvents[0]();assert.equal(b.dialog.open,false);
});
test('Android without native prompt shows the browser installation steps',async()=>{
 const b=browser();await b.clicks.click();assert.ok(b.steps.children.some(n=>n.textContent.includes('Chrome')));assert.ok(b.steps.children.some(n=>n.textContent.includes('Instal aplikasi')));
});
test('native install prompt only runs after user clicks and installed app hides promotion',async()=>{
 const b=browser();let prompted=0,prevented=false;
 b.events.beforeinstallprompt({preventDefault(){prevented=true;},async prompt(){prompted++;},userChoice:Promise.resolve({outcome:'accepted'})});
 assert.equal(prevented,true);assert.equal(prompted,0);await b.clicks.click();assert.equal(prompted,1);b.events.appinstalled();assert.equal(b.promo.hidden,true);
});
test('already installed app does not show installation banner',()=>{assert.equal(browser('Android',true).promo.hidden,true);});
function worker(failNetwork=false){
 const events={}, calls=[], deleted=[];
 const cache={addAll:async urls=>calls.push(['precache',...urls])};
 const caches={open:async()=>cache,keys:async()=>['unrelated-cache','ps-seleraku-static-v0','ps-seleraku-static-v1'],delete:async key=>deleted.push(key),match:async key=>{calls.push(['match',key]);return {offline:true};}};
 const self={location:{origin:'https://ps.test'},clients:{claim:async()=>{}},skipWaiting:async()=>{},addEventListener:(name,cb)=>events[name]=cb};
 vm.runInNewContext(fs.readFileSync(path.join(root,'public/sw.js'),'utf8'),{self,caches,URL,fetch:async()=>{calls.push(['network']);if(failNetwork)throw Error('offline');return {private:true};}});
 const dispatch=(url,method='GET',mode='navigate')=>{let response;events.fetch({request:{url,method,mode},respondWith:p=>response=p});return response;};
 return {events,calls,deleted,dispatch};
}
test('worker precaches only public offline page and icons and preserves unrelated caches',async()=>{
 const w=worker();let p;w.events.install({waitUntil:value=>p=value});await p;assert.deepEqual(w.calls[0],['precache','/offline.html','/icons/app-192.png','/icons/app-512.png']);
 w.events.activate({waitUntil:value=>p=value});await p;assert.deepEqual(w.deleted,['ps-seleraku-static-v0']);
});
test('worker never intercepts POST, OAuth, admin, API polling or external requests',()=>{
 const w=worker();for(const args of [['https://ps.test/reservasi','POST'],['https://ps.test/auth/google/callback'],['https://ps.test/admin'],['https://ps.test/reservasi/1/status','GET','cors'],['https://accounts.google.com']])assert.equal(w.dispatch(...args),undefined);
});
test('private navigation goes to network and only falls back to public offline page',async()=>{
 const w=worker();assert.equal((await w.dispatch('https://ps.test/rental-saya')).private,true);assert.deepEqual(w.calls,[['network']]);
 const offline=worker(true);assert.equal((await offline.dispatch('https://ps.test/rental-saya')).offline,true);assert.deepEqual(offline.calls,[['network'],['match','/offline.html']]);
});
