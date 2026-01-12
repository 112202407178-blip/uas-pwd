document.addEventListener('DOMContentLoaded', function(){
  // Mobile nav toggle
  var toggle = document.querySelector('.mobile-toggle');
  var nav = document.querySelector('.main-nav ul');
  if(toggle && nav){
    toggle.addEventListener('click', function(){
      var expanded = this.getAttribute('aria-expanded') === 'true';
      this.setAttribute('aria-expanded', (!expanded).toString());
      nav.style.display = expanded ? 'none' : 'flex';
    });
  }

  // Confirm modal
  document.body.addEventListener('click', function(e){
    var target = e.target.closest('[data-confirm]');
    if(!target) return;
    e.preventDefault();
    var msg = target.getAttribute('data-confirm');
    showConfirm(msg, function(ok){ if(ok) window.location = target.href; });
  });

  // Booking modal: open modal when clicking .open-booking
  document.body.addEventListener('click', function(e){
    var b = e.target.closest('.open-booking');
    if(!b) return;
    e.preventDefault();
    openBookingModal(b.dataset);
  });

  // Booking modal actions
  var bookingBackdrop = document.getElementById('booking-backdrop');
  var bookingForm = document.getElementById('booking-form');
  var bookingCancel = document.getElementById('booking-cancel');
  if (bookingCancel) bookingCancel.addEventListener('click', function(){ hideBookingModal(); });
  if (bookingBackdrop) bookingBackdrop.addEventListener('click', function(e){ if (e.target === bookingBackdrop) hideBookingModal(); });
  if (bookingForm) bookingForm.addEventListener('submit', function(e){
    e.preventDefault();
    submitBookingForm(new FormData(bookingForm));
  });

  // when modal inputs change, check availability for the selected studio
  ['b-date','b-start','b-end'].forEach(function(id){
    var el = document.getElementById(id);
    if (!el) return;
    el.addEventListener('change', function(){
      var sid = document.getElementById('b-studio-id').value;
      var d = document.getElementById('b-date').value;
      var s = document.getElementById('b-start').value;
      var e = document.getElementById('b-end').value;
      if (sid && d && s && e) checkStudioAvailabilityForModal(sid,d,s,e);
    });
  });
});

function checkStudioAvailabilityForModal(sid,d,s,e){
  var submitBtn = document.getElementById('booking-submit');
  var errors = document.getElementById('booking-errors');
  if (!submitBtn || !errors) return;
  errors.textContent = '';
  submitBtn.disabled = true; submitBtn.textContent = 'Mengecek...';
  fetch('checkAvailability.php?date='+encodeURIComponent(d)+'&start='+encodeURIComponent(s)+'&end='+encodeURIComponent(e))
    .then(r => {
      if (!r.ok) return r.text().then(t => { throw new Error('Server returned '+r.status+': '+(t||'no message')); });
      return r.text().then(t => { try { return JSON.parse(t); } catch(e){ throw new Error('Invalid JSON: '+(t||'empty')); } });
    }).then(json => {
      submitBtn.disabled = false; submitBtn.textContent = 'Kirim Booking';
      if (!json.success) { errors.textContent = json.message || 'Gagal cek'; return; }
      var found = (json.data || []).find(function(it){ return it.studio_id == sid; });
      if (found) {
        errors.textContent = 'Waktu ini sudah terisi: ' + found.bookings.map(b=>b.start+'–'+b.end).join(', ');
        submitBtn.disabled = true; submitBtn.textContent = 'Tidak tersedia';
      } else {
        errors.textContent = '';
        submitBtn.disabled = false; submitBtn.textContent = 'Kirim Booking';
      }
    }).catch(function(err){
      submitBtn.disabled=false; submitBtn.textContent='Kirim Booking';
      errors.textContent='Gagal koneksi: ' + (err && err.message ? err.message : 'Unknown error');
      console.error('checkStudioAvailabilityForModal error:', err);
    });
}

