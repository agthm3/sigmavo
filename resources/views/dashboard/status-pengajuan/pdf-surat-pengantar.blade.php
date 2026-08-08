<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Pengantar Magang Mahasiswa</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
            margin: 0;
            padding: 20px 30px;
        }

        .header-kop {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header-kop h3 {
            margin: 0;
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header-kop h2 {
            margin: 2px 0;
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header-kop p {
            margin: 0;
            font-size: 10pt;
            font-style: italic;
        }

        .surat-meta {
            width: 100%;
            margin-bottom: 20px;
        }

        .surat-meta td {
            vertical-align: top;
        }

        .table-mhs {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .table-mhs th, .table-mhs td {
            border: 1px solid #000;
            padding: 6px 10px;
            text-align: left;
            font-size: 11pt;
        }

        .table-mhs th {
            background-color: #f2f2f2;
        }

        .ttd-box {
            float: right;
            width: 250px;
            text-align: center;
            margin-top: 30px;
        }

        .clear {
            clear: both;
        }
    </style>
</head>
<body>

    <!-- KOP SURAT FAKULTAS -->
    <div class="header-kop">
        <h3>KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</h3>
        <h2>UNIVERSITAS HASANUDDIN</h2>
        <h2>FAKULTAS VOKASI</h2>
        <p>Jalan Perintis Kemerdekaan Km. 10 Makassar 90245 | Website: vokasi.unhas.ac.id</p>
    </div>

    <!-- NOMOR SURAT & PERIHAL -->
    <table class="surat-meta">
        <tr>
            <td style="width: 15%;">Nomor</td>
            <td style="width: 50%;">: {{ $pendaftaran->nomor_surat ?? '---/UN4.15/TU.02/2026' }}</td>
            <td style="text-align: right;">Makassar, {{ \Carbon\Carbon::parse($pendaftaran->updated_at)->isoFormat('D MMMM YYYY') }}</td>
        </tr>
        <tr>
            <td>Hal</td>
            <td colspan="2">: <strong>{{ $pendaftaran->perihal_surat ?? 'Permohonan Pelaksanaan Magang Industri' }}</strong></td>
        </tr>
    </table>

    <!-- TUJUAN SURAT -->
    <p>
        Kepada Yth.<br>
        <strong>Pimpinan / HRD {{ $pendaftaran->jalur_magang === 'mandiri' ? $pendaftaran->nama_instansi_mandiri : ($pendaftaran->lowongan->perusahaan->nama_perusahaan ?? 'Perusahaan Tujuan') }}</strong><br>
        Di Tempat
    </p>

    <!-- ISI SURAT -->
    <p style="text-align: justify; text-indent: 30px;">
        Dengan hormat, dalam rangka meningkatkan kompetensi kualifikasi lulusan Pendidikan Vokasi yang adaptif dengan dunia industri, bersama ini kami memohon kesediaan Bapak/Ibu untuk dapat menerima mahasiswa Fakultas Vokasi Universitas Hasanuddin dalam melaksanakan program <strong>Magang Industri / Praktik Kerja Lapangan (PKL)</strong>.
    </p>

    <p style="margin-bottom: 5px;">Adapun data mahasiswa pemohon adalah sebagai berikut:</p>

    <!-- TABEL BIODATA MAHASISWA -->
    <table class="table-mhs">
        <thead>
            <tr>
                <th style="width: 8%; text-align: center;">No</th>
                <th style="width: 35%;">Nama Mahasiswa</th>
                <th style="width: 22%;">NIM</th>
                <th style="width: 35%;">Program Studi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: center;">1</td>
                <td><strong>{{ $user->name }}</strong></td>
                <td>{{ $user->mahasiswaProfile->nim ?? '-' }}</td>
                <td>{{ $user->mahasiswaProfile->prodi->nama_prodi ?? '-' }}</td>
            </tr>
        </tbody>
    </table>

    <p style="text-align: justify; text-indent: 30px;">
        Pelaksanaan magang ini direncanakan berlangsung mulai tanggal 
        <strong>{{ \Carbon\Carbon::parse($pendaftaran->tgl_mulai_magang ?? now())->isoFormat('D MMMM YYYY') }}</strong> s.d. 
        <strong>{{ \Carbon\Carbon::parse($pendaftaran->tgl_selesai_magang ?? now()->addMonths(6))->isoFormat('D MMMM YYYY') }}</strong>.
    </p>

    <p style="text-align: justify; text-indent: 30px;">
        Demikian permohonan ini kami sampaikan. Atas perhatian, bantuan, dan kerja sama Bapak/Ibu, kami ucapkan terima kasih.
    </p>

    <!-- TANDA TANGAN -->
    <div class="ttd-box">
        <p>An. Dekan,<br>Wakil Dekan Bidang Akademik & Kemahasiswaan</p>
        <br><br><br><br>
        <p><strong><u>test nama dekan atau wakil dekan disini</u></strong><br>NIP. 198102152008011009</p>
    </div>

    <div class="clear"></div>

</body>
</html>