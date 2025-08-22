<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan - <?= esc($pengiriman->id_pengiriman) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .company-info {
            font-size: 11px;
            color: #666;
            margin-bottom: 10px;
        }
        
        .document-title {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-top: 10px;
        }
        
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        
        .info-box {
            width: 48%;
            border: 1px solid #ddd;
            padding: 10px;
            background-color: #f9f9f9;
        }
        
        .info-box h4 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #2c3e50;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 5px;
        }
        
        .info-label {
            width: 40%;
            font-weight: bold;
        }
        
        .info-value {
            width: 60%;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .items-table th,
        .items-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        .items-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }
        
        .items-table .text-center {
            text-align: center;
        }
        
        .items-table .text-right {
            text-align: right;
        }
        
        .total-row {
            background-color: #e8f4f8;
            font-weight: bold;
        }
        
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }
        
        .signature-box {
            width: 30%;
            text-align: center;
            border: 1px solid #ddd;
            padding: 15px;
            min-height: 80px;
        }
        
        .signature-title {
            font-weight: bold;
            margin-bottom: 50px;
        }
        
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 10px;
            padding-top: 5px;
        }
        
        .qr-section {
            text-align: center;
            margin-top: 20px;
            border: 1px solid #ddd;
            padding: 15px;
            background-color: #f9f9f9;
        }
        
        .notes-section {
            margin-top: 20px;
            border: 1px solid #ddd;
            padding: 15px;
        }
        
        .notes-title {
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-transit { background-color: #d1ecf1; color: #0c5460; }
        .status-delivered { background-color: #d4edda; color: #155724; }
        .status-cancelled { background-color: #f8d7da; color: #721c24; }
        
        @media print {
            body { margin: 0; padding: 10px; }
            .no-print { display: none; }
        }
        
        @page {
            margin: 1cm;
        }
    </style>
</head>
<body>
    <!-- Print Button -->
    <div class="no-print" style="text-align: right; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
            <i class="fas fa-print"></i> Cetak
        </button>
    </div>

    <!-- Header -->
    <div class="header">
        <div class="company-name">PT. PUNINAR YUSEN LOGISTICS INDONESIA</div>
        <div class="company-info">
            Jl. Raya Logistics No. 123, Jakarta Selatan 12345<br>
            Telp: (021) 1234-5678 | Email: info@puninarlogistics.com
        </div>
        <div class="document-title">Surat Jalan</div>
    </div>

    <!-- Document Info -->
    <div class="info-section">
        <div class="info-box">
            <h4>Informasi Pengiriman</h4>
            <div class="info-row">
                <div class="info-label">No. Surat Jalan:</div>
                <div class="info-value"><?= esc($pengiriman->id_pengiriman) ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Tanggal:</div>
                <div class="info-value"><?= date('d F Y', strtotime($pengiriman->tanggal)) ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">No. PO:</div>
                <div class="info-value"><?= esc($pengiriman->no_po) ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">No. Kendaraan:</div>
                <div class="info-value"><?= esc($pengiriman->no_kendaraan) ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Status:</div>
                <div class="info-value">
                    <?php
                    $statusClass = '';
                    $statusText = '';
                    switch ($pengiriman->status) {
                        case 1:
                            $statusClass = 'status-pending';
                            $statusText = 'Pending';
                            break;
                        case 2:
                            $statusClass = 'status-transit';
                            $statusText = 'Dalam Perjalanan';
                            break;
                        case 3:
                            $statusClass = 'status-delivered';
                            $statusText = 'Terkirim';
                            break;
                        case 4:
                            $statusClass = 'status-cancelled';
                            $statusText = 'Dibatalkan';
                            break;
                    }
                    ?>
                    <span class="status-badge <?= $statusClass ?>"><?= $statusText ?></span>
                </div>
            </div>
        </div>

        <div class="info-box">
            <h4>Informasi Penerima</h4>
            <div class="info-row">
                <div class="info-label">Nama Pelanggan:</div>
                <div class="info-value"><?= esc($pengiriman->nama_pelanggan) ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Alamat:</div>
                <div class="info-value"><?= esc($pengiriman->alamat_pelanggan) ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Telepon:</div>
                <div class="info-value"><?= esc($pengiriman->telepon_pelanggan) ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Penerima:</div>
                <div class="info-value"><?= esc($pengiriman->penerima) ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Kurir:</div>
                <div class="info-value"><?= esc($pengiriman->nama_kurir) ?> (<?= esc($pengiriman->telepon_kurir) ?>)</div>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="30%">Nama Barang</th>
                <th width="15%">Kategori</th>
                <th width="8%">Jumlah</th>
                <th width="8%">Satuan</th>
                <th width="12%">Harga Satuan</th>
                <th width="12%">Total</th>
                <th width="10%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($detail_pengiriman)): ?>
                <?php 
                $totalNilai = 0;
                $totalItem = 0;
                foreach ($detail_pengiriman as $index => $detail): 
                    $subtotal = $detail->jumlah * $detail->harga;
                    $totalNilai += $subtotal;
                    $totalItem += $detail->jumlah;
                ?>
                <tr>
                    <td class="text-center"><?= $index + 1 ?></td>
                    <td><?= esc($detail->nama_barang) ?></td>
                    <td><?= esc($detail->nama_kategori) ?></td>
                    <td class="text-center"><?= number_format($detail->jumlah) ?></td>
                    <td class="text-center"><?= esc($detail->satuan) ?></td>
                    <td class="text-right">Rp <?= number_format($detail->harga, 0, ',', '.') ?></td>
                    <td class="text-right">Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
                    <td><?= !empty($detail->keterangan) ? esc($detail->keterangan) : '-' ?></td>
                </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td colspan="3" class="text-center"><strong>TOTAL</strong></td>
                    <td class="text-center"><strong><?= number_format($totalItem) ?></strong></td>
                    <td colspan="2" class="text-center"><strong>TOTAL NILAI:</strong></td>
                    <td class="text-right"><strong>Rp <?= number_format($totalNilai, 0, ',', '.') ?></strong></td>
                    <td></td>
                </tr>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center">Tidak ada detail barang</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Notes Section -->
    <?php if (!empty($pengiriman->keterangan)): ?>
    <div class="notes-section">
        <div class="notes-title">Keterangan:</div>
        <div><?= nl2br(esc($pengiriman->keterangan)) ?></div>
    </div>
    <?php endif; ?>

    <!-- QR Code Section -->
    <div class="qr-section">
        <div style="font-weight: bold; margin-bottom: 10px;">QR Code untuk Tracking Pengiriman</div>
        <div id="qrcode" style="display: inline-block;"></div>
        <div style="margin-top: 10px; font-size: 10px; color: #666;">
            Scan QR Code untuk melacak status pengiriman secara real-time<br>
            URL: <?= base_url('track/' . $pengiriman->id_pengiriman) ?>
        </div>
    </div>

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-title">Pengirim</div>
            <div class="signature-line">
                <div style="margin-top: 5px;">PT. Puninar Yusen Logistics Indonesia</div>
            </div>
        </div>

        <div class="signature-box">
            <div class="signature-title">Kurir</div>
            <div class="signature-line">
                <div style="margin-top: 5px;"><?= esc($pengiriman->nama_kurir) ?></div>
            </div>
        </div>

        <div class="signature-box">
            <div class="signature-title">Penerima</div>
            <div class="signature-line">
                <div style="margin-top: 5px;">( _________________ )</div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div style="text-align: center; margin-top: 30px; font-size: 10px; color: #666; border-top: 1px solid #ddd; padding-top: 10px;">
        <p>Dokumen ini dicetak secara otomatis pada <?= date('d F Y H:i:s') ?></p>
        <p>PT. Puninar Yusen Logistics Indonesia - Solusi Terpercaya untuk Kebutuhan Logistik Anda</p>
    </div>

    <!-- Include QR Code Library -->
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
    
    <script>
        // Generate QR Code
        document.addEventListener('DOMContentLoaded', function() {
            const trackingUrl = '<?= base_url('track/' . $pengiriman->id_pengiriman) ?>';
            
            QRCode.toCanvas(document.getElementById('qrcode'), trackingUrl, {
                width: 120,
                height: 120,
                colorDark: '#000000',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.M
            }, function (error) {
                if (error) console.error(error);
            });
        });

        // Auto print when page loads (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>