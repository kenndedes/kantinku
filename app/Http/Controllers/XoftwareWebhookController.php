<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class XoftwareWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Handle GET requests (for health checks)
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

            // Find transaction by ref_id
            $transaction = Transaction::where('ref_id', $ref_id)->first();

            if (!$transaction) {
                Log::warning('Xoftware Webhook: Transaction not found', ['ref_id' => $ref_id]);
                return response()->json(['error' => 'Transaction not found'], 404);
            }

            // Update transaction status
            $transaction->update([
                'payment_status' => $payment_status,
                'status' => in_array($payment_status, ['success', 'completed']) ? 'completed' : 'pending',
                'metadata' => $request->all(),
            ]);

            // If successful, credit user balance (only once)
            if (in_array($payment_status, ['success', 'completed']) && $transaction->status === 'completed') {
                $user = $transaction->user;
                
                // Prevent double-crediting
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
