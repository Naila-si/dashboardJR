<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard Kecelakaan</title>

  <!-- ======= CDN Leaflet & Chart.js ======= -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&display=swap" rel="stylesheet">

  <!-- ======= Style ======= -->
  <style>
    :root { --card: #f3f4f6; --sd: 0 2px 6px rgba(0,0,0,.08); }
    body { font-family: Arial, Helvetica, sans-serif; margin: 20px; }
    h2, h3 { margin: 0 0 .6rem; }
    #map { height: 400px; margin-top: 20px; }

    .flex { display: flex; flex-wrap: wrap; gap: 20px; }
    .card { flex: 1 1 180px; background: var(--card); padding: 20px; border-radius: 10px; text-align: center; box-shadow: var(--sd); }
    .box { flex: 1 1 300px; min-width: 260px; background: #fff; padding: 12px 16px; border-radius: 8px; box-shadow: var(--sd); }
    .wide { flex: 1 1 480px; min-width: 320px; }

    canvas { max-height: 300px; }
    .hBar { max-height: 460px; }

    form select { margin-right: 10px; padding: 6px 8px; border: 1px solid #d1d5db; border-radius: 6px; }
    .stats-container {
    display: flex;
    gap: 10px;
    justify-content: space-between;
    margin: 20px 0;
  }

  .stat-card {
    flex: 1;
    background: white;
    border: 1px solid #ddd;
    border-radius: 6px;
    padding: 15px 20px;
    text-align: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
  }

  .stat-title {
    font-size: 0.9rem;
    color: #6b7280;
    margin-bottom: 5px;
  }

  .stat-value {
    font-size: 1.8rem;
    font-weight: bold;
    color: #111827;
  }

  .stat-change {
    font-size: 0.9rem;
    color: #2563eb;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    margin-bottom: 8px;
  }

  .stat-subinfo {
    font-size: 0.85rem;
    color: #4b5563;
    line-height: 1.4;
  }
  .header-container {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 16px;
      margin: 20px 0;
    }
    .header-container img {
      max-height: 40px;
      width: auto;
    }
    .header-container h2 {
      margin: 0;
      font-size: 2rem;               /* Ukuran font */
      font-weight: 600;              /* Berat font */
      font-family: 'Poppins', sans-serif;  /* Font Poppins */
      color: #333;                  /* Warna teks */
      text-transform: uppercase;    /* Semua huruf besar */
      letter-spacing: 2px;          /* Spasi antar huruf */
      text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1); /* Efek bayangan pada teks */
    }
</style>
  </style>
</head>
<body>
<div class="header-container">
  <!-- Ganti src di bawah dengan path ke file logo BUMN Anda -->

  <h2>Dashboard Kecelakaan Lalu Lintas</h2>

  <!-- Ganti src di bawah dengan path ke file logo Jasa Raharja Anda -->

</div>

  <!-- ======= FILTER ======= -->
  <form id="filterForm" method="GET">
    <label>Bulan:
      <select name="bulan" onchange="filterForm.submit()">
        <option value="all">Semua</option>
        @foreach($bulanNames as $num=>$name)
          <option value="{{ $num }}" @selected($selectedMonth==$num)>{{ $name }}</option>
        @endforeach
      </select>
    </label>

    @php
  // Parsing dari filter global $yearFilter ke tahunDari & tahunSampai
  $tahunDari = $tahunSampai = null;
  if ($yearFilter !== 'all') {
      if (str_contains($yearFilter, '-')) {
          [$tahunDari, $tahunSampai] = explode('-', $yearFilter);
      } else {
          $tahunDari = $tahunSampai = $yearFilter;
      }
  }
@endphp

<label>Tahun:
  <select name="tahun" onchange="filterForm.submit()">
    <option value="all" @selected($yearFilter == 'all')>Semua</option>
    @foreach($tahunList as $y)
      <option value="{{ $y }}" @selected($yearFilter == $y)>{{ $y }}</option>
    @endforeach
  </select>
</label>

    <label>Provinsi/Kota:
      <select name="provinsi" onchange="filterForm.submit()">
        <option value="all">Semua</option>
        @foreach($provinsiList as $p)
          <option value="{{ $p }}" @selected($selectedProvince==$p)>{{ $p }}</option>
        @endforeach
      </select>
    </label>
  </form><br>

  <!-- ======= STATISTIK RINGKAS ======= -->
 <!-- ======= STATISTIK RINGKAS ======= -->
 <div class="stats-container">
  <div class="stat-card">
    <div class="stat-title">Jumlah Kendaraan Laka</div>
    <div class="stat-subinfo">{{ number_format($stat['total_kecelakaan']) }}</div>

  </div>

  <div class="stat-card">
    <div class="stat-title">Jumlah Korban</div>


    <div class="stat-subinfo">

    {{ number_format($stat['total_korban']) }}<br>
    MD: {{ $stat['total_md'] }} | LL: {{ $stat['total_ll'] }}
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-title">Jumlah Berkas</div>
    <div class="stat-subinfo">{{ number_format($stat['total_berkas']) }}
    </div>

  </div>

  <div class="stat-card">
    <div class="stat-title">Rp Santunan</div>
    <div class="stat-subinfo"> Rp {{ number_format($stat['total_santunan'], 0, ',', '.') }}
    </div>

  </div>
</div>


  <!-- ======= PETA ======= -->
  <div id="map" style="height: 500px;"></div>




  <!-- ======= FILTER TAHUN UNTUK GRAFIK ======= -->
  <h3 style="margin-top:30px">Perbandingan Berdasarkan Tahun</h3>
  <div style="margin:10px 0">
  <label>Tahun Dari:
  <select id="tahunDari" onchange="updateAllCharts()">
    @foreach($tahunList as $y)
      <option value="{{ $y }}" @selected($tahunDari == $y)>{{ $y }}</option>
    @endforeach
  </select>
</label>

<label>Sampai:
  <select id="tahunSampai" onchange="updateAllCharts()">
    @foreach($tahunList as $y)
      <option value="{{ $y }}" @selected($tahunSampai == $y)>{{ $y }}</option>
    @endforeach
  </select>
</label>
  </div>

  <!-- ======= GRAFIK VERTIKAL & WILAYAH ======= -->
  <h3 style="margin-top:30px">Grafik Santunan & Korban (Per Tahun)</h3>
  <div class="flex">
    <div class="box">
      <h3>Santunan</h3>
      <canvas id="santunanChart"></canvas>
    </div>
    <div class="box">
      <h3>Korban Meninggal</h3>
      <canvas id="korbanMDChart"></canvas>
    </div>
    <div class="box">
      <h3>Korban Luka</h3>
      <canvas id="korbanLLChart"></canvas>
    </div>
  </div>

  <!-- ======= GRAFIK WILAYAH ======= -->
  <div class="flex">
    <div class="box wide">
      <h3>Korban per Kota</h3>
      <canvas id="cityChart" class="hBar"></canvas>
    </div>
    <div class="box wide">
      <h3>Korban per Kecamatan</h3>
      <canvas id="districtChart" class="hBar"></canvas>
    </div>
  </div>

  <!-- ======= DEMOGRAFI ======= -->
  <h3 style="margin-top:40px">Korban berdasarkan Usia & Jenis Kelamin</h3>
  <div class="flex">
    <div class="box wide">
      <h3>Korban per Usia</h3>
      <canvas id="ageChart" class="hBar"></canvas>
    </div>
    <div class="box wide">
      <h3>Korban per Jenis Kelamin</h3>
      <canvas id="genderChart" class="hBar"></canvas>
    </div>
  </div>

  <!-- ======= JENIS TABRAKAN & WAKTU ======= -->
  <h3 style="margin-top:40px">Jenis Tabrakan & Waktu Kecelakaan</h3>
  <div class="flex">
    <div class="box wide">
      <h3>Jenis Tabrakan</h3>
      <canvas id="typeChart" class="hBar"></canvas>
    </div>
    <div class="box wide">
      <h3>Waktu Kecelakaan</h3>
      <canvas id="timeChart" class="hBar"></canvas>
    </div>
  </div> <br>

  <!-- ======= JENIS KENDARAAN & PEKERJAAN ======= -->
  <div class="flex">
    <div class="box wide">
      <h3>Jenis Kendaraan</h3>
      <canvas id="vehicleChart" class="hBar"></canvas>
    </div>
    <div class="box wide">
      <h3>Pekerjaan Korban</h3>
      <canvas id="occupationChart" class="hBar"></canvas>
    </div>
  </div>

  <!-- ======= SCRIPT JS ======= -->
 <!-- === MODIFIKASI SCRIPT GRAFIK DI BAGIAN BAWAH FILE === -->
 <script>
  const rawData = @json($data);
  const chartData = @json($chartData);
  const victimCityAll = @json($victimByCity);
  const victimDistAll = @json($victimByDistrict);
  const victimAgeAll = @json($victimByAge);
  const victimGenAll = @json($victimByGender);
  const victimTypeAll = @json($accidentTypeData);
  const victimTimeAll = @json($accidentTimeData);
  const vehicleTypeData = @json($vehicleTypeData);
  const occupationData = @json($occupationData);

  const monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
  const colors = ['#ef4444', '#3b82f6', '#10b981', '#eab308', '#8b5cf6', '#ec4899', '#14b8a6', '#f97316'];

  const map = L.map('map', {
    center: [-2, 107],
    zoom: 6,
    maxZoom: 18,
    minZoom: 4
  });

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
  }).addTo(map);

  let markers = [];
  let jalanHighlight = null;

  // Pemetaan jenis tabrakan ke gambar
  const gambarTabrakan = {
    "tabrakan depan": "https://media.istockphoto.com/id/1455492016/id/vektor/dua-kecelakaan-tabrakan-mobil-diisolasi-di-atas-putih.jpg?s=612x612&w=0&k=20&c=UjCm-SwopTjsgeINCwHbRnXZaio1ScQNxWWBKv53NdQ=",
    "tabrakan belakang": "data:image/jpeg;base64,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",
    "tabrakan samping":"https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQkuPSepqHGuWzgMXy08akMJRy4dpMUfYdpeTc2Vcydk5USU9L6yz1vfSw6zIMNP0iHivQ&usqp=CAU",
    "lainnya": "https://upload.wikimedia.org/wikipedia/commons/3/36/Warning_icon.png"
  };

  function refreshMap(data) {
    markers.forEach(marker => map.removeLayer(marker));
    markers = [];

    data.forEach(item => {
      if (item.latitude && item.longitude) {
        let jenisTabrakan = (item.jenis_tabrakan || 'lainnya').toLowerCase().trim();
        if (!gambarTabrakan[jenisTabrakan]) {
          jenisTabrakan = 'lainnya';
        }
        const gambar = gambarTabrakan[jenisTabrakan];

        const marker = L.marker([item.latitude, item.longitude]).addTo(map);

        // Memperbaiki tampilan pop-up
        marker.bindPopup(`
          <div style="font-family: Arial, sans-serif; color: #333; max-width: 300px;">
            <!-- Gambar Tabrakan -->
            <img src="${gambar}"
                 onerror="this.onerror=null;this.src='https://tinyjpg.com/images/social/website.jpg';"
                 style="width: 100%; height: 150px; object-fit: cover; border-radius: 8px; margin-bottom: 12px;"
                 alt="Gambar Tabrakan" />

            <!-- Informasi Lokasi -->
            <div style="padding: 10px; background-color: #f8f9fa; border-radius: 8px; margin-bottom: 10px;">
              <strong style="font-size: 16px; color: #333;">${item.nama_jalan ?? '-'}, ${item.kelurahan_desa ?? '-'}, ${item.kecamatan ?? '-'}, ${item.kota ?? '-'}</strong><br>
              <span style="font-size: 12px; color: #888;">Tahun: ${item.tahun_laka ?? '-'}</span><br>
              <div style="font-size: 12px; margin-top: 8px;">
                <strong>MD:</strong> ${item.korban_md ?? 0} <br>
                <strong>LL:</strong> ${item.korban_ll ?? 0} <br>
                <strong>Total Korban:</strong> ${item.korban_total ?? 0}
              </div>
            </div>

            <!-- Rencana Penanganan -->
            <div style="padding: 10px; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
              <strong style="color: #444;">Rencana Penanganan:</strong>
              <div style="max-height: 150px; overflow-y: auto; padding-right: 10px; border: 1px solid #ddd; border-radius: 4px; background-color: #f9f9f9; padding: 8px; font-size: 12px; color: #555;">
                ${item.rencana_penanganan ? item.rencana_penanganan.slice(0, 200) + '...' : 'Tidak ada informasi'}
              </div>

              <button style="font-size: 12px; color: #007bff; background-color: transparent; border: none; cursor: pointer; padding: 5px 0; margin-top: 8px; text-decoration: underline;" onclick="lihatSelengkapnya(${item.id})">Lihat Selengkapnya</button>
            </div>
          </div>
        `, { maxWidth: 300 });

        marker.on('click', () => {
          if (jalanHighlight) {
            map.removeLayer(jalanHighlight);
          }
          jalanHighlight = L.circleMarker([item.latitude, item.longitude], {
            radius: 30,
            color: '#ff0000',
            fillColor: '#ffcccc',
            fillOpacity: 0.5,
            weight: 2
          }).addTo(map).bindTooltip(item.nama_jalan ?? 'Tidak diketahui', {
            permanent: true,
            direction: 'top',
            className: 'custom-tooltip'
          }).openTooltip();

          map.setView([item.latitude, item.longitude], 13, { animate: true });
        });

        markers.push(marker);
      }
    });
  }

  // Fungsi untuk menampilkan rencana penanganan secara lengkap dalam modal
  function lihatSelengkapnya(id) {
    const item = rawData.find(record => record.id === id);
    if (item) {
      // Membuka modal untuk menampilkan informasi lengkap
      const modal = document.getElementById('rencanaModal');
      const modalContent = document.getElementById('rencanaModalContent');
      modalContent.innerHTML = `
        <h3 style="font-size: 20px; font-weight: bold; color: #333;">Rencana Penanganan Kecelakaan</h3>
        <p><strong>Lokasi:</strong> ${item.nama_jalan ?? '-'}, ${item.kelurahan_desa ?? '-'}, ${item.kecamatan ?? '-'}, ${item.kota ?? '-'}</p>
        <p><strong>Tahun:</strong> ${item.tahun_laka ?? '-'}</p>
        <p><strong>Korban MD:</strong> ${item.korban_md ?? 0}</p>
        <p><strong>Korban LL:</strong> ${item.korban_ll ?? 0}</p>
        <p><strong>Total Korban:</strong> ${item.korban_total ?? 0}</p>
        <p><strong>Rencana Penanganan Lengkap:</strong></p>
        <p>${item.rencana_penanganan || 'Tidak ada informasi mengenai rencana penanganan.'}</p>
        <button onclick="tutupModal()" style="background-color: #007bff; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; margin-top: 20px;">Tutup</button>
      `;
      modal.style.display = 'block';
    }
  }

  // Menutup modal
  function tutupModal() {
    const modal = document.getElementById('rencanaModal');
    modal.style.display = 'none';
  }

  // Refresh map dengan data terbaru
  refreshMap(rawData);

  function sliceByYear(array) {
    const from = +tahunDari.value;
    const to = +tahunSampai.value;
    return array.filter(item => item.tahun >= from && item.tahun <= to);
  }

  function dataset(field) {
    const from = +tahunDari.value;
    const to = +tahunSampai.value;
    let idx = 0;
    return Object.keys(chartData)
      .filter(year => year >= from && year <= to)
      .map(year => {
        const monthly = chartData[year];
        return {
          label: year,
          data: monthLabels.map((_, i) => {
            const record = monthly.find(v => v.bulan_laka == (i + 1));
            return record ? (record[field] ?? 0) : 0;
          }),
          backgroundColor: colors[idx++ % colors.length]
        };
      });
  }

  function makeBar(context, field, prefix = '', suffix = '') {
    return new Chart(context, {
      type: 'bar',
      data: {
        labels: monthLabels,
        datasets: dataset(field)
      },
      options: {
        responsive: true,
        plugins: {
          tooltip: {
            callbacks: {
              label: ctx => `${prefix}${(ctx.raw ?? 0).toLocaleString()}${suffix}`
            }
          }
        },
        scales: { y: { beginAtZero: true } }
      }
    });
  }

  const santunanChart = makeBar(document.getElementById('santunanChart'), 'total_santunan', 'Rp ');
  const korbanMDChart = makeBar(document.getElementById('korbanMDChart'), 'md', '', ' org');
  const korbanLLChart = makeBar(document.getElementById('korbanLLChart'), 'll', '', ' org');

  function makeHBar(context, dataArr, suffix = ' org', multiYear = false) {
    const isMultiYear = multiYear && new Set(dataArr.map(o => o.tahun)).size > 1;
    return new Chart(context, {
      type: 'bar',
      data: {
        labels: dataArr.map(o => o.label),
        datasets: [{
          data: dataArr.map(o => o.total ?? 0),
          backgroundColor: isMultiYear
            ? dataArr.map((_, i) => colors[i % colors.length])
            : '#60a5fa',
          borderRadius: 4
        }]
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: ctx => `${(ctx.raw ?? 0).toLocaleString()}${suffix}`
            }
          }
        },
        scales: { x: { beginAtZero: true } }
      }
    });
  }

  let cityChart = makeHBar(document.getElementById('cityChart'), sliceByYear(victimCityAll), ' org', true);
  let districtChart = makeHBar(document.getElementById('districtChart'), sliceByYear(victimDistAll), ' org', true);
  let ageChart = makeHBar(document.getElementById('ageChart'), sliceByYear(victimAgeAll), ' org', true);
  let genderChart = makeHBar(document.getElementById('genderChart'), sliceByYear(victimGenAll), ' org', true);
  let typeChart = makeHBar(document.getElementById('typeChart'), victimTypeAll, ' org');
  let timeChart = makeHBar(document.getElementById('timeChart'), victimTimeAll, ' org');
  let vehicleChart = makeHBar(document.getElementById('vehicleChart'), vehicleTypeData, ' org');
  let occupationChart = makeHBar(document.getElementById('occupationChart'), occupationData, ' org');

  function updateAllCharts() {
    santunanChart.data.datasets = dataset('total_santunan');
    santunanChart.update();
    korbanMDChart.data.datasets = dataset('md');
    korbanMDChart.update();
    korbanLLChart.data.datasets = dataset('ll');
    korbanLLChart.update();

    const updateChart = (chart, data) => {
      chart.data.labels = data.map(o => o.label);
      chart.data.datasets[0].data = data.map(o => o.total ?? 0);
      chart.data.datasets[0].backgroundColor = new Set(data.map(o => o.tahun)).size > 1
        ? data.map((_, i) => colors[i % colors.length])
        : '#60a5fa';
      chart.update();
    };

    updateChart(cityChart, sliceByYear(victimCityAll));
    updateChart(districtChart, sliceByYear(victimDistAll));
    updateChart(ageChart, sliceByYear(victimAgeAll));
    updateChart(genderChart, sliceByYear(victimGenAll));
  }
</script>

<!-- Modal Rencana Penanganan -->
<div id="rencanaModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 9999;">
  <div style="background-color: white; margin: 50px auto; padding: 20px; border-radius: 8px; width: 80%; max-width: 600px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
    <div id="rencanaModalContent"></div>
  </div>
</div>

<!-- Style Tooltip Biar Keren -->
<style>
  .custom-tooltip {
    background: #ff4444;
    color: white;
    padding: 4px 8px;
    border-radius: 6px;
    font-weight: bold;
    font-size: 13px;
  }
</style>

</body>
</html>
