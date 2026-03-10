# Xoftware Pay QRIS Integration Setup Guide

## Overview
This guide covers the setup and testing of Xoftware Pay QRIS integration for the E-Canteen top-up feature.

## Configuration

### 1. Environment Variables
Add the following to your `.env` file:

```env
XOFTWARE_API_KEY=OTaX_sSz3QBfc5lK6DL-s4Km47mqEvZmFmmSK-6Jh_0
XOFTWARE_MERCHANT_ID=19
XOFTWARE_BASE_URL=https://payment.xoftware.id/v1/api
XOFTWARE_NOTIFY_URL=https://your-ngrok-url.ngrok-free.dev/webhook/xoftware
```

### 2. Database Migration
Run the migration to create the transactions table:
```bash
php artisan migrate
```

### 3. Clear Config Cache
After updating environment variables:
```bash
php artisan config:clear
```

## Xoftware Pay Service

The `XoftwarePayService` handles all API interactions with Xoftware:

### Key Features:
- **HMAC-SHA256 Signature Generation**: Automatically generates secure signatures for each request
- **Transaction Management**: Create, check status, and cancel transactions
- **Error Logging**: Comprehensive logging of all API requests and responses
- **Webhook Verification**: Verify incoming webhook signatures

### Usage Example:
```php
$xoftwareService = app(XoftwarePayService::class);

// Create a transaction
$response = $xoftwareService->createTransaction(
    merchant_id: config('services.xoftware.merchant_id'),
    ref_id: 'topup_1_1234567890',
    amount: 100000,
    channel_code: 'QRIS',
    notify_url: config('services.xoftware.notify_url')
);

// Check transaction status
$status = $xoftwareService->getTransactionStatus(
    ref_id: 'topup_1_1234567890'
);
```

## API Reference

### Create Transaction
**Endpoint**: `POST /transactions`

**Parameters**:
- `merchant_id`: Your merchant ID from Xoftware
- `ref_id`: Unique reference ID (format: `topup_{user_id}_{timestamp}`)
- `amount`: Amount in Rupiah (integer)
- `channel_code`: Payment channel (use `QRIS`)
- `expires_in_minutes`: Transaction expiry time (default: 15)
- `notify_url`: Webhook URL for payment notifications

**Response**:
```json
{
    "success": true,
    "data": {
        "transaction_id": "txn_...",
        "payment_intent_id": "pi_...",
        "qris_text": "00...",
        "qris_url": "https://...",
        "payment_status": "initiated",
        "expires_at": "2026-03-03T10:30:00Z"
    }
}
```

### Check Transaction Status
**Endpoint**: `POST /transactions/status`

**Parameters**:
- `merchant_id`: Your merchant ID
- `ref_id`: Reference ID from creation

**Response**:
```json
{
    "success": true,
    "data": {
        "ref_id": "topup_1_1234567890",
        "payment_status": "success|pending|failed",
        "transaction_id": "txn_...",
        "amount": 100000
    }
}
```

### Cancel Transaction
**Endpoint**: `POST /transactions/cancel`

**Parameters**:
- `merchant_id`: Your merchant ID
- `ref_id`: Reference ID to cancel

## Webhook Setup

### Webhook Handler
The `XoftwareWebhookController` receives payment notifications from Xoftware.

**Endpoint**: `POST /webhook/xoftware`

**Processing**:
1. Validates incoming webhook signature
2. Updates transaction status in database
3. Increments user balance on success
4. Prevents double-crediting

### Webhook Payload Example:
```json
{
    "ref_id": "topup_1_1234567890",
    "transaction_id": "txn_...",
    "payment_status": "success",
    "amount": 100000,
    "timestamp": "2026-03-03T10:25:00Z"
}
```

### Testing Webhooks with Ngrok

1. **Start Ngrok** (tunnel port 8000 where Laravel runs):
```bash
ngrok http 8000
```

2. **Copy the Forwarding URL** (e.g., `https://abc123.ngrok-free.dev`)

3. **Update .env**:
```env
XOFTWARE_NOTIFY_URL=https://abc123.ngrok-free.dev/webhook/xoftware
```

4. **Clear Config Cache**:
```bash
php artisan config:clear
```

5. **Test Webhook with cURL**:
```bash
curl -X POST https://abc123.ngrok-free.dev/webhook/xoftware \
  -H "Content-Type: application/json" \
  -d '{
    "ref_id": "topup_1_1234567890",
    "transaction_id": "txn_test",
    "payment_status": "success",
    "amount": 100000
  }'
```

## Transaction Model

The `Transaction` model stores all payment transaction data:

**Fields**:
- `user_id`: FK to users table
- `ref_id`: Unique reference ID (indexed)
- `transaction_id`: Xoftware transaction ID
- `channel_code`: Payment channel (QRIS)
- `amount`: Amount in Rupiah
- `status`: pending|completed|failed|cancelled
- `payment_status`: initiated|pending|success|failed
- `qris_text`: Raw QRIS string for QR generation
- `qris_url`: URL to QRIS image (if provided)
- `expires_at`: Transaction expiry timestamp
- `metadata`: JSON field for storing full API response

## Top Up Controller

### Routes:
```
GET    /topup                  → TopUpController@index       (topup.index)
POST   /topup/qris             → TopUpController@createQris  (topup.qris)
GET    /topup/{transaction}    → TopUpController@show        (topup.show)
POST   /topup/{transaction}/check-status → checkStatus      (topup.check-status)
DELETE /topup/{transaction}/cancel → cancel                 (topup.cancel)
```

