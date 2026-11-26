const test=require('node:test');const assert=require('node:assert/strict');const fs=require('node:fs');const vm=require('node:vm');
function fixture(initial=5400,running=false){
 let time=0,reply=null,fail=true;const timers=new Map();
 const nodes=new Map();const node=s=>{if(!nodes.has(s))nodes.set(s,{textContent:'',hidden:false});return nodes.get(s)};
 const root={dataset:{initialSeconds:String(initial),initialRunning:String(running),statusUrl:'/status'},querySelector:s=>['[data-rs-extensions]','[data-rs-cancel]','.rs-extension'].includes(s)?null:node(s)};
 const context={document:{hidden:false,querySelector:s=>s==='#rs-live-rental'?root:null,querySelectorAll:()=>[],addEventListener(){}},window:{addEventListener(){}},performance:{now:()=>time},Intl,
 fetch:async()=>{if(fail)throw new Error('offline');return {ok:true,json:async()=>reply}},setInterval(fn,ms){timers.set(ms,fn);return ms},clearInterval(id){timers.delete(id)}};
 vm.runInNewContext(fs.readFileSync('public/rentals.js','utf8'),context);
 return {clock:()=>node('[data-rs-clock]').textContent,tick(ms){time+=ms;timers.get(1000)()},async sync(data){reply=data;fail=false;await timers.get(5000)()},async flush(){await Promise.resolve();await Promise.resolve()}};
}
const status=(remaining,running)=>({remaining,running,status:'playing',label:'Sedang bermain',price:'Rp15.000',duration_minutes:90,ends_at:'WIB',hint:running?'Timer sedang berjalan.':'Timer dijeda oleh admin.',extensions:[]});
test('ninety minute booking stays visible before start and during an initial network failure',async()=>{const f=fixture();assert.equal(f.clock(),'01:30:00');await f.flush();f.tick(60000);assert.equal(f.clock(),'01:30:00')});
test('clock ticks from server duration and freezes on pause, then resynchronizes extension',async()=>{const f=fixture();await f.flush();await f.sync(status(5400,true));f.tick(1000);assert.equal(f.clock(),'01:29:59');f.tick(59000);assert.equal(f.clock(),'01:29:00');await f.sync(status(5340,false));f.tick(60000);assert.equal(f.clock(),'01:29:00');await f.sync(status(7140,true));f.tick(1000);assert.equal(f.clock(),'01:58:59');await f.sync(status(0,false));f.tick(1000);assert.equal(f.clock(),'00:00:00')});
