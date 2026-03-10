# 📚 Alur Implementasi QRIS Xoftware Pay - E-Canteen

**Last Updated**: 3 Maret 2026  
**Status**: ✅ Setup Complete, ⏳ Awaiting Merchant Approval

---

## 🎯 Overview

Dokumentasi lengkap implementasi Xoftware Pay QRIS payment gateway untuk sistem top-up saldo E-Canteen. Mencakup semua langkah dari setup API hingga testing pembayaran.

---

## 📋 Table of Contents

1. [Phase 1: Setup & Konfigurasi](#phase-1-setup--konfigurasi)
2. [Phase 2: Database & Model](#phase-2-database--model)
3. [Phase 3: Service Layer API](#phase-3-service-layer-api)
4. [Phase 4: Controller & Routes](#phase-4-controller--routes)
5. [Phase 5: Views & Frontend](#phase-5-views--frontend)
6. [Phase 6: Webhook Integration](#phase-6-webhook-integration)
7. [Phase 7: Ngrok & External Testing](#phase-7-ngrok--external-testing)
8. [Phase 8: Merchant Approval](#phase-8-merchant-approval)
9. [Testing & Deployment](#testing--deployment)

---

## Phase 1: Setup & Konfigurasi

### 1.1 Dapatkan Credentials Xoftware

**Langkah:**

1. Buka https://dashboard.xoftware.id
2. Login dengan akun Anda
3. Navigasi ke **Settings** atau **API Keys**
4. Copy:
    - API Key
    - Merchant ID

**Hasil:**

```
API Key: OTaX_sSz3QBfc5lK6DL-s4Km47mqEvZmFmmSK-6Jh_0
Merchant ID: 19
Base URL: https://payment.xoftware.id/v1/api (sandbox)
```

### 1.2 Update Environment Variables

**File**: `.env`

```env
# Xoftware Pay Configuration
XOFTWARE_API_KEY=OTaX_sSz3QBfc5lK6DL-s4Km47mqEvZmFmmSK-6Jh_0
XOFTWARE_MERCHANT_ID=19
XOFTWARE_BASE_URL=https://payment.xoftware.id/v1/api
XOFTWARE_NOTIFY_URL=https://sectorial-swishiest-grisel.ngrok-free.dev/webhook/xoftware
```

**File**: `.env.example`

```env
XOFTWARE_API_KEY=
XOFTWARE_MERCHANT_ID=
XOFTWARE_BASE_URL=https://payment.xoftware.id/v1/api
XOFTWARE_NOTIFY_URL=
```

### 1.3 Update Config Services

**File**: `config/services.php`

```php
'xoftware' => [
    'api_key' => env('XOFTWARE_API_KEY'),
    'merchant_id' => env('XOFTWARE_MERCHANT_ID'),
    'base_url' => env('XOFTWARE_BASE_URL', 'https://payment.xoftware.id/v1/api'),
    'notify_url' => env('XOFTWARE_NOTIFY_URL'),
],
```

**Command:**

```bash
php artisan config:clear
```

---

## Phase 2: Database & Model

### 2.1 Create Transactions Table Migration

**File**: `database/migrations/2026_03_03_035311_create_transactions_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('ref_id')->unique();
            $table->string('transaction_id')->nullable();
            $table->string('payment_intent_id')->nullable();
            $table->string('channel_code')->default('QRIS');
            $table->decimal('amount', 15, 2);
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->string('payment_status')->nullable();
            $table->text('qris_text')->nullable();
            $table->string('qris_url')->nullable();
            $table->string('provider_ref')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('ref_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
```

**Command:**

```bash
php artisan migrate
```

### 2.2 Create Transaction Model

**File**: `app/Models/Transaction.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'ref_id',
        'transaction_id',
        'payment_intent_id',
        'channel_code',
        'amount',
        'status',
        'payment_status',
        'qris_text',
        'qris_url',
        'provider_ref',
        'expires_at',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expires_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

### 2.3 Update User Model

**File**: `app/Models/User.php`

Tambahkan relationship:

```php
public function transactions(): HasMany
{
    return $this->hasMany(Transaction::class);
}
```

---

## Phase 3: Service Layer API

### 3.1 Create XoftwarePayService

**File**: `app/Services/XoftwarePayService.php`

Service ini handle semua komunikasi dengan Xoftware API:

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class XoftwarePayService
{
    private $apiKey;
    private $merchantId;
    private $baseUrl;
    private $notifyUrl;

    public function __construct()
    {
        $this->apiKey = config('services.xoftware.api_key');
        $this->merchantId = config('services.xoftware.merchant_id');
        $this->baseUrl = config('services.xoftware.base_url');
        $this->notifyUrl = config('services.xoftware.notify_url');
    }

    // Generate HMAC-SHA256 signature
    private function generateSignature($method, $path, $body = null)
    {
        $timestamp = (string) intval(microtime(true) * 1000);
        $bodyString = $body ? json_encode($body) : '';
        $message = $timestamp . "\n" . $method . "\n" . $path . "\n" . $bodyString;

        $signature = hash_hmac('sha256', $message, $this->apiKey, true);
        $signature = bin2hex($signature);

        return [
            'timestamp' => $timestamp,
            'signature' => $signature,
        ];
    }

    // Make HTTP request dengan signature
    private function makeRequest($method, $path, $body = null)
    {
        $signatureData = $this->generateSignature($method, $path, $body);
        $url = $this->baseUrl . $path;

        $headers = [
            'X-API-Key' => $this->apiKey,
            'X-Timestamp' => $signatureData['timestamp'],
            'X-Signature' => $signatureData['signature'],
            'Content-Type' => 'application/json',
        ];

        Log::info('Xoftware API Request', [
            'method' => $method,
            'url' => $url,
            'body' => $body,
        ]);

        try {
            if ($method === 'GET') {
                $response = Http::withHeaders($headers)->get($url);
            } elseif ($method === 'POST') {
                $response = Http::withHeaders($headers)->post($url, $body ?? []);
            }

            Log::info('Xoftware API Response', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Xoftware API Error', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    // Create QRIS transaction
    public function createTransaction($merchant_id, $ref_id, $amount, $channel_code, $notify_url)
    {
        return $this->makeRequest('POST', '/transactions', [
            'merchant_id' => $merchant_id,
            'ref_id' => $ref_id,
            'amount' => $amount,
            'channel_code' => $channel_code,
            'expires_in_minutes' => 15,
            'notify_url' => $notify_url,
        ]);
    }

    // Check transaction status
    public function getTransactionStatus($ref_id)
    {
        return $this->makeRequest('POST', '/transactions/status', [
            'merchant_id' => $this->merchantId,
            'ref_id' => $ref_id,
        ]);
    }

    // Cancel transaction
    public function cancelTransaction($ref_id)
    {
        return $this->makeRequest('POST', '/transactions/cancel', [
            'merchant_id' => $this->merchantId,
            'ref_id' => $ref_id,
        ]);
    }

    // Get balance
    public function getBalance()
    {
        return $this->makeRequest('POST', '/balance', [
            'merchant_id' => $this->merchantId,
        ]);
    }
}
```

**Key Features:**

- ✅ HMAC-SHA256 signature generation per Xoftware spec
- ✅ Automatic request logging
- ✅ Error handling & logging
- ✅ All required methods: create, status, cancel, balance

---

## Phase 4: Controller & Routes

### 4.1 Create TopUpController

**File**: `app/Http/Controllers/TopUpController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\XoftwarePayService;
use Illuminate\Http\Request;

class TopUpController extends Controller
{
    protected $xoftwarePayService;

    public function __construct(XoftwarePayService $xoftwarePayService)
    {
        $this->xoftwarePayService = $xoftwarePayService;
    }

    // Tampilkan form top up
    public function index()
    {
        return view('topup.index');
    }

    // Buat QRIS & redirect ke display page
    public function createQris(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:10000|max:1000000',
        ]);

        $amount = (int) $validated['amount'];
        $user = auth()->user();

        try {
            $refId = 'topup_' . auth()->id() . '_' . time();

            // Call Xoftware API
            $response = $this->xoftwarePayService->createTransaction(
                merchant_id: config('services.xoftware.merchant_id'),
                ref_id: $refId,
                amount: $amount,
                channel_code: 'QRIS',
                notify_url: config('services.xoftware.notify_url')
            );

            // Save to database
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'ref_id' => $refId,
                'transaction_id' => $response['transaction_id'] ?? null,
                'payment_intent_id' => $response['payment_intent_id'] ?? null,
                'channel_code' => 'QRIS',
                'amount' => $amount,
                'status' => 'pending',
                'payment_status' => $response['payment_status'] ?? 'initiated',
                'qris_text' => $response['qris_text'] ?? null,
                'qris_url' => $response['qris_url'] ?? null,
                'provider_ref' => $response['provider_ref'] ?? null,
                'expires_at' => $response['expires_at'] ?? now()->addMinutes(15),
                'metadata' => $response,
            ]);

            return redirect()->route('topup.show', $transaction->id);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal membuat QRIS: ' . $e->getMessage());
        }
    }

    // Tampilkan QRIS page
    public function show(Transaction $transaction)
    {
        if ($transaction->user_id !== auth()->id()) {
            abort(403);
        }

        return view('topup.qris', ['transaction' => $transaction]);
    }

    // Check status via AJAX
    public function checkStatus(Transaction $transaction)
    {
        if ($transaction->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $response = $this->xoftwarePayService->getTransactionStatus(
                ref_id: $transaction->ref_id
            );

            $status = $response['payment_status'] ?? $transaction->payment_status;

            $transaction->update([
                'payment_status' => $status,
                'status' => in_array($status, ['success', 'completed']) ? 'completed' : 'pending',
            ]);

            // Credit balance if successful
            if (in_array($status, ['success', 'completed']) && $transaction->status !== 'completed') {
                $user = $transaction->user;
                $user->balance += $transaction->amount;
                $user->save();

                $transaction->update(['status' => 'completed']);
            }

            return response()->json([
                'status' => $transaction->status,
                'payment_status' => $transaction->payment_status,
                'amount' => $transaction->amount,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Cancel transaction
    public function cancel(Transaction $transaction)
    {
        if ($transaction->user_id !== auth()->id()) {
            abort(403);
        }

        try {
            $this->xoftwarePayService->cancelTransaction(
                ref_id: $transaction->ref_id
            );

            $transaction->update(['status' => 'cancelled']);

            return redirect()->route('topup.index')
                ->with('info', 'Top up dibatalkan');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal membatalkan top up: ' . $e->getMessage());
        }
    }
}
```

### 4.2 Create XoftwareWebhookController

**File**: `app/Http/Controllers/XoftwareWebhookController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class XoftwareWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Handle GET requests (health checks)
        if ($request->isMethod('get')) {
            return response()->json(['status' => 'ok', 'webhook' => 'xoftware'], 200);
        }

        Log::info('Xoftware Webhook Received', [
            'payload' => $request->all(),
            'headers' => $request->headers->all(),
        ]);

        try {
            $ref_id = $request->input('ref_id');
            $payment_status = $request->input('payment_status');

            if (!$ref_id) {
                Log::warning('Xoftware Webhook: Missing ref_id');
                return response()->json(['error' => 'Missing ref_id'], 400);
            }

            // Find transaction
            $transaction = Transaction::where('ref_id', $ref_id)->first();

            if (!$transaction) {
                Log::warning('Xoftware Webhook: Transaction not found', ['ref_id' => $ref_id]);
                return response()->json(['error' => 'Transaction not found'], 404);
            }

            // Update transaction
            $transaction->update([
                'payment_status' => $payment_status,
                'status' => in_array($payment_status, ['success', 'completed']) ? 'completed' : 'pending',
                'metadata' => $request->all(),
            ]);

            // Credit balance if successful
            if (in_array($payment_status, ['success', 'completed']) && $transaction->status === 'completed') {
                $user = $transaction->user;

                if ($user->balance < $transaction->amount) {
                    $user->balance += $transaction->amount;
                    $user->save();

                    Log::info('Xoftware Webhook: Balance credited', [
                        'user_id' => $user->id,
                        'amount' => $transaction->amount,
                        'new_balance' => $user->balance,
                    ]);
                }
            }

            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            Log::error('Xoftware Webhook Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
```

### 4.3 Update Routes

**File**: `routes/web.php`

```php
<?php

use App\Http\Controllers\TopUpController;
use App\Http\Controllers\XoftwareWebhookController;
// ... other imports

Route::middleware('auth')->group(function () {
    // ... existing routes

    // Top Up routes
    Route::get('/topup', [TopUpController::class, 'index'])->name('topup.index');
    Route::post('/topup/qris', [TopUpController::class, 'createQris'])->name('topup.qris');
    Route::get('/topup/{transaction}', [TopUpController::class, 'show'])->name('topup.show');
    Route::post('/topup/{transaction}/check-status', [TopUpController::class, 'checkStatus'])->name('topup.check-status');
    Route::delete('/topup/{transaction}/cancel', [TopUpController::class, 'cancel'])->name('topup.cancel');
});

// Webhook route (outside auth middleware)
Route::match(['get', 'post'], '/webhook/xoftware', [XoftwareWebhookController::class, 'handle']);

require __DIR__.'/auth.php';
```

### 4.4 Update Middleware Configuration

**File**: `bootstrap/app.php`

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'admin' => \App\Http\Middleware\AdminOnly::class,
    ]);

    // Exclude webhook route from CSRF verification
    $middleware->validateCsrfTokens(except: [
        'webhook/xoftware',
    ]);
})
```

---

## Phase 5: Views & Frontend

### 5.1 Top Up Form View

**File**: `resources/views/topup/index.blade.php`

```blade
<x-app-layout>
    <x-slot name="header">Top Up Saldo</x-slot>

    <div class="px-4 sm:px-6 lg:px-8 py-6">
        <!-- Balance Card -->
        <div class="bg-gradient-to-r from-primary-600 to-primary-800 text-white rounded-lg p-8 mb-8 shadow-lg">
            <p class="text-sm opacity-90">Saldo Anda Saat Ini</p>
            <p class="text-4xl font-bold mt-2">Rp {{ number_format(auth()->user()->balance, 0, ',', '.') }}</p>
        </div>

        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Isi Saldo E-Canteen</h2>

                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700 mb-6">
                        <ul class="list-disc ps-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('topup.qris') }}" class="space-y-6">
                    @csrf

                    <!-- Quick Amount Buttons -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Pilih Nominal Cepat</label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            <button type="button" onclick="setAmount(10000)" class="quick-amount-btn bg-gray-100 hover:bg-primary-100 border-2 border-gray-300 hover:border-primary-600 rounded-lg p-4 text-center transition-all">
                                <p class="text-lg font-bold text-gray-900">Rp 10.000</p>
                            </button>
                            <button type="button" onclick="setAmount(20000)" class="quick-amount-btn bg-gray-100 hover:bg-primary-100 border-2 border-gray-300 hover:border-primary-600 rounded-lg p-4 text-center transition-all">
                                <p class="text-lg font-bold text-gray-900">Rp 20.000</p>
                            </button>
                            <button type="button" onclick="setAmount(50000)" class="quick-amount-btn bg-gray-100 hover:bg-primary-100 border-2 border-gray-300 hover:border-primary-600 rounded-lg p-4 text-center transition-all">
                                <p class="text-lg font-bold text-gray-900">Rp 50.000</p>
                            </button>
                            <button type="button" onclick="setAmount(100000)" class="quick-amount-btn bg-gray-100 hover:bg-primary-100 border-2 border-gray-300 hover:border-primary-600 rounded-lg p-4 text-center transition-all">
                                <p class="text-lg font-bold text-gray-900">Rp 100.000</p>
                            </button>
                            <button type="button" onclick="setAmount(200000)" class="quick-amount-btn bg-gray-100 hover:bg-primary-100 border-2 border-gray-300 hover:border-primary-600 rounded-lg p-4 text-center transition-all">
                                <p class="text-lg font-bold text-gray-900">Rp 200.000</p>
                            </button>
                            <button type="button" onclick="setAmount(500000)" class="quick-amount-btn bg-gray-100 hover:bg-primary-100 border-2 border-gray-300 hover:border-primary-600 rounded-lg p-4 text-center transition-all">
                                <p class="text-lg font-bold text-gray-900">Rp 500.000</p>
                            </button>
                        </div>
                    </div>

                    <!-- Custom Amount Input -->
                    <div>
                        <label for="amount" class="block text-sm font-semibold text-gray-700 mb-2">Atau Masukkan Nominal Manual</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-semibold">Rp</span>
                            <input
                                type="number"
                                name="amount"
                                id="amount"
                                min="10000"
                                max="1000000"
                                step="1000"
                                class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-lg font-semibold"
                                placeholder="10.000"
                                value="{{ old('amount') }}"
                                required>
                        </div>
                        <p class="text-xs text-gray-600 mt-2">Minimal top up Rp 10.000, maksimal Rp 1.000.000</p>
                    </div>

                    <!-- Summary -->
                    <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Saldo Sekarang:</span>
                            <span class="font-semibold text-gray-900">Rp {{ number_format(auth()->user()->balance, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Nominal Top Up:</span>
                            <span class="font-semibold text-primary-600" id="topup-display">Rp 0</span>
                        </div>
                        <div class="border-t border-gray-300 pt-2 flex justify-between">
                            <span class="font-semibold text-gray-900">Saldo Setelah Top Up:</span>
                            <span class="font-bold text-lg text-primary-600" id="new-balance-display">Rp {{ number_format(auth()->user()->balance, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-4">
                        <a href="{{ route('dashboard') }}" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-lg transition text-center">
                            Batal
                        </a>
                        <button type="submit" class="flex-1 bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 px-6 rounded-lg transition transform hover:scale-105">
                            📱 Buat QRIS & Bayar
                        </button>
                    </div>
                </form>
            </div>

            <!-- Info Card -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="font-semibold text-blue-900 mb-2">ℹ️ Informasi Top Up</h3>
                <ul class="text-sm text-blue-800 space-y-1 list-disc ps-5">
                    <li>Top up menggunakan QRIS payment gateway</li>
                    <li>Saldo akan langsung masuk ke akun Anda setelah pembayaran</li>
                    <li>Saldo dapat digunakan untuk memesan makanan dan minuman</li>
                    <li>Minimal top up Rp 10.000, maksimal Rp 1.000.000</li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        const amountInput = document.getElementById('amount');
        const topupDisplay = document.getElementById('topup-display');
        const newBalanceDisplay = document.getElementById('new-balance-display');
        const currentBalance = {{ auth()->user()->balance }};

        function setAmount(amount) {
            amountInput.value = amount;
            updateDisplay();

            document.querySelectorAll('.quick-amount-btn').forEach(btn => {
                btn.classList.remove('bg-primary-100', 'border-primary-600');
                btn.classList.add('bg-gray-100', 'border-gray-300');
            });
            event.target.closest('.quick-amount-btn').classList.remove('bg-gray-100', 'border-gray-300');
            event.target.closest('.quick-amount-btn').classList.add('bg-primary-100', 'border-primary-600');
        }

        function updateDisplay() {
            const amount = parseFloat(amountInput.value) || 0;
            const newBalance = currentBalance + amount;

            topupDisplay.textContent = 'Rp ' + amount.toLocaleString('id-ID');
            newBalanceDisplay.textContent = 'Rp ' + newBalance.toLocaleString('id-ID');
        }

        amountInput.addEventListener('input', updateDisplay);
        updateDisplay();
    </script>
</x-app-layout>
```

### 5.2 QRIS Display View

**File**: `resources/views/topup/qris.blade.php`

```blade
<x-app-layout>
    <x-slot name="header">Pembayaran QRIS</x-slot>

    <div class="px-4 sm:px-6 lg:px-8 py-6">
        <div class="max-w-4xl mx-auto">
            @if ($transaction->status === 'completed')
                <!-- Success State -->
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg p-8 text-center border-2 border-green-300">
                    <div class="text-6xl mb-4">✅</div>
                    <h2 class="text-3xl font-bold text-green-900 mb-2">Pembayaran Berhasil!</h2>
                    <p class="text-green-700 mb-4">Saldo Anda telah berhasil ditambahkan sebesar Rp {{ number_format($transaction->amount, 0, ',', '.') }}</p>
                    <p class="text-sm text-green-600 mb-6">Saldo baru Anda: Rp {{ number_format(auth()->user()->balance, 0, ',', '.') }}</p>
                    <a href="{{ route('dashboard') }}" class="inline-block bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-8 rounded-lg transition">
                        Kembali ke Dashboard
                    </a>
                </div>
            @elseif ($transaction->status === 'failed' || $transaction->status === 'cancelled')
                <!-- Failed State -->
                <div class="bg-gradient-to-r from-red-50 to-rose-50 rounded-lg p-8 text-center border-2 border-red-300">
                    <div class="text-6xl mb-4">❌</div>
                    <h2 class="text-3xl font-bold text-red-900 mb-2">Pembayaran Gagal</h2>
                    <p class="text-red-700 mb-6">{{ $transaction->status === 'cancelled' ? 'Transaksi dibatalkan' : 'Terjadi kesalahan saat memproses pembayaran' }}</p>
                    <div class="flex gap-4 justify-center">
                        <a href="{{ route('topup.index') }}" class="inline-block bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-8 rounded-lg transition">
                            Coba Lagi
                        </a>
                        <a href="{{ route('dashboard') }}" class="inline-block bg-gray-400 hover:bg-gray-500 text-white font-semibold py-3 px-8 rounded-lg transition">
                            Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            @else
                <!-- Pending State -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-primary-600 to-primary-800 text-white p-6">
                        <h2 class="text-2xl font-bold">Selesaikan Pembayaran Anda</h2>
                        <p class="text-primary-100 mt-2">Scan QR Code di bawah dengan aplikasi dompet digital Anda</p>
                    </div>

                    <div class="p-8 space-y-8">
                        <!-- Amount -->
                        <div class="bg-gray-50 rounded-lg p-6 text-center">
                            <p class="text-gray-600 text-sm">Jumlah yang Harus Dibayar</p>
                            <p class="text-4xl font-bold text-primary-600 mt-2">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</p>
                        </div>

                        <!-- QR Code Display -->
                        <div class="flex flex-col items-center">
                            <div class="bg-white p-6 border-4 border-gray-200 rounded-lg">
                                <div id="qrcode" class="flex items-center justify-center" style="width: 300px; height: 300px;"></div>
                            </div>
                            @if ($transaction->qris_text)
                                <p class="text-xs text-gray-500 mt-4 text-center">
                                    <span class="font-semibold">QRIS Text:</span><br>
                                    <code class="text-gray-700">{{ substr($transaction->qris_text, 0, 50) }}...</code>
                                </p>
                            @endif
                        </div>

                        <!-- Status -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-center gap-3">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-8 w-8">
                                    <div id="status-spinner" class="animate-spin h-6 w-6 text-blue-600">
                                        <svg class="h-full w-full" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <p class="font-semibold text-blue-900">Menunggu Pembayaran...</p>
                                <p id="status-text" class="text-sm text-blue-700">Silakan selesaikan pembayaran melalui scan QR Code</p>
                            </div>
                        </div>

                        <!-- Instructions -->
                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-6">
                            <h3 class="font-bold text-amber-900 mb-4">📱 Cara Pembayaran QRIS:</h3>
                            <ol class="space-y-3 text-sm text-amber-800">
                                <li class="flex gap-3">
                                    <span class="flex-shrink-0 font-bold bg-amber-300 text-amber-900 w-6 h-6 flex items-center justify-center rounded-full">1</span>
                                    <span>Buka aplikasi dompet digital Anda (OVO, GCash, Dana, dll)</span>
                                </li>
                                <li class="flex gap-3">
                                    <span class="flex-shrink-0 font-bold bg-amber-300 text-amber-900 w-6 h-6 flex items-center justify-center rounded-full">2</span>
                                    <span>Pilih menu "Scan QRIS" atau "Pembayaran"</span>
                                </li>
                                <li class="flex gap-3">
                                    <span class="flex-shrink-0 font-bold bg-amber-300 text-amber-900 w-6 h-6 flex items-center justify-center rounded-full">3</span>
                                    <span>Arahkan kamera ponsel ke QR Code di atas</span>
                                </li>
                                <li class="flex gap-3">
                                    <span class="flex-shrink-0 font-bold bg-amber-300 text-amber-900 w-6 h-6 flex items-center justify-center rounded-full">4</span>
                                    <span>Konfirmasi dan masukkan PIN atau password</span>
                                </li>
                                <li class="flex gap-3">
                                    <span class="flex-shrink-0 font-bold bg-amber-300 text-amber-900 w-6 h-6 flex items-center justify-center rounded-full">5</span>
                                    <span>Tunggu notifikasi pembayaran berhasil</span>
                                </li>
                            </ol>
                        </div>

                        <!-- Transaction Info -->
                        <div class="grid grid-cols-2 gap-4 bg-gray-50 rounded-lg p-4 text-sm">
                            <div>
                                <p class="text-gray-600">Ref ID</p>
                                <p class="font-semibold text-gray-900">{{ $transaction->ref_id }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Waktu Berlaku</p>
                                <p class="font-semibold text-gray-900" id="expires-text">{{ $transaction->expires_at->format('H:i') }}</p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-4">
                            <button
                                type="button"
                                id="check-status-btn"
                                onclick="checkStatus()"
                                class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-lg transition">
                                🔄 Cek Status
                            </button>
                            <form action="{{ route('topup.cancel', $transaction->id) }}" method="POST" class="flex-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white font-semibold py-3 px-6 rounded-lg transition">
                                    ❌ Batalkan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Info Card -->
                <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h3 class="font-semibold text-blue-900 mb-2">ℹ️ Informasi Pembayaran QRIS</h3>
                    <ul class="text-sm text-blue-800 space-y-1 list-disc ps-5">
                        <li>QRIS berlaku selama 15 menit</li>
                        <li>Pembayaran dapat dilakukan melalui berbagai aplikasi dompet digital</li>
                        <li>Status pembayaran akan diperbarui secara otomatis</li>
                        <li>Jika pembayaran berhasil, saldo Anda akan langsung bertambah</li>
                    </ul>
                </div>
            @endif
        </div>
    </div>

    @if ($transaction->status === 'pending')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
        <script>
            // Generate QR Code
            @if ($transaction->qris_text)
                new QRCode(document.getElementById('qrcode'), {
                    text: '{{ $transaction->qris_text }}',
                    width: 300,
                    height: 300,
                    colorDark: '#000000',
                    colorLight: '#ffffff',
                    correctLevel: QRCode.CorrectLevel.H
                });
            @endif

            // Auto-check status every 5 seconds
            let checkInterval = setInterval(checkStatus, 5000);

            function checkStatus() {
                fetch('{{ route('topup.check-status', $transaction->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Status Check Response:', data);

                    if (data.status === 'completed') {
                        clearInterval(checkInterval);
                        document.getElementById('status-spinner').innerHTML = '✅';
                        document.getElementById('status-text').textContent = 'Pembayaran berhasil! Sedang memproses...';
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    } else if (data.status === 'failed') {
                        clearInterval(checkInterval);
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error checking status:', error);
                });
            }
        </script>
    @endif
</x-app-layout>
```

---

## Phase 6: Webhook Integration

### 6.1 Webhook Endpoint Testing

**Local Test (Development):**

```bash
curl -X POST http://127.0.0.1:8000/webhook/xoftware \
  -H "Content-Type: application/json" \
  -d '{"ref_id":"test_webhook","payment_status":"success","amount":10000}'
```

**Response:**

```json
{ "error": "Transaction not found" }
```

← Normal, karena test_webhook tidak ada di database.

### 6.2 Ngrok Tunnel Setup

**Install Ngrok:**

```bash
# Download dari https://ngrok.com/download
# atau di Windows: winget install ngrok
```

**Start Tunnel:**

```bash
ngrok http 8000
```

**Output:**

```
Forwarding                    https://sectorial-swishiest-grisel.ngrok-free.dev -> http://localhost:8000
```

**Update in .env:**

```env
XOFTWARE_NOTIFY_URL=https://sectorial-swishiest-grisel.ngrok-free.dev/webhook/xoftware
```

**Clear Config:**

```bash
php artisan config:clear
```

---

## Phase 7: Ngrok & External Testing

### 7.1 Verify Webhook is Online

**GET Request:**

```bash
curl https://sectorial-swishiest-grisel.ngrok-free.dev/webhook/xoftware
```

**Expected Response:**

```json
{ "status": "ok", "webhook": "xoftware" }
```

### 7.2 Test with Real Transaction Data

```bash
curl -X POST https://sectorial-swishiest-grisel.ngrok-free.dev/webhook/xoftware \
  -H "Content-Type: application/json" \
  -d '{
    "ref_id":"topup_1_1234567890",
    "transaction_id":"txn_abc123",
    "payment_status":"success",
    "amount":50000
  }'
```

---

## Phase 8: Merchant Approval

### 8.1 Submit Integration Request

**Steps:**

1. Login ke https://dashboard.xoftware.id
2. Navigasi ke **Integration** section
3. Klik **Ajukan Integrasi** / **Add New Integration**
4. Isi form:
    - **Jenis Aplikasi**: Web
    - **URL / Domain**: https://sectorial-swishiest-grisel.ngrok-free.dev
    - **Deskripsi**: [Use provided template]
5. Klik **SUBMIT REQUEST**

### 8.2 Approval Timeline

| Stage           | Timeline    | Status        |
| --------------- | ----------- | ------------- |
| Submit Request  | T+0         | ⏳ Pending    |
| Review by Admin | T+1-2 hours | 📋 Processing |
| Approval Email  | T+1-2 hours | ✅ Approved   |
| Start Testing   | T+2 hours   | 🚀 Ready      |

### 8.3 Status Checks

**In Xoftware Dashboard:**

- Status: **Pending** → Waiting approval
- Status: **Approved** → Ready for production
- Status: **Rejected** → Check email for reason

---

## Testing & Deployment

### Testing Checklist

#### Phase 1: Local Testing (Before Approval)

- [ ] Top-up form loads correctly
- [ ] Amount validation works (min 10k, max 1M)
- [ ] Quick amount buttons work
- [ ] Manual input accepts Rp formatting
- [ ] Balance preview updates correctly
- [ ] Form submits without errors

#### Phase 2: API Integration Testing

- [ ] Xoftware API credentials configured
- [ ] Service layer methods callable
- [ ] HTTP requests logged properly
- [ ] Error handling works
- [ ] Database transactions created

#### Phase 3: Webhook Testing (Before Approval)

- [ ] Webhook endpoint responds to GET
- [ ] Webhook endpoint responds to POST
- [ ] CSRF exemption working
- [ ] Webhook logs appear in laravel.log
- [ ] Ngrok tunnel stable

#### Phase 4: End-to-End Testing (After Approval)

- [ ] QRIS generation successful
- [ ] QR code displays correctly
- [ ] Auto-check status works (polling every 5 sec)
- [ ] Cancel transaction works
- [ ] Webhook receives payment success
- [ ] Balance auto-updates on success
- [ ] User redirected to dashboard
- [ ] Failed payment handled gracefully

### Monitoring & Logs

**View API Logs:**

```bash
tail -f storage/logs/laravel.log | grep -i xoftware
```

**View Webhook Activity:**

```bash
tail -f storage/logs/laravel.log | grep -i webhook
```

**Watch Status Updates:**

```bash
tail -f storage/logs/laravel.log
```

### Common Issues & Solutions

**Issue: "Merchant not verified" (HTTP 403)**

- **Cause**: Merchant approval pending
- **Solution**: Wait for approval email from Xoftware
- **Timeline**: 1-2 hours from submission

**Issue: Webhook endpoint offline**

- **Cause**: Ngrok tunnel down or port mismatch
- **Solution**: Restart ngrok on port 8000
- **Command**: `ngrok http 8000`

**Issue: CSRF token mismatch**

- **Cause**: Webhook not exempted from CSRF
- **Solution**: Verify bootstrap/app.php configuration
- **Check**: `validateCsrfTokens(except: ['webhook/xoftware'])`

**Issue: Balance not updating**

- **Cause**: Webhook signature mismatch or double-credit check
- **Solution**: Check logs for webhook errors
- **Command**: `tail storage/logs/laravel.log | grep -i balance`

---

## 🎯 Success Criteria

✅ **All Completed:**

1. ✅ API credentials configured
2. ✅ Database schema created
3. ✅ Service layer implemented
4. ✅ Controllers & routes defined
5. ✅ Views created (form + QRIS display)
6. ✅ Webhook handler ready
7. ✅ Ngrok tunnel active
8. ✅ Merchant approval submitted
9. ⏳ Awaiting approval (1-2 hours)
10. 🚀 Ready for payment testing after approval

---

## 📞 Support & References

- **Xoftware Dashboard**: https://dashboard.xoftware.id
- **API Documentation**: Contact Xoftware support
- **QRIS Standard**: https://www.bi.go.id/en/
- **Ngrok Docs**: https://ngrok.com/docs
- **Laravel Docs**: https://laravel.com/docs

---

## 🚀 Next Steps

**After Merchant Approval:**

1. Verify approval email from Xoftware
2. Test QRIS generation: `POST /topup/qris`
3. Scan QR with e-wallet
4. Verify balance update
5. Monitor logs for any issues
6. Deploy to production if testing successful

**Current Status**: ✅ Implementation Complete, ⏳ Awaiting Merchant Approval

---

**Good luck! 🎉 Doain approval cepat!**
