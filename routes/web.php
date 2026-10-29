<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanyProfileController;
use App\Http\Controllers\TypeCertificateController;
use App\Http\Controllers\CertificationController;
use App\Http\Controllers\ClientController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

Route::get('/test-wa', function () {
    $response = Http::withHeaders([
    'Authorization' => env('FONNTE_TOKEN')
])->post('https://api.fonnte.com/send', [
    'target' => '6281384282314',
    'message' => 'Test WA'
]);

dd($response->status(), $response->body());

    return $response->body();
});

Route::get('/', function () {
    return view('company-profile');
})->name('company-profile');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/company', [CompanyController::class, 'index'])->name('company.index');
    Route::post('/company', [CompanyController::class, 'store'])->name('company.store');
    Route::patch('/company/{id}', [CompanyController::class, 'update'])->name('company.update');
    Route::delete('/company/{id}', [CompanyController::class, 'destroy'])->name('company.destroy');

    Route::get('/type-certificate', [TypeCertificateController::class, 'index'])->name('type-certificate.index');
    Route::post('/type-certificate', [TypeCertificateController::class, 'store'])->name('type-certificate.store');
    Route::patch('/type-certificate/{id}', [TypeCertificateController::class, 'update'])->name('type-certificate.update');
    Route::delete('/type-certificate/{id}', [TypeCertificateController::class, 'destroy'])->name('type-certificate.destroy');
    
    Route::get('/certification', [CertificationController::class, 'index'])->name('certification.index');
    Route::post('/certification', [CertificationController::class, 'store'])->name('certification.store');
    Route::patch('/certification/{id}', [CertificationController::class, 'update'])->name('certification.update');
    Route::delete('/certification/{id}', [CertificationController::class, 'destroy'])->name('certification.destroy');
    Route::get('/certification/pdf/{id}', [CertificationController::class, 'generatePdf'])->name('certification.generatePdf');
    Route::get('/certification/pdf-draft/{id}', [CertificationController::class, 'generatePdfDraft'])->name('certification.generatePdfDraft');
    Route::get('/certification/image/{id}', [CertificationController::class, 'generateCertificateImage'])->name('certification.generateCertificateImage');

    Route::get('/client', [ClientController::class, 'index'])->name('client.index');
    Route::post('/client', [ClientController::class, 'store'])->name('client.store');
    Route::patch('/client/{id}', [ClientController::class, 'update'])->name('client.update');
    Route::delete('/client/{id}', [ClientController::class, 'destroy'])->name('client.destroy');
    Route::get('/client/photo/{id}', [ClientController::class, 'photo'])->name('client.photo');
    Route::get('/client/document/{id}', [ClientController::class, 'document'])->name('client.document');
    Route::get('/client/attachment/{id}/{type}', [ClientController::class, 'attachment'])->name('client.attachment');

    Route::get('/profile', [CompanyProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [CompanyProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [CompanyProfileController::class, 'destroy'])->name('profile.destroy');
});
    Route::get('/certification/{id}', [CertificationController::class, 'show'])->name('certification.show');
    Route::get('/verify/{code}', [CertificationController::class, 'verify'])->name('certification.verify');

require __DIR__.'/auth.php';
