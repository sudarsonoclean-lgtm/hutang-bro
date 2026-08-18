<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencatatan Hutang</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen pb-12">

    <!-- Container Utama Mode Mobile -->
    <div class="max-w-md mx-auto bg-white min-h-screen shadow-md flex flex-col">
        
        <!-- Header -->
        <div class="bg-indigo-600 p-4 text-white rounded-b-2xl shadow">
            <h1 class="text-xl font-bold">Catatan Hutang</h1>
            <p class="text-xs text-indigo-200">Kelola keuangan hutang-piutangmu</p>
            
            <!-- Ringkasan Otomatis -->
            <div class="grid grid-cols-2 gap-2 mt-4 text-center">
                <div class="bg-indigo-700 p-2 rounded-lg">
                    <span class="text-xs text-indigo-200">Piutang (Ada di orang)</span>
                    <p class="font-bold text-sm text-green-300">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</p>
                </div>
                <div class="bg-indigo-700 p-2 rounded-lg">
                    <span class="text-xs text-indigo-200">Hutang (Harus dibayar)</span>
                    <p class="font-bold text-sm text-red-300">Rp {{ number_format($totalHutang, 0, ',', '.') }}</p>
                </div>
            </div>
            <div class="mt-2 bg-indigo-800 p-2 rounded-lg text-center">
                <span class="text-xs text-indigo-200">Estimasi Saldo Bersih:</span>
                <p class="font-bold text-base {{ $netBalance >= 0 ? 'text-green-300' : 'text-red-300' }}">
                    Rp {{ number_format($netBalance, 0, ',', '.') }}
                </p>
            </div>
        </div>

        <!-- Form Tambah Catatan -->
        <div class="p-4 border-b">
            <form action="{{ route('debts.store') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <input type="text" name="name" placeholder="Nama Orang" class="w-full text-sm p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <select name="type" class="text-sm p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                        <option value="piutang">Piutang (Saya Meminjami)</option>
                        <option value="hutang">Hutang (Saya Meminjam)</option>
                    </select>
                    <input type="number" name="amount" placeholder="Nominal (Rp)" class="text-sm p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <input type="date" name="due_date" class="text-sm p-2 border rounded-lg text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <input type="text" name="description" placeholder="Catatan/Keterangan" class="text-sm p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <button type="submit" class="w-full bg-indigo-600 text-white font-semibold py-2 text-sm rounded-lg hover:bg-indigo-700 transition">
                    + Tambah Catatan
                </button>
            </form>
        </div>

        <!-- Daftar Catatan -->
        <div class="p-4 flex-1 space-y-3">
            <h2 class="text-sm font-bold text-gray-700 mb-2">Riwayat Transaksi</h2>
            
            @forelse($debts as $item)
                <div class="p-3 border rounded-xl shadow-sm bg-gray-50 flex items-center justify-between">
                    <div>
                        <div class="flex items-center space-x-2">
                            <span class="font-semibold text-gray-800 text-sm">{{ $item->name }}</span>
                            <span class="text-[10px] px-2 py-0.5 rounded-full text-white {{ $item->type === 'piutang' ? 'bg-green-500' : 'bg-red-500' }}">
                                {{ ucfirst($item->type) }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $item->description ?? 'Tidak ada catatan' }}
                            @if($item->due_date) | Jatuh Tempo: {{ $item->due_date }} @endif
                        </p>
                        <p class="font-bold text-sm text-gray-800 mt-1">
                            Rp {{ number_format($item->amount, 0, ',', '.') }}
                        </p>
                    </div>

                    <div class="flex flex-col items-end space-y-2">
                        <!-- Toggle Status Lunas -->
                        <form action="{{ route('debts.updateStatus', $item) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-xs px-2 py-1 rounded {{ $item->status === 'paid' ? 'bg-gray-300 text-gray-700' : 'bg-blue-600 text-white' }}">
                                {{ $item->status === 'paid' ? 'Lunas' : 'Belum Lunas' }}
                            </button>
                        </form>
                        
                        <!-- Hapus -->
                        <form action="{{ route('debts.destroy', $item) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-red-500 hover:underline">Hapus</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-center text-xs text-gray-400 py-6">Belum ada catatan transaksi.</p>
            @endforelse
        </div>

    </div>

</body>
</html>