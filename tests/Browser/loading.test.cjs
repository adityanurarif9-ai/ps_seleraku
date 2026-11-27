const {test}=require('node:test');
const assert=require('node:assert/strict');
const fs=require('node:fs');
const path=require('node:path');
const vm=require('node:vm');
function setup(welcome=false){
 const classes=new Set(['app-booting']),windowEvents={},events={},timers=[];
 const root={classList:{add:(...names)=>names.forEach(x=>classes.add(x)),remove:(...names)=>names.forEach(x=>classes.delete(x))}};
 const button={dataset:{},setAttribute(){},removeAttribute(){}},bar={hidden:true};
 const doc={readyState:'loading',documentElement:root,querySelector:()=>bar,querySelectorAll:()=>button.dataset.loading==='true'?[button]:[],addEventListener:(name,cb)=>events[name]=cb};
 const win={addEventListener:(name,cb)=>windowEvents[name]=cb,psWelcomeUntil:welcome?Date.now()+3000:0,setTimeout:(fn,ms)=>{timers.push(fn);fn.delay=ms;return timers.length;},matchMedia:()=>({matches:true})};
 vm.runInNewContext(fs.readFileSync(path.join(__dirname,'../../public/loading.js'),'utf8'),{document:doc,window:win,navigator:{},location:{href:'https://ps.test/login',origin:'https://ps.test',pathname:'/login',search:''},sessionStorage:{setItem(){}},clearTimeout(){},queueMicrotask:fn=>fn(),URL});
 return {classes,events,windowEvents,button,bar,timers};
}
test('startup splash and stale loading clear on page show',()=>{
 const s=setup();assert.equal(s.classes.has('app-booting'),false);s.windowEvents.pageshow();assert.equal(s.classes.has('app-booting'),false);assert.equal(s.bar.hidden,true);
});
test('valid native form shows loading while cancelled forms do not',()=>{
 const s=setup(),form={target:'',action:'https://ps.test/login',hasAttribute:()=>false,querySelector:()=>s.button};
 s.events.submit({defaultPrevented:true,target:form,submitter:s.button});assert.equal(s.bar.hidden,true);
 s.events.submit({defaultPrevented:false,target:form,submitter:s.button});assert.equal(s.bar.hidden,false);assert.equal(s.button.dataset.loading,'true');
 s.windowEvents.pageshow();assert.equal(s.bar.hidden,true);assert.equal(s.button.dataset.loading,undefined);
});
test('same site navigation shows progress and external or hash links do not',()=>{
 const s=setup();const click=href=>s.events.click({button:0,defaultPrevented:false,target:{closest:()=>({href,target:'',hasAttribute:()=>false})}});
 click('https://accounts.google.com');assert.equal(s.bar.hidden,true);
 click('https://ps.test/login#info');assert.equal(s.bar.hidden,true);
 click('https://ps.test/rental-saya');assert.equal(s.bar.hidden,false);
 s.timers.at(-1)();assert.equal(s.bar.hidden,true);
});

test('welcome lasts three seconds despite load and page show, then does not trap restored pages',()=>{ const s=setup(true);assert.equal(s.classes.has('app-booting'),true);s.windowEvents.load();s.windowEvents.pageshow();assert.equal(s.classes.has('app-booting'),true);assert.ok(s.timers[0].delay>2900&&s.timers[0].delay<=3000);s.timers[0]();assert.equal(s.classes.has('app-booting'),false);const b=setup(true);b.windowEvents.pageshow({persisted:true});assert.equal(b.classes.has('app-booting'),false);});
