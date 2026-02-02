<x-admin-layout>
    <x-slot name="title">Ny ordre</x-slot>

    <div class="max-w-2xl">
        <h2 class="text-2xl font-semibold text-gray-900 mb-6">Opprett ny ordre</h2>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <form method="POST" action="{{ route('admin.orders.store') }}">
                @csrf

                <!-- Customer Selection -->
                <div class="mb-6">
                    <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Kunde <span class="text-danger-600">*</span>
                    </label>
                    <select id="customer_id" 
                            name="customer_id" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('customer_id') border-danger-500 @enderror"
                            required>
                        <option value="">Velg kunde</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }} ({{ $customer->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id')
                        <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notes -->
                <div class="mb-6">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Notater
                    </label>
                    <textarea id="notes" 
                              name="notes" 
                              rows="4"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('notes') border-danger-500 @enderror"
                              placeholder="Valgfrie notater om ordren...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('admin.orders.index') }}" 
                       class="px-4 py-2 bg-white text-gray-700 font-medium rounded-lg border border-gray-300 hover:bg-gray-50 focus:ring-4 focus:ring-gray-200 transition-colors duration-150">
                        Avbryt
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 focus:ring-4 focus:ring-primary-200 transition-colors duration-150">
                        Opprett ordre
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
