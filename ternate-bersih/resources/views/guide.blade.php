<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Panduan Pengguna & Alur Sistem') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Tentang Sistem -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6 text-gray-900">
                    <h3 class="text-2xl font-bold text-emerald-800 mb-4 border-b pb-2">Tentang Ternate Bersih</h3>
                    <p class="text-gray-600 leading-relaxed mb-4">
                        <strong>Ternate Bersih (SIPAS Ternate)</strong> adalah platform pelaporan tumpukan sampah berbasis geolokasi. Platform ini menjembatani masyarakat Kota Ternate dengan Dinas Lingkungan Hidup (DLH) untuk mewujudkan kota yang bersih dan sehat melalui respon yang cepat dan akurat.
                    </p>
                    <p class="text-gray-600 leading-relaxed">
                        Sistem ini menggunakan arsitektur tertutup (Closed System) di mana pembuatan akun hanya dapat dilakukan secara internal oleh Administrator demi menjaga validitas data pelapor dan armada.
                    </p>
                </div>
            </div>

            <!-- Alur Kerja (Workflow) -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6 text-gray-900">
                    <h3 class="text-2xl font-bold text-emerald-800 mb-6 border-b pb-2">Alur Kerja Sistem (Workflow)</h3>
                    
                    <div class="relative wrap overflow-hidden p-4 h-full">
                        <div class="border-2-2 absolute border-opacity-20 border-gray-700 h-full border" style="left: 50%;"></div>
                        
                        <!-- Step 1 -->
                        <div class="mb-8 flex justify-between items-center w-full right-timeline">
                            <div class="order-1 w-5/12"></div>
                            <div class="z-20 flex items-center order-1 bg-emerald-600 shadow-xl w-10 h-10 rounded-full">
                                <h1 class="mx-auto font-semibold text-lg text-white">1</h1>
                            </div>
                            <div class="order-1 bg-slate-50 rounded-lg shadow-md w-5/12 px-6 py-4 border-t-4 border-emerald-600">
                                <h3 class="mb-2 font-bold text-gray-800 text-lg">Masyarakat Melapor (Mobile)</h3>
                                <p class="text-sm leading-snug tracking-wide text-gray-600 text-opacity-100">Masyarakat menemukan tumpukan sampah, membuka aplikasi mobile, mengambil foto bukti, lalu menekan tombol "Kunci Lokasi (GPS)". Laporan dikirim dan berstatus <strong>Menunggu Verifikasi</strong>.</p>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="mb-8 flex justify-between flex-row-reverse items-center w-full left-timeline">
                            <div class="order-1 w-5/12"></div>
                            <div class="z-20 flex items-center order-1 bg-blue-600 shadow-xl w-10 h-10 rounded-full">
                                <h1 class="mx-auto text-white font-semibold text-lg">2</h1>
                            </div>
                            <div class="order-1 bg-slate-50 rounded-lg shadow-md w-5/12 px-6 py-4 border-t-4 border-blue-600">
                                <h3 class="mb-2 font-bold text-gray-800 text-lg">Verifikasi Admin (Web)</h3>
                                <p class="text-sm leading-snug tracking-wide text-gray-600 text-opacity-100">Admin mendapat notifikasi realtime di Dashboard. Admin mengecek validitas foto dan lokasi. Jika valid, laporan disetujui dan statusnya berubah menjadi <strong>Diverifikasi</strong>.</p>
                            </div>
                        </div>
                        
                        <!-- Step 3 -->
                        <div class="mb-8 flex justify-between items-center w-full right-timeline">
                            <div class="order-1 w-5/12"></div>
                            <div class="z-20 flex items-center order-1 bg-amber-500 shadow-xl w-10 h-10 rounded-full">
                                <h1 class="mx-auto font-semibold text-lg text-white">3</h1>
                            </div>
                            <div class="order-1 bg-slate-50 rounded-lg shadow-md w-5/12 px-6 py-4 border-t-4 border-amber-500">
                                <h3 class="mb-2 font-bold text-gray-800 text-lg">Penugasan Armada (Web)</h3>
                                <p class="text-sm leading-snug tracking-wide text-gray-600 text-opacity-100">Admin memilih Truk Pengangkut (Driver) yang sedang aktif atau dekat dengan lokasi. Setelah dipilih, status laporan menjadi <strong>Ditugaskan</strong> dan Driver mendapat notifikasi di Mobile App-nya.</p>
                            </div>
                        </div>

                        <!-- Step 4 -->
                        <div class="mb-8 flex justify-between flex-row-reverse items-center w-full left-timeline">
                            <div class="order-1 w-5/12"></div>
                            <div class="z-20 flex items-center order-1 bg-green-500 shadow-xl w-10 h-10 rounded-full">
                                <h1 class="mx-auto text-white font-semibold text-lg">4</h1>
                            </div>
                            <div class="order-1 bg-slate-50 rounded-lg shadow-md w-5/12 px-6 py-4 border-t-4 border-green-500">
                                <h3 class="mb-2 font-bold text-gray-800 text-lg">Penyelesaian Laporan (Mobile)</h3>
                                <p class="text-sm leading-snug tracking-wide text-gray-600 text-opacity-100">Driver menuju lokasi sesuai rute di Peta. Setelah sampah diangkut, Driver mengambil foto bukti bahwa lokasi telah bersih dan menekan "Selesai". Status berubah menjadi <strong>Selesai</strong>.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panduan Fungsional Admin -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-2xl font-bold text-emerald-800 mb-4 border-b pb-2">Fungsi Utama Administrator</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <!-- Card 1 -->
                        <div class="border border-gray-100 bg-gray-50 p-5 rounded-lg">
                            <div class="flex items-center mb-3">
                                <div class="bg-emerald-100 p-2 rounded-md mr-3">
                                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                </div>
                                <h4 class="font-bold text-gray-800 text-lg">Manajemen Pengguna</h4>
                            </div>
                            <p class="text-sm text-gray-600">Karena fitur "Daftar" dimatikan untuk keamanan, Admin <strong>WAJIB</strong> membuatkan akun secara manual melalui menu <em>Manajemen Pengguna</em> untuk Petugas Armada (Driver) maupun Staf DLH lainnya.</p>
                        </div>
                        
                        <!-- Card 2 -->
                        <div class="border border-gray-100 bg-gray-50 p-5 rounded-lg">
                            <div class="flex items-center mb-3">
                                <div class="bg-blue-100 p-2 rounded-md mr-3">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <h4 class="font-bold text-gray-800 text-lg">Verifikasi & Penugasan</h4>
                            </div>
                            <p class="text-sm text-gray-600">Admin berperan sebagai <em>Dispatcher</em>. Anda harus sering memantau "Lonceng Notifikasi". Jika ada laporan baru, segera verifikasi. Laporan yang sudah diverifikasi wajib segera ditugaskan ke Armada yang tersedia.</p>
                        </div>

                        <!-- Card 3 -->
                        <div class="border border-gray-100 bg-gray-50 p-5 rounded-lg">
                            <div class="flex items-center mb-3">
                                <div class="bg-amber-100 p-2 rounded-md mr-3">
                                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                                </div>
                                <h4 class="font-bold text-gray-800 text-lg">Master Data (Kategori & Wilayah)</h4>
                            </div>
                            <p class="text-sm text-gray-600">Pastikan untuk selalu memperbarui data Kecamatan, Kelurahan, serta Kategori Laporan pada menu Master Data. Data ini akan ditarik secara dinamis oleh Aplikasi Mobile saat masyarakat mengisi form pengaduan.</p>
                        </div>

                        <!-- Card 4 -->
                        <div class="border border-gray-100 bg-gray-50 p-5 rounded-lg">
                            <div class="flex items-center mb-3">
                                <div class="bg-purple-100 p-2 rounded-md mr-3">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <h4 class="font-bold text-gray-800 text-lg">Ekspor Data (Laporan)</h4>
                            </div>
                            <p class="text-sm text-gray-600">Pada menu "Ekspor Data", Admin dapat mengunduh seluruh rekam jejak laporan ke dalam format Excel. Sangat berguna untuk bahan evaluasi akhir bulan maupun penyusunan laporan pertanggungjawaban dinas.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
