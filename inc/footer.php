</main>
<footer class="site-footer">
  <div class="container">
    <p>&copy; <?php echo date('Y'); ?> <strong>Sistem Penyewaan Musik</strong>. All rights reserved.</p>
  </div>
</footer>
<script src="js/app.js"></script>

<!-- Booking modal -->
<div class="modal-backdrop booking-backdrop" id="booking-backdrop" aria-hidden="true">
  <div class="modal">
    <h3 id="booking-title">Booking</h3>
    <p id="booking-subtitle" style="margin-bottom:12px;color:var(--muted)"></p>
    <form id="booking-form">
      <input type="hidden" name="studio_id" id="b-studio-id">
      <div class="form-group"><label>Tanggal</label><input type="date" name="booking_date" id="b-date" required></div>
      <div class="form-row">
        <div class="form-group"><label>Waktu Mulai</label><input type="time" name="start_time" id="b-start" required></div>
        <div class="form-group"><label>Waktu Selesai</label><input type="time" name="end_time" id="b-end" required></div>
      </div>
      <div class="errors" id="booking-errors" style="margin-bottom:8px"></div>
      <div class="modal-actions">
        <button type="button" class="btn btn-outline modal-cancel" id="booking-cancel">Batal</button>
        <button type="submit" class="btn" id="booking-submit">Kirim Booking</button>
      </div>
    </form>
  </div>
</div>

<!-- Availability modal -->
<div class="modal-backdrop" id="availability-backdrop" aria-hidden="true">
  <div class="modal">
    <h3>Cek Ketersediaan</h3>
    <form id="availability-form">
      <div class="form-group"><label>Tanggal</label><input type="date" id="filter-date" value="<?php echo date('Y-m-d'); ?>"></div>
      <div class="form-row">
        <div class="form-group"><label>Waktu Mulai</label><input type="time" id="filter-start" value="12:00"></div>
        <div class="form-group"><label>Waktu Selesai</label><input type="time" id="filter-end" value="13:00"></div>
      </div>
      <div style="margin-top:8px" class="modal-actions">
        <button type="button" class="btn btn-outline" id="filter-cancel">Batal</button>
        <button type="button" class="btn" id="filter-check">Cek</button>
        <button type="button" class="btn btn-outline" id="filter-clear">Reset</button>
      </div>
    </form>
  </div>
</div>

</body>
</html>
