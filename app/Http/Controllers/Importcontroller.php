<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Currency;
use App\Models\Method;
use App\Models\Deposit;
use Illuminate\Http\RedirectResponse;

class ImportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function importfile()
    {
        return view('import');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:100048',
        ]);

        $file = $request->file('file');

        $data = array_map('str_getcsv', file($file));
        $header = array_shift($data);

        foreach ($data as $row) {
            $row = array_combine($header, $row);

            //if it is already then skip & Insert Customer , Currency, Method, Deposit
            $customer = Customer::where('customer_code', $row['CustomerID'])->first();
            if ($customer) {
                continue;
            }

            Customer::create([
                'customer_code' => $row['CustomerID'],
                'first_name' => $row['FirstName'],
                'last_name' => $row['LastName'],
                'country' => $row['Country']
            ]);

            $currency = Currency::where('currency_name', $row['Currency'])->first();
            if (!$currency) {
                $currency = Currency::create(['currency_name' => $row['Currency']]);
            }

            $method = Method::where('method_name', $row['Method'])->first();
            if (!$method) {
                $method = Method::create(['method_name' => $row['Method']]);
            }



            Deposit::create([
                'customer_code' => $row['CustomerID'],
                'currency_id' => $currency->id,
                'method_id' => $method->id,
                'amount' => $row['DepositAmount'],
                'converted_amount' => $row['DepositAmountEUR'],
                'status' => $row['Successful'],
                'deposit_date' => $row['Datetime']
            ]);
        }

        return back()->with('success', 'CSV data imported successfully.');
    }
    private function sanitize($data)
    {
        foreach ($data as $key => $value) {
            $data[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }
        return $data;
    }
}
