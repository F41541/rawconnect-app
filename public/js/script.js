document.addEventListener('DOMContentLoaded', function () {
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('overlay');

  // Fungsi untuk toggle sidebar
  function toggleSidebar() {
      sidebar.classList.toggle('show');
      if (overlay) {
          overlay.classList.toggle('show');
      }
  }

  // Tambahkan event listener untuk overlay
  if (overlay) {
      overlay.addEventListener('click', toggleSidebar);
  }

  // Optional: expose toggleSidebar ke global (jika dipakai di onclick HTML)
  window.toggleSidebar = toggleSidebar;

  // Counter
const input = document.getElementById('jumlah');
if (input) {
    const feedback = document.getElementById('jumlah_error'); // Ambil elemen error

    const hideError = () => {
        if (feedback) {
            input.classList.remove('is-invalid');
            feedback.style.display = 'none';
        }
    };

    window.decrement = function () {
        input.value = Math.max(1, parseInt(input.value || 1) - 1); // Batasi minimal 1
        hideError(); // Panggil fungsi untuk sembunyikan error
    };

    window.increment = function () {
        input.value = parseInt(input.value || 0) + 1;
        hideError(); // Panggil fungsi untuk sembunyikan error
    };

    //Memblokir input non-angka
    input.addEventListener('keydown', function(event) {
    // Izinkan: Backspace, Delete, Tab, Escape, Enter, dan semua tombol panah
    const allowedKeys = [
        'Backspace', 'Delete', 'Tab', 'Escape', 'Enter', 
        'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown',
        'Home', 'End'
    ];
    if (allowedKeys.includes(event.key)) {
        return; // Izinkan tombol
    }

    // Izinkan Ctrl+A untuk "Select All"
    if (event.key === 'a' && (event.ctrlKey === true || event.metaKey === true)) {
        return;
    }

    // Jika tombol yang ditekan bukan angka, hentikan
    if (!/^[0-9]$/.test(event.key)) {
        event.preventDefault();
    }
});

    input.addEventListener('input', hideError);
}

  // Dropdown
  const dropdownSidebar = document.getElementById('sidebar');
  const dropdownButtons = dropdownSidebar.querySelectorAll('[data-bs-toggle="collapse"]');

  dropdownButtons.forEach((button) => {
      button.addEventListener('click', function () {
          const target = document.querySelector(this.getAttribute('data-bs-target'));

          // Tutup semua dropdown kecuali yang diklik
          dropdownSidebar.querySelectorAll('.collapse').forEach((dropdown) => {
              if (dropdown !== target && dropdown.classList.contains('show')) {
                  const collapseInstance = bootstrap.Collapse.getInstance(dropdown);
                  if (collapseInstance) {
                      collapseInstance.hide();
                  }
              }
          });

          // Tunggu hingga animasi selesai sebelum membuka dropdown lain
          target.addEventListener('shown.bs.collapse', () => {
              dropdownSidebar.querySelectorAll('.collapse').forEach((dropdown) => {
                  if (dropdown !== target && dropdown.classList.contains('show')) {
                      const collapseInstance = bootstrap.Collapse.getInstance(dropdown);
                      if (collapseInstance) {
                          collapseInstance.hide();
                      }
                  }
              });
          });
      });
  });

    // Validasi Form Buat Pengiriman
  window.validateForm = function () {
      const fields = [
          { id: 'nama_toko', msg: 'Nama toko wajib diisi.' },
          { id: 'nama_merchant', msg: 'Nama merchant wajib diisi.' },
          { id: 'nama_ekspedisi', msg: 'Nama ekspedisi wajib diisi.' },
          { id: 'jenis_produk', msg: 'Jenis produk wajib diisi.' },
          { id: 'nama_produk', msg: 'Nama produk wajib diisi.' },
          { id: 'jumlah_produk', msg: 'Jumlah produk wajib diisi.' }
      ];

      let valid = true;

      fields.forEach(field => {
          const input = document.getElementById(field.id);
          const feedback = field.id === 'jumlah_produk'
              ? document.getElementById('jumlah_produk_error')
              : input.closest('.mb-3').querySelector('.invalid-feedback');

          if (!input || !feedback) return;

          if (!input.value || input.value.trim() === '' || parseInt(input.value) < 1) {
              input.classList.add('is-invalid');
              feedback.textContent = field.msg;
              feedback.style.display = 'block';
              valid = false;
          }

          // Hapus alert merah saat user ngetik
          input.addEventListener('input', () => {
              input.classList.remove('is-invalid');
              feedback.style.display = 'none';
          });
      });

      return valid;
  };

  // Copy data ke form "Langsung Kirim"
  window.copyFormData = function () {
      const fields = [
          { id: 'nama_toko', msg: 'Nama toko wajib diisi.' },
          { id: 'nama_merchant', msg: 'Nama merchant wajib diisi.' },
          { id: 'nama_ekspedisi', msg: 'Nama ekspedisi wajib diisi.' },
          { id: 'jenis_produk', msg: 'Jenis produk wajib diisi.' },
          { id: 'nama_produk', msg: 'Nama produk wajib diisi.' },
          { id: 'jumlah_produk', msg: 'Jumlah produk wajib diisi.' }
      ];

      let valid = true;

      fields.forEach(field => {
          const input = document.getElementById(field.id);
          const hidden = document.getElementById('form_' + field.id);
          const feedback = field.id === 'jumlah_produk'
              ? document.getElementById('jumlah_produk_error')
              : input.closest('.mb-3').querySelector('.invalid-feedback');

          if (!input || !hidden || !feedback) return;

          hidden.value = input.value;

          if (!input.value || input.value.trim() === '' || parseInt(input.value) < 1) {
              input.classList.add('is-invalid');
              feedback.textContent = field.msg;
              feedback.style.display = 'block';
              valid = false;
          }

          input.addEventListener('input', () => {
              input.classList.remove('is-invalid');
              feedback.style.display = 'none';
          });
      });

      return valid;
  };

});