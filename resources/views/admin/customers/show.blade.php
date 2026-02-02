<x-admin-layout>
    <x-slot name="title">{{ $customer->name }}</x-slot>

    <!-- Temporary password display (one-time) -->
    @if(session('temp_password'))
        <div class="mb-6 p-4 bg-success-50 border border-success-200 rounded-lg" x-data="{ show: true }" x-show="show">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-success-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="text-sm font-medium text-success-800">Kunde opprettet!</h3>
                    </div>
                    <div class="mt-2">
                        <p class="text-sm text-success-700">
                            Midlertidig passord (vis dette til kunden):
                        </p>
                        <div class="mt-2 flex items-center space-x-2">
                            <code class="px-3 py-2 bg-white border border-success-300 rounded text-sm font-mono text-gray-900">
                                {{ session('temp_password') }}
                            </code>
                            <button onclick="copyToClipboard('{{ session('temp_password') }}')" 
                                    class="px-3 py-2 bg-success-600 text-white text-sm font-medium rounded hover:bg-success-700 transition-colors duration-150">
                                Kopier
                            </button>
                        </div>
                        <p class="mt-2 text-xs text-success-600">
                            Dette passordet vises kun én gang. Kunden må endre det ved første innlogging.
                        </p>
                    </div>
                </div>
                <button @click="show = false" class="text-success-600 hover:text-success-700">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    @endif

    <!-- Header -->
    <div class="flex justify-between items-start mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900">{{ $customer->name }}</h2>
            <p class="mt-1 text-sm text-gray-600">{{ $customer->email }}</p>
            @if($customer->password_change_required)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-warning-100 text-warning-800 mt-2">
                    Må endre passord
                </span>
            @endif
        </div>
        <div class="flex space-x-3">
            <button onclick="copyDashboardLink()" 
                    class="px-4 py-2 bg-white text-gray-700 font-medium rounded-lg border border-gray-300 hover:bg-gray-50 focus:ring-4 focus:ring-gray-200 transition-colors duration-150">
                Kopier link
            </button>
            <a href="{{ route('admin.customers.edit', $customer) }}" 
               class="px-4 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 focus:ring-4 focus:ring-primary-200 transition-colors duration-150">
                Rediger
            </a>
            <button onclick="confirmDelete()" 
                    class="px-4 py-2 bg-danger-600 text-white font-medium rounded-lg hover:bg-danger-700 focus:ring-4 focus:ring-danger-200 transition-colors duration-150">
                Slett
            </button>
        </div>
    </div>

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

    <!-- Orders Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Ordrer</h3>
        </div>
        
        @if($orders->count() > 0)
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
                        @foreach($orders as $order)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $order->order_number }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($order->status === 'open')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-success-100 text-success-800">
                                            Åpen
                                        </span>
                                    @elseif($order->status === 'closed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Lukket
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-danger-100 text-danger-800">
                                            Kansellert
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="text-sm font-semibold text-gray-900">{{ format_nok($order->outstanding) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="#" class="text-primary-600 hover:text-primary-700">
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
                <h3 class="mt-2 text-sm font-medium text-gray-900">Ingen ordrer ennå</h3>
                <p class="mt-1 text-sm text-gray-500">Denne kunden har ingen ordrer.</p>
            </div>
        @endif
    </div>

    <!-- Delete confirmation modal -->
    <div id="deleteModal" class="fixed inset-0 z-50 overflow-y-auto hidden" x-data="{ open: false }" x-show="open" style="display: none;">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" @click="open = false"></div>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Slett kunde?</h3>
                <p class="text-sm text-gray-600 mb-6">
                    Er du sikker på at du vil slette denne kunden? Kunden kan gjenopprettes senere.
                </p>
                <div class="flex justify-end space-x-3">
                    <button @click="open = false" 
                            class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Avbryt
                    </button>
                    <form method="POST" action="{{ route('admin.customers.destroy', $customer) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="px-4 py-2 bg-danger-600 text-white rounded-lg hover:bg-danger-700">
                            Slett
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('Passord kopiert til utklippstavle');
            });
        }

        function copyDashboardLink() {
            const link = '{{ url('/dashboard') }}';
            navigator.clipboard.writeText(link).then(() => {
                alert('Link kopiert til utklippstavle');
            });
        }

        function confirmDelete() {
            const modal = document.getElementById('deleteModal');
            modal.classList.remove('hidden');
            modal.setAttribute('x-data', '{ open: true }');
        }
    </script>
</x-admin-layout>
