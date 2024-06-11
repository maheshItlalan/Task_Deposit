<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Deposit;
use App\Models\Customer;
use App\Models\Currency;
use App\Models\Method;
use Illuminate\Support\Facades\DB;

class Depositcontroller extends Controller
{
    public function index(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'customer_id' => 'integer|nullable',
            'name' => 'string|nullable',
            'min_amount' => 'numeric|nullable',
            'date_from' => 'date|nullable',
            'date_to' => 'date|nullable',
            'country' => 'string|nullable',
            'method' => 'string|nullable',
            'group_by' => 'in:country,customer,method,day,month,year|nullable',
        ]);

        $query = Deposit::query();

        // Join related tables
        $query->join('customers', 'deposits.customer_code', '=', 'customers.customer_code')
              ->join('currencies', 'deposits.currency_id', '=', 'currencies.id')
              ->join('methods', 'deposits.method_id', '=', 'methods.id');

        // Apply filters
        if ($request->filled('customer_id')) {
            $query->where('deposits.customer_code', $request->input('customer_id'));
        }
        if ($request->filled('name')) {
            $query->where(function($q) use ($request) {
                $q->where('customers.first_name', 'like', '%' . $request->input('name') . '%')
                  ->orWhere('customers.last_name', 'like', '%' . $request->input('name') . '%');
            });
        }
        if ($request->filled('min_amount')) {
            $query->where('deposits.amount', '>=', $request->input('min_amount'));
        }
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('deposits.deposit_date', [$request->input('date_from'), $request->input('date_to')]);
        }
        if ($request->filled('country')) {
            $query->where('customers.country', $request->input('country'));
        }
        if ($request->filled('method')) {
            $query->where('methods.method_name', $request->input('method'));
        }

        // Apply grouping
        if ($request->filled('group_by')) {
            switch ($request->input('group_by')) {
                case 'country':
                    $query->groupBy('customers.country')
                          ->select(DB::raw('customers.country, SUM(deposits.amount) as total_amount, SUM(deposits.converted_amount) as total_amount_eur'));
                    break;
                case 'customer':
                    $query->groupBy('deposits.customer_code')
                          ->select(DB::raw('deposits.customer_code, customers.first_name, customers.last_name, SUM(deposits.amount) as total_amount, SUM(deposits.converted_amount) as total_amount_eur'));
                    break;
                case 'method':
                    $query->groupBy('methods.method_name')
                          ->select(DB::raw('methods.method_name, SUM(deposits.amount) as total_amount, SUM(deposits.converted_amount) as total_amount_eur'));
                    break;
                case 'day':
                    $query->groupBy(DB::raw('DATE(deposits.deposit_date)'))
                          ->select(DB::raw('DATE(deposits.deposit_date) as period, SUM(deposits.amount) as total_amount, SUM(deposits.converted_amount) as total_amount_eur'));
                    break;
                case 'month':
                    $query->groupBy(DB::raw('YEAR(deposits.deposit_date), MONTH(deposits.deposit_date)'))
                          ->select(DB::raw('YEAR(deposits.deposit_date) as year, MONTH(deposits.deposit_date) as month, SUM(deposits.amount) as total_amount, SUM(deposits.converted_amount) as total_amount_eur'));
                    break;
                case 'year':
                    $query->groupBy(DB::raw('YEAR(deposits.deposit_date)'))
                          ->select(DB::raw('YEAR(deposits.deposit_date) as period, SUM(deposits.amount) as total_amount, SUM(deposits.converted_amount) as total_amount_eur'));
                    break;
            }
        } else {
            $query->select('deposits.*', 'customers.first_name', 'customers.last_name', 'customers.country', 'currencies.currency_name', 'methods.method_name');
        }

        $deposits = $query->get();

        // Calculate totals
        $totalAmount = $request->filled('group_by') ? $deposits->sum('total_amount') : $deposits->sum('amount');
        $totalAmountEUR = $request->filled('group_by') ? $deposits->sum('total_amount_eur') : $deposits->sum('converted_amount');

        return view('deposit.depositpage', compact('deposits', 'totalAmount', 'totalAmountEUR'));
    }


    public function failedDeposits(Request $request)
    {
        $request->validate([
            'date_from' => 'date|nullable',
            'date_to' => 'date|nullable',
            'country' => 'string|nullable',
            'currency' => 'integer|nullable',
            'method' => 'integer|nullable',
        ]);

        $query = Deposit::where('status', 'FALSE')
            ->join('customers', 'deposits.customer_code', '=', 'customers.customer_code')
            ->join('currencies', 'deposits.currency_id', '=', 'currencies.id')
            ->join('methods', 'deposits.method_id', '=', 'methods.id')
            ->select('deposits.*', 'customers.first_name', 'customers.last_name', 'customers.country', 'currencies.currency_name', 'methods.method_name');

        if ($request->date_from) {
            $query->where('deposits.deposit_date', '>=', $request->input('date_from'));
        }
        if ($request->date_to) {
            $query->where('deposits.deposit_date', '<=', $request->input('date_to'));
        }
        if ($request->country) {
            $query->where('customers.country', 'like', '%' . $request->input('country') . '%');
        }
        if ($request->currency) {
            $query->where('deposits.currency_id', $request->input('currency'));
        }
        if ($request->method) {
            $query->where('deposits.method_id', $request->input('method'));
        }

        $failedDeposits = $query->get();
        $totalAmount = $failedDeposits->sum('amount');
        $totalAmountEUR = $failedDeposits->sum('converted_amount');

        $currencies = Currency::all();
        $methods = Method::all();

        return view('deposit.depositfailpage', compact('failedDeposits', 'totalAmount', 'totalAmountEUR', 'currencies', 'methods'));
    }


}
