<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>KUITANSI ADMIN - {{ $pembayaran->id }}</title>

  <style>
    @page { 
      margin: 0; 
      size: A4; 
    }

    body {
      font-family: "Times New Roman", serif;
      margin: 15mm 18mm;
      color: #000;
      line-height: 1.4;
      position: relative;
      font-size: 14px; /* Ukuran font dasar yang lebih besar */
    }

    /* WATERMARK */
    .watermark {
      position: fixed;
      top: 42%;
      left: 50%;
      transform: translate(-50%, -50%) rotate(-25deg);
      font-size: 100px; /* Sedikit lebih besar */
      font-weight: bold;
      color: #707070;
      opacity: 0.08;
      white-space: nowrap;
      z-index: -1;
      pointer-events: none;
      user-select: none;
    }

    /* HEADER */
    .header {
      text-align: center;
      border-bottom: 1.5px solid #333;
      padding-bottom: 8px;
      margin-bottom: 18px;
    }
    .header h1 { 
      margin: 0; 
      font-size: 22px; /* Sedikit lebih besar */
      font-weight: bold; 
    }
    .header p { 
      margin: 4px 0; 
      font-size: 14px; /* Lebih besar */
    }

    /* INFO BOX */
    .info-box {
      border: 1px solid #333;
      padding: 12px 15px;
      margin-top: 12px;
      background: #fafafa;
      border-radius: 4px;
      font-size: 14px; /* Lebih besar */
    }

    .info-box table { 
      width: 100%; 
    }

    /* CONTENT */
    .content { 
      margin-top: 20px; 
    }
    .info-table { 
      width: 100%; 
      border-collapse: collapse; 
      font-size: 14px; /* Lebih besar */
    }
    .info-table th {
      width: 30%;
      background: #f0f0f0;
      padding: 8px 10px; /* Sedikit lebih besar */
      text-align: left;
      font-weight: bold;
      border-bottom: 1px solid #e2e2e2;
    }
    .info-table td {
      padding: 8px 10px; /* Sedikit lebih besar */
      border-bottom: 1px solid #ececec;
    }

    /* JUMLAH */
    .jumlah-section {
      text-align: center;
      margin: 30px 0 25px 0; /* Sedikit lebih besar */
    }
    .jumlah-section .nominal {
      font-size: 26px; /* Sedikit lebih besar */
      font-weight: bold;
      margin-top: 5px;
    }

    /* TTD */
    .ttd-center {
      text-align: center;
      margin-top: 40px; /* Sedikit lebih besar */
    }
    .qr-ttd {
      width: 130px; /* Sedikit lebih besar */
      margin-bottom: 12px;
    }
    .ttd-line {
      width: 200px; /* Sedikit lebih besar */
      border-top: 2px solid #000;
      margin: 40px auto 8px auto; /* Sedikit lebih besar */
    }
    .nama-ttd {
      font-weight: bold;
      font-size: 14px; /* Lebih besar */
    }

    /* FOOTER */
    .footer {
      text-align: center;
      margin-top: 35px; /* Sedikit lebih besar */
      font-size: 12px; /* Lebih besar */
      color: #555;
      border-top: 1px solid #ccc;
      padding-top: 8px;
    }
  </style>
</head>

<body>

  <!-- WATERMARK ADMIN -->
  <div class="watermark">INTERNAL USE ONLY</div>

  <!-- HEADER -->
  <div class="header">
    <h1>KUITANSI PEMBAYARAN (ADMIN)</h1>
    <p>Dokumen Internal | Osman Course</p>
  </div>

  <!-- INFOBOX -->
  <div class="info-box">
    <table>
      <tr><td><strong>No. Kuitansi</strong></td><td>: KTN/{{ $pembayaran->id }}/{{ now()->format('Ym') }}</td></tr>
      <tr><td><strong>Tanggal</strong></td><td>: {{ $tanggal_sekarang }}</td></tr>
      <tr><td><strong>Jam</strong></td><td>: {{ $jam_sekarang }}</td></tr>
      <tr><td><strong>ID Pembayaran</strong></td><td>: {{ $pembayaran->id }}</td></tr>
      <tr><td><strong>Admin Input</strong></td><td>: {{ $pembayaran->admin->nama ?? 'Administrasi' }}</td></tr>
    </table>
  </div>

  <!-- CONTENT -->
  <div class="content">
    <table class="info-table">
      <tr><th>Nama Murid</th><td>{{ $pembayaran->user->nama }}</td></tr>
      <tr><th>Email Murid</th><td>{{ $pembayaran->user->email }}</td></tr>
      <tr><th>Keterangan</th><td>{{ $pembayaran->keterangan }}</td></tr>
      @if($pembayaran->range_bulan)
      <tr><th>Periode</th><td>{{ $pembayaran->range_bulan }}</td></tr>
      @endif
      <tr><th>Metode Pembayaran</th><td>{{ $pembayaran->metode }}</td></tr>
      <tr><th>Status</th><td>{{ strtoupper($pembayaran->jenis_bayar) }}</td></tr>
    </table>

    <div class="jumlah-section">
      <div>Jumlah Pembayaran</div>
      <div class="nominal">Rp {{ number_format($pembayaran->jumlah,0,',','.') }}</div>
    </div>

    <div style="font-size:14px;">
      <strong>Terbilang:</strong> {{ \App\Helpers\Terbilang::make($pembayaran->jumlah) }} Rupiah
    </div>
  </div>

  <!-- TTD -->
  <div class="ttd-center">
    <img src="https://res.cloudinary.com/dunynusuh/image/upload/v1761883622/barcode_ttg_admin_krwhcb.png" class="qr-ttd">
    <div class="ttd-line"></div>
    <div class="nama-ttd">{{ $pembayaran->admin->nama ?? 'Administrasi' }}</div>
    <div style="font-size: 14px;">Admin Osman Course</div>
  </div>

  <!-- FOOTER -->
  <div class="footer">
    Dokumen internal Osman Course | Dicetak: {{ $tanggal_sekarang }} {{ $jam_sekarang }}
  </div>

</body>
</html>