function showConfirm(message, cb){
  // Use a dedicated confirm backdrop so we don't collide with other modals (e.g., booking-backdrop)
  var backdrop = document.querySelector('.confirm-backdrop');
  if(!backdrop){
    backdrop = document.createElement('div'); backdrop.className='modal-backdrop confirm-backdrop';
    backdrop.innerHTML = '<div class="modal"><h3>Konfirmasi</h3><p class="modal-msg"></p><div class="modal-actions"><button class="btn btn-outline modal-cancel">Batal</button><button class="btn modal-ok">Lanjutkan</button></div></div>';
    document.body.appendChild(backdrop);
  }
  var msgEl = backdrop.querySelector('.modal-msg'); if (msgEl) msgEl.textContent = message;
  backdrop.classList.add('show');
  var cancelBtn = backdrop.querySelector('.modal-cancel'); if (cancelBtn) cancelBtn.focus();

  function cleanup(){ backdrop.classList.remove('show'); if (backdrop.querySelector('.modal-cancel')) backdrop.querySelector('.modal-cancel').removeEventListener('click', onCancel); if (backdrop.querySelector('.modal-ok')) backdrop.querySelector('.modal-ok').removeEventListener('click', onOk); }
  function onCancel(){ cleanup(); cb(false); }
  function onOk(){ cleanup(); cb(true); }
  if (cancelBtn) cancelBtn.addEventListener('click', onCancel);
  var okBtn = backdrop.querySelector('.modal-ok'); if (okBtn) okBtn.addEventListener('click', onOk);
}

function openBookingModal(data){
  var sid = data['studioId'] || data['studio-id'] || '';
  document.getElementById('b-studio-id').value = sid;
  document.getElementById('booking-title').textContent = 'Booking: ' + (data['studioName'] || data['studio-name'] || '');
  document.getElementById('booking-subtitle').textContent = 'Harga: Rp ' + (data['studioPrice'] || data['studio-price'] || '?') + '/jam';
  document.getElementById('booking-errors').innerHTML = '';

  // set sensible defaults (prefer current availability filter values if present)
  var today = new Date().toISOString().slice(0,10);
  var d = (document.getElementById('filter-date') ? document.getElementById('filter-date').value : (document.getElementById('b-date') ? document.getElementById('b-date').value : today)) || today;
  var s = (document.getElementById('filter-start') ? document.getElementById('filter-start').value : (document.getElementById('b-start') ? document.getElementById('b-start').value : '12:00')) || '12:00';
  var e = (document.getElementById('filter-end') ? document.getElementById('filter-end').value : (document.getElementById('b-end') ? document.getElementById('b-end').value : '13:00')) || '13:00';
  if (document.getElementById('b-date')) document.getElementById('b-date').value = d;
  if (document.getElementById('b-start')) document.getElementById('b-start').value = s;
  if (document.getElementById('b-end')) document.getElementById('b-end').value = e;

  var backdrop = document.getElementById('booking-backdrop'); backdrop.classList.add('show'); backdrop.setAttribute('aria-hidden','false');

  // immediately re-check availability so modal shows fresh state (reflects cancellations)
  if (sid && d && s && e) checkStudioAvailabilityForModal(sid,d,s,e);
}
function hideBookingModal(){ var backdrop = document.getElementById('booking-backdrop'); backdrop.classList.remove('show'); backdrop.setAttribute('aria-hidden','true'); }

function submitBookingForm(formData){
  // include ajax marker
  formData.append('ajax', '1');
  var submitBtn = document.getElementById('booking-submit'); submitBtn.disabled = true; submitBtn.textContent = 'Mengirim...';
  fetch('simpanBooking.php', { method: 'POST', body: formData, credentials: 'same-origin' })
    .then(r => {
      if (!r.ok) return r.text().then(t => { throw new Error('Server returned '+r.status+': '+(t||'no message')); });
      return r.text().then(t => { try { return JSON.parse(t); } catch(e){ throw new Error('Invalid JSON: '+(t||'empty')); } });
    }).then(json => {
      submitBtn.disabled = false; submitBtn.textContent = 'Kirim Booking';
      if (json.success) {
        hideBookingModal();
        showFlash(json.message || 'Booking berhasil.');
        // optionally redirect to riwayat after short delay
        setTimeout(function(){ window.location = 'riwayatBooking.php'; }, 1200);
      } else {
        document.getElementById('booking-errors').textContent = json.message || 'Terjadi kesalahan.';
      }
    }).catch(err => {
      submitBtn.disabled = false; submitBtn.textContent = 'Kirim Booking';
      document.getElementById('booking-errors').textContent = 'Koneksi gagal: ' + (err && err.message ? err.message : 'Unknown');
      console.error('submitBookingForm error:', err);
    });
}

