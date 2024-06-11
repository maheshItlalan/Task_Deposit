@extends('layouts.app')

@section('title', 'Failed Deposits')

@section('contents')
<div class="container mx-auto px-4">
    <h1 class="font-bold text-2xl mb-4">Failed Deposits</h1>

    <form method="GET" action="{{ route('depositFail.failed') }}" class="mb-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <input type="date" name="date_from" placeholder="Date From" class="p-2 border rounded">
            <input type="date" name="date_to" placeholder="Date To" class="p-2 border rounded">
            <input type="text" name="country" placeholder="Country" class="p-2 border rounded">
            <select name="currency" class="p-2 border rounded">
                <option value="">Select Currency</option>
                @foreach($currencies as $currency)
                    <option value="{{ $currency->id }}">{{ $currency->currency_name }}</option>
                @endforeach
            </select>
            <select name="method" class="p-2 border rounded">
                <option value="">Select Method</option>
                @foreach($methods as $method)
                    <option value="{{ $method->id }}">{{ $method->method_name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="mt-2 px-4 py-2 bg-blue-500 text-white rounded">Filter</button>
    </form>

    <div class="overflow-x-auto">
        <div class="bg-white overflow-y-auto" style="max-height: 400px;">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-200 sticky top-0">
                    <tr>
                        <th class="px-4 py-2">Customer Code</th>
                        <th class="px-4 py-2">First Name</th>
                        <th class="px-4 py-2">Last Name</th>
                        <th class="px-4 py-2">Amount (Local)</th>
                        <th class="px-4 py-2">Amount (EUR)</th>
                        <th class="px-4 py-2">Date</th>
                        <th class="px-4 py-2">Country</th>
                        <th class="px-4 py-2">Method</th>
                        <th class="px-4 py-2">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($failedDeposits as $fail_deposit)
                        <tr>
                            <td class="border px-4 py-2">{{ $fail_deposit->customer_code }}</td>
                            <td class="border px-4 py-2">{{ $fail_deposit->first_name }}</td>
                            <td class="border px-4 py-2">{{ $fail_deposit->last_name }}</td>
                            <td class="border px-4 py-2">{{ $fail_deposit->amount }}</td>
                            <td class="border px-4 py-2">{{ $fail_deposit->converted_amount }}</td>
                            <td class="border px-4 py-2">{{ $fail_deposit->deposit_date }}</td>
                            <td class="border px-4 py-2">{{ $fail_deposit->country }}</td>
                            <td class="border px-4 py-2">{{ $fail_deposit->method_name }}</td>
                            <td class="border px-4 py-2 text-red-500">{{ $fail_deposit->status }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="border px-4 py-2 text-center">No failed deposits found</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-200">
                    <tr>
                        <td colspan="3" class="px-4 py-2 font-bold">Total</td>
                        <td class="border px-4 py-2 font-bold">{{ $totalAmount }}</td>
                        <td class="border px-4 py-2 font-bold">{{ $totalAmountEUR }}</td>
                        <td colspan="4"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
