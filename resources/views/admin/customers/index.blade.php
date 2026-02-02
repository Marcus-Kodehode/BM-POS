<x-admin-layout>
    <x-slot name="title">Kunder</x-slot>

    <!-- Header with action buttons -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-900">Kunder</h2>
        <div class="flex space-x-3">
            <a href="{{ route('admin.customers.deleted') }}" 
               class="px-4 py-2 bg-white text-gray-700 font-medium rounded-lg border border-gray-300 hover:bg-gray-50 focus:ring-4 focus:ring-gray-200 transition-colors duration-150">
                Slettede kunder
            </a>
            <a href="{{ route('admin.customers.create') }}" 
               class="px-4 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 focus:ring-4 focus:ring-primary-200 transition-colors duration-150">
                Ny kunde
            </a>
        </div>
    </div>

    <!-- Customers Table -->
    @if($customers->count() > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Navn
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                E-post
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
                        @foreach($customers as $customer)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $customer->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ $customer->email ?? '-' }}</div>
                                    @if($customer->phone)
                                        <div class="text-xs text-gray-400">{{ $customer->phone }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="text-sm font-semibold text-gray-900">{{ format_nok($customer->outstanding) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
                                    <a href="{{ route('admin.customers.show', $customer) }}" 
                                       class="text-primary-600 hover:text-primary-700">
                                        Se detaljer
                                    </a>
                                    <a href="{{ route('admin.customers.edit', $customer) }}" 
                                       class="text-gray-600 hover:text-gray-700">
                                        Rediger
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 px-6 py-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Ingen kunder ennå</h3>
            <p class="mt-1 text-sm text-gray-500">Kom i gang ved å opprette din første kunde.</p>
            <div class="mt-6">
                <a href="{{ route('admin.customers.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 focus:ring-4 focus:ring-primary-200 transition-colors duration-150">
                    Opprett første kunde
                </a>
            </div>
        </div>
    @endif
</x-admin-layout>
