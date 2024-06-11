@extends('layouts.app')

@section('title', 'Deposit Dashboard')

@section('contents')
<div class="container mx-auto px-4">
    <h1 class="font-bold text-2xl mb-4">Deposit Dashboard</h1>

    <form method="GET" action="{{ route('deposits.index') }}" class="mb-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <input type="text" name="customer_id" placeholder="Customer ID" class="p-2 border rounded">
            <input type="text" name="name" placeholder="Name" class="p-2 border rounded">
            <input type="number" step="0.01" name="min_amount" placeholder="Min Deposit Amount" class="p-2 border rounded">
            <input type="date" name="date_from" placeholder="Date From" class="p-2 border rounded">
            <input type="date" name="date_to" placeholder="Date To" class="p-2 border rounded">
            <input type="text" name="country" placeholder="Country" class="p-2 border rounded">
            <input type="text" name="method" placeholder="Method" class="p-2 border rounded">
            <select name="group_by" class="p-2 border rounded">
                <option value="">Group By</option>
                <option value="country">Country</option>
                <option value="customer">Customer</option>
                <option value="method">Method</option>
                <option value="day">Day</option>
                <option value="month">Month</option>
                <option value="year">Year</option>
            </select>
        </div>
        <button type="submit" class="mt-2 px-4 py-2 bg-blue-500 text-white rounded">Filter</button>
    </form>

    <div class="overflow-x-auto">
        <div class="bg-white overflow-y-auto" style="max-height: 400px;">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-200 sticky top-0">
                    <tr>
                        @if(request('group_by') === 'country')
                            <th class="px-4 py-2">Country</th>
                            <th class="px-4 py-2">Amount (Local)</th>
                            <th class="px-4 py-2">Amount (EUR)</th>
                        @elseif(request('group_by') === 'customer')
                            <th class="px-4 py-2">Customer Code</th>
                            <th class="px-4 py-2">First Name</th>
                            <th class="px-4 py-2">Last Name</th>
                            <th class="px-4 py-2">Amount (Local)</th>
                            <th class="px-4 py-2">Amount (EUR)</th>
                        @elseif(request('group_by') === 'method')
                            <th class="px-4 py-2">Method</th>
                            <th class="px-4 py-2">Amount (Local)</th>
                            <th class="px-4 py-2">Amount (EUR)</th>
                        @elseif(request('group_by') === 'day' || request('group_by') === 'month' || request('group_by') === 'year')
                            <th class="px-4 py-2">Period</th>
                            <th class="px-4 py-2">Amount (Local)</th>
                            <th class="px-4 py-2">Amount (EUR)</th>
                        @else
                            <th class="px-4 py-2">Customer Code</th>
                            <th class="px-4 py-2">First Name</th>
                            <th class="px-4 py-2">Last Name</th>
                            <th class="px-4 py-2">Amount (Local)</th>
                            <th class="px-4 py-2">Amount (EUR)</th>
                            <th class="px-4 py-2">Date</th>
                            <th class="px-4 py-2">Country</th>
                            <th class="px-4 py-2">Method</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($deposits as $deposit)
                        <tr>
                            @if(request('group_by') === 'country')
                                <td class="border px-4 py-2">{{ $deposit->country }}</td>
                                <td class="border px-4 py-2">{{ $deposit->total_amount }}</td>
                                <td class="border px-4 py-2">{{ $deposit->total_amount_eur }}</td>
                            @elseif(request('group_by') === 'customer')
                                <td class="border px-4 py-2">{{ $deposit->customer_code }}</td>
                                <td class="border px-4 py-2">{{ $deposit->first_name }}</td>
                                <td class="border px-4 py-2">{{ $deposit->last_name }}</td>
                                <td class="border px-4 py-2">{{ $deposit->total_amount }}</td>
                                <td class="border px-4 py-2">{{ $deposit->total_amount_eur }}</td>
                            @elseif(request('group_by') === 'method')
                                <td class="border px-4 py-2">{{ $deposit->method_name }}</td>
                                <td class="border px-4 py-2">{{ $deposit->amount }}</td>
                                <td class="border px-4 py-2">{{ $deposit->converted_amount }}</td>
                            @elseif(request('group_by') === 'day' || request('group_by') === 'month' || request('group_by') === 'year')
                                <td class="border px-4 py-2">{{ $deposit->period ?? ($deposit->year . '-' . str_pad($deposit->month, 2, '0', STR_PAD_LEFT)) }}</td>
                                <td class="border px-4 py-2">{{ $deposit->total_amount }}</td>
                                <td class="border px-4 py-2">{{ $deposit->total_amount_eur }}</td>
                            @else
                                <td class="border px-4 py-2">{{ $deposit->customer_code }}</td>
                                <td class="border px-4 py-2">{{ $deposit->first_name }}</td>
                                <td class="border px-4 py-2">{{ $deposit->last_name }}</td>
                                <td class="border px-4 py-2">{{ $deposit->amount }}</td>
                                <td class="border px-4 py-2">{{ $deposit->converted_amount }}</td>
                                <td class="border px-4 py-2">{{ $deposit->deposit_date }}</td>
                                <td class="border px-4 py-2">{{ $deposit->country }}</td>
                                <td class="border px-4 py-2">{{ $deposit->method_name }}</td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="border px-4 py-2 text-center">No deposits found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        <p>Total Amount (Local): {{ $totalAmount }}</p>
        <p>Total Amount (EUR): {{ $totalAmountEUR }}</p>
    </div>
</div>
@endsection
