const {test}=require('node:test');
const assert=require('node:assert/strict');
const fs=require('node:fs');
const path=require('node:path');
const vm=require('node:vm');
function page(){
 const formEvents={},windowEvents={},scripts=[],startEvents={},retryEvents={};
 const submit={disabled:true,textContent:'MASUK SEKARANG'},status={textContent:''},retry={hidden:true,addEventListener:(name,cb)=>retryEvents[name]=cb},start={hidden:false,addEventListener:(name,cb)=>startEvents[name]=cb},widget={dataset:{sitekey:'test-site'}};
 const form={dataset:{},querySelector:selector=>selector==='[type="submit"]'?submit:{value:''},addEventListener:(name,cb)=>formEvents[name]=cb};
 const doc={querySelector:selector=>({'.login-form':form,'#verification-status':status,'#verification-retry':retry,'#turnstile-widget':widget,'#verification-start':start})[selector]??null,querySelectorAll:()=>[],createElement:()=>({remove(){}}),head:{append:script=>scripts.push(script)}};
 const win={addEventListener:(name,cb)=>windowEvents[name]=cb};
 vm.runInNewContext(fs.readFileSync(path.join(__dirname,'../../public/login.js'),'utf8'),{document:doc,window:win,setTimeout:()=>1,clearTimeout(){},location:{hostname:'ps.test'}});
 return {formEvents,scripts,startEvents,retryEvents,win,submit,retry};
}
test('verification downloads no third party script before user interacts',()=>{
 const p=page();assert.equal(p.scripts.length,0);p.formEvents.focusin();assert.equal(p.scripts.length,1);assert.match(p.scripts[0].src,/^https:\/\/challenges.cloudflare.com\/turnstile/);p.formEvents.input();assert.equal(p.scripts.length,1);
});
test('failed verification download can retry without losing entered form',()=>{
 const p=page();p.startEvents.click();p.scripts[0].onerror();assert.equal(p.retry.hidden,false);p.retryEvents.click();assert.equal(p.scripts.length,2);
});
test('unverified form cannot submit and verified state expires correctly',()=>{
 const p=page();let prevented=false;p.formEvents.submit({preventDefault(){prevented=true;}});assert.equal(prevented,true);assert.equal(p.submit.disabled,true);
 p.win.onTurnstileSuccess();assert.equal(p.submit.disabled,false);p.win.onTurnstileExpired();assert.equal(p.submit.disabled,true);
});
