<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Min Oversikt
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Password change alert -->
            @if(auth()->user()->password_change_required)
                <div class="mb-6 p-4 bg-warning-50 border border-warning-200 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-warning-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-warning-800">
                                Du må endre passordet ditt før du kan fortsette. 
                                <a href="{{ route('profile.edit') }}" class="underline font-semibold">Endre passord nå</a>
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 gap-6 mb-8 sm:grid-cols-3">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <p class="text-sm font-medium text-gray-500">Total kjøpt</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ format_nok($totalPurchased) }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <p class="text-sm font-medium text-gray-500">Total betalt</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ format_nok($totalPaid) }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <p class="text-sm font-medium text-gray-500">Utestående</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ format_nok($outstanding) }}</p>
                </div>
            </div>

            <!-- Open Orders -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Åpne ordrer</h3>
                    <a href="{{ route('customer.orders.index') }}" 
                       class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                        Se alle ordrer →
                    </a>
                </div>
                
                @if($openOrders->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Ordrenummer
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Utestående
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Handlinger
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($openOrders as $order)
                                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $order->order_number }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-success-100 text-success-800">
                                                Åpen
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <div class="text-sm font-semibold text-gray-900">{{ format_nok($order->outstanding) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('customer.orders.show', $order) }}" 
                                               class="text-primary-600 hover:text-primary-700">
                                                Se detaljer
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="px-6 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Ingen åpne ordrer</h3>
                        <p class="mt-1 text-sm text-gray-500">Du har ingen åpne ordrer for øyeblikket.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
