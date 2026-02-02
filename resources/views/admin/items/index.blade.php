<x-admin-layout>
    <x-slot name="title">Varer</x-slot>

    <!-- Header with filters and action button -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-900">Varer</h2>
        <a href="{{ route('admin.items.create') }}" 
           class="px-4 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 focus:ring-4 focus:ring-primary-200 transition-colors duration-150">
            Ny vare
        </a>
    </div>

    <!-- Status Filter -->
    <div class="mb-6 flex space-x-2">
        <a href="{{ route('admin.items.index', ['status' => 'all']) }}" 
           class="px-4 py-2 text-sm font-medium rounded-lg transition-colors duration-150 {{ $statusFilter === 'all' ? 'bg-primary-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">
            Alle
        </a>
        <a href="{{ route('admin.items.index', ['status' => 'available']) }}" 
           class="px-4 py-2 text-sm font-medium rounded-lg transition-colors duration-150 {{ $statusFilter === 'available' ? 'bg-success-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">
            Tilgjengelig
        </a>
        <a href="{{ route('admin.items.index', ['status' => 'reserved']) }}" 
           class="px-4 py-2 text-sm font-medium rounded-lg transition-colors duration-150 {{ $statusFilter === 'reserved' ? 'bg-warning-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">
            Reservert
        </a>
        <a href="{{ route('admin.items.index', ['status' => 'sold']) }}" 
           class="px-4 py-2 text-sm font-medium rounded-lg transition-colors duration-150 {{ $statusFilter === 'sold' ? 'bg-gray-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">
            Solgt
        </a>
        <a href="{{ route('admin.items.index', ['status' => 'archived']) }}" 
           class="px-4 py-2 text-sm font-medium rounded-lg transition-colors duration-150 {{ $statusFilter === 'archived' ? 'bg-gray-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">
            Arkivert
        </a>
    </div>

    <!-- Items Table -->
    @if($items->count() > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Navn
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Beskrivelse
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Innkjøpspris
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Målpris
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Handlinger
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($items as $item)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $item->name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-500">{{ Str::limit($item->description, 50) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="text-sm text-gray-900">
                                        {{ $item->purchase_price ? format_nok($item->purchase_price) : '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="text-sm text-gray-900">
                                        {{ $item->target_price ? format_nok($item->target_price) : '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($item->status === 'available')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-success-100 text-success-800">
                                            Tilgjengelig
                                        </span>
                                    @elseif($item->status === 'reserved')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-warning-100 text-warning-800">
                                            Reservert
                                        </span>
                                    @elseif($item->status === 'sold')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Solgt
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                            Arkivert
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
                                    <a href="{{ route('admin.items.edit', $item) }}" 
                                       class="text-primary-600 hover:text-primary-700">
                                        Rediger
                                    </a>
                                    <button onclick="confirmDelete({{ $item->id }})" 
                                            class="text-danger-600 hover:text-danger-700">
                                        Slett
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
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Ingen varer ennå</h3>
            <p class="mt-1 text-sm text-gray-500">Kom i gang ved å opprette din første vare.</p>
            <div class="mt-6">
                <a href="{{ route('admin.items.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 focus:ring-4 focus:ring-primary-200 transition-colors duration-150">
                    Opprett første vare
                </a>
            </div>
        </div>
    @endif

    <!-- Delete confirmation modal -->
    <div id="deleteModal" class="fixed inset-0 z-50 overflow-y-auto hidden" x-data="{ open: false, itemId: null }" x-show="open" style="display: none;">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" @click="open = false"></div>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Slett vare?</h3>
                <p class="text-sm text-gray-600 mb-6">
                    Er du sikker på at du vil slette denne varen?
                </p>
                <div class="flex justify-end space-x-3">
                    <button @click="open = false" 
                            class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Avbryt
                    </button>
                    <form :action="`{{ route('admin.items.index') }}/${itemId}`" method="POST">
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
        function confirmDelete(itemId) {
            const modal = document.getElementById('deleteModal');
            modal.classList.remove('hidden');
            modal.setAttribute('x-data', `{ open: true, itemId: ${itemId} }`);
        }
    </script>
</x-admin-layout>
