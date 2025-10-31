<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>KUITANSI - {{ $pembayaran->id }}</title>
  <style>
    @page {
      margin: 0;
      size: A4;
    }

    body {
      font-family: "Times New Roman", serif;
      margin: 20mm 25mm;
      color: #000;
      line-height: 1.6;
      background: #fff;
      height: 297mm;
      overflow: hidden;
      position: relative;
    }

    .container {
      min-height: 257mm;
      display: flex;
      flex-direction: column;
    }

    /* === WATERMARK === */
    .watermark {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%) rotate(-45deg);
      font-size: 80px;
      color: rgba(0, 0, 0, 0.05);
      z-index: -1;
      white-space: nowrap;
      font-weight: bold;
      pointer-events: none;
    }

    /* === JUDUL KUITANSI === */
    .judul-kuitansi {
      text-align: center;
      margin: 10px 0 20px 0;
    }

    .judul-kuitansi h1 {
      font-size: 22px;
      font-weight: bold;
      text-decoration: underline;
      margin: 0 0 8px 0;
      letter-spacing: 1px;
      color: #2c5aa0;
    }

    .nomor-kuitansi {
      font-size: 14px;
      font-weight: bold;
      margin: 0;
    }

    /* === KONTEN KUITANSI === */
    .konten-kuitansi {
      margin: 20px 0;
      flex: 1;
    }

    .info-table {
      width: 100%;
      border-collapse: collapse;
      margin: 15px 0;
    }

    .info-table td {
      padding: 10px 5px;
      vertical-align: top;
      border-bottom: 1px solid #f0f0f0;
    }

    .info-table tr:last-child td {
      border-bottom: none;
    }

    .label {
      width: 35%;
      font-weight: bold;
      color: #333;
    }

    .value {
      width: 65%;
      color: #000;
    }

    /* === JUMLAH PEMBAYARAN === */
    .jumlah-section {
      text-align: center;
      margin: 25px 0;
      padding: 20px;
      background: linear-gradient(135deg, #f0f5ff 0%, #e1ebff 100%);
      border: 2px solid #2c5aa0;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .jumlah-label {
      font-size: 18px;
      font-weight: bold;
      margin-bottom: 10px;
      color: #2c5aa0;
    }

    .jumlah-nominal {
      font-size: 24px;
      font-weight: bold;
      margin: 0;
      color: #2c5aa0;
      text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
    }

    /* === TERBILANG === */
    .terbilang {
      background: #f0f5ff;
      border-left: 5px solid #2c5aa0;
      padding: 15px;
      margin: 20px 0;
      font-style: italic;
      border-radius: 0 5px 5px 0;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .terbilang strong {
      color: #2c5aa0;
    }

    /* === TANDA TANGAN & BARCODE === */
    .ttd-barcode-container {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-top: 50px;
      gap: 30px;
    }

    .barcode-section {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .ttd-section {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .ttd-wrapper {
      flex: 2;
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 60px;
      margin-top: 20px;
    }

    .ttd-box {
      flex: 1;
      text-align: center;
    }

    .barcode-container {
      background: #f9f9f9;
      border: 1px solid #ddd;
      border-radius: 6px;
      padding: 15px;
      margin: 10px 0;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .barcode {
      font-family: 'Courier New', monospace;
      font-size: 14px;
      letter-spacing: 2px;
      margin: 0;
      padding: 8px 0;
      text-align: center;
      background: #fff;
      border: 1px solid #eee;
      border-radius: 4px;
      font-weight: bold;
    }

    .barcode-info {
      display: flex;
      justify-content: space-between;
      font-size: 11px;
      margin-top: 8px;
      line-height: 1.4;
      color: #666;
      gap: 10px;
    }

    .barcode-info-item {
      flex: 1;
    }

    .ttd-line {
      border-top: 2px solid #000;
      width: 180px;
      margin: 40px auto 8px auto;
    }

    .nama-ttd {
      font-weight: bold;
      margin-top: 8px;
      font-size: 14px;
    }

    .jabatan {
      font-size: 12px;
      color: #666;
      margin-top: 3px;
    }

    .ttd-image {
      width: 80px;
      height: 80px;
      margin: 10px auto;
      display: block;
      object-fit: contain;
    }

    /* === FOOTER === */
    .footer {
      text-align: center;
      margin-top: 30px;
      padding-top: 15px;
      border-top: 1px solid #ddd;
      font-size: 10px;
      color: #666;
      line-height: 1.4;
    }

    /* === PRINT STYLES === */
    @media print {
      body {
        margin: 20mm 25mm;
        height: 297mm;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }
      .no-print {
        display: none !important;
      }
      .jumlah-section {
        background: #f0f5ff !important;
        -webkit-print-color-adjust: exact;
      }
      .terbilang {
        background: #f0f5ff !important;
        -webkit-print-color-adjust: exact;
      }
      .barcode-container {
        box-shadow: none;
      }
    }

    /* === RESPONSIVE === */
    @media (max-width: 768px) {
      body {
        margin: 15mm;
      }
      .ttd-wrapper {
        flex-direction: column;
        gap: 30px;
      }
      .barcode-info {
        flex-direction: column;
      }
    }
  </style>
</head>

<body>
  <!-- WATERMARK -->
  <div class="watermark">KUITANSI RESMI</div>

  <div class="container">
    <!-- === JUDUL KUITANSI === -->
    <div class="judul-kuitansi">
      <h1>KUITANSI PEMBAYARAN</h1>
      <p class="nomor-kuitansi">
        Nomor: {{ sprintf('%04d', $pembayaran->id) }}/KUIT/AR-RAHMAN/{{ now()->format('Y') }}
      </p>
    </div>

    <!-- === KONTEN KUITANSI === -->
    <div class="konten-kuitansi">
      <table class="info-table">
        <tr>
          <td class="label">Telah diterima dari</td>
          <td class="value">: <strong>{{ $pembayaran->user->nama }}</strong></td>
        </tr>
        <tr>
          <td class="label">NIS</td>
          <td class="value">: {{ $pembayaran->user->username }}</td>
        </tr>
        <tr>
          <td class="label">Untuk pembayaran</td>
          <td class="value">:
            @if($pembayaran->tagihan_id)
              <strong>{{ $pembayaran->tagihan->keterangan ?? 'Tagihan Sekolah' }}</strong>
            @else
              <strong>SPP {{ $pembayaran->keterangan }}</strong>
              @if($pembayaran->bulan_mulai && $pembayaran->bulan_akhir)
                (Periode: {{ getNamaBulan($pembayaran->bulan_mulai) }} - {{ getNamaBulan($pembayaran->bulan_akhir) }} {{ $pembayaran->tahun }})
              @endif
            @endif
          </td>
        </tr>
        <tr>
          <td class="label">Metode Pembayaran</td>
          <td class="value">: {{ ucfirst($pembayaran->metode) }}</td>
        </tr>
        <tr>
          <td class="label">Tanggal Pembayaran</td>
          <td class="value">: {{ $pembayaran->tanggal_upload->format('d F Y') }}</td>
        </tr>
        <tr>
          <td class="label">Tanggal Verifikasi</td>
          <td class="value">: {{ $pembayaran->tanggal_proses->format('d F Y') }}</td>
        </tr>
      </table>

      <div class="jumlah-section">
        <div class="jumlah-label">JUMLAH PEMBAYARAN</div>
        <div class="jumlah-nominal">Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}</div>
      </div>

      <div class="terbilang">
        <strong>Terbilang:</strong> # {{ terbilang_sederhana($pembayaran->jumlah) }} Rupiah #
      </div>
    </div>

    <!-- === TANDA TANGAN & BARCODE === -->
    <div class="ttd-barcode-container">
      <!-- BARCODE SECTION -->
      <div class="barcode-section">
        <p style="margin-bottom: 12px; font-weight: bold; font-size: 14px;">Kode Verifikasi Dokumen:</p>
        <div class="barcode-container">
          <div class="barcode">*AR{{ sprintf('%06d', $pembayaran->id) }}{{ now()->format('md') }}*</div>
          <div class="barcode-info">
            <div class="barcode-info-item">ID Transaksi: {{ $pembayaran->id }}</div>
            <div class="barcode-info-item">Tanggal: {{ $pembayaran->tanggal_proses->format('d/m/Y') }}</div>
            <div class="barcode-info-item">Status: TERVERIFIKASI</div>
          </div>
        </div>
      </div>

      <!-- TANDA TANGAN SECTION -->
      <div class="ttd-section">
        <p style="margin-bottom: 5px; text-align: center;">Bekasi, {{ $pembayaran->tanggal_proses->format('d F Y') }}</p>
        <p style="margin-bottom: 25px; font-weight: bold; text-align: center;">Mengetahui,</p>

        <div class="ttd-wrapper">
          <!-- MUDIR - KIRI -->
          <div class="ttd-box">
            <img src="https://res.cloudinary.com/dunynusuh/image/upload/v1761883622/barcode_ttg_admin_krwhcb.png"
                 alt="Tanda Tangan Mudir" class="ttd-image">
            <div class="ttd-line"></div>
            <div class="nama-ttd">Ust Ziyad Khairi Al-Hafiedz S.E</div>
            <div class="jabatan">Mudir Pesantren</div>
          </div>

          <!-- ADMINISTRATOR - KANAN -->
          <div class="ttd-box">
            <img src="https://res.cloudinary.com/dunynusuh/image/upload/v1761883622/barcode_ttg_admin_krwhcb.png"
                 alt="Tanda Tangan Administrator" class="ttd-image">
            <div class="ttd-line"></div>
            <div class="nama-ttd">{{ $pembayaran->admin->nama ?? 'Administrasi' }}</div>
            <div class="jabatan">Administrator</div>
          </div>
        </div>
      </div>
    </div>

    <!-- === FOOTER === -->
    <div class="footer">
      Kuitansi ini merupakan bukti pembayaran yang sah dan telah diverifikasi sistem.<br>
      Dicetak otomatis pada: {{ $tanggal_sekarang }} pukul {{ $jam_sekarang }}
    </div>
  </div>
</body>
</html>
