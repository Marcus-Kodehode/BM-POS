<x-admin-layout>
    <x-slot name="title">Rediger kunde</x-slot>

    <div class="max-w-2xl">
        <!-- Header -->
        <div class="mb-6">
            <h2 class="text-2xl font-semibold text-gray-900">Rediger kunde</h2>
            <p class="mt-1 text-sm text-gray-600">
                Oppdater kundens navn og e-postadresse.
            </p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <form method="POST" action="{{ route('admin.customers.update', $customer) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        Navn
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name', $customer->name) }}"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-150 @error('name') border-danger-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        E-post
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email', $customer->email) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-150 @error('email') border-danger-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                        Telefonnummer
                    </label>
                    <input type="text" 
                           id="phone" 
                           name="phone" 
                           value="{{ old('phone', $customer->phone) }}"
                           placeholder="f.eks. 12345678"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-150 @error('phone') border-danger-500 @enderror">
                    @error('phone')
                        <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Du må oppgi minst e-post eller telefonnummer.</p>
                </div>

                <!-- Info box -->
                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-gray-700">
                                Rolle og passord kan ikke endres her. Kunden kan endre sitt eget passord via profilen.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-3 pt-4">
                    <a href="{{ route('admin.customers.show', $customer) }}" 
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
