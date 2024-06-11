<?php

namespace App\Http\Controllers;
use App\Models\Deposit;

use Illuminate\Http\Request;

class API_DepositDetailsController extends Controller
{
    public function getReport($customerId)
    {
        // Validate the customer ID
        if (!is_numeric($customerId)) {
            return response()->json(['error' => 'Invalid customer ID'], 400);
        }

        // Fetch deposits for the customer
        $deposits = Deposit::where('customer_code', $customerId)->get();

        if ($deposits->isEmpty()) {
            return response()->json(['error' => 'No deposits found for this customer'], 404);
        }

        // Calculate total deposits in local currency and EUR
        $totalDepositsLocal = $deposits->sum('amount');
        $totalDepositsEUR = $deposits->sum('converted_amount');

        // Get the first and last deposit dates
        $firstDeposit = $deposits->first()->deposit_date;
        $lastDeposit = $deposits->last()->deposit_date;

        return response()->json([
            'total_deposits_local' => $totalDepositsLocal,
            'total_deposits_eur' => $totalDepositsEUR,
            'first_deposit' => $firstDeposit,
            'last_deposit' => $lastDeposit,
        ]);
    }
}
