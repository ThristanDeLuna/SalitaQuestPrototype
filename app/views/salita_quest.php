<!DOCTYPE html>
<html lang="en"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>Salita Quest</title>
<style>
:root{--bg:#fdf8f2;--side:#ddd0f5;--sideA:#c7b8ee;--ink:#1f1b2e;--pri:#3b2a78;--card:#fff;--line:#d9d3e6;--peach:#ffb199;--peachD:#6b2410;--mint:#8fd3b0;--ok:#e0f3e8;--okT:#1c6b3f;--hd:#ffeab5;--info:#e3edf3;--sea:#d3ebf6;--land:#c1e8cf;box-sizing:border-box;padding-top:env(safe-area-inset-top,0px);padding-bottom:env(safe-area-inset-bottom,0px)}
@media (prefers-color-scheme:dark){:root:not([data-theme="light"]){--bg:#17141f;--side:#2a2145;--sideA:#40336b;--ink:#eee9fb;--pri:#c9b9ff;--card:#221d30;--line:#443c5c;--peach:#c4684f;--peachD:#fff;--ok:#1d3a2b;--okT:#8fe0b2;--hd:#4a4020;--info:#223341;--sea:#1d3342;--land:#2d5a45}}
*{box-sizing:border-box}html{scroll-padding-top:env(safe-area-inset-top,0px)}
body{margin:0;background:var(--bg);color:var(--ink);font:15px/1.45 system-ui,Arial,sans-serif;min-height:100vh}
h1,h2{font-family:Georgia,serif;color:var(--pri);margin:0 0 6px}
.app{display:flex;min-height:100vh}
aside{width:220px;background:var(--side);padding:20px 0;display:flex;flex-direction:column;flex-shrink:0}
.brand{padding:0 20px 18px}.brand b{font:700 20px Georgia,serif;color:var(--pri);display:block}
nav a{display:block;padding:10px 20px;cursor:pointer;border-left:4px solid transparent}
nav a.on{background:var(--sideA);border-color:var(--pri);font-weight:600}
.foot{margin-top:auto;padding:16px 20px;font-size:13px}.foot button{width:100%;margin-top:8px}
main{flex:1;padding:28px 32px;min-width:0;position:relative}
.sub{font-size:13px;opacity:.8;margin:0 0 18px}
button{font:600 14px system-ui;padding:10px 16px;border:1px solid var(--pri);background:var(--card);color:var(--pri);border-radius:3px;cursor:pointer}
button.p{background:var(--peach);border-color:var(--peach);color:var(--peachD)}
button:disabled{opacity:.5;cursor:default}
.row{display:flex;gap:12px;flex-wrap:wrap;margin-top:16px}
.card{background:var(--card);border:1px solid var(--line);border-left:4px solid var(--mint);padding:20px;max-width:760px}
.grid{display:flex;gap:20px;flex-wrap:wrap}
.map{position:relative;flex:1 1 420px;min-height:380px;background:var(--sea);border:1px solid var(--line);overflow:hidden}
.blob{position:absolute;background:var(--land);border-radius:50%}
.pin{position:absolute;padding:9px 14px;background:var(--card);border:1px solid var(--line);font-weight:700;cursor:pointer;color:var(--ink)}
.pin.on{background:var(--peach);border-color:#c0583a;color:var(--peachD)}
.side{flex:0 1 340px}
.kv{display:flex;justify-content:space-between;padding:9px 0;border-bottom:1px solid var(--line)}
table{width:100%;border-collapse:collapse;background:var(--card);border:1px solid var(--line)}
th{background:var(--hd);text-align:left;font-size:12px;letter-spacing:.05em;padding:12px;text-transform:uppercase}
td{padding:12px;border-top:1px solid var(--line)}tr:nth-child(even) td{background:rgba(255,234,181,.12)}
.tbl{overflow-x:auto}
.badge{display:inline-block;font-size:13px;padding:3px 9px;border:1px solid #5b8aa6;background:var(--info);border-radius:3px}
.badge.ok{background:var(--ok);color:var(--okT);border-color:var(--okT)}
.badge.pd{background:var(--hd);border-color:#b08a1a}
.flash{max-width:700px;min-height:220px;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;cursor:pointer}
.flash h2{font-size:38px}
.opt{display:block;width:100%;text-align:left;margin:8px 0;padding:13px 16px;color:var(--ink);font-weight:600}
.opt.sel{background:rgba(124,92,210,.14);border:2px solid #7c5cd2}
.opt.done{background:var(--ok);color:var(--okT);border-color:var(--okT)}
.opt.bad{background:#f9e0df;color:#9a2a22;border-color:#9a2a22}
.two{display:grid;grid-template-columns:1fr 1fr;gap:12px;max-width:760px}
.bar{height:16px;border:1px solid var(--line);background:var(--card);max-width:760px}.bar i{display:block;height:100%;background:#5fbd8a}
.score{position:absolute;right:32px;top:24px;text-align:right;font-size:13px}.score b{display:block;font:700 20px ui-monospace,monospace}
.msg{background:var(--ok);border:1px solid var(--okT);color:var(--okT);padding:12px 16px;margin-bottom:14px;max-width:760px}
.msg.err{background:#f9e0df;border-color:#9a2a22;color:#9a2a22}
.note{background:var(--info);border:1px solid #5b8aa6;padding:12px 16px;margin-top:16px;max-width:760px}
label{display:block;font-weight:700;margin:12px 0 4px}input{width:100%;padding:11px;border:1px solid var(--line);background:var(--card);color:var(--ink);font:inherit}
.mono{font-family:ui-monospace,monospace}
.admin aside{background:#cfe5f4}
@media(max-width:700px){.app{flex-direction:column}aside{width:auto;padding:10px 0}nav{display:flex;overflow-x:auto}nav a{border-left:0;white-space:nowrap}.foot{display:none}main{padding:18px 16px}.score{position:static;text-align:left;margin-bottom:8px}}
</style></head><body>
<div class="app" id="app"></div>
<script>
const D={
Tagalog:{area:'Luzon',w:[['Kumusta','How are you','Kumusta ka na?'],['Salamat','Thank you','Salamat sa tulong mo.'],['Tubig','Water','Gusto ko ng tubig.'],['Maganda','Beautiful','Maganda ang umaga.'],['Kain','Eat','Kain na tayo.']],pos:[22,28],lang:'fil-PH'},
Hiligaynon:{area:'Panay',w:[['Kamusta','How are you','Kamusta ka na?'],['Salamat','Thank you','Salamat sa bulig mo.'],['Tubig','Water','Gusto ko sang tubig.'],['Matahum','Beautiful','Matahum ang aga.'],['Kaon','Eat','Kaon na kita.']],pos:[40,52],lang:'fil-PH'},
Bisaya:{area:'Visayas',w:[['Kumusta','How are you','Kumusta ka na?'],['Salamat','Thank you','Salamat sa tabang.'],['Tubig','Water','Gusto ko og tubig.'],['Nindot','Beautiful','Nindot ang buntag.'],['Kaon','Eat','Kaon na ta.']],pos:[58,74],lang:'fil-PH'}};
const S={role:null,admin:false,view:'map',region:'Tagalog',pts:0,studied:{Tagalog:0,Hiligaynon:0,Bisaya:0},match:{},listen:{},
pending:[{id:'SUB-0012',w:'Palangga',l:'Hiligaynon',t:'Love; dear',by:'USR-0412',s:'Pending'},{id:'SUB-0013',w:'Gwapa',l:'Bisaya',t:'Beautiful (female)',by:'USR-0388',s:'Pending'}],
fc:{i:0,flip:false},m:null,q:null,sub:{w:'Mahal',l:'Tagalog',t:'Love; dear'},sent:false,flash:''};
const $=s=>document.querySelector(s);
const esc=s=>String(s).replace(/[&<>"]/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));
function say(t){try{const u=new SpeechSynthesisUtterance(t);u.lang='fil-PH';speechSynthesis.cancel();speechSynthesis.speak(u)}catch(e){}}
const shuffle=a=>a.map(x=>[Math.random(),x]).sort((a,b)=>a[0]-b[0]).map(x=>x[1]);
const stamp=r=>S.match[r]&&S.listen[r];
function go(v,r){if(r)S.region=r;S.view=v;S.flash='';
 if(v==='fc')S.fc={i:0,flip:false};
 if(v==='match'){const w=D[S.region].w.slice(0,4);S.m={left:shuffle(w.map(x=>x[0])),right:shuffle(w.map(x=>x[1])),sel:null,done:[],msg:'',err:false}}
 if(v==='listen'){const w=D[S.region].w;S.q={i:0,sel:null,res:null,order:shuffle(w.map((x,i)=>i)),correct:0,opts:null}}
 render()}
function nav(){const a=S.admin;const items=a?[['pend','Pending approvals'],['reg','Regions'],['words','Words'],['audio','Audio files']]:[['map','Map'],['pass','Passport'],['sub','Submit a word']];
 const act=v=>S.view===v||(v==='map'&&['words','fc','match','listen'].includes(S.view)&&!a);
 return items.map(([v,l])=>`<a class="${act(v)?'on':''}" onclick="go('${v}')">${l}</a>`).join('')}
function render(){
 const a=S.admin;document.body.className=a?'admin':'';
 let body='';
 const v=S.view,R=D[S.region];
 if(a)body=adminView();
 else if(v==='map'){
  const sel=D[S.region];
  body=`<h1>Choose a region</h1><p class="sub">Select a region on the map to open its word list. Finish both mini-games to earn the region stamp.</p>
  <div class="grid"><div class="map"><div class="blob" style="left:12%;top:6%;width:35%;height:38%"></div><div class="blob" style="left:25%;top:35%;width:30%;height:38%"></div><div class="blob" style="left:35%;top:62%;width:42%;height:30%"></div>
  ${Object.entries(D).map(([n,d])=>`<div class="pin ${n===S.region?'on':''}" style="left:${d.pos[0]+8}%;top:${d.pos[1]}%" onclick="S.region='${n}';render()">📍 ${n}</div>`).join('')}</div>
  <div class="card side"><h2>${S.region}</h2><div class="kv"><span>Area</span><span>${sel.area}</span></div><div class="kv"><span>Words</span><span>${sel.w.length}</span></div><div class="kv"><span>Mini-games</span><span>2</span></div><div class="kv"><span>Stamp</span><span class="badge ${stamp(S.region)?'ok':''}">${stamp(S.region)?'✓ Earned':'ⓘ Not earned'}</span></div>
  <div class="row"><button class="p" style="width:100%" onclick="go('words')">Open word list →</button></div></div></div>`}
 else if(v==='words'){
  body=`<h1>${S.region} word list</h1><p class="sub">${R.area} · ${R.w.length} words · audio stored on Cloudinary</p>
  <div class="tbl"><table><tr><th>Word</th><th>Translation</th><th>Example sentence</th><th>Audio</th></tr>${R.w.map(x=>`<tr><td><b>${x[0]}</b></td><td>${x[1]}</td><td>${x[2]}</td><td><button onclick="say('${x[0]}')">▷ Play audio</button></td></tr>`).join('')}</table></div>
  <div class="row"><button class="p" onclick="go('fc')">Start flashcards →</button><button onclick="go('match')">Play matching game</button><button onclick="go('listen')">Play listening quiz</button><button onclick="go('sub')">Submit a new word</button></div>`}
 else if(v==='fc'){
  const c=R.w[S.fc.i];
  body=`<h1>Flashcards: ${S.region}</h1><p class="sub">Card ${S.fc.i+1} of ${R.w.length}. Select Flip card to see the other side.</p>
  <div class="card flash" onclick="S.fc.flip=!S.fc.flip;render()">${S.fc.flip?`<h2>${c[1]}</h2><span>Example: ${c[2]}</span>`:`<h2>${c[0]}</h2><span>${S.region} · front of card</span>`}</div>
  <div class="row"><button onclick="say('${c[0]}')">▷ Play audio</button><button onclick="S.fc.flip=!S.fc.flip;render()">Flip card</button><button onclick="fcNav(-1)">Previous</button><button class="p" onclick="fcNav(1)">Next →</button></div>`}
 else if(v==='match'){
  const m=S.m,all=m.done.length===4;
  body=`<div class="score">Score<b>${S.pts} pts</b></div><h1>Matching game: ${S.region}</h1><p class="sub">Select a word, then select its meaning.</p>
  ${m.msg?`<div class="msg ${m.err?'err':''}">${m.msg}</div>`:''}
  <div class="two"><div>${m.left.map(w=>`<button class="opt ${m.done.includes(w)?'done':''} ${m.sel===w?'sel':''}" ${m.done.includes(w)?'disabled':''} onclick="mPick('L','${w}')">${w}${m.done.includes(w)?' ✓ Matched':''}</button>`).join('')}</div>
  <div>${m.right.map(t=>`<button class="opt ${m.done.some(w=>tr(w)===t)?'done':''}" ${m.done.some(w=>tr(w)===t)?'disabled':''} onclick="mPick('R','${t}')">${t}</button>`).join('')}</div></div>
  <p class="mono" style="font-size:13px">Matched ${m.done.length} of 4</p><div class="bar"><i style="width:${m.done.length*25}%"></i></div>
  <div class="row"><button class="p" onclick="mCheck()">Check answers</button><button onclick="go('listen')">Switch to listening quiz</button></div>`}
 else if(v==='listen'){
  const q=S.q,w=R.w;
  if(q.i>=w.length){body=`<h1>Listening quiz: ${S.region}</h1><div class="card"><h2>Quiz complete</h2><p>You got ${q.correct} of ${w.length} right.</p>${stamp(S.region)?'<p><span class="badge ok">✓ Region stamp earned!</span></p>':'<p>Finish the matching game too to earn the stamp.</p>'}<div class="row"><button class="p" onclick="go('pass')">View passport</button><button onclick="go('words')">Back to word list</button></div></div>`}
  else{const idx=q.order[q.i],c=w[idx];
   if(!q.opts)q.opts=shuffle([c[1],...shuffle(w.filter((_,i)=>i!==idx).map(x=>x[1])).slice(0,3)]);
   body=`<div class="score">Score<b>${S.pts} pts</b></div><h1>Listening quiz: ${S.region}</h1><p class="sub">Question ${q.i+1} of ${w.length}</p>
   <div class="card"><button class="p" onclick="say('${c[0]}')">▷ Play audio</button><h2 style="margin-top:16px">Which meaning matches what you heard?</h2>
   ${q.opts.map(o=>`<button class="opt ${q.sel===o&&q.res===null?'sel':''} ${q.res!==null&&o===c[1]?'done':''} ${q.res===false&&q.sel===o?'bad':''}" ${q.res!==null?'disabled':''} onclick="S.q.sel='${o.replace(/'/g,"\\'")}';render()">${o}</button>`).join('')}
   <p class="mono" style="font-size:13px">Answered ${q.i} of ${w.length}</p><div class="bar"><i style="width:${q.i/w.length*100}%"></i></div></div>
   <div class="row">${q.res===null?`<button class="p" ${q.sel?'':'disabled'} onclick="lSubmit()">Submit answer</button><button onclick="say('${c[0]}')">Replay audio</button>`:`<span class="msg ${q.res?'':'err'}" style="margin:0">${q.res?'Correct! +10 pts':'Not quite. It means '+c[1]}</span><button class="p" onclick="S.q.i++;S.q.sel=null;S.q.res=null;S.q.opts=null;lEnd();render()">Next →</button>`}</div>`}}
 else if(v==='pass'){
  body=`<div class="score">Total points<b>${S.pts}</b></div><h1>My passport</h1><p class="sub">Complete both mini-games in a region to earn its stamp.</p>
  <div class="tbl"><table><tr><th>Region</th><th>Words studied</th><th>Matching game</th><th>Listening quiz</th><th>Stamp</th></tr>${Object.keys(D).map(r=>`<tr><td><b>${r}</b></td><td class="mono">${S.studied[r]} / 5</td><td>${b(S.match[r])}</td><td>${b(S.listen[r])}</td><td>${b(stamp(r),'Earned','Not earned')}</td></tr>`).join('')}</table></div>`}
 else if(v==='sub'){
  body=`<h1>Submit a word</h1><p class="sub">The admin reviews every word before it appears in a region.</p>
  <div class="card" style="max-width:520px"><label>Word</label><input value="${esc(S.sub.w)}" oninput="S.sub.w=this.value"><label>Language</label><input value="${esc(S.sub.l)}" oninput="S.sub.l=this.value"><label>Meaning</label><input value="${esc(S.sub.t)}" oninput="S.sub.t=this.value"><p class="sub" style="margin-top:6px">Suggested by MyMemory. You can edit it.</p>
  <div class="row" style="margin-top:6px"><button class="p" onclick="sendWord()">Send for review</button><button onclick="go('map')">Cancel</button></div></div>
  ${S.sent?'<div class="msg" style="margin-top:16px">Sent! The admin will review it shortly.</div>':''}<div class="note"><b>What happens next.</b> You will get an email through Brevo when the admin decides.</div>`}
 const foot=S.role?`<div class="foot"><div class="mono" style="font-size:12px">${a?'admin@example.com':'learner@example.com'}</div><button onclick="S.role=null;S.admin=false;S.view='map';render()">Sign out</button></div>`:`<div class="foot">Not signed in<button onclick="S.role='L';render()">Sign in with Google</button><button style="margin-top:6px;font-size:12px" onclick="S.role='A';S.admin=true;S.view='pend';render()">Demo: sign in as admin</button></div>`;
 $('#app').innerHTML=`<aside><div class="brand"><b>Salita Quest</b>${a?'Administrator':'Learner'}</div><nav>${nav()}</nav>${foot}</aside><main>${body}</main>`}
function tr(w){return D[S.region].w.find(x=>x[0]===w)[1]}
function b(ok,y='Completed',n='Not started'){return ok?`<span class="badge ok">✓ ${y}</span>`:`<span class="badge">ⓘ ${n}</span>`}
function fcNav(d){const n=D[S.region].w.length;S.fc.i=(S.fc.i+d+n)%n;S.fc.flip=false;S.studied[S.region]=Math.max(S.studied[S.region],S.fc.i+1);render()}
let pend=null;
function mPick(side,val){const m=S.m;m.msg='';
 if(side==='L'){m.sel=val;render();return}
 if(!m.sel){m.msg='Select a word first.';m.err=true;render();return}
 if(tr(m.sel)===val){m.done.push(m.sel);S.pts+=10;m.msg=`<b>Match saved.</b> ${m.sel} means ${val}.`;m.err=false}else{m.msg='Not a match. Try again.';m.err=true}
 m.sel=null;render()}
function mCheck(){const m=S.m;if(m.done.length===4){S.match[S.region]=true;m.msg='<b>All matched!</b> Matching game completed.'+(stamp(S.region)?' Region stamp earned!':'');m.err=false}else{m.msg=`${4-m.done.length} left to match.`;m.err=true}render()}
function lSubmit(){const q=S.q,c=D[S.region].w[q.order[q.i]];q.res=q.sel===c[1];if(q.res){q.correct++;S.pts+=10}render()}
function lEnd(){const q=S.q;if(q.i>=D[S.region].w.length)S.listen[S.region]=true}
function sendWord(){S.sent=true;S.pending.push({id:'SUB-00'+(14+S.pending.length-2),w:S.sub.w,l:S.sub.l,t:S.sub.t,by:'USR-0501',s:'Pending'});render()}
function decide(i,s){S.pending[i].s=s;render()}
function adminView(){const v=S.view;
 if(v==='pend'||!['reg','words','audio'].includes(v)){const n=S.pending.filter(p=>p.s==='Pending').length;
  return `<h1>Pending approvals</h1><p class="sub">${n} submitted word${n===1?' is':'s are'} waiting for review.</p><div class="tbl"><table><tr><th>ID</th><th>Word</th><th>Language</th><th>Suggested translation</th><th>By</th><th>Status</th><th>Action</th></tr>
  ${S.pending.map((p,i)=>`<tr><td class="mono">${p.id}</td><td><b>${esc(p.w)}</b></td><td>${esc(p.l)}</td><td>${esc(p.t)}</td><td class="mono">${p.by}</td><td><span class="badge ${p.s==='Approved'?'ok':'pd'}">${p.s==='Pending'?'ⓘ ':''}${p.s}</span></td><td>${p.s==='Pending'?`<button class="p" onclick="decide(${i},'Approved')">✓ Approve</button> <button onclick="decide(${i},'Rejected')">× Reject</button>`:'—'}</td></tr>`).join('')}</table></div>
  <div class="note"><b>How this works.</b> Translations are suggested by MyMemory. Approving a word emails the learner through Brevo.</div>`}
 if(v==='reg')return `<h1>Regions</h1><p class="sub">Regions shown on the learner map.</p><div class="tbl"><table><tr><th>Region</th><th>Area</th><th>Words</th></tr>${Object.entries(D).map(([n,d])=>`<tr><td><b>${n}</b></td><td>${d.area}</td><td>${d.w.length}</td></tr>`).join('')}</table></div>`;
 if(v==='words')return `<h1>Words</h1><p class="sub">All approved words.</p><div class="tbl"><table><tr><th>Word</th><th>Region</th><th>Translation</th></tr>${Object.entries(D).flatMap(([n,d])=>d.w.map(x=>`<tr><td><b>${x[0]}</b></td><td>${n}</td><td>${x[1]}</td></tr>`)).join('')}</table></div>`;
 return `<h1>Audio files</h1><p class="sub">Pronunciation clips stored on Cloudinary.</p><div class="tbl"><table><tr><th>Word</th><th>Region</th><th>File</th></tr>${Object.entries(D).flatMap(([n,d])=>d.w.map(x=>`<tr><td><b>${x[0]}</b></td><td>${n}</td><td class="mono">${n.toLowerCase()}_${x[0].toLowerCase()}.mp3</td></tr>`)).join('')}</table></div>`}
render();
</script></body></html>
