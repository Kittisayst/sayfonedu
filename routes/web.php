<?php

use App\Http\Controllers\PrintController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect("/admin");
});

Route::middleware(['auth'])->group(function () {
    // Print Receipt Routes
    Route::get('/print/receipt/{payment}', [PrintController::class, 'receipt'])
        ->name('print.receipt');
        
    Route::get('/print/receipt/{payment}/pdf', [PrintController::class, 'receiptPdf'])
        ->name('print.receipt.pdf');
        
    Route::get('/print/receipt/{payment}/preview', [PrintController::class, 'receiptPreview'])
        ->name('print.receipt.preview');

    // Additional Print Routes
    Route::get('/print/payment-summary/{from}/{to}', [PrintController::class, 'paymentSummary'])
        ->name('print.payment.summary');
        
    Route::get('/print/student-statement/{student}', [PrintController::class, 'studentStatement'])
        ->name('print.student.statement');
});