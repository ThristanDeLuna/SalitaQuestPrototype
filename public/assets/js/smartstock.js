/* SmartStock - local-first app logic.
 * All data (readings, alerts, sensor state) lives in localStorage on this device.
 * The server only delivers page shells; a service worker caches them for offline use. */
(function () {
  'use strict';
  var KEY = 'smartstock.v1', INTERVAL = 5 * 60 * 1000, TEMP_T = 30.0, HUM_T = 80;
  var ITEMS = [
    { product: 'Screws', category: 'Fasteners', offset: 0 },
    { product: 'Iron Nails', category: 'Fasteners', offset: 1 },
    { product: 'Steel Bolts', category: 'Fasteners', offset: 0 },
    { product: 'Galvanized Pipes', category: 'Plumbing', offset: -1 },
    { product: 'Stainless Steel Hinges', category: 'Hardware', offset: -2 }
  ];

  /* ---------- store ---------- */
  var state;
  function seed() {
    var now = Date.now(), n = 40, r = [], i, hum, temp, s = 42;
    function rnd() { s = (s * 16807) % 2147483647; return s / 2147483647; } // deterministic noise
    for (i = 0; i < n; i++) {
      hum = i < 6 ? 58 + Math.round(rnd() * 4)
        : Math.max(65, Math.min(70, Math.round(67 + 2.5 * Math.sin(i / 4 + 1) + (rnd() - .5) * 2)));
      temp = Math.max(27.4, Math.min(29.8, +(28.2 + 1.1 * Math.sin(i / 5) + (i / n) * .3 + (rnd() - .5) * .4).toFixed(1)));
      if (i === n - 1) { hum = 67; temp = 28.7; }
      r.push({ ts: now - (n - 1 - i) * INTERVAL, temp: temp, hum: hum });
    }
    return {
      online: true, readings: r,
      alerts: [
        { id: 1, type: 'success', title: 'Sensor Online', message: 'Sensor Successfully Turned On', ts: now - 900000, read: true },
        { id: 2, type: 'danger', title: 'Sensor Offline', message: 'Sensor Disconnected', ts: now - 600000, read: true },
        { id: 3, type: 'warning', title: 'High Temperature', message: 'Sensor Reached 35.4C (Threshold 30.0 C).', ts: now - 300000, read: false },
        { id: 4, type: 'warning', title: 'High Humidity', message: 'Sensor Reached 85% RH (Threshold 80%).', ts: now - 240000, read: false }
      ]
    };
  }
  function load() {
    try { var s = JSON.parse(localStorage.getItem(KEY)); if (s && s.readings && s.readings.length) return s; } catch (e) {}
    return seed();
  }
  var memoryOnly = false;
  function save() {
    try { localStorage.setItem(KEY, JSON.stringify(state)); } catch (e) { memoryOnly = true; }
  }
  state = load(); save();

  /* ---------- risk logic ---------- */
  function points(t, h) { return (h >= 80 ? 3 : h >= 65 ? 2 : h >= 50 ? 1 : 0) + (t >= 30 ? 2 : t >= 28 ? 1 : 0); }
  function level(p) { return p >= 5 ? 'Critical' : p >= 3 ? 'High' : p >= 2 ? 'Moderate' : 'Low'; }
  function latest() { return state.readings[state.readings.length - 1]; }
  function readingStatus(r) { return (r.temp >= TEMP_T || r.hum >= HUM_T) ? 'Critical' : points(r.temp, r.hum) >= 3 ? 'Elevated' : 'Normal'; }
  function unread() { return state.alerts.filter(function (a) { return !a.read; }).length; }
  function exposureMin() {
    var c = 0, i; for (i = state.readings.length - 1; i >= 0; i--) { if (points(state.readings[i].temp, state.readings[i].hum) < 2) break; c++; }
    return c * 5;
  }
  function dur(m) { return m < 60 ? m + 'm' : Math.floor(m / 60) + 'h ' + ('0' + (m % 60)).slice(-2) + 'm'; }
  function items() {
    var l = latest(), base = points(l.temp, l.hum);
    return ITEMS.map(function (it) { var p = Math.max(0, base + it.offset); return { product: it.product, category: it.category, points: p, risk: level(p), status: 'On stock' }; })
      .sort(function (a, b) { return b.points - a.points; });
  }
  function counts() { var c = { Low: 0, Moderate: 0, High: 0, Critical: 0 }; items().forEach(function (i) { c[i.risk]++; }); return c; }
  function message() {
    var l = latest(), hot = l.temp >= 28, humid = l.hum >= 65;
    if (humid && hot) return 'High humidity and elevated temperature detected. Prolonged exposure may increase the risk of corrosion for metal hardware.';
    if (humid) return 'High humidity detected. Moisture in the air can speed up rust on unprotected metal hardware.';
    if (hot) return 'Elevated temperature detected. Heat combined with rising humidity can accelerate corrosion.';
    return 'Storage conditions are within the safe range. No corrosion risk action needed.';
  }

  /* ---------- actions ---------- */
  function pushAlert(type, title, msg, isRead) {
    state.alerts.push({ id: state.alerts.length + 1, type: type, title: title, message: msg, ts: Date.now(), read: !!isRead });
  }
  function rand(a, b) { return a + Math.floor(Math.random() * (b - a + 1)); }
  function startSensor() {
    if (!state.online) { state.online = true; pushAlert('success', 'Sensor Online', 'Sensor Successfully Turned On'); }
    var p = latest(),
      temp = +Math.max(22, Math.min(38, p.temp + rand(-8, 14) / 10)).toFixed(1),
      hum = Math.max(35, Math.min(95, p.hum + rand(-5, 9)));
    state.readings.push({ ts: Date.now(), temp: temp, hum: hum });
    state.readings = state.readings.slice(-200);
    if (hum >= HUM_T) pushAlert('warning', 'High Humidity', 'Sensor Reached ' + hum + '% RH (Threshold ' + HUM_T + '%).');
    if (temp >= TEMP_T) pushAlert('warning', 'High Temperature', 'Sensor Reached ' + temp + 'C (Threshold ' + TEMP_T.toFixed(1) + ' C).');
    save(); render(); toast('Sensor started - new reading ' + temp + ' C / ' + hum + '% RH');
  }
  function stopSensor() {
    if (state.online) { state.online = false; pushAlert('danger', 'Sensor Offline', 'Sensor Disconnected', true); }
    save(); render(); toast('Sensor taken offline');
  }
  function markAll() { state.alerts.forEach(function (a) { a.read = true; }); save(); render(); toast('All notifications marked as read'); }
  function reset() { state = seed(); save(); render(); toast('Demo data reset'); }

  /* ---------- rendering ---------- */
  function esc(s) { return String(s).replace(/[&<>"]/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]; }); }
  function ic(n, s) { return '<svg width="' + s + '" height="' + s + '" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><use href="#i-' + n + '"/></svg>'; }
  function tm(ts) { return new Date(ts).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' }); }
  function dt(ts) { return new Date(ts).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) + ' · ' + tm(ts); }
  var RC = { Low: 'low', Moderate: 'mod', High: 'high', Critical: 'crit' };
  function stat(icon, label, value, extra, cls) {
    return '<div class="card stat"><div class="stat-label"><span class="chip-ic">' + ic(icon, 14) + '</span>' + label + '</div>' + value + (extra || '') + '</div>';
  }
  function val(v, unit, cls) { return '<div class="stat-value ' + (cls || '') + '">' + v + (unit ? ' <small>' + unit + '</small>' : '') + '</div>'; }

  function chart(values, id) {
    var w = 600, h = 150, pad = 8, n = values.length; if (n < 2) return '';
    var mn = Math.min.apply(null, values), mx = Math.max.apply(null, values), span = Math.max(.5, mx - mn);
    mn -= span * .35; mx += span * .15; span = mx - mn;
    var pts = values.map(function (v, i) { return [(i * w / (n - 1)).toFixed(1), (h - pad - ((v - mn) / span) * (h - 2 * pad)).toFixed(1)]; });
    var line = 'M' + pts.map(function (p) { return p.join(','); }).join(' L');
    var grid = [.25, .5, .75].map(function (g) { return '<line x1="0" y1="' + (h * g) + '" x2="' + w + '" y2="' + (h * g) + '" class="grid"/>'; }).join('');
    return '<svg class="chart" viewBox="0 0 ' + w + ' ' + h + '" preserveAspectRatio="none" role="img" aria-label="' + id + ' trend"><defs><linearGradient id="g-' + id + '" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#17a9b8" stop-opacity=".28"/><stop offset="1" stop-color="#17a9b8" stop-opacity="0"/></linearGradient></defs>' +
      grid + '<path d="' + line + ' L' + w + ',' + h + ' L0,' + h + ' Z" fill="url(#g-' + id + ')"/><path d="' + line + '" class="line"/></svg>';
  }

  var views = {
    dashboard: function () {
      var l = latest(), c = counts(), it = items(), total = it.length, risk = level(points(l.temp, l.hum)),
        avg = Math.round(state.readings.slice(-12).reduce(function (a, r) { return a + r.hum; }, 0) / Math.min(12, state.readings.length)),
        colors = { Low: '#9fe3ea', Moderate: '#17a9b8', High: '#f59e0b', Critical: '#dc2626' };
      var R = 52, C = 2 * Math.PI * R, off = 0, arcs = '';
      Object.keys(c).forEach(function (k) {
        if (!c[k]) return; var len = C * c[k] / total;
        arcs += '<circle cx="70" cy="70" r="' + R + '" fill="none" stroke="' + colors[k] + '" stroke-width="12" stroke-dasharray="' + (len - 1.5).toFixed(2) + ' ' + (C - len + 1.5).toFixed(2) + '" stroke-dashoffset="' + (-off).toFixed(2) + '" transform="rotate(-90 70 70)"/>';
        off += len;
      });
      return '<section class="stat-grid four">' +
        stat('thermo', 'Temperature', val(l.temp.toFixed(1), 'C')) +
        stat('drop', 'Avg. Humidity', val(avg + '%', 'RH')) +
        stat('box', 'High Risk Items', val(c.High + c.Critical, '', 'orange'), '<div class="stat-sub">of ' + total + ' items</div>') +
        stat('bell', 'Active Alerts', val(unread(), '', 'red'), '<div class="stat-sub">needs attention</div>') +
        '</section><section class="dash-grid"><div class="col">' +
        '<div class="card"><table class="table compact"><thead><tr><th>Temperature</th><th>Humidity</th><th>Exposure Duration</th><th>Risk Level</th><th>Status</th></tr></thead><tbody><tr><td>' +
        l.temp.toFixed(1) + ' C</td><td>' + l.hum + '%</td><td>' + dur(exposureMin()) + '</td><td><strong class="risk-text ' + RC[risk] + '">' + risk + '</strong></td><td><strong>' + (state.online ? 'Online' : 'Offline') + '</strong></td></tr></tbody></table></div>' +
        '<div class="card grow"><div class="card-title">Top Affected Metal Hardware</div><table class="table"><thead><tr><th>Product</th><th>Category</th><th>Risk Level</th><th>Status</th></tr></thead><tbody>' +
        it.slice(0, 3).map(function (i) { return '<tr><td>' + esc(i.product) + '</td><td>' + i.category + '</td><td><span class="risk-text ' + RC[i.risk] + '">' + i.risk + '</span></td><td>' + i.status + '</td></tr>'; }).join('') +
        '</tbody></table></div></div><div class="col"><div class="card grow donut-card"><svg class="donut" viewBox="0 0 140 140" role="img" aria-label="Items by risk level"><circle cx="70" cy="70" r="' + R + '" fill="none" stroke="#eef2f5" stroke-width="12"/>' + arcs +
        '<text x="70" y="68" text-anchor="middle" class="donut-n">' + total + '</text><text x="70" y="84" text-anchor="middle" class="donut-l">items</text></svg><ul class="legend">' +
        Object.keys(c).map(function (k) { return '<li><i style="background:' + colors[k] + '"></i><span>' + k + '</span><b>' + c[k] + '</b></li>'; }).join('') +
        '</ul></div><div class="card last-update"><div class="stat-label tiny">Last Sensor Update</div><div class="update-time">' + ic('clock', 15) + ' ' + tm(l.ts) + ' · ' + new Date(l.ts).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) + '</div></div></div></section>';
    },
    monitoring: function () {
      var l = latest(), s = state.readings.slice(-12), rec = state.readings.slice(-5).reverse(),
        pc = { Normal: 'normal', Elevated: 'elevated', Critical: 'critical' };
      return '<section class="stat-grid three">' +
        stat('thermo', 'Temperature', val(l.temp.toFixed(1), 'C')) +
        stat('drop', 'Humidity', val(l.hum + '%', 'RH')) +
        stat('wifi', 'Sensor Status', '<div class="status-line ' + (state.online ? 'on' : 'off') + '"><i></i>' + (state.online ? 'Online' : 'Offline') + '</div><div class="stat-sub">Last Update: ' + tm(l.ts).toLowerCase() + ' · <a href="#" class="link" data-' + (state.online ? 'stop' : 'start') + '-sensor>' + (state.online ? 'Simulate offline' : 'Bring online') + '</a></div>') +
        '</section><section class="chart-grid"><div class="card"><div class="card-title upper">Temperature (C)</div>' + chart(s.map(function (r) { return r.temp; }), 'temp') +
        '</div><div class="card"><div class="card-title upper">Humidity (% RH)</div>' + chart(s.map(function (r) { return r.hum; }), 'hum') + '</div></section>' +
        '<section class="card grow"><div class="card-title">Recent Readings</div><table class="table"><thead><tr><th>Date &amp; Time</th><th>Temperature</th><th>Humidity</th><th>Status</th></tr></thead><tbody>' +
        rec.map(function (r) { var st = readingStatus(r); return '<tr><td>' + dt(r.ts) + '</td><td>' + r.temp.toFixed(1) + ' C</td><td>' + r.hum + '% RH</td><td><span class="pill ' + pc[st] + '">' + st + '</span></td></tr>'; }).join('') +
        '</tbody></table></section>';
    },
    corrosion: function () {
      var l = latest(), c = counts(), risk = level(points(l.temp, l.hum)), hi = c.High + c.Critical, total = c.Low + c.Moderate + hi;
      return '<section class="risk-row"><div class="card risk-card"><div class="stat-label tiny upper">Corrosion Risk Level</div><div class="risk-badge ' + RC[risk] + '">' + ic('alert', 26) + '<span>' + risk.toUpperCase() + '</span></div></div>' +
        '<button class="card start-card" data-start-sensor><span class="play">' + ic('play', 14) + '</span><span>START SENSOR MANUALLY</span></button></section>' +
        '<section class="stat-grid four">' + stat('thermo', 'Temperature', val(l.temp.toFixed(1), 'C')) + stat('drop', 'Humidity', val(l.hum + '%', 'RH')) +
        stat('clock', 'Exposure Duration', val(dur(exposureMin()), '', 'orange'), '<div class="stat-sub">affecting ' + hi + ' of ' + total + ' items</div>') +
        stat('wifi', 'Sensor Status', val(state.online ? 'Online' : 'Offline', '', 'teal sm'), '<div class="stat-sub">' + (state.online ? 'needs attention' : 'no new readings') + '</div>') + '</section>' +
        '<section class="card"><div class="card-head">Environment Condition</div><div class="condition"><span class="warn-dot">!</span><p>' + esc(message()) + '</p></div></section>' +
        '<a class="btn btn-dark float-btn" href="' + SS.dashboardUrl + '">Go back to Dashboard</a>';
    },
    alerts: function () {
      var ico = { warning: 'alert', danger: 'xcircle', success: 'check' };
      return '<section class="card alerts-card"><div class="alerts-head"><button class="btn btn-teal btn-sm" id="markAll"' + (unread() ? '' : ' disabled') + '>Mark all as Read</button></div><ul class="alert-list">' +
        state.alerts.slice().reverse().map(function (a) {
          return '<li class="alert-row"><span class="alert-ic ' + a.type + '">' + ic(ico[a.type], 18) + '</span><div class="alert-body"><div class="alert-title">' + esc(a.title) + '</div><div class="alert-msg">' + esc(a.message) +
            '</div></div><time>' + tm(a.ts) + '</time><span class="read-pill ' + (a.read ? 'read' : 'unread') + '">' + (a.read ? 'Read' : 'Unread') + '</span></li>';
        }).join('') + '</ul><div class="end-note">--- End of Notification ---</div></section>';
    }
  };

  var view = document.getElementById('view'), page = view.dataset.page;
  function render() {
    view.innerHTML = views[page]();
    document.getElementById('bellDot').hidden = unread() === 0;
    var note = document.getElementById('netText');
    if (note) note.textContent = memoryOnly ? 'Storage blocked - not saved' : (navigator.onLine ? 'Saved on this device' : 'Offline - saved on this device');
    var nd = document.getElementById('netDot'); if (nd) nd.className = navigator.onLine ? '' : 'off';
  }

  var toastEl = document.getElementById('toast'), tt;
  function toast(msg) { toastEl.textContent = msg; toastEl.classList.add('show'); clearTimeout(tt); tt = setTimeout(function () { toastEl.classList.remove('show'); }, 2600); }

  /* one delegated listener - survives re-renders */
  document.addEventListener('click', function (e) {
    var t = e.target.closest('[data-start-sensor],[data-stop-sensor],#markAll,#resetData');
    if (!t || t.disabled) return;
    e.preventDefault();
    if (t.hasAttribute('data-start-sensor')) startSensor();
    else if (t.hasAttribute('data-stop-sensor')) stopSensor();
    else if (t.id === 'markAll') markAll();
    else if (t.id === 'resetData') reset();
  });

  /* keep tabs in sync, reflect connectivity */
  window.addEventListener('storage', function (e) { if (e.key === KEY) { state = load(); render(); } });
  window.addEventListener('online', render); window.addEventListener('offline', render);

  render();

  /* offline support */
  if ('serviceWorker' in navigator) {
    var q = '?u=' + encodeURIComponent(JSON.stringify(SS.pages.concat(SS.assets)));
    navigator.serviceWorker.register(SS.sw + q, { scope: SS.scope }).catch(function () {});
  }
})();
