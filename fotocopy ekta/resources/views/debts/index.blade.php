<!DOCTYPE html>
<html lang="id" x-data="{ 
    darkMode: localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),
    toggleTheme() {
        this.darkMode = !this.darkMode;
        localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
    }
}" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catatan Hutang Tergabung</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
    <!-- Alpine.js untuk Modal Detail & Theme Switcher -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Font & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-100 dark:bg-slate-900 min-h-screen text-slate-800 dark:text-slate-100 antialiased selection:bg-indigo-500 selection:text-white pb-10 transition-colors duration-200" x-data="{ openModal: false, activePerson: null }">

    <!-- Container Utama -->
    <div class="max-w-md md:max-w-5xl mx-auto min-h-screen bg-slate-50 dark:bg-slate-800/60 shadow-2xl md:my-6 md:rounded-3xl flex flex-col overflow-hidden border-x md:border border-slate-200/60 dark:border-slate-700/50">

        <!-- Header Bar -->
        <div class="bg-indigo-600 dark:bg-indigo-700 text-white p-4 md:px-8 flex justify-between items-center shadow-md">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-indigo-500/40 rounded-2xl border border-indigo-400/30">
                    <i class="ph-bold ph-wallet text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-lg md:text-xl font-bold tracking-tight">Pencatatan Hutang</h1>
                    <p class="text-xs text-indigo-100/80">Akumulasi hutang otomatis berdasarkan nama</p>
                </div>
            </div>

            <div class="flex items-center space-x-2">
                <!-- Tombol Ganti Tema (Light/Dark Toggle) -->
                <button @click="toggleTheme()" class="p-2 bg-indigo-500/40 hover:bg-indigo-500/60 rounded-2xl border border-indigo-400/30 transition text-white flex items-center justify-center" title="Ganti Tema">
                    <i class="ph-bold text-xl" :class="darkMode ? 'ph-sun text-amber-300' : 'ph-moon text-indigo-100'"></i>
                </button>

                <span class="text-xs bg-indigo-700 dark:bg-indigo-800 px-3 py-1.5 rounded-full border border-indigo-500/40 font-medium hidden sm:inline-block">
                    {{ count($groupedDebts) }} Orang
                </span>
            </div>
        </div>

        <!-- Layout Grid -->
        <div class="grid grid-cols-1 md:grid-cols-12 flex-1">

            <!-- KANAN / ATAS: Total & Form Input (4 Kolom di Desktop) -->
            <div class="md:col-span-5 p-4 md:p-6 border-b md:border-b-0 md:border-r border-slate-200/80 dark:border-slate-700/60 space-y-4 bg-white dark:bg-slate-800">
                
                <!-- Card Total Akumulasi -->
                <div class="bg-indigo-600 dark:bg-indigo-700 text-white p-4 rounded-2xl shadow-lg shadow-indigo-600/10 text-center">
                    <span class="text-[11px] font-medium tracking-wide uppercase text-indigo-100">Total Keseluruhan Belum Lunas</span>
                    <p class="text-2xl md:text-3xl font-extrabold mt-0.5 text-white-300">
                        Rp {{ number_format($grandTotal, 0, ',', '.') }}
                    </p>
                </div>

                <!-- Form Tambah Transaksi -->
                <div class="pt-2">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3 flex items-center gap-1.5">
                        <i class="ph-bold ph-plus-circle text-indigo-600 dark:text-indigo-400 text-sm"></i>
                        <span>Tambah Catatan Hutang</span>
                    </h2>

                    <form action="{{ route('debts.store') }}" method="POST" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-[11px] font-medium text-slate-500 dark:text-slate-400 mb-1">Nama Pembeli / Penghutang</label>
                            <input type="text" name="name" placeholder="Misal: Budi Santoso" class="w-full text-xs p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:text-white" required>
                        </div>

                        <div>
                            <label class="block text-[11px] font-medium text-slate-500 dark:text-slate-400 mb-1">Nominal Hutang (Rp)</label>
                            <input type="number" name="amount" placeholder="0" class="w-full text-xs p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:text-white" required>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-1 lg:grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[11px] font-medium text-slate-500 dark:text-slate-400 mb-1">Jatuh Tempo (Opsional)</label>
                                <input type="date" name="due_date" class="w-full text-xs p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 text-slate-500 dark:text-slate-400">
                            </div>
                            <div>
                                <label class="block text-[11px] font-medium text-slate-500 dark:text-slate-400 mb-1">Keterangan (Opsional)</label>
                                <input type="text" name="description" placeholder="Misal: Fotocopy 50 lembar" class="w-full text-xs p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:text-white">
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 text-xs rounded-xl shadow-md shadow-indigo-600/20 transition duration-150 active:scale-[0.98]">
                            Simpan Catatan
                        </button>
                    </form>
                </div>
            </div>

            <!-- KIRI / BAWAH: Daftar Nama & Akumulasi (7 Kolom di Desktop) -->
            <main class="md:col-span-7 p-4 md:p-6 space-y-3 bg-slate-50 dark:bg-slate-800/40">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Daftar Penghutang</h2>
                </div>

                <div class="space-y-3">
                    @forelse($groupedDebts as $group)
                        <div class="p-4 bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/50 rounded-2xl shadow-sm hover:shadow-md transition-all flex items-center justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center space-x-2">
                                    <span class="font-bold text-slate-800 dark:text-slate-100 text-base truncate">{{ $group['name'] }}</span>
                                    <span class="text-[10px] font-medium px-2 py-0.5 rounded-full {{ $group['is_all_paid'] ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400' : 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-400' }}">
                                        {{ $group['is_all_paid'] ? 'Lunas' : count($group['items']) . ' Transaksi' }}
                                    </span>
                                </div>
                                
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                    Total Belum Lunas:
                                </p>
                                <p class="font-bold text-lg {{ $group['total_unpaid'] > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                                    Rp {{ number_format($group['total_unpaid'], 0, ',', '.') }}
                                </p>
                            </div>

                            <!-- Tombol Lihat Rincian -->
                            <div class="shrink-0">
                                <button @click="openModal = true; activePerson = {{ json_encode($group) }}" class="bg-indigo-50 dark:bg-indigo-950/50 hover:bg-indigo-100 text-indigo-600 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800/50 text-xs font-medium px-3 py-2 rounded-xl transition flex items-center gap-1.5">
                                    <i class="ph-bold ph-list-magnifying-glass text-base"></i>
                                    <span>Lihat Rincian</span>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 bg-white dark:bg-slate-800 rounded-2xl border border-dashed border-slate-200 dark:border-slate-700">
                            <div class="inline-flex p-3 bg-slate-100 dark:bg-slate-700 rounded-full text-slate-400 mb-2">
                                <i class="ph-bold ph-receipt text-2xl"></i>
                            </div>
                            <p class="text-xs text-slate-400">Belum ada catatan hutang.</p>
                        </div>
                    @endforelse
                </div>
            </main>

        </div>
    </div>

    <!-- MODAL POPUP DETAIL RINCIAN -->
    <div x-cloak x-show="openModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4" x-transition>
        <div @click.outside="openModal = false" class="bg-white dark:bg-slate-800 rounded-3xl max-w-lg w-full p-5 md:p-6 shadow-2xl border border-slate-200 dark:border-slate-700 space-y-4">
            
            <!-- Header Modal -->
            <div class="flex items-center justify-between border-b dark:border-slate-700/60 pb-3">
                <div>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white" x-text="activePerson ? 'Rincian: ' + activePerson.name : ''"></h3>
                    <p class="text-xs text-slate-400">Daftar item hutang per transaksi</p>
                </div>
                <button @click="openModal = false" class="p-1 rounded-lg text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700">
                    <i class="ph-bold ph-x text-lg"></i>
                </button>
            </div>

            <!-- List Item Transaksi -->
            <div class="space-y-3 max-h-80 overflow-y-auto pr-1">
                <template x-for="item in activePerson?.items" :key="item.id">
                    <div class="p-3 bg-slate-50 dark:bg-slate-900 border border-slate-200/60 dark:border-slate-700/50 rounded-xl flex items-center justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold text-slate-700 dark:text-slate-200" x-text="item.description || 'Tanpa keterangan'"></p>
                            <p class="text-[10px] text-slate-400 mt-0.5" x-text="item.due_date ? 'Jatuh Tempo: ' + item.due_date : 'Tidak ada jatuh tempo'"></p>
                            <p class="text-xs font-bold text-slate-900 dark:text-white mt-1" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(item.amount)"></p>
                        </div>

                        <div class="flex items-center space-x-2">
                            <!-- Toggle Status Lunas -->
                            <form :action="'/debts/' + item.id + '/status'" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-[10px] font-medium px-2.5 py-1 rounded-lg transition" :class="item.status === 'paid' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400' : 'bg-indigo-600 text-white'">
                                    <span x-text="item.status === 'paid' ? 'Lunas' : 'Bayar'"></span>
                                </button>
                            </form>

                            <!-- Hapus Item -->
                            <form :action="'/debts/' + item.id" method="POST" onsubmit="return confirm('Hapus item transaksi ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-slate-400 hover:text-rose-500 p-1">
                                    <i class="ph-bold ph-trash text-sm"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Footer Modal -->
            <div class="pt-2 border-t dark:border-slate-700/60 flex justify-end">
                <button @click="openModal = false" class="bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-200 text-xs px-4 py-2 rounded-xl font-medium">
                    Tutup
                </button>
            </div>

        </div>
    </div>

</body>
</html>