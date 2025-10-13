<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Status Pembayaran</title>
    <style>
        body { font-family: 'Poppins', sans-serif; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #1E8449, #145A32); color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f8fafc; padding: 20px; border-radius: 0 0 10px 10px; }
        .status-accepted { color: #27AE60; }
        .status-rejected { color: #e74c3c; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Status Pembayaran</h2>
        </div>
        <div class="content">
            <p>Halo <strong>{{ $pembayaran->user->nama }}</strong>,</p>
            
            <p>Pembayaran Anda untuk tagihan <strong>{{ $pembayaran->tagihan->keterangan }}</strong> telah 
            <span class="status-{{ $pembayaran->status }}"><strong>{{ $status }}</strong></span>.</p>
            
            <p><strong>Detail:</strong></p>
            <ul>
                <li>Jumlah: Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}</li>
                <li>Metode: {{ $pembayaran->metode }}</li>
                <li>Tanggal Proses: {{ $pembayaran->tanggal_proses->format('d/m/Y H:i') }}</li>
                <li>Admin: {{ $pembayaran->admin->nama ?? 'System' }}</li>
            </ul>
            
            @if($pembayaran->status == 'rejected')
            <p style="color: #e74c3c;">Silakan upload ulang bukti pembayaran yang valid.</p>
            @endif
            
            <p>Terima kasih.</p>
        </div>
    </div>
</body>
</html>