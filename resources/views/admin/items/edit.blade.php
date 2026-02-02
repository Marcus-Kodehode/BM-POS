<x-admin-layout>
    <x-slot name="title">Rediger vare</x-slot>

    <div class="max-w-2xl">
        <!-- Header -->
        <div class="mb-6">
            <h2 class="text-2xl font-semibold text-gray-900">Rediger vare</h2>
            <p class="mt-1 text-sm text-gray-600">
                Oppdater vareinformasjon og status.
            </p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <form method="POST" action="{{ route('admin.items.update', $item) }}" class="space-y-6" x-data="{ 
                oldStatus: '{{ $item->status }}',
                newStatus: '{{ old('status', $item->status) }}',
                showWarning() {
                    return (this.oldStatus === 'sold' || this.oldStatus === 'archived') && this.newStatus === 'available';
                }
            }">
                @csrf
                @method('PUT')

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        Navn <span class="text-danger-600">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name', $item->name) }}"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-150 @error('name') border-danger-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                        Beskrivelse
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-150 @error('description') border-danger-500 @enderror">{{ old('description', $item->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Purchase Price -->
                <div>
                    <label for="purchase_price" class="block text-sm font-medium text-gray-700 mb-1">
                        Innkjøpspris (øre)
                    </label>
                    <input type="number" 
                           id="purchase_price" 
                           name="purchase_price" 
                           value="{{ old('purchase_price', $item->purchase_price) }}"
                           min="0"
                           step="1"
                           placeholder="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-150 @error('purchase_price') border-danger-500 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Oppgi beløp i øre (100 øre = 1 kr)</p>
                    @error('purchase_price')
                        <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Target Price -->
                <div>
                    <label for="target_price" class="block text-sm font-medium text-gray-700 mb-1">
                        Målpris (øre)
                    </label>
                    <input type="number" 
                           id="target_price" 
                           name="target_price" 
                           value="{{ old('target_price', $item->target_price) }}"
                           min="0"
                           step="1"
                           placeholder="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-150 @error('target_price') border-danger-500 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Oppgi beløp i øre (100 øre = 1 kr)</p>
                    @error('target_price')
                        <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                        Status <span class="text-danger-600">*</span>
                    </label>
                    <select id="status" 
                            name="status" 
                            x-model="newStatus"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-150 @error('status') border-danger-500 @enderror">
                        <option value="available">Tilgjengelig</option>
                        <option value="reserved">Reservert</option>
                        <option value="sold">Solgt</option>
                        <option value="archived">Arkivert</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Warning for status change -->
                <div x-show="showWarning()" class="p-4 bg-warning-50 border border-warning-200 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-warning-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-warning-800">
                                Advarsel: Du endrer status fra "{{ $item->status === 'sold' ? 'Solgt' : 'Arkivert' }}" til "Tilgjengelig". 
                                Er du sikker på at dette er riktig?
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-3 pt-4">
                    <a href="{{ route('admin.items.index') }}" 
                       class="px-4 py-2 bg-white text-gray-700 font-medium rounded-lg border border-gray-300 hover:bg-gray-50 focus:ring-4 focus:ring-gray-200 transition-colors duration-150">
                        Avbryt
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 focus:ring-4 focus:ring-primary-200 transition-colors duration-150">
                        Lagre endringer
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
