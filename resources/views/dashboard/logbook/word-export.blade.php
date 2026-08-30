<html xmlns:o="urn:schemas-microsoft-com:office:office" 
      xmlns:w="urn:schemas-microsoft-com:office:word" 
      xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta charset="utf-8">
    <title>Laporan Logbook Magang Mahasiswa</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            color: #2d3748;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #1a202c;
            padding-bottom: 8px;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            font-size: 14pt;
            text-transform: uppercase;
        }
        .header h3 {
            margin: 3px 0 0 0;
            font-size: 12pt;
            color: #37A7AC;
            text-transform: uppercase;
        }
        .header p {
            margin: 2px 0 0 0;
            font-size: 9pt;
            color: #718096;
            font-style: italic;
        }
        .section-title {
            background-color: #37A7AC;
            color: #ffffff;
            padding: 6px 10px;
            font-size: 10pt;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        table.table-data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table.table-data th, table.table-data td {
            border: 1px solid #cbd5e0;
            padding: 6px 8px;
            vertical-align: top;
        }
        table.table-data th {
            background-color: #f7fafc;
            font-weight: bold;
        }
        .bg-gray {
            background-color: #f3f4f6;
        }
        .badge-susulan {
            color: #b91c1c;
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
            padding: 2px 4px;
            border: 1px solid #b91c1c;
            display: inline-block;
            margin-top: 4px;
        }
    </style>
</head>
<body>

    <!-- KOP HEADER -->
    <div class="header">
        <h2>FAKULTAS VOKASI UNIVERSITAS HASANUDDIN</h2>
        <h3>LAPORAN JURNAL LOGBOOK KEGIATAN MAGANG</h3>
        <p>Sistem Informasi Pengelolaan Magang Vokasi (SIGMAVO)</p>
    </div>

    <!-- 1. IDENTITAS MAHASISWA -->
    <div class="section-title">1. IDENTITAS MAHASISWA & PENEMPATAN</div>
    <table class="table-data">
        <tr>
            <th style="width: 28%; text-align: left;">Nama Mahasiswa</th>
            <td><strong>{{ $user->name }}</strong></td>
        </tr>
        <tr>
            <th style="text-align: left;">NIM</th>
            <td>{{ $user->mahasiswaProfile->nim ?? '-' }}</td>
        </tr>
        <tr>
            <th style="text-align: left;">Program Studi</th>
            <td>{{ $user->mahasiswaProfile->prodi->nama_prodi ?? '-' }}</td>
        </tr>
        <tr>
            <th style="text-align: left;">Instansi Penempatan</th>
            <td>{{ $pendaftaran->lowongan->perusahaan->nama_perusahaan ?? $pendaftaran->nama_instansi_mandiri ?? '-' }}</td>
        </tr>
        <tr>
            <th style="text-align: left;">Posisi Magang</th>
            <td>{{ $pendaftaran->lowongan->judul_posisi ?? 'Magang Mandiri' }}</td>
        </tr>
    </table>

    <!-- 2. CATATAN LOGBOOK KEGIATAN HARIAN -->
    <div class="section-title">2. RIWAYAT LOGBOOK KEGIATAN HARIAN</div>

    @foreach($logbooks as $index => $logbook)
    <table class="table-data" style="margin-bottom: 15px;">
        <tr class="bg-gray">
            <th colspan="2" style="text-align: left; padding: 6px 8px;">
                No. {{ $index + 1 }} | Tanggal: {{ \Carbon\Carbon::parse($logbook->tanggal)->isoFormat('dddd, D MMMM YYYY') }}
                @if($logbook->is_susulan)
                    <br><span class="badge-susulan">Terkirim via Jalur Susulan</span>
                @endif
            </th>
        </tr>
        <tr>
            <td style="width: 25%; font-weight: bold;">Uraian Kegiatan</td>
            <td>{!! nl2br(e($logbook->uraian_kegiatan ?? '-')) !!}</td>
        </tr>
        @if(!empty($logbook->mata_kuliah) && is_array($logbook->mata_kuliah))
        <tr>
            <td style="font-weight: bold;">CPMK Terkait</td>
            <td>{{ implode(', ', $logbook->mata_kuliah) }}</td>
        </tr>
        @endif
        @if(!empty($logbook->foto_base64))
        <tr>
            <td style="font-weight: bold;">Dokumentasi Foto</td>
            <td>
                <img src="{{ $logbook->foto_base64 }}" width="220" style="border: 1px solid #cbd5e0; margin-top: 4px;" alt="Foto Kegiatan">
            </td>
        </tr>
        @endif
        <tr>
            <td style="font-weight: bold;">Status Evaluasi</td>
            <td>
                {{ strtoupper(str_replace('_', ' ', $logbook->status_asistensi)) }}
                @if($logbook->catatan_dosen)
                    <br><span style="font-style: italic; color: #4a5568; font-size: 10pt;">Catatan: {{ $logbook->catatan_dosen }}</span>
                @endif
            </td>
        </tr>
    </table>
    @endforeach

</body>
</html>