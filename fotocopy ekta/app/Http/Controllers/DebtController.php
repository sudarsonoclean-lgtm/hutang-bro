<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use Illuminate\Http\Request;

class DebtController extends Controller
{
    public function index()
    {
        $allDebts = Debt::orderBy('created_at', 'desc')->get();

        // 1. Kelompokkan data berdasarkan nama orang (Case-insensitive)
        $groupedDebts = $allDebts->groupBy(function ($item) {
            return strtolower(trim($item->name));
        })->map(function ($items) {
            // Ambil nama asli dari item pertama
            $originalName = $items->first()->name;
            
            // Hitung total hanya untuk transaksi yang belum lunas
            $totalUnpaid = $items->where('status', 'unpaid')->sum('amount');
            $totalAll    = $items->sum('amount');
            $isAllPaid   = $items->every(fn($item) => $item->status === 'paid');

            return [
                'name'         => $originalName,
                'total_unpaid' => $totalUnpaid,
                'total_all'    => $totalAll,
                'is_all_paid'  => $isAllPaid,
                'items'        => $items // Menyimpan rincian item transaksi
            ];
        });

        // 2. Perhitungan Otomatis Total Keseluruhan (Hanya yang belum lunas)
        $grandTotal = Debt::where('status', 'unpaid')->sum('amount');

        return view('debts.index', compact('groupedDebts', 'grandTotal'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'amount'      => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'due_date'    => 'nullable|date',
        ]);

        // Jenis transaksi di-default ke 'hutang' karena input jenis transaksi dihilangkan
        Debt::create([
            'name'        => $request->name,
            'type'        => 'hutang',
            'amount'      => $request->amount,
            'description' => $request->description,
            'due_date'    => $request->due_date,
        ]);

        return redirect()->route('debts.index')->with('success', 'Catatan berhasil ditambahkan!');
    }

    public function updateStatus(Debt $debt)
    {
        $debt->update([
            'status' => $debt->status === 'unpaid' ? 'paid' : 'unpaid',
        ]);

        return redirect()->route('debts.index')->with('success', 'Status transaksi diperbarui!');
    }

    public function destroy(Debt $debt)
    {
        $debt->delete();

        return redirect()->route('debts.index')->with('success', 'Catatan dihapus!');
    }
}