<x-layout-admin>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Pengajuan Cuti</h3>
        </div>
        <div class="card-body">
            <table id="example1" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Karyawan</th>
                        <th>Jenis Cuti</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cutis as $cuti)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $cuti->user->name }}</td>
                            <td>{{ ucfirst($cuti->jenis_cuti) }}</td>
                            <td>{{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d/m/Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d/m/Y') }}</td>
                            <td>
                                @if ($cuti->status == 'pending')
                                    <span class="badge badge-warning">Menunggu Persetujuan</span>
                                @elseif($cuti->status == 'disetujui')
                                    <span class="badge badge-success">Disetujui</span>
                                @else
                                    <span class="badge badge-danger">Ditolak</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.cuti.show', $cuti->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> Detail
                                </a>

                                {{-- Tombol Aksi HANYA muncul jika user adalah approver & status masih pending --}}
                                @if (Auth::id() == $cuti->approver_id && $cuti->status == 'pending')
                                    <form action="{{ route('admin.cuti.update', $cuti->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="disetujui">
                                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Anda yakin ingin menyetujui pengajuan ini?')">
                                            <i class="fas fa-check"></i> Setujui
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.cuti.update', $cuti->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="ditolak">
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Anda yakin ingin menolak pengajuan ini?')">
                                            <i class="fas fa-times"></i> Tolak
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layout-admin>