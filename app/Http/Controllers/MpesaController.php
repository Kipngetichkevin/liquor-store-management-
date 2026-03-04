<?php

namespace App\Http\Controllers;

use App\Services\MpesaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MpesaController extends Controller
{
    protected $mpesaService;

    public function __construct(MpesaService $mpesaService)
    {
        $this->mpesaService = $mpesaService;
    }

    /**
     * Initiate STK Push
     */
    public function initiate(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'account_reference' => 'required|string|max:12',
            'transaction_desc' => 'required|string|max:13',
            'sale_id' => 'required|integer'
        ]);

        try {
            $callbackUrl = route('mpesa.callback'); // Ensure this route name exists

            $response = $this->mpesaService->stkPush(
                $request->phone,
                $request->amount,
                $request->account_reference,
                $request->transaction_desc,
                $callbackUrl
            );

            // Store CheckoutRequestID in the sale record
            $sale = \App\Models\Sale::find($request->sale_id);
            if ($sale) {
                $sale->payment_reference = $response['CheckoutRequestID'];
                $sale->payment_status = 'pending';
                $sale->save();
            }

            return response()->json([
                'success' => true,
                'CheckoutRequestID' => $response['CheckoutRequestID'],
                'message' => 'STK push initiated. Please check your phone.'
            ]);

        } catch (\Exception $e) {
            Log::error('M-Pesa initiation failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle M-Pesa callback
     */
    public function callback(Request $request)
    {
        // Log the callback for debugging
        Log::info('M-Pesa callback received', $request->all());

        // Process the callback data
        $callbackData = $request->all();

        if (isset($callbackData['Body']['stkCallback'])) {
            $stkCallback = $callbackData['Body']['stkCallback'];
            $checkoutRequestID = $stkCallback['CheckoutRequestID'] ?? null;
            $resultCode = $stkCallback['ResultCode'];
            $resultDesc = $stkCallback['ResultDesc'];

            // Find the sale by payment_reference
            $sale = \App\Models\Sale::where('payment_reference', $checkoutRequestID)->first();

            if ($sale) {
                if ($resultCode == 0) {
                    // Success – extract M-Pesa receipt number
                    $metadata = $stkCallback['CallbackMetadata']['Item'] ?? [];
                    $mpesaReceipt = null;
                    foreach ($metadata as $item) {
                        if ($item['Name'] == 'MpesaReceiptNumber') {
                            $mpesaReceipt = $item['Value'];
                            break;
                        }
                    }
                    $sale->payment_status = 'completed';
                    $sale->mpesa_receipt = $mpesaReceipt;
                } else {
                    $sale->payment_status = 'failed';
                }
                $sale->save();
            }
        }

        // Respond to Safaricom
        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Success']);
    }

    /**
     * Query transaction status (used by polling)
     */
    public function query($id)
    {
        $sale = \App\Models\Sale::find($id);
        if (!$sale || !$sale->payment_reference) {
            return response()->json(['success' => false, 'message' => 'Sale not found or no reference'], 404);
        }

        try {
            $response = $this->mpesaService->queryStatus($sale->payment_reference);

            // Update status based on response
            if (isset($response['ResultCode'])) {
                if ($response['ResultCode'] == '0') {
                    $sale->payment_status = 'completed';
                } elseif (in_array($response['ResultCode'], ['1032', '1037', '1046', '2001'])) {
                    // User cancelled or request expired
                    $sale->payment_status = 'failed';
                }
                $sale->save();
            }

            return response()->json([
                'success' => true,
                'status' => $sale->payment_status,
                'data' => $response
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}