### Flow:
1. User enters amount on `/topup` form
2. POST to `/topup/qris` validates and creates transaction
3. Redirects to `/topup/{id}` to display QRIS
4. Client polls `/topup/{id}/check-status` every 5 seconds
5. Webhook also notifies completion at `/webhook/xoftware`
6. On success, balance is credited and user redirected to dashboard

## Common Issues & Solutions

### Issue: "Merchant not verified" (HTTP 403)
**Cause**: Merchant ID must be approved by Xoftware admin before QRIS generation works.

**Solution**:
1. Go to Xoftware dashboard at https://dashboard.xoftware.id
2. Navigate to Integration → Add New Integration
3. Fill in the form:
   - Jenis Aplikasi: Web
   - URL/Domain: Your ngrok URL
   - Deskripsi: E-Canteen QRIS top-up payment
4. Submit and wait for admin approval (~1-2 hours)
5. You'll receive approval email

### Issue: Webhook endpoint offline (ERR_NGROK_3200)
**Cause**: Ngrok tunnel port mismatch (Laravel runs on port 8000, ngrok tunneled port 80)

**Solution**:
1. Kill existing ngrok process
2. Run: `ngrok http 8000`
3. Update `.env XOFTWARE_NOTIFY_URL` with new URL
4. Run: `php artisan config:clear`
5. Re-test webhook with cURL

### Issue: Signature validation fails
**Cause**: HMAC-SHA256 signature generation incorrect or timestamp stale

**Solution**:
1. Ensure system clock is synced: `ntpdate -s time.nist.gov`
2. Check API key is copied correctly (no extra spaces)
3. Review logs: `tail -50 storage/logs/laravel.log`

## Testing Workflow

### Local Testing:
1. **Start Laravel**:
   ```bash
   php artisan serve
   ```

2. **Start Ngrok** (new terminal):
   ```bash
   ngrok http 8000
   ```

3. **Update .env** with ngrok URL and run:
   ```bash
   php artisan config:clear
   ```

4. **Test QRIS Creation**:
   - Login at http://localhost:8000/dashboard
   - Click "Top Up" button
   - Enter amount (e.g., 50000)
   - Click "Buat QRIS & Bayar"
   - Verify QRIS QR code displays

5. **Test Status Checking**:
   - While on QRIS page, check browser network tab
   - "Check Status" button should POST to `/topup/{id}/check-status`
   - Response should include current payment status

6. **Test Webhook** (if merchant verified):
   - After successful scan and payment, webhook should fire
   - Check Laravel logs: `tail -50 storage/logs/laravel.log`
   - Verify user balance incremented in database

### Sandbox vs. Production:
- **Current API**: `https://payment.xoftware.id/v1/api` (Sandbox)
- **Production**: Use different API endpoint when ready
- **Merchant ID**: 19 (sandbox testing only)

## Security Considerations

1. **API Key**: Keep `XOFTWARE_API_KEY` secret (never commit to repo)
2. **Signature Verification**: Every request/webhook is HMAC-SHA256 signed
3. **Webhook Validation**: Check signature before processing payment confirmations
4. **CSRF Protection**: All webhook signatures verified server-side
5. **Amount Validation**: Server-side validation prevents tampering
6. **User Verification**: Only authenticated users can initiate top-ups

## Logs & Debugging

### View API Logs:
```bash
tail -f storage/logs/laravel.log | grep -i xoftware
```

### Log Locations:
- Laravel logs: `storage/logs/laravel.log`
- Ngrok logs: Terminal where ngrok runs
- Webhook logs: Check Laravel logs for webhook handler activity

### Example Log Entries:
```
[2026-03-03 10:20:15] local.INFO: Xoftware API Request {"method":"POST","url":"https://payment.xoftware.id/v1/api/transactions"...}
[2026-03-03 10:20:20] local.INFO: Xoftware API Response {"status":201,"body":{"success":true...}}
[2026-03-03 10:25:00] local.INFO: Xoftware Webhook Received {"payload":{"ref_id":"topup_1_..."...}}
```

## Database Schema

### Transactions Table:
```sql
CREATE TABLE transactions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL FOREIGN KEY,
    ref_id VARCHAR(255) UNIQUE NOT NULL,
    transaction_id VARCHAR(255),
    payment_intent_id VARCHAR(255),
    channel_code VARCHAR(20) DEFAULT 'QRIS',
    amount DECIMAL(15,2) NOT NULL,
    status ENUM('pending','completed','failed','cancelled') DEFAULT 'pending',
    payment_status VARCHAR(50),
    qris_text TEXT,
    qris_url VARCHAR(255),
    provider_ref VARCHAR(255),
    expires_at DATETIME,
    metadata JSON,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    INDEX(user_id),
    INDEX(ref_id)
);
```

## References

- **Xoftware Pay Dashboard**: https://dashboard.xoftware.id
- **API Documentation**: Contact Xoftware support
- **QRIS Standard**: https://www.bi.go.id/en/
- **Ngrok Documentation**: https://ngrok.com/docs

## Support

For issues or questions:
1. Check logs in `storage/logs/laravel.log`
2. Review this guide's troubleshooting section
3. Contact Xoftware support with merchant ID 19
4. Check webhook delivery in Xoftware dashboard