function showFlash(msg){
  var f = document.createElement('div'); f.className = 'flash'; f.textContent = msg; document.querySelector('.container').insertBefore(f, document.querySelector('.container').firstChild);
  setTimeout(function(){ f.style.opacity = '0'; setTimeout(()=>f.remove(),400); }, 2200);
}

// Availability checking
function markBusyStudios(results){
  // clear existing markers
  document.querySelectorAll('.studio-card').forEach(function(card){
    card.classList.remove('studio-busy');
    var badge = card.querySelector('.busy-badge'); if (badge) badge.remove();
    var foot = card.querySelector('.busy-footer'); if (foot) foot.remove();
    var btn = card.querySelector('.open-booking'); if (btn) { btn.classList.remove('disabled'); btn.removeAttribute('aria-disabled'); btn.innerHTML = '<i class="fa fa-calendar-plus"></i> Booking'; }
  });

  results.forEach(function(item){
    var sid = item.studio_id.toString();
    var card = document.querySelector('.studio-card[data-studio-id="'+sid+'"]');
    if (!card) return;
    card.classList.add('studio-busy');
    var first = item.bookings[0];
    var short = first.start + '–' + first.end;
    var badge = document.createElement('div'); badge.className='busy-badge'; badge.textContent='Booked • ' + short;
    card.appendChild(badge);

    

    var btn = card.querySelector('.open-booking'); if (btn) { btn.classList.add('disabled'); btn.setAttribute('aria-disabled','true'); btn.innerHTML = '<i class="fa fa-calendar-times"></i> Tidak Tersedia'; }
  });
}

// attach filter controls
document.addEventListener('DOMContentLoaded', function(){
  var checkBtn = document.getElementById('filter-check');
  var clearBtn = document.getElementById('filter-clear');
  var openFilterBtn = document.getElementById('open-filter');
  var availBackdrop = document.getElementById('availability-backdrop');
  var filterCancel = document.getElementById('filter-cancel');

  if (openFilterBtn) openFilterBtn.addEventListener('click', function(){ if (availBackdrop) { availBackdrop.classList.add('show'); availBackdrop.setAttribute('aria-hidden','false'); } });
  if (filterCancel) filterCancel.addEventListener('click', function(){ if (availBackdrop) { availBackdrop.classList.remove('show'); availBackdrop.setAttribute('aria-hidden','true'); } });
  if (availBackdrop) availBackdrop.addEventListener('click', function(e){ if (e.target === availBackdrop) { availBackdrop.classList.remove('show'); availBackdrop.setAttribute('aria-hidden','true'); } });

  if (checkBtn) checkBtn.addEventListener('click', function(){
    var d = document.getElementById('filter-date').value;
    var s = document.getElementById('filter-start').value;
    var e = document.getElementById('filter-end').value;
    if (!d || !s || !e) { showFlash('Masukkan tanggal dan jam.'); return; }
    checkBtn.disabled = true; checkBtn.textContent = 'Mengecek...';
    fetch('checkAvailability.php?date='+encodeURIComponent(d)+'&start='+encodeURIComponent(s)+'&end='+encodeURIComponent(e))
      .then(r => {
        if (!r.ok) return r.text().then(t => { throw new Error('Server returned '+r.status+': '+(t||'no message')); });
        return r.text().then(t => { try { return JSON.parse(t); } catch(e){ throw new Error('Invalid JSON: '+(t||'empty')); } });
      }).then(json => {
        checkBtn.disabled = false; checkBtn.textContent = 'Cek';
        if (json.success) {
          markBusyStudios(json.data);
          // close modal if open
          if (availBackdrop) { availBackdrop.classList.remove('show'); availBackdrop.setAttribute('aria-hidden','true'); }
        } else showFlash(json.message || 'Gagal cek');
      }).catch(function(err){ checkBtn.disabled=false; checkBtn.textContent='Cek'; showFlash('Gagal koneksi: ' + (err && err.message ? err.message : 'Unknown')); console.error('filter availability error:', err); });
  });
  if (clearBtn) clearBtn.addEventListener('click', function(){ document.getElementById('filter-date').value = new Date().toISOString().slice(0,10); document.getElementById('filter-start').value='12:00'; document.getElementById('filter-end').value='13:00'; markBusyStudios([]); if (availBackdrop) { availBackdrop.classList.remove('show'); availBackdrop.setAttribute('aria-hidden','true'); } });
  // run a default availability check on load (optional)
  setTimeout(function(){ if (checkBtn) checkBtn.click(); }, 300);
});