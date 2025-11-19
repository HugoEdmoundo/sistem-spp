<!-- resources/views/murid/kuitansi-pdf.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>KUITANSI - {{ $pembayaran->id }}</title>

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
      font-size: 14px;
    }

    /* WATERMARK KEREN */
    .watermark {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%) rotate(-15deg);
      font-size: 120px;
      font-weight: 900;
      color: #4CAF50;
      opacity: 0.08;
      white-space: nowrap;
      z-index: -1;
      pointer-events: none;
      user-select: none;
      text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
      font-family: "Arial", sans-serif;
    }

    .watermark-circle {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 400px;
      height: 400px;
      border: 8px solid rgba(76, 175, 80, 0.05);
      border-radius: 50%;
      z-index: -1;
    }

    .watermark-pattern {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: 
        radial-gradient(circle at 20% 80%, rgba(76, 175, 80, 0.03) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(76, 175, 80, 0.03) 0%, transparent 50%);
      z-index: -1;
    }

    /* HEADER */
    .header {
      text-align: center;
      border-bottom: 2px solid #4CAF50;
      padding-bottom: 10px;
      margin-bottom: 20px;
      position: relative;
    }
    .header h1 { 
      margin: 0; 
      font-size: 24px; 
      font-weight: bold; 
      color: #2E7D32;
    }
    .header p { 
      margin: 5px 0; 
      font-size: 14px; 
      color: #666;
    }

    .header-decoration {
      position: absolute;
      bottom: -2px;
      left: 50%;
      transform: translateX(-50%);
      width: 100px;
      height: 3px;
      background: linear-gradient(90deg, transparent, #4CAF50, transparent);
    }

    /* INFO BOX */
    .info-box {
      border: 2px solid #4CAF50;
      padding: 15px;
      margin-top: 15px;
      background: linear-gradient(135deg, #f8fff8 0%, #f0f9f0 100%);
      border-radius: 8px;
      font-size: 14px;
      box-shadow: 0 2px 8px rgba(76, 175, 80, 0.1);
    }

    .info-box table { 
      width: 100%; 
    }

    /* CONTENT */
    .content { 
      margin-top: 25px; 
    }
    .info-table { 
      width: 100%; 
      border-collapse: collapse; 
      font-size: 14px; 
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
      border-radius: 6px;
      overflow: hidden;
    }
    .info-table th {
      width: 28%;
      background: linear-gradient(135deg, #4CAF50, #45a049);
      color: white;
      padding: 10px 12px;
      text-align: left;
      font-weight: bold;
      border-bottom: 1px solid #e2e2e2;
    }
    .info-table td {
      padding: 10px 12px;
      border-bottom: 1px solid #f0f0f0;
      background: white;
    }

    .info-table tr:last-child th,
    .info-table tr:last-child td {
      border-bottom: none;
    }

    /* JUMLAH */
    .jumlah-section {
      text-align: center;
      margin: 35px 0 30px 0;
      padding: 20px;
      background: linear-gradient(135deg, #f8fff8, #f0f9f0);
      border-radius: 10px;
      border: 2px dashed #4CAF50;
    }
    .jumlah-section .nominal {
      font-size: 28px;
      font-weight: bold;
      margin-top: 8px;
      color: #2E7D32;
      text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
    }

    /* TTD */
    .ttd-center {
      text-align: center;
      margin-top: 45px;
      padding: 20px;
      background: white;
      border-radius: 8px;
      position: relative;
    }
    .qr-ttd {
      width: 140px;
      margin-bottom: 15px;
      border: 2px solid #4CAF50;
      border-radius: 8px;
      padding: 5px;
      background: white;
    }
    .ttd-line {
      width: 220px;
      border-top: 3px solid #4CAF50;
      margin: 45px auto 10px auto;
      position: relative;
    }
    .ttd-line:before {
      content: "";
      position: absolute;
      top: -3px;
      left: 50%;
      transform: translateX(-50%);
      width: 20px;
      height: 3px;
      background: #4CAF50;
    }
    .nama-ttd {
      font-weight: bold;
      font-size: 15px;
      color: #2E7D32;
    }

    /* FOOTER */
    .footer {
      text-align: center;
      margin-top: 40px;
      font-size: 12px;
      color: #666;
      border-top: 1px solid #4CAF50;
      padding-top: 12px;
      background: linear-gradient(135deg, #f8fff8, #f0f9f0);
      padding: 15px;
      border-radius: 8px;
    }

    /* STAMP EFFECT */
    .stamp {
      position: absolute;
      top: 50%;
      right: 30px;
      transform: translateY(-50%) rotate(5deg);
      width: 140px;
      height: 140px;
      border: 4px solid #4CAF50;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #4CAF50;
      font-weight: bold;
      font-size: 18px;
      text-align: center;
      background: rgba(255,255,255,0.95);
      box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
      animation: pulse 2s infinite;
    }

    .stamp:before {
      content: "";
      position: absolute;
      width: 120px;
      height: 120px;
      border: 2px solid #4CAF50;
      border-radius: 50%;
    }

    @keyframes pulse {
      0% { transform: translateY(-50%) rotate(5deg) scale(1); }
      50% { transform: translateY(-50%) rotate(5deg) scale(1.05); }
      100% { transform: translateY(-50%) rotate(5deg) scale(1); }
    }

    /* DECORATIVE ELEMENTS */
    .corner-decoration {
      position: fixed;
      width: 100px;
      height: 100px;
      z-index: -1;
      opacity: 0.1;
    }

    .corner-tl {
      top: 10px;
      left: 10px;
      border-top: 3px solid #4CAF50;
      border-left: 3px solid #4CAF50;
    }

    .corner-tr {
      top: 10px;
      right: 10px;
      border-top: 3px solid #4CAF50;
      border-right: 3px solid #4CAF50;
    }

    .corner-bl {
      bottom: 10px;
      left: 10px;
      border-bottom: 3px solid #4CAF50;
      border-left: 3px solid #4CAF50;
    }

    .corner-br {
      bottom: 10px;
      right: 10px;
      border-bottom: 3px solid #4CAF50;
      border-right: 3px solid #4CAF50;
    }
  </style>
</head>

<body>

  <!-- WATERMARK KEREN -->
  <div class="watermark-pattern"></div>
  <div class="watermark-circle"></div>
  <div class="watermark">LUNAS ✓</div>

  <!-- CORNER DECORATIONS -->
  <div class="corner-decoration corner-tl"></div>
  <div class="corner-decoration corner-tr"></div>
  <div class="corner-decoration corner-bl"></div>
  <div class="corner-decoration corner-br"></div>

  <!-- STAMP LUNAS -->
  <div class="stamp">
    ✓ LUNAS<br>
    <span style="font-size: 14px;">OSMAN COURSE</span>
  </div>

  <!-- HEADER -->
  <div class="header">
    <h1>KUITANSI PEMBAYARAN</h1>
    <p>Osman Course Center - Jl. Pendidikan No. 123, Jakarta</p>
    <p>📞 (021) 123-4567 | ✉️ info@osmancourse.com</p>
    <div class="header-decoration"></div>
  </div>

  <!-- INFOBOX -->
  <div class="info-box">
    <table>
      <tr><td><strong>No. Kuitansi</strong></td><td>: KTN/{{ $pembayaran->id }}/{{ now()->format('Ym') }}</td></tr>
      <tr><td><strong>Tanggal</strong></td><td>: {{ $tanggal_sekarang }}</td></tr>
      <tr><td><strong>Jam</strong></td><td>: {{ $jam_sekarang }}</td></tr>
    </table>
  </div>

  <!-- CONTENT -->
  <div class="content">
    <table class="info-table">
      <tr><th>Nama Murid</th><td>{{ $pembayaran->user->nama }}</td></tr>
      <tr><th>Email</th><td>{{ $pembayaran->user->email }}</td></tr>
      <tr><th>Keterangan</th><td>{{ $pembayaran->keterangan }}</td></tr>
      @if($pembayaran->range_bulan)
      <tr><th>Periode</th><td>{{ $pembayaran->range_bulan }}</td></tr>
      @endif
      <tr><th>Metode Pembayaran</th><td>{{ $pembayaran->metode }}</td></tr>
      <tr><th>Status</th><td><strong style="color: #4CAF50;">{{ strtoupper($pembayaran->jenis_bayar) }}</strong></td></tr>
    </table>

    <div class="jumlah-section">
      <div style="font-size: 16px; color: #666;">Jumlah Pembayaran</div>
      <div class="nominal">Rp {{ number_format($pembayaran->jumlah,0,',','.') }}</div>
    </div>

    <div style="font-size:14px; background: #f8fff8; padding: 12px; border-radius: 6px; border-left: 4px solid #4CAF50;">
      <strong>Terbilang:</strong> <em>{{ \App\Helpers\Terbilang::make($pembayaran->jumlah) }} Rupiah</em>
    </div>
  </div>

  <!-- TTD -->
  <div class="ttd-center">
    <img src="https://res.cloudinary.com/dunynusuh/image/upload/v1761883622/barcode_ttg_admin_krwhcb.png" class="qr-ttd">
    <div class="ttd-line"></div>
    <div class="nama-ttd">{{ $pembayaran->admin->nama ?? 'Administrasi' }}</div>
    <div style="font-size: 14px; color: #666;">Admin Osman Course</div>
  </div>

  <!-- FOOTER -->
  <div class="footer">
    <strong>Kuitansi ini sah dan diterbitkan oleh sistem Osman Course</strong><br>
    Dicetak pada: {{ $tanggal_sekarang }} pukul {{ $jam_sekarang }}
  </div>

</body>
</html>
{{-- data:image/png;base64,{{ $qrCode }} --}}