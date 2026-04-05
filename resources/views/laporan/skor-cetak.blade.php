<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembar Skor Kehadiran — {{ $pegawai->name }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            color: #000;
            background: #fff;
            padding: 20px;
        }

        /* Print setup */
        @page {
            size: A4 portrait;
            margin: 20mm 20mm 20mm 25mm;
        }
        @media print {
            body { padding: 0; }
            .no-print { display: none !important; }
            .page-break { page-break-before: always; }
        }

        /* Document header */
        .doc-header {
            text-align: center;
            margin-bottom: 18px;
        }
        .doc-title {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .doc-subtitle {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .doc-divider {
            border: none;
            border-top: 3px double #000;
            margin: 10px 0;
        }

        /* Metadata baris */
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            font-size: 11pt;
        }
        .meta-table td { padding: 2px 0; }
        .meta-table td:first-child { width: 180px; }
        .meta-table td:nth-child(2) { width: 12px; padding: 0 4px; }

        /* Identity tables */
        .identity-section {
            display: flex;
            gap: 12px;
            margin-bottom: 14px;
        }
        .identity-box {
            flex: 1;
            border: 1px solid #000;
        }
        .identity-box-title {
            background: #d9d9d9;
            text-align: center;
            font-weight: bold;
            font-size: 10pt;
            padding: 4px 8px;
            border-bottom: 1px solid #000;
            text-transform: uppercase;
        }
        .identity-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
        }
        .identity-table td {
            padding: 3px 8px;
            vertical-align: top;
            border-bottom: 1px solid #ddd;
        }
        .identity-table tr:last-child td { border-bottom: none; }
        .identity-table .lbl { width: 110px; }
        .identity-table .sep { width: 10px; }

        /* Scoring table */
        .score-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
            margin-bottom: 14px;
        }
        .score-table th, .score-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: middle;
        }
        .score-table thead th {
            background: #d9d9d9;
            text-align: center;
            font-weight: bold;
        }
        .score-table .center { text-align: center; }
        .score-table .kode { font-weight: bold; text-align: center; }
        .score-table .indikator {
            text-align: center;
            font-weight: bold;
            writing-mode: horizontal-tb;
            background: #f2f2f2;
        }
        .score-table tfoot td {
            background: #1e3a5f;
            color: #fff;
            font-weight: bold;
            text-align: center;
            font-size: 11pt;
        }
        .score-table .val-red { color: #c00; }

        /* Result box */
        .result-box {
            border: 2px solid #000;
            padding: 10px 16px;
            display: flex;
            align-items: center;
            gap: 24px;
            margin-bottom: 14px;
        }
        .result-score {
            text-align: center;
            min-width: 100px;
        }
        .result-score .big { font-size: 28pt; font-weight: bold; }
        .result-score .lbl { font-size: 10pt; }
        .result-predikat { font-size: 13pt; font-weight: bold; }

        /* Signature */
        .signature-section {
            margin-top: 20px;
            display: flex;
            justify-content: flex-end;
        }
        .signature-box { text-align: center; min-width: 200px; }
        .signature-place-date { margin-bottom: 60px; font-size: 11pt; }
        .signature-name { font-weight: bold; font-size: 11pt; border-bottom: 1px solid #000; padding-bottom: 2px; }
        .signature-nip { font-size: 10pt; margin-top: 2px; }

        /* Keterangan legend */
        .legend {
            font-size: 10pt;
            margin-top: 12px;
            border-top: 1px solid #ccc;
            padding-top: 8px;
        }
        .legend strong { display: block; margin-bottom: 4px; }

        /* Print button */
        .print-bar {
            background: #1e3a5f;
            color: #fff;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            border-radius: 8px;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }
        .print-bar a { color: #93c5fd; font-size: 13px; text-decoration: none; }
        .print-bar a:hover { text-decoration: underline; }
        .btn-print {
            background: #fff;
            color: #1e3a5f;
            border: none;
            padding: 8px 20px;
            font-size: 13px;
            font-weight: 700;
            border-radius: 6px;
            cursor: pointer;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }
    </style>
</head>
<body>

{{-- Action bar (tidak dicetak) --}}
<div class="print-bar no-print">
    <a href="{{ route('skor.index', ['user_id'=>$pegawai->id,'bulan'=>$bulan,'tahun'=>$tahun]) }}">
        ← Kembali ke Skor Kehadiran
    </a>
    <div style="font-size:14px;font-weight:700">
        Cetak: {{ $pegawai->name }} —
        {{ \Carbon\Carbon::create($tahun,$bulan)->locale('id')->isoFormat('MMMM YYYY') }}
    </div>
    <button class="btn-print" onclick="window.print()">🖨 Cetak / Simpan PDF</button>
</div>

{{-- ===== DOKUMEN ===== --}}

<div class="doc-header">
    <div class="doc-title">Lembar Perhitungan Skor Kehadiran Pegawai</div>
    <div class="doc-subtitle">Dinas Kesehatan Kota Baubau</div>
</div>
<hr class="doc-divider">

{{-- Periode & SKPD --}}
<table class="meta-table">
    <tr>
        <td>Periode Penilaian</td>
        <td>:</td>
        <td><strong>{{ \Carbon\Carbon::create($tahun,$bulan)->locale('id')->isoFormat('MMMM YYYY') }}</strong></td>
    </tr>
    <tr>
        <td>SKPD</td>
        <td>:</td>
        <td>RSUD Kota Baubau / Dinas Kesehatan</td>
    </tr>
</table>

{{-- Identitas --}}
<div class="identity-section">
    {{-- Pejabat Penilai --}}
    <div class="identity-box">
        <div class="identity-box-title">Pejabat Penilai</div>
        <table class="identity-table">
            <tr>
                <td class="lbl">Nama</td>
                <td class="sep">:</td>
                <td>{{ $pejabatPenilai->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="lbl">NIP</td>
                <td class="sep">:</td>
                <td>{{ $pejabatPenilai->nip ?? '-' }}</td>
            </tr>
            <tr>
                <td class="lbl">Pangkat/Gol</td>
                <td class="sep">:</td>
                <td>{{ $pejabatPenilai->pangkat_gol ?? '-' }}</td>
            </tr>
            <tr>
                <td class="lbl">Jabatan</td>
                <td class="sep">:</td>
                <td>{{ $pejabatPenilai->jabatan ?? '-' }}</td>
            </tr>
            <tr>
                <td class="lbl">Unit Kerja</td>
                <td class="sep">:</td>
                <td>{{ $pejabatPenilai->unit ?? '-' }}</td>
            </tr>
        </table>
    </div>

    {{-- PNS Yang Dinilai --}}
    <div class="identity-box">
        <div class="identity-box-title">PNS / ASN Yang Dinilai</div>
        <table class="identity-table">
            <tr>
                <td class="lbl">Nama</td>
                <td class="sep">:</td>
                <td>{{ $pegawai->name }}</td>
            </tr>
            <tr>
                <td class="lbl">NIP</td>
                <td class="sep">:</td>
                <td>{{ $pegawai->nip ?? '-' }}</td>
            </tr>
            <tr>
                <td class="lbl">Pangkat/Gol</td>
                <td class="sep">:</td>
                <td>{{ $pegawai->pangkat_gol ?? '-' }}</td>
            </tr>
            <tr>
                <td class="lbl">Jabatan</td>
                <td class="sep">:</td>
                <td>{{ $pegawai->jabatan ?? '-' }}</td>
            </tr>
            <tr>
                <td class="lbl">Unit Kerja</td>
                <td class="sep">:</td>
                <td>{{ $pegawai->unit ?? '-' }}</td>
            </tr>
        </table>
    </div>
</div>

{{-- Rekap kehadiran --}}
<table class="meta-table" style="margin-bottom:14px">
    <tr>
        <td>Total Hadir</td>
        <td>:</td>
        <td><strong>{{ $skor['total_hadir'] }} hari</strong></td>
        <td style="width:40px"></td>
        <td>Total Alpha</td>
        <td>:</td>
        <td><strong>{{ $skor['total_alpha'] }} hari</strong></td>
        <td style="width:40px"></td>
        <td>Total Izin/Sakit</td>
        <td>:</td>
        <td><strong>{{ $skor['total_izin'] }} hari</strong></td>
    </tr>
</table>

{{-- Tabel Perhitungan --}}
<table class="score-table">
    <thead>
        <tr>
            <th rowspan="2" style="width:28px">No</th>
            <th rowspan="2" style="width:90px">Indikator</th>
            <th rowspan="2" style="width:42px">Kode</th>
            <th rowspan="2">Kriteria</th>
            <th rowspan="2" style="width:48px">%<br>Potongan</th>
            <th colspan="2">Hasil Pengukuran</th>
        </tr>
        <tr>
            <th style="width:55px">Kali (TL)</th>
            <th style="width:65px">Jumlah<br>Potongan</th>
        </tr>
        <tr>
            <th colspan="5" style="background:#1e3a5f;color:#fff;text-align:right;padding-right:8px">Skor Awal</th>
            <th style="background:#1e3a5f;color:#fff">TL</th>
            <th style="background:#1e3a5f;color:#fff">100,00</th>
        </tr>
    </thead>
    <tbody>
        {{-- KT1 --}}
        <tr>
            <td class="center">1</td>
            <td class="indikator" rowspan="7">Ketidakhadiran</td>
            <td class="kode">KT1</td>
            <td>{{ $skor['detail']['KT1']['label'] }}</td>
            <td class="center">{{ $skor['detail']['KT1']['persen'] }}</td>
            <td class="center">{{ $skor['detail']['KT1']['kali'] }}</td>
            <td class="center {{ $skor['detail']['KT1']['jumlah'] > 0 ? 'val-red' : '' }}">
                {{ $skor['detail']['KT1']['jumlah'] > 0 ? number_format($skor['detail']['KT1']['jumlah'],2) : '-' }}
            </td>
        </tr>
        @foreach(['KT2','KT3','KT4','KT5','KT6','KT7'] as $kode)
        <tr>
            <td class="center"></td>
            <td class="kode">{{ $kode }}</td>
            <td>{{ $skor['detail'][$kode]['label'] }}</td>
            <td class="center">{{ $skor['detail'][$kode]['persen'] }}</td>
            <td class="center">{{ $skor['detail'][$kode]['kali'] }}</td>
            <td class="center {{ $skor['detail'][$kode]['jumlah'] > 0 ? 'val-red' : '' }}">
                {{ $skor['detail'][$kode]['jumlah'] > 0 ? number_format($skor['detail'][$kode]['jumlah'],2) : '-' }}
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5" style="text-align:right;padding-right:8px;font-size:11pt">
                Total Potongan
            </td>
            <td class="center" style="color:#fbbf24">—</td>
            <td class="center" style="color:#fbbf24;font-size:12pt">
                {{ number_format($skor['total_potongan'],2) }}
            </td>
        </tr>
        <tr>
            <td colspan="5" style="text-align:right;padding-right:8px;font-size:12pt;background:#0f2744">
                SKOR KEHADIRAN AKHIR
            </td>
            <td class="center" style="background:#0f2744;color:#fde68a">
                @php
                    $s = $skor['skor_akhir'];
                    $label = $s >= 90 ? 'Sangat Baik' : ($s >= 75 ? 'Baik' : ($s >= 60 ? 'Cukup' : 'Kurang'));
                @endphp
                {{ $label }}
            </td>
            <td class="center" style="background:#0f2744;color:#fde68a;font-size:16pt">
                {{ number_format($s,2) }}
            </td>
        </tr>
    </tfoot>
</table>

{{-- Keterangan Predikat --}}
<div class="legend">
    <strong>Keterangan Predikat Penilaian:</strong>
    ≥ 90 = <strong>Sangat Baik</strong> &nbsp;|&nbsp;
    75 s/d 89 = <strong>Baik</strong> &nbsp;|&nbsp;
    60 s/d 74 = <strong>Cukup</strong> &nbsp;|&nbsp;
    &lt; 60 = <strong>Kurang</strong>
</div>

{{-- Tanda Tangan --}}
<div class="signature-section">
    <div class="signature-box">
        <div class="signature-place-date">
            Baubau, {{ \Carbon\Carbon::create($tahun,$bulan)->endOfMonth()->locale('id')->isoFormat('D MMMM YYYY') }}
        </div>
        <div style="margin-bottom:4px;font-size:11pt">Pejabat Penilai,</div>
        <br><br><br>
        <div class="signature-name">{{ $pejabatPenilai->name ?? '____________________' }}</div>
        <div class="signature-nip">NIP. {{ $pejabatPenilai->nip ?? '____________________' }}</div>
    </div>
</div>

</body>
</html>
