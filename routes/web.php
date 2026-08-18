<?php

use App\Http\Controllers\DebtController;

Route::get('/', [DebtController::class, 'index'])->name('debts.index');
Route::post('/debts', [DebtController::class, 'store'])->name('debts.store');
Route::patch('/debts/{debt}/status', [DebtController::class, 'updateStatus'])->name('debts.updateStatus');
Route::delete('/debts/{debt}', [DebtController::class, 'destroy'])->name('debts.destroy');