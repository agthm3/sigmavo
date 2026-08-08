<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kompilasi Berkas Akhir Magang</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #2d3748;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            border-bottom: 3px double #1a202c;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
            color: #1a202c;
            text-transform: uppercase;
        }

        .header h3 {
            margin: 3px 0 0 0;
            font-size: 13px;
            font-weight: bold;
            color: #37A7AC;
            text-transform: uppercase;
        }

        .header p {
            margin: 3px 0 0 0;
            font-size: 10px;
            color: #718096;
        }

        .section-title {
            background-color: #37A7AC;
            color: #ffffff;
            padding: 6px 10px;
            font-size: 11px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
            border-radius: 3px;
            text-transform: uppercase;
        }

        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .table-data th, .table-data td {
            border: 1px solid #cbd5e0;
            padding: 6px 8px;
            vertical-align: top;
        }

        .table-data th {
            background-color: #f7fafc;
            color: #2d3748;
            font-weight: bold;
        }

        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }

        .nilai-box {
            border: 2px solid #37A7AC;
            background-color: #f0fdfa;
            padding: 10px;
            width: 210px;
            text-align: center;
            float: right;
            margin-bottom: 15px;
            border-radius: 6px;
        }

        .nilai-box .score {
            font-size: 24px;
            font-weight: bold;
            color: #37A7AC;
            margin: 4px 0;
        }

        .nilai-box .grade {
            font-size: 11px;
            font-weight: bold;
            color: #2d3748;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        .logbook-item {
            border: 1px solid #e2e8f0;
            background-color: #ffffff;
            padding: 10px;
            margin-bottom: 12px;
            border-radius: 5px;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

    @foreach($pendaftarans as $index => $pendaftaran)
    
        <!-- KOP SURAT HEADER -->
        <div class="header">
            <h2>FAKULTAS VOKASI UNIVERSITAS HASANUDDIN</h2>
            <h3>BERKAS EVALUASI & LOGBOOK KEGIATAN MAGANG</h3>
            <p>Sistem Informasi Pengelolaan Magang Vokasi (SIGMAVO)</p>
        </div>

        <!-- 1. DATA DIRI MAHASISWA -->
        <div class="section-title">1. IDENTITAS MAHASISWA & PENEMPATAN</div>
        <table class="table-data">
            <tr>
                <th style="width: 25%;">Nama Mahasiswa</th>
                <td><strong>{{ $pendaftaran->user->name }}</strong></td>
            </tr>
            <tr>
                <th>NIM</th>
                <td>{{ $pendaftaran->user->mahasiswaProfile->nim ?? '-' }}</td>
            </tr>
            <tr>
                <th>Program Studi</th>
                <td>{{ $pendaftaran->user->mahasiswaProfile->prodi->nama_prodi ?? '-' }}</td>
            </tr>
            <tr>
                <th>Instansi Penempatan</th>
                <td>{{ $pendaftaran->lowongan->perusahaan->nama_perusahaan ?? $pendaftaran->nama_instansi_mandiri ?? '-' }}</td>
            </tr>
            <tr>
                <th>Posisi Magang</th>
                <td>{{ $pendaftaran->lowongan->judul_posisi ?? 'Program Magang Mandiri' }}</td>
            </tr>
        </table>

        <!-- 2. REKAPITULASI PENILAIAN RUBRIK -->
        <div class="section-title">2. REKAPITULASI PENILAIAN AKADEMIK DOSEN</div>
        
        @php
            $penilaianDosen = $pendaftaran->penilaians->where('tipe_penilai', 'dosen')->first();
            $isDinilai = $penilaianDosen !== null;
            $nilaiAkhir = $isDinilai ? $penilaianDosen->nilai_akhir : 0;
            
            $hurufMutu = '-';
            if($isDinilai) {
                if($nilaiAkhir >= 85) $hurufMutu = 'A';
                elseif($nilaiAkhir >= 80) $hurufMutu = 'A-';
                elseif($nilaiAkhir >= 75) $hurufMutu = 'B+';
                elseif($nilaiAkhir >= 70) $hurufMutu = 'B';
                elseif($nilaiAkhir >= 65) $hurufMutu = 'B-';
                elseif($nilaiAkhir >= 60) $hurufMutu = 'C+';
                elseif($nilaiAkhir >= 50) $hurufMutu = 'C';
                elseif($nilaiAkhir >= 40) $hurufMutu = 'D';
                else $hurufMutu = 'E';
            }
        @endphp

        <div class="clearfix">
            <div class="nilai-box">
                <span style="font-size: 9px; color: #718096; text-transform: uppercase; font-weight: bold;">Nilai Akhir Akumulasi</span>
                <div class="score">{{ number_format($nilaiAkhir, 2) }}</div>
                <div class="grade">HURUF MUTU: <strong>{{ $hurufMutu }}</strong></div>
            </div>
        </div>

        @if($isDinilai && $penilaianDosen->details->count() > 0)
            <table class="table-data">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 6%;">No</th>
                        <th>Komponen Penilaian</th>
                        <th class="text-center" style="width: 15%;">Bobot (%)</th>
                        <th class="text-center" style="width: 15%;">Nilai Mentah</th>
                        <th class="text-center" style="width: 20%;">Nilai Tertimbang</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($penilaianDosen->details as $idx => $detail)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td>{{ $detail->rubrik->komponen ?? 'Komponen' }}</td>
                        <td class="text-center">{{ floatval($detail->rubrik->bobot ?? 0) }}%</td>
                        <td class="text-center">{{ floatval($detail->nilai_mentah) }}</td>
                        <td class="text-center font-bold">{{ number_format($detail->nilai_mentah * (($detail->rubrik->bobot ?? 0)/100), 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="color: #e53e3e; font-style: italic; margin-bottom: 15px;">
                * Belum ada rekam nilai evaluasi rubrik dari Dosen Pembimbing.
            </p>
        @endif

        <!-- 3. RIWAYAT LOGBOOK HARIAN -->
        <div class="section-title">3. CATATAN LOGBOOK KEGIATAN HARIAN</div>
        
        @if($pendaftaran->user->logbooks && $pendaftaran->user->logbooks->count() > 0)
            <p style="margin-bottom: 10px;">Total Jurnal Terdata: <strong>{{ $pendaftaran->user->logbooks->count() }} Laporan Kegiatan</strong></p>
            
            @foreach($pendaftaran->user->logbooks as $logbook)
            <div class="logbook-item">
                <table style="width: 100%; border: none; font-size: 10px;">
                    <tr>
                        <td style="width: 18%; font-weight: bold; border: none;">Tanggal Kegiatan</td>
                        <td style="border: none;">: {{ \Carbon\Carbon::parse($logbook->tanggal)->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; border: none; vertical-align: top;">Uraian Kegiatan</td>
                        <td style="border: none;">: {{ $logbook->uraian_kegiatan ?? '-' }}</td>
                    </tr>
                    @if(!empty($logbook->foto_base64))
                    <tr>
                        <td style="font-weight: bold; border: none; vertical-align: top;">Dokumentasi Foto</td>
                        <td style="border: none;">
                            <img src="{{ $logbook->foto_base64 }}" width="180" style="border: 1px solid #cbd5e0; border-radius: 4px; display: block; margin-top: 5px;" alt="Dokumentasi Foto Logbook">
                        </td>
                    </tr>
                    @endif
                </table>
            </div>
            @endforeach
        @else
            <p style="font-style: italic; color: #718096;">Belum ada catatan logbook harian yang diisi oleh mahasiswa.</p>
        @endif

        @if(!$loop->last)
            <div class="page-break"></div>
        @endif

    @endforeach

</body>
</html>