<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PT RAKHA MEDIKA NUSANTARA - Pengajuan Dana</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1e40af',
                        secondary: '#9ca3af',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar (sama dengan halaman absen) -->
        <div class="w-64 bg-white shadow-lg">
            <!-- Company Name -->
            <div class="bg-primary text-white p-4 text-center">
                <h1 class="text-xl font-bold">RAKHA MEDIKA NUSANTARA</h1>
            </div>
            
            <!-- Sidebar Menu -->
            <div class="p-4">
                <div class="mb-6">
                    <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Menu Utama</h2>
                    <ul>
                        <li class="mb-1">
                            <a href="#" class="flex items-center p-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                                <i class="fas fa-tachometer-alt mr-3"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="mb-1">
                            <a href="#" class="flex items-center p-2 text-primary bg-blue-50 rounded-lg">
                                <i class="fas fa-money-bill-wave mr-3"></i>
                                <span>Pengajuan Dana</span>
                            </a>
                        </li>
                        <li class="mb-1">
                            <a href="#" class="flex items-center p-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                                <i class="fas fa-envelope mr-3"></i>
                                <span>Email</span>
                            </a>
                        </li>
                        <li class="mb-1">
                            <a href="#" class="flex items-center p-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                                <i class="fas fa-file-upload mr-3"></i>
                                <span>Pengajuan Dok</span>
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="border-t border-gray-200 pt-4">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">FORM ABSEN</h2>
                    <ul class="space-y-2">
                        <li>
                            <a href="#" class="flex items-center p-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                                <i class="fas fa-user-check mr-3 text-secondary"></i>
                                <span>Hadir</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center p-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                                <i class="fas fa-envelope mr-3 text-secondary"></i>
                                <span>Izin</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center p-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                                <i class="fas fa-procedures mr-3 text-secondary"></i>
                                <span>Sakit</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center p-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                                <i class="fas fa-user-times mr-3 text-secondary"></i>
                                <span>Absen</span>
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="border-t border-gray-200 pt-4 mt-4">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Akun</h3>
                    <ul>
                        <li class="mb-1">
                            <a href="#" class="flex items-center p-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                                <i class="fas fa-user-circle mr-3"></i>
                                <span>Profile</span>
                            </a>
                        </li>
                        <li class="mb-1">
                            <a href="#" class="flex items-center p-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                                <i class="fas fa-sign-out-alt mr-3"></i>
                                <span>Logout</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <div class="container mx-auto p-6">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Form Pengajuan Dana</h2>
                    <div id="current-time" class="text-xl font-bold text-primary"></div>
                </div>

                <!-- Pengajuan Dana Form -->
                <div class="bg-white rounded-lg shadow p-6">
                    <!-- Form Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <!-- Nama Pengaju -->
                        <div>
                            <label class="block text-gray-700 font-medium mb-2" for="nama-pengaju">
                                Nama Pengaju <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="nama-pengaju" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Masukkan nama lengkap">
                        </div>
                        
                        <!-- Departemen -->
                        <div>
                            <label class="block text-gray-700 font-medium mb-2" for="departemen">
                                Departemen <span class="text-red-500">*</span>
                            </label>
                            <select id="departemen" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                                <option value="">Pilih Departemen</option>
                                <option value="hrd">HRD</option>
                                <option value="keuangan">Keuangan</option>
                                <option value="marketing">Marketing</option>
                                <option value="produksi">Produksi</option>
                                <option value="it">IT</option>
                            </select>
                        </div>
                        
                        <!-- Tanggal Pengajuan -->
                        <div>
                            <label class="block text-gray-700 font-medium mb-2" for="tanggal-pengajuan">
                                Tanggal Pengajuan <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="tanggal-pengajuan" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>
                        
                        <!-- Jumlah Dana -->
                        <div>
                            <label class="block text-gray-700 font-medium mb-2" for="jumlah-dana">
                                Jumlah Dana (Rp) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-gray-500">Rp</span>
                                <input type="number" id="jumlah-dana" class="w-full pl-8 p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Masukkan jumlah">
                            </div>
                        </div>
                        
                        <!-- Deskripsi Penggunaan -->
                        <div class="md:col-span-2">
                            <label class="block text-gray-700 font-medium mb-2" for="deskripsi">
                                Deskripsi Penggunaan Dana <span class="text-red-500">*</span>
                            </label>
                            <textarea id="deskripsi" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" rows="3" placeholder="Jelaskan secara detail untuk apa dana ini akan digunakan"></textarea>
                        </div>
                        
                        <!-- Upload File Pendukung -->
                        <div class="md:col-span-2">
                            <label class="block text-gray-700 font-medium mb-2" for="file-pendukung">
                                Upload File Pendukung
                            </label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                                <input type="file" id="file-pendukung" class="hidden">
                                <div class="flex flex-col items-center justify-center py-4">
                                    <i class="fas fa-cloud-upload-alt text-3xl text-primary mb-2"></i>
                                    <p class="text-gray-600 mb-1">Drag & drop file here or</p>
                                    <label for="file-pendukung" class="bg-primary hover:bg-blue-800 text-white font-medium py-2 px-4 rounded-lg cursor-pointer transition duration-200">
                                        Pilih File
                                    </label>
                                    <p class="text-xs text-gray-500 mt-2">Format file: PDF, DOC, JPG, PNG (max. 5MB)</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Pilih Diajukan Ke -->
                        <div class="md:col-span-2">
                            <label class="block text-gray-700 font-medium mb-2">
                                Diajukan Kepada <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                                <!-- Checkbox options -->
                                <div class="flex items-center">
                                    <input type="checkbox" id="atasan-langsung" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                    <label for="atasan-langsung" class="ml-2 text-gray-700">Atasan Langsung</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" id="hrd" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                    <label for="hrd" class="ml-2 text-gray-700">HRD</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" id="keuangan" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                    <label for="keuangan" class="ml-2 text-gray-700">Keuangan</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" id="direktur" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                    <label for="direktur" class="ml-2 text-gray-700">Direktur</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" id="lainnya" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                    <label for="lainnya" class="ml-2 text-gray-700">Lainnya</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end space-x-4">
                        <button class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded-lg shadow transition duration-200">
                            Reset
                        </button>
                        <button class="bg-primary hover:bg-blue-800 text-white font-bold py-2 px-6 rounded-lg shadow transition duration-200">
                            Ajukan Dana
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Update time in real-time
        function updateTime() {
            const now = new Date();
            const timeElement = document.getElementById('current-time');
            
            // Format time (HH:MM:SS)
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            timeElement.textContent = `${hours}:${minutes}:${seconds}`;
            
            // Update every second
            setTimeout(updateTime, 1000);
        }
        
        // Set today's date as default for tanggal pengajuan
        function setDefaultDate() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('tanggal-pengajuan').value = today;
        }
        
        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            updateTime();
            setDefaultDate();
        });
    </script>
</body>
</html>