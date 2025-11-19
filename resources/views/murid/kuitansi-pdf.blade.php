<!-- resources/views/murid/kuitansi-pdf.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>KUITANSI - {{ $pembayaran->id }}</title>
  
  <style>
    /* RESET DAN PENGATURAN DASAR UNTUK PDF */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    @page {
      size: A4;
      margin: 15mm 18mm;
    }
    
    body {
      font-family: "Times New Roman", serif;
      color: #000;
      line-height: 1.4;
      position: relative;
      font-size: 14px;
      background: white;
      width: 100%;
      min-height: 297mm;
      padding: 0;
      margin: 0;
    }

    /* KONTAINER UTAMA */
    .container {
      width: 100%;
      max-width: 210mm;
      margin: 0 auto;
      position: relative;
    }

    /* WATERMARK - DIPERBAIKI UNTUK PDF */
    .watermark {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%) rotate(-45deg);
      font-size: 100px;
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

    /* HEADER - DIPERBAIKI */
    .header {
      text-align: center;
      border-bottom: 2px solid #4CAF50;
      padding-bottom: 15px;
      margin-bottom: 20px;
      position: relative;
    }
    
    .header h1 { 
      margin: 0 0 10px 0; 
      font-size: 24px; 
      font-weight: bold; 
      color: #2E7D32;
      letter-spacing: 1px;
    }
    
    .header p { 
      margin: 3px 0; 
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

    /* INFO BOX - DIPERBAIKI */
    .info-box {
      border: 2px solid #4CAF50;
      padding: 15px;
      margin: 15px 0 25px 0;
      background: #f8fff8;
      border-radius: 8px;
      font-size: 14px;
    }

    .info-box table { 
      width: 100%; 
      border-collapse: collapse;
    }
    
    .info-box tr td:first-child {
      width: 140px;
      font-weight: bold;
      vertical-align: top;
    }
    
    .info-box tr td {
      padding: 4px 0;
    }

    /* TABEL INFORMASI - DIPERBAIKI */
    .info-table { 
      width: 100%; 
      border-collapse: collapse; 
      font-size: 14px; 
      margin: 20px 0;
      border: 1px solid #e0e0e0;
    }
    
    .info-table th {
      width: 30%;
      background: #4CAF50;
      color: white;
      padding: 12px 15px;
      text-align: left;
      font-weight: bold;
      border-right: 1px solid #e0e0e0;
    }
    
    .info-table td {
      padding: 12px 15px;
      border-bottom: 1px solid #f0f0f0;
      background: white;
    }

    .info-table tr:last-child td {
      border-bottom: none;
    }

    /* BAGIAN JUMLAH - DIPERBAIKI */
    .jumlah-section {
      text-align: center;
      margin: 30px 0;
      padding: 25px 20px;
      background: #f8fff8;
      border-radius: 8px;
      border: 2px dashed #4CAF50;
    }
    
    .jumlah-label {
      font-size: 16px; 
      color: #666;
      margin-bottom: 10px;
    }
    
    .nominal {
      font-size: 28px;
      font-weight: bold;
      color: #2E7D32;
    }

    /* BAGIAN TERBILANG - DIPERBAIKI */
    .terbilang {
      font-size: 14px;
      background: #f8fff8;
      padding: 12px 15px;
      border-radius: 6px;
      border-left: 4px solid #4CAF50;
      margin: 15px 0 25px 0;
    }

    /* BAGIAN TANDA TANGAN - DIPERBAIKI */
    .ttd-section {
      text-align: center;
      margin: 40px 0 30px 0;
      padding: 20px;
    }
    
    .qr-ttd {
      width: 120px;
      height: 120px;
      margin: 0 auto 15px auto;
      border: 1px solid #4CAF50;
      border-radius: 8px;
      padding: 5px;
      background: white;
      display: block;
    }
    
    .ttd-line {
      width: 200px;
      border-top: 2px solid #4CAF50;
      margin: 40px auto 8px auto;
    }
    
    .nama-ttd {
      font-weight: bold;
      font-size: 16px;
      color: #2E7D32;
      margin-top: 5px;
    }
    
    .jabatan-ttd {
      font-size: 14px; 
      color: #666;
    }

    /* FOOTER - DIPERBAIKI */
    .footer {
      text-align: center;
      margin-top: 40px;
      font-size: 12px;
      color: #666;
      border-top: 1px solid #4CAF50;
      padding-top: 15px;
      background: #f8fff8;
      padding: 15px;
      border-radius: 8px;
    }

    /* STAMP EFFECT - DIPERBAIKI UNTUK PDF */
    .stamp {
      position: absolute;
      top: 120px;
      right: 20px;
      transform: rotate(15deg);
      width: 120px;
      height: 120px;
      border: 3px solid #4CAF50;
      border-radius: 50%;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      color: #4CAF50;
      font-weight: bold;
      font-size: 16px;
      text-align: center;
      background: rgba(255,255,255,0.9);
      box-shadow: 0 3px 10px rgba(76, 175, 80, 0.3);
      line-height: 1.2;
      z-index: 10;
    }

    /* STATUS BADGE */
    .status-badge {
      display: inline-block;
      padding: 5px 12px;
      border-radius: 4px;
      background-color: #4CAF50;
      color: white;
      font-weight: bold;
      font-size: 12px;
    }
    
    /* DEKORASI SEDERHANA */
    .corner {
      position: absolute;
      width: 20px;
      height: 20px;
    }
    
    .corner-tl {
      top: 0;
      left: 0;
      border-top: 2px solid #4CAF50;
      border-left: 2px solid #4CAF50;
    }
    
    .corner-tr {
      top: 0;
      right: 0;
      border-top: 2px solid #4CAF50;
      border-right: 2px solid #4CAF50;
    }
    
    .corner-bl {
      bottom: 0;
      left: 0;
      border-bottom: 2px solid #4CAF50;
      border-left: 2px solid #4CAF50;
    }
    
    .corner-br {
      bottom: 0;
      right: 0;
      border-bottom: 2px solid #4CAF50;
      border-right: 2px solid #4CAF50;
    }

    /* MEDIA QUERY UNTUK CETAK/PDF */
    @media print {
      body {
        margin: 0;
        padding: 0;
        width: 210mm;
        height: 297mm;
      }
      
      .container {
        width: 210mm;
        margin: 0;
        padding: 15mm 18mm;
      }
      
      .watermark {
        opacity: 0.1;
      }
      
      .stamp {
        transform: rotate(15deg);
        box-shadow: 0 0 10px rgba(76, 175, 80, 0.3);
      }
    }
  </style>
</head>

<body>
  <div class="container">
    <!-- WATERMARK -->
    <div class="watermark">LUNAS ✓</div>

    <!-- DEKORASI CORNER -->
    <div class="corner corner-tl"></div>
    <div class="corner corner-tr"></div>
    <div class="corner corner-bl"></div>
    <div class="corner corner-br"></div>

    <!-- STAMP LUNAS -->
    {{-- <div class="stamp">
      ✓ LUNAS<br>
      <span style="font-size: 12px;">OSMAN COURSE</span>
    </div> --}}

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
        <tr>
          <td><strong>No. Kuitansi</strong></td>
          <td>: KTN/{{ $pembayaran->id }}/{{ now()->format('Ym') }}</td>
        </tr>
        <tr>
          <td><strong>Tanggal</strong></td>
          <td>: {{ $tanggal_sekarang }}</td>
        </tr>
        <tr>
          <td><strong>Jam</strong></td>
          <td>: {{ $jam_sekarang }}</td>
        </tr>
      </table>
    </div>

    <!-- INFORMASI PEMBAYARAN -->
    <table class="info-table">
      <tr>
        <th>Nama Murid</th>
        <td>{{ $pembayaran->user->nama }}</td>
      </tr>
      <tr>
        <th>Email</th>
        <td>{{ $pembayaran->user->email }}</td>
      </tr>
      <tr>
        <th>Keterangan</th>
        <td>{{ $pembayaran->keterangan }}</td>
      </tr>
      @if($pembayaran->range_bulan)
      <tr>
        <th>Periode</th>
        <td>{{ $pembayaran->range_bulan }}</td>
      </tr>
      @endif
      <tr>
        <th>Metode Pembayaran</th>
        <td>{{ $pembayaran->metode }}</td>
      </tr>
      <tr>
        <th>Status</th>
        <td><span class="status-badge">{{ strtoupper($pembayaran->jenis_bayar) }}</span></td>
      </tr>
    </table>

    <!-- JUMLAH PEMBAYARAN -->
    <div class="jumlah-section">
      <div class="jumlah-label">Jumlah Pembayaran</div>
      <div class="nominal">Rp {{ number_format($pembayaran->jumlah,0,',','.') }}</div>
    </div>

    <!-- TERBILANG -->
    <div class="terbilang">
      <strong>Terbilang:</strong> <em>{{ \App\Helpers\Terbilang::make($pembayaran->jumlah) }} Rupiah</em>
    </div>

    <!-- TANDA TANGAN -->
    <div class="ttd-section">
      <img src="https://res.cloudinary.com/dunynusuh/image/upload/v1761883622/barcode_ttg_admin_krwhcb.png" 
           class="qr-ttd" 
           alt="QR Code Tanda Tangan">
      <div class="ttd-line"></div>
      <div class="nama-ttd">{{ $pembayaran->admin->nama ?? 'Administrasi' }}</div>
      <div class="jabatan-ttd">Admin Osman Course</div>
    </div>

    <!-- FOOTER -->
    <div class="footer">
      <strong>Kuitansi ini sah dan diterbitkan oleh sistem Osman Course</strong><br>
      Dicetak pada: {{ $tanggal_sekarang }} pukul {{ $jam_sekarang }}
    </div>
  </div>
</body>
</html>
{{-- data:image/png;base64,{{ $qrCode }} --}}