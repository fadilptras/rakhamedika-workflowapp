<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="UTF-8">
    <title>Data Pribadi Karyawan - {{ $user->name }}</title>
    <style>
        @page { margin: 20mm 15mm; }
        body {
            font-family: 'DejaVu Sans', 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
        }
        .container { width: 100%; }

        .header {
            text-align: center;
            margin-bottom: 20px;
            /* TAMBAHKAN border biru */
            border-bottom: 2px solid #2563EB; /* Biru Tailwind 600 */
            padding-bottom: 8px;
        }
        .header h1 {
            font-size: 16px;
            /* WARNA BIRU DIKEMBALIKAN */
            color: #2563EB; /* Biru Tailwind 600 */
            margin: 0;
            text-transform: uppercase;
        }
        .header p { font-size: 11px; color: #555; margin: 4px 0 0 0; }

        h2 {
            font-size: 12px;
            /* WARNA BIRU DIKEMBALIKAN */
            color: #1E40AF; /* Biru Tailwind 800 (lebih gelap) */
            margin-top: 15px;
            margin-bottom: 8px;
            padding-bottom: 3px;
            /* Border biru */
            border-bottom: 1px solid #93C5FD; /* Biru Tailwind 300 (lebih terang) */
            font-weight: bold;
            text-transform: uppercase;
        }

        .profile-picture { text-align: center; margin-bottom: 15px; }
        .profile-picture img {
            max-width: 90px;
            max-height: 90px;
            border-radius: 50%;
            border: 2px solid #BFDBFE; /* Border biru muda */
            background-color: #eee;
        }

        /* Tabel untuk Data Utama */
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .data-table td {
            border: 1px solid #ddd;
            padding: 5px 8px;
            vertical-align: top;
        }
        .data-table .label {
            font-weight: bold;
            width: 30%;
            /* Latar belakang biru muda */
            background-color: #EFF6FF; /* Biru Tailwind 50 */
            color: #1E3A8A; /* Biru Tailwind 900 */
        }
        .data-table .value { width: 70%; word-wrap: break-word; }

        /* Tabel untuk Riwayat */
        .history-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 9px; }
        .history-table th, .history-table td {
            border: 1px solid #ccc;
            padding: 4px 6px;
            text-align: left;
            vertical-align: top;
        }
        .history-table th {
            /* Header tabel biru */
            background-color: #DBEAFE; /* Biru Tailwind 200 */
            font-weight: bold;
            color: #1E3A8A; /* Biru Tailwind 900 */
        }

        .no-data { font-style: italic; color: #888; margin-bottom: 15px; font-size: 10px; }

        .footer {
            text-align: right;
            font-size: 8px;
            color: #999;
            margin-top: 25px;
            border-top: 1px solid #eee;
            padding-top: 5px;
            width: 100%;
            position: fixed;
            bottom: 10mm;
            left: 0mm;
            right: 15mm;
        }
    </style>
</head>
<body>
    <div class="container">

        <div class="header">
            {{-- <img src="{{ public_path('images/logo_perusahaan.png') }}" alt="Logo"> --}}
            <h1>Data Pribadi Karyawan</h1>
            <p>PT Rakha Nusantara Medika</p>
        </div>

        <div class="profile-picture">
             @php
                 $imgPath = $user->profile_picture ? storage_path('app/public/' . $user->profile_picture) : public_path('images/default-avatar.png');
                 if (!file_exists($imgPath)) { $imgPath = public_path('images/default-avatar.png'); }
             @endphp
             <img src="{{ $imgPath }}" alt="Foto">
        </div>

        <h2>Informasi Akun & Kontak</h2>
        <table class="data-table">
            <tr><td class="label">Nama Lengkap</td><td class="value">{{ $user->name ?? '-' }}</td></tr>
            <tr><td class="label">Email</td><td class="value">{{ $user->email ?? '-' }}</td></tr>
            <tr><td class="label">Nomor Telepon</td><td class="value">{{ $user->nomor_telepon ?? '-' }}</td></tr>
        </table>

        <h2>Data Pribadi</h2>
        <table class="data-table">
            <tr><td class="label">NIK</td><td class="value">{{ $user->nik ?? '-' }}</td></tr>
            <tr><td class="label">Tempat Lahir</td><td class="value">{{ $user->tempat_lahir ?? '-' }}</td></tr>
            <tr><td class="label">Tanggal Lahir</td><td class="value">{{ $user->tanggal_lahir ? $user->tanggal_lahir->translatedFormat('d F Y') : '-' }}</td></tr>
            <tr><td class="label">Jenis Kelamin</td><td class="value">{{ $user->jenis_kelamin ?? '-' }}</td></tr>
            <tr><td class="label">Agama</td><td class="value">{{ $user->agama ?? '-' }}</td></tr>
            <tr><td class="label">Golongan Darah</td><td class="value">{{ $user->golongan_darah ?? '-' }}</td></tr>
            <tr><td class="label">Status Pernikahan</td><td class="value">{{ $user->status_pernikahan ?? '-' }}</td></tr>
            <tr><td class="label">Alamat KTP</td><td class="value">{{ $user->alamat_ktp ?? '-' }}</td></tr>
            <tr><td class="label">Alamat Domisili</td><td class="value">{{ $user->alamat_domisili ?: ($user->alamat_ktp ?: '-') }}</td></tr>
        </table>

        <h2>Informasi Ketenagakerjaan</h2>
        <table class="data-table">
            <tr><td class="label">NIP</td><td class="value">{{ $user->nip ?? '-' }}</td></tr>
            <tr><td class="label">Status Karyawan</td><td class="value">{{ $user->status_karyawan ?? '-' }}</td></tr>
            <tr><td class="label">Jabatan</td><td class="value">{{ $user->jabatan ?? '-' }}</td></tr>
            <tr><td class="label">Divisi</td><td class="value">{{ $user->divisi ?? '-' }}</td></tr>
            <tr><td class="label">Lokasi Kerja</td><td class="value">{{ $user->lokasi_kerja ?? '-' }}</td></tr>
            <tr><td class="label">Tanggal Bergabung</td><td class="value">{{ $user->tanggal_bergabung ? $user->tanggal_bergabung->translatedFormat('d F Y') : '-' }}</td></tr>
            @if($user->status_karyawan == 'Kontrak')
                <tr><td class="label">Mulai Kontrak</td><td class="value">{{ $user->tanggal_mulai_kontrak ? $user->tanggal_mulai_kontrak->translatedFormat('d F Y') : '-' }}</td></tr>
                <tr><td class="label">Akhir Kontrak</td><td class="value">{{ $user->tanggal_akhir_kontrak ? $user->tanggal_akhir_kontrak->translatedFormat('d F Y') : '-' }}</td></tr>
            @endif
             <tr><td class="label">Tanggal Berhenti</td><td class="value">{{ $user->tanggal_berhenti ? $user->tanggal_berhenti->translatedFormat('d F Y') : 'Aktif' }}</td></tr>
        </table>

        <h2>Informasi Administrasi</h2>
        <table class="data-table">
            <tr><td class="label">NPWP</td><td class="value">{{ $user->npwp ?? '-' }}</td></tr>
            <tr><td class="label">Status PTKP</td><td class="value">{{ $user->ptkp ?? '-' }}</td></tr>
            <tr><td class="label">No. BPJS Kesehatan</td><td class="value">{{ $user->bpjs_kesehatan ?? '-' }}</td></tr>
            <tr><td class="label">No. BPJS Ketenagakerjaan</td><td class="value">{{ $user->bpjs_ketenagakerjaan ?? '-' }}</td></tr>
        </table>

        <h2>Informasi Bank</h2>
        <table class="data-table">
            <tr><td class="label">Nama Bank</td><td class="value">{{ $user->nama_bank ?? '-' }}</td></tr>
            <tr><td class="label">Nomor Rekening</td><td class="value">{{ $user->nomor_rekening ?? '-' }}</td></tr>
            <tr><td class="label">Pemilik Rekening</td><td class="value">{{ $user->pemilik_rekening ?? '-' }}</td></tr>
        </table>

        <h2>Kontak Darurat</h2>
        <table class="data-table">
            <tr><td class="label">Nama</td><td class="value">{{ $user->kontak_darurat_nama ?? '-' }}</td></tr>
            <tr><td class="label">Nomor Telepon</td><td class="value">{{ $user->kontak_darurat_nomor ?? '-' }}</td></tr>
            <tr><td class="label">Hubungan</td><td class="value">{{ $user->kontak_darurat_hubungan ?? '-' }}</td></tr>
        </table>

        <h2>Riwayat Pendidikan</h2>
        @if($user->riwayatPendidikan && $user->riwayatPendidikan->count() > 0)
            <table class="history-table">
                <thead>
                    <tr>
                        <th style="width: 20%;">Jenjang</th>
                        <th style="width: 35%;">Nama Institusi</th>
                        <th style="width: 30%;">Jurusan</th>
                        <th style="width: 15%;">Tahun Lulus</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($user->riwayatPendidikan->sortByDesc('tahun_lulus') as $pendidikan)
                    <tr>
                        <td>{{ $pendidikan->jenjang ?? '-' }}</td>
                        <td>{{ $pendidikan->nama_institusi ?? '-' }}</td>
                        <td>{{ $pendidikan->jurusan ?? '-' }}</td>
                        <td>{{ $pendidikan->tahun_lulus ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="no-data">Belum ada data riwayat pendidikan.</p>
        @endif

        <h2>Riwayat Pekerjaan</h2>
         @if($user->riwayatPekerjaan && $user->riwayatPekerjaan->count() > 0)
            <table class="history-table">
                <thead>
                    <tr>
                        <th style="width: 25%;">Perusahaan</th>
                        <th style="width: 20%;">Posisi</th>
                        <th style="width: 12%;">Mulai</th>
                        <th style="width: 12%;">Selesai</th>
                        <th style="width: 31%;">Deskripsi</th> {{-- Lebar deskripsi disesuaikan --}}
                    </tr>
                </thead>
                <tbody>
                    @foreach($user->riwayatPekerjaan->sortByDesc('tanggal_selesai') as $pekerjaan)
                    <tr>
                        <td>{{ $pekerjaan->nama_perusahaan ?? '-' }}</td>
                        <td>{{ $pekerjaan->posisi ?? '-' }}</td>
                        <td>{{ $pekerjaan->tanggal_mulai ? \Carbon\Carbon::parse($pekerjaan->tanggal_mulai)->format('d/m/Y') : '-' }}</td> {{-- Format tanggal lebih singkat --}}
                        <td>{{ $pekerjaan->tanggal_selesai ? \Carbon\Carbon::parse($pekerjaan->tanggal_selesai)->format('d/m/Y') : '-' }}</td>
                        <td>{{ $pekerjaan->deskripsi_pekerjaan ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="no-data">Belum ada data riwayat pekerjaan.</p>
        @endif


        <div class="footer">
            Dicetak pada: {{ $tanggal_cetak }} oleh {{ Auth::user()->name }}
        </div>
    </div>
</body>
</html>