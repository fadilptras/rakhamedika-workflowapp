<x-layout-users>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="flex-1 overflow-auto">
        <div class="container mx-auto p-4 md:p-6">
            
            <div class="bg-white rounded-lg shadow p-4 md:p-6 mb-8">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Form Pengajuan Dana</h2>
                
                <form action="{{ route('pengajuan_dana.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 mb-8">
                        <div>
                            <label class="block text-gray-700 font-medium mb-2" for="nama-pengaju">
                                Nama Pemohon <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="nama-pengaju" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" value="{{ Auth::user()->name }}" disabled>
                            <input type="hidden" name="nama_pemohon" value="{{ Auth::user()->name }}">
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 font-medium mb-2" for="divisi">
                                Divisi<span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="divisi" name="divisi" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Masukan Divisi" value="{{ Auth::user()->divisi }}" disabled>
                            <input type="hidden" name="divisi" value="{{ Auth::user()->divisi }}">
                        </div>

                        <div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-gray-700 font-medium mb-2" for="pilih-bank">
                                        Pilih Bank <span class="text-red-500">*</span>
                                    </label>
                                    <select id="pilih-bank" name="nama_bank" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" required>
                                        <option value="" disabled selected>Pilih salah satu</option>
                                        <option value="BCA">BCA</option>
                                        <option value="BRI">BRI</option>
                                        <option value="BNI">BNI</option>
                                        <option value="Mandiri">Mandiri</option>
                                        <option value="other">Lainnya</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-gray-700 font-medium mb-2" for="no-rekening">
                                        Nomor Rekening <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="no-rekening" name="no_rekening" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Masukkan nomor rekening" required>
                                </div>
                            </div>

                            <div id="bank-lainnya-container" class="mt-4 hidden">
                                <label class="block text-gray-700 font-medium mb-2" for="input-bank-lainnya">
                                    Nama Bank Lainnya <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="input-bank-lainnya" name="nama_bank_lainnya" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Masukkan nama bank">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 font-medium mb-2" for="tanggal-pengajuan">
                                Tanggal Pengajuan <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="tanggal-pengajuan" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" value="{{ date('Y-m-d') }}" disabled>
                            <input type="hidden" name="tanggal_pengajuan" value="{{ date('Y-m-d') }}">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-medium mb-2" for="judul-pengajuan">
                                Judul Pengajuan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="judul-pengajuan" name="judul_pengajuan" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Contoh: Pembelian Perlengkapan Kantor" required>
                        </div>
                        
                        <div class="hidden md:block"></div>
                    </div>

                    <div class="mt-6">
                        <label class="block text-gray-700 font-medium mb-2">
                            Rincian Penggunaan Dana <span class="text-red-500">*</span>
                        </label>
                        
                        <div class="hidden md:block overflow-x-auto">
                            <table class="min-w-full border border-gray-200 text-sm rounded-lg">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 text-left font-medium text-gray-700 w-2/3">Deskripsi Pengeluaran</th>
                                        <th class="px-4 py-2 text-left font-medium text-gray-700 w-1/3">Dana yang Dibutuhkan (Rp)</th>
                                        <th class="px-4 py-2 text-center font-medium text-gray-700">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="rincian-dana-body">
                                </tbody>
                                <tfoot>
                                    <tr class="border-t-2 border-gray-300">
                                        <td class="px-4 py-2 font-bold text-gray-800 text-right">Total:</td>
                                        <td class="px-4 py-2 font-bold text-gray-800">
                                            Rp <span id="total-dana-desktop">0</span>
                                            <input type="hidden" id="jumlah-dana-total" name="jumlah_dana_total">
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div id="rincian-dana-container-mobile" class="block md:hidden space-y-4">
                        </div>
                        
                        <div class="block md:hidden mt-4">
                            <div class="flex items-center justify-between p-4 bg-gray-100 rounded-lg border-2 border-gray-300">
                                <span class="text-gray-700 font-bold">Total:</span>
                                <span class="text-gray-800 font-bold">Rp <span id="total-dana-mobile">0</span></span>
                            </div>
                        </div>

                        <button id="tambah-baris-btn" type="button" class="mt-4 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-1 px-4 rounded-lg transition duration-200 w-full md:w-auto">
                            Tambah Baris
                        </button>
                    </div>
                    
                    <div class="mt-6">
                        <label class="block text-gray-700 font-medium mb-2" for="file-pendukung">
                            Upload File Pendukung
                        </label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                            <input type="file" id="file-pendukung" name="file_pendukung" class="hidden">
                            <div class="flex flex-col items-center justify-center py-4">
                                <i class="fas fa-cloud-upload-alt text-3xl text-blue-600 mb-2"></i>
                                <p class="text-gray-600 mb-1">Drag & drop file here or</p>
                                <label for="file-pendukung" class="bg-blue-600 hover:bg-blue-800 text-white font-medium text-xs py-0.5 px-1 rounded-lg cursor-pointer transition duration-200">
                                    Pilih File
                                </label>
                                <p class="text-xs text-gray-500 mt-2">Format file: PDF, DOC, JPG, PNG (max. 5MB)</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4 mt-8">
                        <button 
                            type="reset"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-1 px-4 rounded-lg border border-gray-300 transition duration-200">
                            Reset
                        </button>
                        <button 
                            type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition duration-200">
                            Ajukan Dana
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-lg shadow p-4 md:p-6 mb-8">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Status Pengajuan</h2>
                
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full border border-gray-200 text-sm rounded-lg">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left font-medium text-gray-700">Tanggal</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-700">Judul Pengajuan</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-700">Total Dana</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-700">Status</th>
                                <th class="px-4 py-2 text-center font-medium text-gray-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pengajuanDanas as $request)
                            <tr class="border-t">
                                <td class="px-4 py-2">{{ $request->created_at->format('d/m/Y') }}</td>
                                <td class="px-4 py-2">{{ $request->judul_pengajuan }}</td>
                                <td class="px-4 py-2">Rp {{ number_format($request->total_dana, 0, ',', '.') }}</td>
                                <td class="px-4 py-2">
                                    @if ($request->status == 'diajukan')
                                        <span class="inline-block px-2 py-1 leading-none text-orange-600 bg-orange-100 rounded-full font-semibold uppercase tracking-wide text-xs">Diajukan</span>
                                    @elseif ($request->status == 'disetujui')
                                        <span class="inline-block px-2 py-1 leading-none text-green-600 bg-green-100 rounded-full font-semibold uppercase tracking-wide text-xs">Disetujui</span>
                                    @elseif ($request->status == 'ditolak')
                                        <span class="inline-block px-2 py-1 leading-none text-red-600 bg-red-100 rounded-full font-semibold uppercase tracking-wide text-xs">Ditolak</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <a href="{{ route('pengajuan_dana.show', $request->id) }}" class="text-blue-600 hover:underline text-sm font-medium">Lihat Detail</a>
                                </td>
                            </tr>
                            @empty
                            <tr class="border-t">
                                <td class="px-4 py-4 text-center text-gray-500" colspan="5">Belum ada pengajuan dana yang dibuat.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="block md:hidden space-y-4">
                    @forelse ($pengajuanDanas as $request)
                    <div class="bg-gray-50 rounded-lg p-4 shadow-sm border border-gray-200">
                        <div class="flex justify-between items-center mb-2">
                            <div class="text-sm text-gray-500">{{ $request->created_at->format('d/m/Y') }}</div>
                            @if ($request->status == 'diajukan')
                                <span class="inline-block px-2 py-1 leading-none text-orange-600 bg-orange-100 rounded-full font-semibold uppercase tracking-wide text-xs">Diajukan</span>
                            @elseif ($request->status == 'disetujui')
                                <span class="inline-block px-2 py-1 leading-none text-green-600 bg-green-100 rounded-full font-semibold uppercase tracking-wide text-xs">Disetujui</span>
                            @elseif ($request->status == 'ditolak')
                                <span class="inline-block px-2 py-1 leading-none text-red-600 bg-red-100 rounded-full font-semibold uppercase tracking-wide text-xs">Ditolak</span>
                            @endif
                        </div>
                        <div class="font-bold text-gray-800 text-lg mb-1">{{ $request->judul_pengajuan }}</div>
                        <div class="text-gray-600 mb-4">Total: <span class="font-semibold">Rp {{ number_format($request->total_dana, 0, ',', '.') }}</span></div>
                        <a href="{{ route('pengajuan_dana.show', $request->id) }}" class="text-blue-600 hover:underline text-sm font-medium">Lihat Detail</a>
                    </div>
                    @empty
                    <div class="text-center text-gray-500 p-4">Belum ada pengajuan dana yang dibuat.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('pilih-bank').addEventListener('change', function() {
            const bankContainer = document.getElementById('bank-lainnya-container');
            const otherBankInput = document.getElementById('input-bank-lainnya');
            if (this.value === 'other') {
                bankContainer.classList.remove('hidden');
                otherBankInput.setAttribute('required', 'required');
            } else {
                bankContainer.classList.add('hidden');
                otherBankInput.removeAttribute('required');
            }
        });

        const tambahBarisBtn = document.getElementById('tambah-baris-btn');
        const rincianDanaBodyDesktop = document.getElementById('rincian-dana-body');
        const rincianDanaContainerMobile = document.getElementById('rincian-dana-container-mobile');
        const totalDanaSpanDesktop = document.getElementById('total-dana-desktop');
        const totalDanaSpanMobile = document.getElementById('total-dana-mobile');
        const jumlahDanaTotalInput = document.getElementById('jumlah-dana-total');

        function updateTotal() {
            let total = 0;
            const jumlahInputs = document.querySelectorAll('input[name="rincian_jumlah[]"]');
            jumlahInputs.forEach(input => {
                total += parseInt(input.value) || 0;
            });
            const formattedTotal = total.toLocaleString('id-ID');
            totalDanaSpanDesktop.textContent = formattedTotal;
            totalDanaSpanMobile.textContent = formattedTotal;
            jumlahDanaTotalInput.value = total;
        }

        function addRow() {
            // Check screen size to determine where to append the new row
            const isMobile = window.innerWidth < 768;
            const container = isMobile ? rincianDanaContainerMobile : rincianDanaBodyDesktop;

            const newRow = document.createElement(isMobile ? 'div' : 'tr');
            if (isMobile) {
                newRow.classList.add('bg-gray-50', 'rounded-lg', 'p-4', 'shadow-sm', 'border', 'border-gray-200');
                newRow.innerHTML = `
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-sm text-gray-500 font-medium">Deskripsi Pengeluaran</label>
                        <button type="button" class="text-red-500 hover:text-red-700 font-medium text-sm">Hapus</button>
                    </div>
                    <input type="text" name="rincian_deskripsi[]" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary mb-4" placeholder="Masukkan deskripsi pengeluaran" required>
                    <label class="block text-sm text-gray-500 font-medium">Dana yang Dibutuhkan (Rp)</label>
                    <div class="relative">
                        <input type="number" name="rincian_jumlah[]" class="w-full p-2 pl-8 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary jumlah-input" placeholder="0" required>
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                    </div>
                `;
            } else {
                newRow.classList.add('border-t');
                newRow.innerHTML = `
                    <td class="px-4 py-2">
                        <input type="text" name="rincian_deskripsi[]" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Masukkan deskripsi pengeluaran" required>
                    </td>
                    <td class="px-4 py-2 relative">
                        <input type="number" name="rincian_jumlah[]" class="w-full p-2 pl-8 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary jumlah-input" placeholder="0" required>
                        <span class="absolute left-6 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                    </td>
                    <td class="px-4 py-2 text-center">
                        <button type="button" class="text-red-500 hover:text-red-700 font-medium">Hapus</button>
                    </td>
                `;
            }

            container.appendChild(newRow);

            const amountInput = newRow.querySelector('input[name="rincian_jumlah[]"]');
            amountInput.addEventListener('input', updateTotal);

            const deleteButton = newRow.querySelector('button');
            deleteButton.addEventListener('click', function() {
                newRow.remove();
                updateTotal();
            });
        }

        // Initial call to add the first row
        addRow();

        tambahBarisBtn.addEventListener('click', addRow);
    </script>
</x-layout-users>