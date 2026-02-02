<x-admin-layout>
    <x-slot name="title">Slettede kunder</x-slot>

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900">Slettede kunder</h2>
            <p class="mt-1 text-sm text-gray-600">
                Kunder som er slettet kan gjenopprettes eller slettes permanent.
            </p>
        </div>
        <a href="{{ route('admin.customers.index') }}" 
           class="px-4 py-2 bg-white text-gray-700 font-medium rounded-lg border border-gray-300 hover:bg-gray-50 focus:ring-4 focus:ring-gray-200 transition-colors duration-150">
            Tilbake til kunder
        </a>
    </div>

    <!-- Deleted Customers Table -->
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
                                    <form method="POST" action="{{ route('admin.customers.restore', $customer->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-success-600 hover:text-success-700">
                                            Gjenopprett
                                        </button>
                                    </form>
                                    <button onclick="confirmPermanentDelete({{ $customer->id }})" 
                                            class="text-danger-600 hover:text-danger-700">
                                        Slett permanent
                                    </button>
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
            <h3 class="mt-2 text-sm font-medium text-gray-900">Ingen slettede kunder</h3>
            <p class="mt-1 text-sm text-gray-500">Alle kunder er aktive.</p>
        </div>
    @endif

    <!-- Permanent delete confirmation modal -->
    <div id="permanentDeleteModal" class="fixed inset-0 z-50 overflow-y-auto hidden" x-data="{ open: false, customerId: null }" x-show="open" style="display: none;">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" @click="open = false"></div>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-danger-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="ml-3 text-lg font-semibold text-gray-900">Slett permanent?</h3>
                </div>
                <p class="text-sm text-gray-600 mb-6">
                    Dette kan ikke angres! Kunden vil bli permanent slettet fra systemet.
                </p>
                <div class="flex justify-end space-x-3">
                    <button @click="open = false" 
                            class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Avbryt
                    </button>
                    <form :action="`{{ route('admin.customers.index') }}/${customerId}/force`" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="px-4 py-2 bg-danger-600 text-white rounded-lg hover:bg-danger-700">
                            Slett permanent
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmPermanentDelete(customerId) {
            const modal = document.getElementById('permanentDeleteModal');
            modal.classList.remove('hidden');
            modal.setAttribute('x-data', `{ open: true, customerId: ${customerId} }`);
        }
    </script>
</x-admin-layout>
