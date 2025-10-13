<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Notifikasi Pembayaran Baru</title>
    <style>
        body { font-family: 'Poppins', sans-serif; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #1E8449, #145A32); color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f8fafc; padding: 20px; border-radius: 0 0 10px 10px; }
        .info-box { background: white; padding: 15px; border-radius: 8px; margin: 10px 0; border-left: 4px solid #1E8449; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Notifikasi Pembayaran Baru</h2>
        </div>
        <div class="content">
            <p>Halo Admin,</p>
            <p>Murid <strong>{{ $pembayaran->user->nama }}</strong> telah mengupload bukti pembayaran baru.</p>
            
            <div class="info-box">
                <h4>Detail Pembayaran:</h4>
                <p><strong>Tagihan:</strong> {{ $pembayaran->tagihan->keterangan }}</p>
                <p><strong>Jumlah:</strong> Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}</p>
                <p><strong>Metode:</strong> {{ $pembayaran->metode }}</p>
                <p><strong>Tanggal Upload:</strong> {{ $pembayaran->tanggal_upload->format('d/m/Y H:i') }}</p>
            </div>
            
            <p>Silakan login ke dashboard admin untuk memverifikasi pembayaran ini.</p>
            <a href="{{ url('/admin/pembayaran') }}" style="background: #1E8449; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Verifikasi Sekarang</a>
        </div>
    </div>
</body>
</html>