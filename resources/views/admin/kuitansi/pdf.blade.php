<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kuitansi Pembayaran</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .content { margin: 20px 0; }
        .table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .table td { padding: 8px; border: 1px solid #ddd; }
        .table .label { font-weight: bold; width: 30%; }
        .footer { margin-top: 50px; text-align: right; }
        .ttd-section { margin-top: 100px; text-align: center; }
        .qr-code { width: 100px; height: 100px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>KUITANSI PEMBAYARAN</h2>
        <h3>SEKOLAH ISLAM TERPADU</h3>
    </div>

    <div class="content">
        <table class="table">
            <tr>
                <td class="label">No. Kuitansi</td>
                <td>{{ $pembayaran->id }}/KUITANSI/{{ date('Y') }}</td>
            </tr>
            <tr>
                <td class="label">Nama Siswa</td>
                <td>{{ $pembayaran->user->nama }}</td>
            </tr>
            <tr>
                <td class="label">Keterangan</td>
                <td>{{ $pembayaran->keterangan }}</td>
            </tr>
            <tr>
                <td class="label">Jumlah</td>
                <td>Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">Metode Bayar</td>
                <td>{{ $pembayaran->metode }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal Bayar</td>
                <td>{{ $pembayaran->tanggal_proses->format('d/m/Y') }}</td>
            </tr>
        </table>
    </div>

    <div class="ttd-section">
        <p>Bendahara</p>
        <br><br><br>
        <p><strong>{{ auth()->user()->nama }}</strong></p>
        <img src="data:image/png;base64,{{ $qrCode }}" alt="TTD Digital" class="qr-code">
        <p>TTD Digital</p>
    </div>
</body>
</html>