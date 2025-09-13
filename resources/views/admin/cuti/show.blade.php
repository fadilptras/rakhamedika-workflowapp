<x-layout-admin>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Detail Pengajuan Cuti</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Nama Karyawan:</strong> {{ $cuti->user->name }}</p>
                    <p><strong>Divisi:</strong> {{ $cuti->user->divisi }}</p>
                    <p><strong>Jenis Cuti:</strong> {{ ucfirst($cuti->jenis_cuti) }}</p>
                    <p><strong>Tanggal Mulai:</strong> {{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d F Y') }}</p>
                    <p><strong>Tanggal Selesai:</strong> {{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d F Y') }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Status:</strong>
                        @if ($cuti->status == 'pending')
                            <span class="badge badge-warning">Menunggu Persetujuan</span>
                        @elseif($cuti->status == 'disetujui')
                            <span class="badge badge-success">Disetujui</span>
                        @else
                            <span class="badge badge-danger">Ditolak</span>
                        @endif
                    </p>
                    <p><strong>Keterangan:</strong></p>
                    <p>{{ $cuti->keterangan }}</p>
                    <p><strong>Penanggung Jawab Persetujuan:</strong> {{ $cuti->approver->name ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        {{-- Tampilkan tombol aksi HANYA jika pengguna adalah approver yang sah DAN status masih 'pending' --}}
        @if (Auth::id() == $cuti->approver_id && $cuti->status == 'pending')
            <div class="card-footer">
                <form action="{{ route('admin.cuti.update', $cuti->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="disetujui">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Setujui
                    </button>
                </form>
                <form action="{{ route('admin.cuti.update', $cuti->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="ditolak">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times"></i> Tolak
                    </button>
                </form>
            </div>
        @endif
    </div>
</x-layout-admin>