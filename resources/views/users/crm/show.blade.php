<x-layout-users :title="'Detail Klien & Sales'">

    <div class="w-full max-w-7xl mx-auto px-0 sm:px-0 lg:px-0 py-0 relative">
        
        {{-- Tombol Kembali --}}
        <div class="mb-6">
            <a href="{{ route('crm.index') }}" 
            class="inline-flex items-center justify-center w-10 h-10 sm:w-auto sm:h-10 sm:px-4 rounded-lg bg-gradient-to-r from-blue-700 to-blue-600 text-white shadow-md hover:shadow-lg hover:brightness-110 transition-all gap-2"
            title="Kembali ke Data Sales">
                <i class="fas fa-arrow-left"></i>
                <span class="hidden sm:inline font-medium text-sm">Kembali</span>
            </a>
        </div>

        {{-- BAGIAN 1: HEADER PROFIL --}}
        <div class="bg-[#001BB7] rounded-2xl shadow-xl shadow-blue-900/10 border border-blue-900/10 mb-6 overflow-hidden relative">
            <div class="absolute top-0 right-0 -mt-10 -mr-10 w-64 h-64 bg-white opacity-10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-40 h-40 bg-blue-400 opacity-20 rounded-full blur-2xl pointer-events-none"></div>

            <div class="p-6 md:p-8 text-white relative z-10">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
                    {{-- Kiri: Identitas --}}
                    <div class="flex-grow space-y-4">
                        <div>
                            <span class="bg-white/20 backdrop-blur-md text-white text-[11px] font-bold px-3 py-1 rounded-full uppercase tracking-wider border border-white/30 shadow-sm inline-flex items-center">
                                <i class="fas fa-hospital mr-2 opacity-80"></i> {{ $client->nama_perusahaan }}
                            </span>
                        </div>
                        <h2 class="text-3xl md:text-5xl font-extrabold tracking-tight text-white drop-shadow-sm">
                            {{ $client->nama_user }}
                        </h2>
                        
                        {{-- Badges --}}
                        <div class="flex flex-wrap gap-3 text-sm font-medium">
                            <div class="flex items-center bg-white/10 backdrop-blur-md px-4 py-2 rounded-full border border-white/20 shadow-sm transition hover:bg-white/20">
                                <i class="fas fa-map-marker-alt mr-2 text-blue-200"></i> 
                                <span class="text-blue-50">{{ $client->area ?? 'Belum set Area' }}</span>
                            </div>
                            <div class="flex items-center bg-white/10 backdrop-blur-md px-4 py-2 rounded-full border border-white/20 shadow-sm transition hover:bg-white/20">
                                <i class="fas fa-user-tie mr-2 text-blue-200"></i> 
                                <span class="text-blue-50">PIC: {{ $client->pic }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Kanan: Statistik Sales --}}
                    <div class="flex flex-col items-end gap-3 min-w-[240px]">
                        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-5 border border-white/20 w-full text-right shadow-lg relative overflow-hidden group hover:bg-white/15 transition-all">
                            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/20 to-transparent opacity-0 group-hover:opacity-100 transition duration-500"></div>
                            <p class="text-blue-100 text-xs font-bold uppercase tracking-widest mb-1 relative z-10">Total Sales (Gross)</p>
                            <div class="text-3xl md:text-4xl font-extrabold text-white drop-shadow-md relative z-10 flex items-start justify-end">
                                <span class="text-lg opacity-70 font-medium mr-1 mt-1">Rp</span>
                                <span>{{ number_format($client->interactions->where('jenis_transaksi', 'IN')->sum('nilai_kontribusi'), 0, ',', '.') }}</span>
                            </div>
                        </div>

                        {{-- Tombol Aksi --}}
                        @if($client->user_id == Auth::id())
                            <div class="flex items-center gap-2 mt-2">
                                <button onclick="toggleModal('editClientModal')" class="group flex items-center text-xs font-semibold text-yellow-200 hover:text-white transition-colors bg-yellow-500/20 hover:bg-yellow-500/80 px-4 py-2 rounded-lg backdrop-blur-sm border border-transparent hover:border-yellow-300/50 shadow-sm cursor-pointer">
                                    <i class="fas fa-edit mr-2 transition-transform group-hover:scale-110"></i> Edit Detail
                                </button>
                                <form action="{{ route('crm.client.destroy', $client->id) }}" method="POST" onsubmit="return confirm('PERINGATAN: Yakin ingin menghapus klien ini beserta seluruh data riwayatnya?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="group flex items-center text-xs font-semibold text-red-200 hover:text-white transition-colors bg-red-900/20 hover:bg-red-600/80 px-4 py-2 rounded-lg backdrop-blur-sm border border-transparent hover:border-red-400/50 shadow-sm">
                                        <i class="fas fa-trash-alt mr-2 transition-transform group-hover:scale-110"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- BAGIAN 2: GRID KARTU DETAIL (INFORMASI) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            
            {{-- KARTU 1: INFO CLIENT (BIRU) --}}
            <div class="bg-blue-600 p-8 rounded-2xl shadow-lg text-white relative overflow-hidden group hover:shadow-xl transition-all h-full">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
                <div class="absolute -left-6 -bottom-6 w-20 h-20 bg-white/10 rounded-full blur-xl pointer-events-none"></div>

                <h4 class="text-blue-100 text-[11px] font-bold uppercase tracking-widest mb-4 flex items-center relative z-10 border-b border-white/20 pb-2">
                    <i class="fas fa-user mr-2 text-white"></i> Informasi Client
                </h4>
                <div class="space-y-3 relative z-10">
                    
                    {{-- [BARU] Jabatan (Ditampilkan di atas Email) --}}
                    @if($client->jabatan)
                    <div class="flex items-start relative pl-8">
                        <div class="absolute left-0 top-1 text-blue-300"><i class="fas fa-id-badge"></i></div>
                        <div>
                            <p class="text-[10px] text-blue-200 font-bold uppercase mb-0.5">Jabatan</p>
                            <p class="font-bold text-sm tracking-wide">{{ $client->jabatan }}</p>
                        </div>
                    </div>
                    @endif

                    {{-- Email --}}
                    <div class="flex items-start relative pl-8">
                        <div class="absolute left-0 top-1 text-blue-200"><i class="fas fa-envelope"></i></div>
                        <div>
                            <p class="text-[10px] text-blue-200 font-bold uppercase mb-0.5">Email</p>
                            <p class="font-medium text-sm break-all">{{ $client->email ?? '-' }}</p>
                        </div>
                    </div>

                    {{-- Telepon --}}
                    <div class="flex items-start relative pl-8">
                        <div class="absolute left-0 top-1 text-green-300"><i class="fab fa-whatsapp text-lg -ml-0.5"></i></div>
                        <div>
                            <p class="text-[10px] text-blue-200 font-bold uppercase mb-0.5">Telepon / WA</p>
                            <p class="font-medium text-sm">{{ $client->no_telpon ?? '-' }}</p>
                        </div>
                    </div>

                    {{-- Tgl Lahir & Hobi (Digabung agar hemat tempat) --}}
                    <div class="grid grid-cols-2 gap-2">
                        <div class="flex items-start relative pl-8">
                            <div class="absolute left-0 top-1 text-pink-200"><i class="fas fa-birthday-cake"></i></div>
                            <div>
                                <p class="text-[10px] text-blue-200 font-bold uppercase mb-0.5">Tgl Lahir</p>
                                <div class="font-medium text-sm">
                                    @if($client->tanggal_lahir)
                                        {{ \Carbon\Carbon::parse($client->tanggal_lahir)->format('d M Y') }}
                                    @else
                                        <span class="italic opacity-70">-</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        {{-- [BARU] Hobi --}}
                        <div class="flex items-start relative pl-6">
                            <div class="absolute left-0 top-1 text-yellow-300"><i class="fas fa-star"></i></div>
                            <div>
                                <p class="text-[10px] text-blue-200 font-bold uppercase mb-0.5">Hobi</p>
                                <p class="font-medium text-sm">{{ $client->hobby_client ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Alamat Rumah --}}
                    <div class="flex items-start relative pl-8">
                        <div class="absolute left-0 top-1 text-white/50"><i class="fas fa-home"></i></div>
                        <div>
                            <p class="text-[10px] text-blue-200 font-bold uppercase mb-0.5">Alamat Rumah</p>
                            <p class="text-sm leading-relaxed opacity-90">{{ $client->alamat_user ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KARTU 2: INFO PERUSAHAAN (ORANGE) --}}
            <div class="bg-orange-500 p-8 rounded-2xl shadow-lg text-white relative overflow-hidden group hover:shadow-xl transition-all h-full">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
                
                <h4 class="text-orange-100 text-[11px] font-bold uppercase tracking-widest mb-4 flex items-center relative z-10 border-b border-white/20 pb-2">
                    <i class="fas fa-building mr-2 text-white"></i> Informasi Perusahaan
                </h4>
                <div class="space-y-3 relative z-10">
                    <div class="p-4 bg-white/20 backdrop-blur-md rounded-lg border border-white/20 relative overflow-hidden">
                        <i class="fas fa-hospital absolute right-2 bottom-2 text-5xl text-white/20 -rotate-12 pointer-events-none"></i>
                        <p class="text-[10px] text-orange-100 font-bold uppercase mb-1">Nama Instansi / RS</p>
                        <p class="font-bold text-lg leading-tight">{{ $client->nama_perusahaan }}</p>
                    </div>
                    {{-- Tgl Berdiri --}}
                    <div class="flex items-start relative pl-8">
                        <div class="absolute left-0 top-1 text-white/80"><i class="fas fa-calendar-alt"></i></div>
                        <div>
                            <p class="text-[10px] text-orange-200 font-bold uppercase mb-0.5">Tanggal Berdiri</p>
                            <div class="flex items-center font-medium text-sm">
                                @if($client->tanggal_berdiri)
                                    <span>{{ \Carbon\Carbon::parse($client->tanggal_berdiri)->format('d F Y') }}</span>
                                    <span class="ml-2 text-[10px] bg-white text-orange-600 px-2 py-0.5 rounded-full font-bold shadow-sm">
                                        {{ \Carbon\Carbon::parse($client->tanggal_berdiri)->age }} Th
                                    </span>
                                @else
                                    <span class="italic opacity-70">Belum diisi</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    {{-- Alamat Perusahaan (UPDATED from alamat) --}}
                    <div class="flex items-start relative pl-8">
                        <div class="absolute left-0 top-1 text-white/80"><i class="fas fa-map-marked-alt"></i></div>
                        <div>
                            <p class="text-[10px] text-orange-200 font-bold uppercase mb-0.5">Alamat Kantor</p>
                            <p class="text-sm leading-relaxed opacity-90">{{ $client->alamat_perusahaan ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KARTU 3: INFO BANK (DARK/EMERALD) --}}
            <div class="bg-gradient-to-br from-gray-900 via-slate-800 to-gray-900 p-5 rounded-2xl shadow-lg border border-gray-700 text-white relative overflow-hidden flex flex-col justify-between group hover:shadow-2xl transition duration-500 h-full">
                <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-500 opacity-10 rounded-full -mr-16 -mt-16 blur-2xl pointer-events-none group-hover:opacity-20 transition duration-500"></div>
                <div class="absolute bottom-0 left-0 w-24 h-24 bg-blue-500 opacity-10 rounded-full -ml-12 -mb-12 blur-2xl pointer-events-none group-hover:opacity-20 transition duration-500"></div>
                
                <div class="flex justify-between items-start mb-4 relative z-10 border-b border-gray-700 pb-2">
                    <h4 class="text-gray-400 text-[11px] font-bold uppercase tracking-widest flex items-center">
                        <span class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center mr-3 text-emerald-400">
                            <i class="fas fa-wallet"></i>
                        </span>
                        Informasi Bank
                    </h4>
                    <i class="fas fa-wifi text-gray-600 transform rotate-90 text-xl opacity-50"></i>
                </div>
                
                <div class="relative z-10 space-y-4">
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase tracking-wider mb-1">Bank & Rekening</p>
                        <div class="flex flex-col">
                            <span class="font-bold text-xl tracking-wider text-white mb-1">{{ $client->bank ?? 'BANK -' }}</span>
                            {{-- Nama di Rekening (NEW) --}}
                            <p class="text-xs text-gray-400 mb-1">{{ $client->nama_di_rekening ? 'A/n '.$client->nama_di_rekening : '' }}</p>
                            
                            <div class="flex items-center gap-2 font-mono text-emerald-400 tracking-widest text-sm bg-white/5 px-3 py-1.5 rounded-lg w-fit border border-white/5">
                                <i class="fas fa-credit-card text-xs"></i>
                                <span class="font-semibold">{{ $client->no_rekening ?? '----' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-700/50 pt-3">
                        <p class="text-[10px] text-gray-500 uppercase tracking-wider mb-1">Saldo Awal</p>
                        <p class="text-3xl font-mono font-bold text-emerald-400 tracking-tight text-shadow-sm">
                            <span class="text-sm text-gray-500 mr-1 font-normal">IDR</span>{{ number_format($client->saldo_awal ?? 0, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
                <i class="fas fa-coins text-emerald-500/20 text-4xl absolute right-0 bottom-0 -mb-2 -mr-2 pointer-events-none"></i>
            </div>
        </div>

        {{-- MENU NAVIGASI --}}
        <div class="mb-4 flex justify-center md:justify-start">
            <div class="bg-white rounded-xl grid grid-cols-2 md:inline-flex gap-1 border border-gray-200 shadow-sm">
                <button id="btn-sales" onclick="switchTab('sales')" class="nav-btn px-5 py-2.5 rounded-lg text-sm font-bold transition-all duration-200 flex items-center justify-center gap-2 focus:outline-none">
                    <i class="fas fa-plus-circle text-sm"></i> Sales (In)
                </button>
                <button id="btn-support" onclick="switchTab('support')" class="nav-btn px-5 py-2.5 rounded-lg text-sm font-bold transition-all duration-200 flex items-center justify-center gap-2 focus:outline-none">
                    <i class="fas fa-hand-holding-usd text-sm"></i> Usage (Out)
                </button>
                <button id="btn-activity" onclick="switchTab('activity')" class="nav-btn px-5 py-2.5 rounded-lg text-sm font-bold transition-all duration-200 flex items-center justify-center gap-2 focus:outline-none">
                    <i class="fas fa-glass-cheers text-sm"></i> Aktivitas
                </button>
                <button id="btn-history" onclick="switchTab('history')" class="nav-btn px-5 py-2.5 rounded-lg text-sm font-bold transition-all duration-200 flex items-center justify-center gap-2 focus:outline-none">
                    <i class="fas fa-history text-sm"></i> Riwayat
                </button>
                <button id="btn-recap" onclick="switchTab('recap')" class="nav-btn px-5 py-2.5 rounded-lg text-sm font-bold transition-all duration-200 flex items-center justify-center gap-2 focus:outline-none">
                    <i class="fas fa-calendar-check text-sm"></i> Rekap Sales
                </button>
            </div>
        </div>
        
        {{-- 1. INPUT SALES --}}
        <div id="section-sales" class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden relative">
            <div class="bg-blue-600 px-6 py-4 border-b border-blue-100 flex justify-between items-center">
                <h3 class="font-bold text-white text-lg flex items-center">
                    <span class="w-8 h-8 bg-white text-blue-600 rounded-lg flex items-center justify-center mr-3 text-sm shadow">
                        <i class="fas fa-plus"></i>
                    </span>
                    Input Sales
                </h3>
            </div>
            <div class="p-6 md:p-8">
                <form action="{{ route('crm.interaction.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="client_id" value="{{ $client->id }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal Transaksi <span class="text-red-500">*</span></label>
                                <input type="date" name="tanggal_interaksi" class="w-full border-2 border-blue-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5" required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Nama Produk / Layanan <span class="text-red-500">*</span></label>
                                <input type="text" name="nama_produk" class="w-full border-2 border-blue-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5" placeholder="Contoh: Kassa Lipat" required>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-1">Catatan Tambahan</label>
                                <textarea name="catatan" rows="3" class="w-full border-2 border-blue-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5"></textarea>
                            </div>
                        </div>
                        <div class="space-y-5 bg-blue-100 p-6 rounded-xl border border-gray-100">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Nilai Sales (Rp) <span class="text-red-500">*</span></label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                      <span class="text-gray-500 sm:text-sm">Rp</span>
                                    </div>
                                    <input type="text" name="nilai_sales" onkeyup="formatRupiah(this)" class="w-full border-blue-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 pl-10 px-4 py-2.5 font-mono text-lg" placeholder="0" required>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Komisi (%) <span class="text-red-500">*</span></label>
                                <div class="relative rounded-md shadow-sm">
                                    <input type="number" name="komisi" 
                                        step="0.1" 
                                        max="100" 
                                        class="input-spinner-left w-full border-blue-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 pl-8 pr-10 py-2.5 font-mono" 
                                        placeholder="10" 
                                        required
                                    >
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4">
                                        <span class="text-gray-500 sm:text-sm font-bold">%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="pt-2">
                                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg shadow-md transition transform active:scale-95">
                                    <i class="fas fa-save mr-2"></i> Simpan
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- 2. INPUT SUPPORT --}}
        <div id="section-support" class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden relative hidden">
            <div class="bg-red-600 px-6 py-4 border-b border-red-100 flex justify-between items-center">
                <h3 class="font-bold text-white text-lg flex items-center">
                    <span class="w-8 h-8 bg-white text-red-600 rounded-lg flex items-center justify-center mr-3 text-sm shadow">
                        <i class="fas fa-hand-holding-usd"></i>
                    </span>
                    Pengeluaran
                </h3>
            </div>
            <div class="p-6 md:p-8">
                <form action="{{ route('crm.interaction.support') }}" method="POST">
                    @csrf
                    <input type="hidden" name="client_id" value="{{ $client->id }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal <span class="text-red-500">*</span></label>
                                <input type="date" name="tanggal_interaksi" class="w-full border-2 border-red-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 px-4 py-2.5" required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Keperluan Support <span class="text-red-500">*</span></label>
                                <input type="text" name="keperluan" class="w-full border-2 border-red-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 px-4 py-2.5" placeholder="Contoh: Transport" required>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-1">Catatan Detail</label>
                                <textarea name="catatan" rows="3" class="w-full border-2 border-red-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 px-4 py-2.5"></textarea>
                            </div>
                        </div>
                        <div class="space-y-5 bg-red-50 p-6 rounded-xl border border-red-100">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Nominal Keluar (Rp) <span class="text-red-500">*</span></label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                      <span class="text-gray-500 sm:text-sm">Rp</span>
                                    </div>
                                    <input type="text" name="nominal" onkeyup="formatRupiah(this)" class="w-full border-red-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 pl-10 px-4 py-2.5 font-mono text-lg" placeholder="0" required>
                                </div>
                                <p class="text-xs text-red-500 mt-2 flex items-center"><i class="fas fa-info-circle mr-1"></i> Mengurangi saldo kontribusi</p>
                            </div>
                            <div class="pt-10">
                                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-lg shadow-md transition transform active:scale-95">
                                    <i class="fas fa-minus-circle mr-2"></i> Simpan
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- 3. INPUT AKTIVITAS --}}
        <div id="section-activity" class="space-y-8 hidden">
            
            <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden relative">
                <div class="bg-orange-500 px-6 py-4 border-b border-orange-100 flex justify-between items-center">
                    <h3 class="font-bold text-white text-lg flex items-center">
                        <span class="w-8 h-8 bg-white text-orange-600 rounded-lg flex items-center justify-center mr-3 text-sm shadow">
                            <i class="fas fa-glass-cheers"></i>
                        </span>
                        Input Aktivitas
                    </h3>
                </div>

                <div class="p-6 md:p-8">
                    <form action="{{ route('crm.interaction.entertain') }}" method="POST">
                        @csrf
                        <input type="hidden" name="client_id" value="{{ $client->id }}">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Tanggal Kegiatan <span class="text-red-500">*</span></label>
                                    <input type="date" name="tanggal_interaksi" class="w-full border-2 border-orange-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 px-3 py-2" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Lokasi / Venue</label>
                                    <div class="relative">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                            <i class="fas fa-map-marker-alt text-xs"></i>
                                        </div>
                                        <input type="text" name="lokasi" class="w-full border-2 border-orange-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 pl-8 px-3 py-2" placeholder="Contoh: Restoran X, Kantor Klien...">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Partisipan / Klien Terkait</label>
                                    <div class="relative">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                            <i class="fas fa-users text-xs"></i>
                                        </div>
                                        <input type="text" name="peserta" class="w-full border-2 border-orange-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 pl-8 px-3 py-2" placeholder="Sebutkan nama User/Client">
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4 bg-orange-50/40 p-5 rounded-xl border border-orange-100 flex flex-col h-full">
                                <div class="flex-grow">
                                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Keterangan Aktivitas <span class="text-red-500">*</span></label>
                                    <textarea name="catatan" rows="3" class="w-full border-2 border-orange-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 px-3 py-2 text-sm" placeholder="Contoh: Makan siang membahas proyek baru, dll." required></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Nominal Biaya (Rp) <span class="text-red-500">*</span></label>
                                    <div class="relative rounded-md shadow-sm">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                          <span class="text-gray-500 sm:text-xs font-bold">Rp</span>
                                        </div>
                                        <input type="text" name="nominal" onkeyup="formatRupiah(this)" class="w-full border-2 border-orange-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 pl-8 px-3 py-2 font-mono font-bold text-lg text-orange-700" placeholder="0" required>
                                    </div>
                                </div>
                                <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-2.5 px-4 rounded-lg shadow-md transition transform active:scale-95 flex items-center justify-center gap-2 mt-2">
                                    <i class="fas fa-save"></i> Simpan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- TABEL HISTORY AKTIVITAS --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-3 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-700 text-sm uppercase tracking-wide flex items-center gap-2">
                        <i class="fas fa-history text-orange-500"></i> Riwayat Aktivitas & Entertain
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-orange-50 text-orange-800 uppercase text-xs font-bold tracking-wider">
                            <tr>
                                <th class="px-5 py-3">Tanggal</th>
                                <th class="px-5 py-3">Aktivitas / Keterangan</th>
                                <th class="px-5 py-3">Lokasi & Partisipan</th>
                                <th class="px-5 py-3 text-right">Biaya</th>
                                <th class="px-5 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @php
                                $activities = $client->interactions->where('jenis_transaksi', 'ENTERTAIN')->sortByDesc('tanggal_interaksi');
                            @endphp
                            
                            @forelse($activities as $act)
                            <tr class="hover:bg-orange-50/30 transition">
                                <td class="px-5 py-3 whitespace-nowrap font-medium text-gray-700">
                                    {{ \Carbon\Carbon::parse($act->tanggal_interaksi)->format('d/m/Y') }}
                                </td>
                                <td class="px-5 py-3">
                                    <div class="font-bold text-gray-800">{{ $act->nama_produk }}</div>
                                    <div class="text-xs text-gray-500">{{ $act->catatan }}</div>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center text-xs text-gray-600 mb-0.5">
                                        <i class="fas fa-map-marker-alt w-4 text-center mr-1 text-gray-400"></i> {{ $act->lokasi ?? '-' }}
                                    </div>
                                    <div class="flex items-center text-xs text-gray-600">
                                        <i class="fas fa-users w-4 text-center mr-1 text-gray-400"></i> {{ $act->peserta ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-right font-mono font-bold text-orange-600">
                                    {{ number_format($act->nilai_kontribusi, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <form action="{{ route('crm.interaction.destroy', $act->id) }}" method="POST" onsubmit="return confirm('Hapus aktivitas ini?');" class="inline">
                                        @csrf @method('DELETE')
                                        <button class="text-gray-300 hover:text-red-500 transition"><i class="fas fa-trash-alt"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-400 italic bg-gray-50/50">
                                    Belum ada data aktivitas
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- 4. RIWAYAT TRANSAKSI UTAMA --}}
        <div id="section-history" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50/50">
                <h3 class="font-bold text-gray-800 text-lg flex items-center gap-2"><i class="fas fa-history text-gray-400"></i> Riwayat Transaksi</h3>
            </div>
            <div class="overflow-x-auto w-full">
                <table class="w-full text-sm text-left min-w-[800px]">
                    <thead class="bg-gray-100 text-gray-600 uppercase text-xs font-bold tracking-wider">
                        <tr>
                            <th class="px-4 py-4">Tanggal</th>
                            <th class="px-4 py-4">Produk / Keterangan</th>
                            <th class="px-4 py-4 text-right">Nilai Sales (Gross)</th>
                            <th class="px-4 py-4 text-center w-16">Komisi</th>
                            <th class="px-4 py-4 text-right text-blue-800">Value (Net)</th>
                            <th class="px-4 py-4 text-right text-red-600">Usage (Out)</th>
                            <th class="px-4 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($interactions as $item)
                            @if($item->jenis_transaksi == 'ENTERTAIN') @continue @endif

                            @php
                                $isOut = $item->jenis_transaksi == 'OUT';
                                $rate = 0; 
                                if(preg_match('/\[Rate:([\d\.]+)\]/', $item->catatan, $m)) { $rate = $m[1]; }
                                $displayNote = trim(preg_replace('/\[Rate:[\d\.]+\]/', '', $item->catatan));
                                $valueNet = (!$isOut) ? ($item->nilai_kontribusi * ($rate/100)) : 0;
                            @endphp
                        
                        <tr class="{{ $isOut ? 'bg-red-50/50' : 'hover:bg-blue-50/50' }} transition">
                            <td class="px-4 py-3 whitespace-nowrap font-bold text-gray-700">
                                {{ \Carbon\Carbon::parse($item->tanggal_interaksi)->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-bold {{ $isOut ? 'text-red-800' : 'text-blue-900' }}">{{ $item->nama_produk }}</div>
                                <div class="text-xs text-gray-500 italic">{{ $displayNote }}</div>
                            </td>
                            <td class="px-4 py-3 text-right font-mono text-gray-600">
                                {{ (!$isOut) ? number_format($item->nilai_kontribusi, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if(!$isOut && $rate > 0)
                                    <span class="bg-gray-200 text-gray-700 px-2 py-0.5 rounded text-xs font-bold shadow-sm border border-gray-300">{{ $rate }}%</span>
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-blue-700">
                                {{ (!$isOut) ? number_format($valueNet, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-red-600">
                                {{ $isOut ? number_format($item->nilai_kontribusi, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <form action="{{ route('crm.interaction.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus?');" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="text-gray-400 hover:text-red-600 transition"><i class="fas fa-trash-alt"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center py-8 text-gray-400">Belum ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($interactions->hasPages())
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">{{ $interactions->links() }}</div>
            @endif
        </div>

        {{-- 5. REKAP TAHUNAN --}}
        <div id="section-recap" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex flex-col md:flex-row justify-between items-center gap-4 bg-gray-50/50">
                <h3 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                    <span class="bg-white p-1.5 rounded-lg shadow-sm border border-gray-100"><i class="fas fa-chart-bar text-blue-600"></i></span>
                    Rekapitulasi Tahun {{ $year }}
                </h3>
                
                <div class="flex items-center gap-2">
                    <form action="{{ route('crm.show', $client->id) }}" method="GET" class="flex items-center">
                        <input type="hidden" name="tab" value="recap"> 
                        <div class="relative">
                            <select name="year" onchange="this.form.submit()" class="pl-4 pr-10 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 cursor-pointer hover:bg-gray-50 transition appearance-none">
                                @for($y = date('Y'); $y >= 2020; $y--)
                                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                                @endfor
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                                <i class="fas fa-chevron-down text-xs"></i>
                            </div>
                        </div>
                    </form>

                    <a href="{{ route('crm.client.export', ['client' => $client->id, 'year' => $year]) }}" class="inline-flex items-center bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold py-2 px-4 rounded-lg shadow-sm transition hover:shadow-md border border-emerald-700">
                        <i class="fas fa-file-excel mr-2"></i> Export Excel
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto w-full">
    <table class="w-full text-sm text-left min-w-[800px]">
        <thead class="bg-gray-100 text-gray-600 uppercase text-xs font-bold tracking-wider border-b border-gray-200">
            <tr>
                <th class="px-4 py-3">Bulan</th>
                <th class="px-4 py-3 text-right">Sales (In)</th>
                <th class="px-4 py-3 text-center w-16">Komisi</th>
                <th class="px-4 py-3 text-right text-blue-700 bg-blue-50/50">Value (Net)</th>
                <th class="px-4 py-3 text-right text-red-600">Usage (Out)</th>
                <th class="px-4 py-3 text-right text-gray-800 border-l border-gray-200 bg-gray-50">Saldo</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            
            {{-- === BARIS SALDO AWAL (DINAMIS) === --}}
            <tr class="bg-yellow-50 hover:bg-yellow-100 transition border-b border-yellow-200">
                {{-- Gabungkan 5 kolom pertama untuk Label --}}
                <td class="px-4 py-3 font-bold text-gray-800 italic" colspan="5">
                    <div class="flex items-center">
                        <span class="w-6 h-6 rounded-full bg-yellow-200 text-yellow-700 flex items-center justify-center mr-2 text-xs">
                            <i class="fas fa-forward"></i>
                        </span>
                        {{ $startingLabel }} {{-- Variabel dari Controller --}}
                    </div>
                </td>
                {{-- Kolom Saldo --}}
                <td class="px-4 py-3 text-right font-mono font-bold text-gray-900 border-l border-yellow-200 bg-yellow-100">
                    {{ number_format($startingBalance, 0, ',', '.') }} {{-- Variabel dari Controller --}}
                </td>
            </tr>
            {{-- === END BARIS SALDO AWAL === --}}

            @foreach ($recap as $r)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3 font-bold text-gray-700">{{ $r['month_name'] }}</td>
                <td class="px-4 py-3 text-right font-mono text-gray-600">
                    {{ $r['gross_in'] > 0 ? number_format($r['gross_in'], 0, ',', '.') : '-' }}
                </td>
                <td class="px-4 py-3 text-center font-mono text-xs text-gray-500">
                    {{ $r['komisi_text'] }}
                </td>
                <td class="px-4 py-3 text-right font-mono font-bold text-blue-800 bg-blue-50/30">
                    {{ $r['net_value'] > 0 ? number_format($r['net_value'], 0, ',', '.') : '-' }}
                </td>
                <td class="px-4 py-3 text-right font-mono text-red-600">
                    {{ $r['out'] > 0 ? number_format($r['out'], 0, ',', '.') : '-' }}
                </td>
                <td class="px-4 py-3 text-right font-mono font-bold text-gray-900 border-l border-gray-200 bg-gray-50/30">
                    {{ number_format($r['saldo'], 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot class="bg-gray-100 font-bold text-gray-800 border-t-2 border-gray-200">
            <tr>
                <td class="px-4 py-4 uppercase">Total</td>
                <td class="px-4 py-4 text-right text-gray-600">{{ number_format($yearlyTotals['gross_in'], 0, ',', '.') }}</td>
                <td class="px-4 py-4 text-center">-</td> 
                <td class="px-4 py-4 text-right text-blue-900 bg-blue-100">{{ number_format($yearlyTotals['net_value'], 0, ',', '.') }}</td>
                <td class="px-4 py-4 text-right text-red-700">{{ number_format($yearlyTotals['out'], 0, ',', '.') }}</td>
                <td class="px-4 py-4 text-right border-l border-gray-300">{{ number_format($yearlyTotals['saldo'], 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</div>
        </div>
    </div>

    {{-- MODAL EDIT DATA KLIEN (LANDSCAPE 3 KOLOM) --}}
    @push('modals')
        <div id="editClientModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-60 z-[9999] flex items-center justify-center p-4 backdrop-blur-sm transition-opacity duration-300">
            
            {{-- MODAL CONTAINER: Lebar (6xl) mengikuti style Index --}}
            <div class="bg-white w-full md:max-w-6xl rounded-2xl shadow-2xl overflow-hidden border border-gray-200 flex flex-col max-h-[90vh]">
                
                {{-- HEADER MODAL --}}
                <div class="bg-gradient-to-r from-blue-700 to-blue-600 px-6 py-4 border-b border-blue-500 flex justify-between items-center shadow-md z-10 shrink-0">
                    <h3 class="font-bold text-lg text-white flex items-center">
                        <i class="fas fa-edit mr-3"></i> Edit Data Klien
                    </h3>
                    <button onclick="toggleModal('editClientModal')" class="text-white hover:text-red-200 transition text-2xl font-bold focus:outline-none">&times;</button>
                </div>
                
                {{-- FORM BODY --}}
                <form action="{{ route('crm.client.update', $client->id) }}" method="POST" class="flex flex-col flex-grow overflow-hidden">
                    @csrf
                    @method('PUT')
                    
                    {{-- Scrollable Content Area --}}
                    <div class="overflow-y-auto p-6 custom-scrollbar flex-grow bg-gray-50/30">
                        
                        {{-- GRID 3 KOLOM (Landscape) --}}
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-stretch h-full">

                            {{-- KOLOM 1: INFO CLIENT (Style Biru) - EDIT MODE --}}
                            <div class="bg-white rounded-xl border border-blue-100 shadow-sm overflow-hidden flex flex-col h-full">
                                <div class="bg-blue-50/80 px-4 py-3 border-b border-blue-300 flex items-center">
                                    <span class="bg-blue-600 text-white text-[10px] font-bold px-2 py-0.5 rounded mr-2">1</span>
                                    <h4 class="text-blue-800 text-xs font-bold uppercase tracking-wider">Identitas Personal</h4>
                                </div>
                                <div class="p-4 space-y-3 flex-grow">
                                    {{-- NAMA & JABATAN --}}
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-700 mb-1 uppercase">Nama & Jabatan <span class="text-red-500">*</span></label>
                                        <div class="space-y-2">
                                            <input type="text" name="nama_user" value="{{ old('nama_user', $client->nama_user) }}" required class="w-full border-2 border-blue-100 rounded focus:ring-blue-500 focus:border-blue-500 text-sm px-3 py-2 font-bold" placeholder="Nama Lengkap User">
                                            {{-- [BARU] Input Jabatan --}}
                                            <input type="text" name="jabatan" value="{{ old('jabatan', $client->jabatan) }}" class="w-full border-2 border-blue-100 rounded focus:ring-blue-500 focus:border-blue-500 text-xs px-3 py-2" placeholder="Jabatan">
                                        </div>
                                    </div>

                                    {{-- KONTAK --}}
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-500 mb-1 uppercase">Kontak Personal</label>
                                        <div class="grid grid-cols-2 gap-2">
                                            <input type="text" name="no_telpon" value="{{ old('no_telpon', $client->no_telpon) }}" class="w-full border-2 border-blue-100 rounded text-sm focus:ring-blue-500 px-3 py-2" placeholder="08xxxx (WA)">
                                            <input type="email" name="email" value="{{ old('email', $client->email) }}" class="w-full border-2 border-blue-100 rounded text-sm focus:ring-blue-500 px-3 py-2" placeholder="Email">
                                        </div>
                                    </div>

                                    {{-- TGL LAHIR & HOBI --}}
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-[11px] font-bold text-gray-500 mb-1 uppercase">Tanggal Lahir</label>
                                            <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', optional($client->tanggal_lahir)->format('Y-m-d')) }}" class="w-full border-2 border-blue-100 rounded text-sm focus:ring-blue-500 px-3 py-2">
                                        </div>
                                        <div>
                                            {{-- [BARU] Input Hobi --}}
                                            <label class="block text-[11px] font-bold text-gray-500 mb-1 uppercase">Hobi / Minat</label>
                                            <input type="text" name="hobby_client" value="{{ old('hobby_client', $client->hobby_client) }}" class="w-full border-2 border-blue-100 rounded text-sm focus:ring-blue-500 px-3 py-2" placeholder="Hobi">
                                        </div>
                                    </div>

                                    {{-- ALAMAT --}}
                                    <div class="flex-grow">
                                        <label class="block text-[11px] font-bold text-gray-500 mb-1 uppercase">Alamat Rumah</label>
                                        <textarea name="alamat_user" rows="2" class="w-full border-2 border-blue-100 rounded text-sm focus:ring-blue-500 px-3 py-2 resize-none" placeholder="Alamat tempat tinggal...">{{ old('alamat_user', $client->alamat_user) }}</textarea>
                                    </div>
                                </div>
                            </div>

                            {{-- KOLOM 2: INFO PERUSAHAAN (Style Orange) --}}
                            <div class="bg-white rounded-xl border border-orange-100 shadow-sm overflow-hidden flex flex-col h-full">
                                <div class="bg-orange-50/80 px-4 py-3 border-b border-orange-100 flex items-center">
                                    <span class="bg-orange-500 text-white text-[10px] font-bold px-2 py-0.5 rounded mr-2">2</span>
                                    <h4 class="text-orange-800 text-xs font-bold uppercase tracking-wider">Data Perusahaan</h4>
                                </div>
                                <div class="p-4 space-y-3 flex-grow">
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-700 mb-1 uppercase">Nama Perusahaan / PT <span class="text-red-500">*</span></label>
                                        <input type="text" name="nama_perusahaan" value="{{ old('nama_perusahaan', $client->nama_perusahaan) }}" required class="w-full border-2 border-orange-100 rounded focus:ring-orange-500 focus:border-orange-500 text-sm px-3 py-2 font-semibold" placeholder="Nama Instansi">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-500 mb-1 uppercase">Detail Perusahaan</label>
                                        <div class="grid grid-cols-2 gap-2">
                                            <input type="text" name="area" value="{{ old('area', $client->area) }}" class="w-full border-2 border-orange-100 rounded text-sm focus:ring-orange-500 px-3 py-2" placeholder="Area (Ex: Jaksel)">
                                            <input type="date" name="tanggal_berdiri" value="{{ old('tanggal_berdiri', optional($client->tanggal_berdiri)->format('Y-m-d')) }}" class="w-full border-2 border-orange-100 rounded text-sm focus:ring-orange-500 px-3 py-2" title="Tanggal Berdiri">
                                        </div>
                                    </div>
                                    <div class="flex-grow">
                                        <label class="block text-[11px] font-bold text-gray-500 mb-1 uppercase">Alamat Perusahaan</label>
                                        <textarea name="alamat_perusahaan" rows="5" class="w-full border-2 border-orange-100 rounded text-sm focus:ring-orange-500 px-3 py-2 resize-none" placeholder="Lokasi kantor...">{{ old('alamat_perusahaan', $client->alamat_perusahaan) }}</textarea>
                                    </div>
                                </div>
                            </div>

                            {{-- KOLOM 3: INFO BANK (Style Hijau) --}}
                            <div class="bg-white rounded-xl border border-emerald-100 shadow-sm overflow-hidden flex flex-col h-full">
                                <div class="bg-emerald-50/80 px-4 py-3 border-b border-emerald-100 flex items-center">
                                    <span class="bg-emerald-600 text-white text-[10px] font-bold px-2 py-0.5 rounded mr-2">3</span>
                                    <h4 class="text-emerald-800 text-xs font-bold uppercase tracking-wider">Keuangan & Bank</h4>
                                </div>
                                <div class="p-4 space-y-3 flex-grow">
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-500 mb-1 uppercase">Nama Bank</label>
                                        <input type="text" name="bank" value="{{ old('bank', $client->bank) }}" class="w-full border-2 border-emerald-100 rounded text-sm focus:ring-emerald-500 px-3 py-2" placeholder="Ex: BCA / Mandiri">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-500 mb-1 uppercase">No. Rekening</label>
                                        <input type="text" name="no_rekening" value="{{ old('no_rekening', $client->no_rekening) }}" class="w-full border-2 border-emerald-100 rounded text-sm focus:ring-emerald-500 px-3 py-2 font-mono" placeholder="123xxxxx">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-500 mb-1 uppercase">Atas Nama (A/N)</label>
                                        <input type="text" name="nama_di_rekening" value="{{ old('nama_di_rekening', $client->nama_di_rekening) }}" class="w-full border-2 border-emerald-100 rounded text-sm focus:ring-emerald-500 px-3 py-2" placeholder="Pemilik Rekening">
                                    </div>
                                    
                                    <div class="mt-auto pt-3 border-t border-emerald-50">
                                        <label class="block text-[11px] font-bold text-emerald-700 mb-1 uppercase">Saldo Awal</label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-2 text-emerald-600 font-bold text-xs">Rp</span>
                                            <input type="number" name="saldo_awal" value="{{ old('saldo_awal', $client->saldo_awal) }}" class="w-full pl-8 border-2 border-emerald-100 bg-emerald-50/30 rounded text-lg font-bold text-emerald-800 focus:ring-emerald-500 px-3 py-1.5" placeholder="0">
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- FOOTER MODAL --}}
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end gap-3 shrink-0">
                        <button type="button" onclick="toggleModal('editClientModal')" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-bold hover:bg-gray-100 transition">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-bold shadow-md hover:shadow-lg transition transform active:scale-95 flex items-center">
                            <i class="fas fa-save mr-2"></i> Update Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endpush

    {{-- Script untuk Modal --}}
    @push('scripts')
    <script>
        function toggleModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal.classList.contains('hidden')) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.style.overflow = 'hidden'; 
            } else {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = 'auto'; 
            }
        }

        window.onclick = function(event) {
            const modal = document.getElementById('editClientModal');
            if (event.target == modal) {
                toggleModal('editClientModal');
            }
        }

        function switchTab(tabName) {
            const sections = { 
                'sales': 'section-sales', 
                'support': 'section-support', 
                'activity': 'section-activity',
                'history': 'section-history', 
                'recap': 'section-recap' 
            };
            const buttons = { 
                'sales': 'btn-sales', 
                'support': 'btn-support', 
                'activity': 'btn-activity',
                'history': 'btn-history', 
                'recap': 'btn-recap' 
            };
            
            const inactiveClass = "nav-btn px-5 py-2.5 rounded-lg text-sm font-bold transition-all duration-200 flex items-center justify-center gap-2 focus:outline-none text-gray-500 hover:text-gray-700 hover:bg-gray-50";
            const activeBase = "nav-btn px-5 py-2.5 rounded-lg text-sm font-bold transition-all duration-200 flex items-center justify-center gap-2 focus:outline-none shadow-md ring-1 ring-inset";

            for (const k in sections) {
                document.getElementById(sections[k]).classList.add('hidden');
                document.getElementById(buttons[k]).className = inactiveClass;
            }

            document.getElementById(sections[tabName]).classList.remove('hidden');
            
            let specificActiveClass = "";
            if (tabName === 'sales') {
                specificActiveClass = "bg-blue-600 text-white ring-blue-700";
            } else if (tabName === 'support') {
                specificActiveClass = "bg-red-600 text-white ring-red-700";
            } else if (tabName === 'activity') {
                specificActiveClass = "bg-orange-500 text-white ring-orange-600";
            } else {
                specificActiveClass = "bg-gray-800 text-white ring-gray-900";
            }
            
            document.getElementById(buttons[tabName]).className = activeBase + " " + specificActiveClass;
            localStorage.setItem('activeTab', tabName);
        }

        function formatRupiah(input) {
            let value = input.value.replace(/[^0-9]/g, '');
            if (value) {
                value = parseInt(value, 10).toLocaleString('id-ID');
            }
            input.value = value;
        }
        
        const urlParams = new URLSearchParams(window.location.search);
        if(urlParams.has('year') || urlParams.get('tab') === 'recap') {
            switchTab('recap');
        } else {
            document.addEventListener("DOMContentLoaded", () => switchTab(localStorage.getItem('activeTab') || 'sales'));
        }
    </script>
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 3px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #a8a8a8; }
    </style>
    @endpush

</x-layout-users>