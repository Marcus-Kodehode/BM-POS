<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Slett konto
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            Når kontoen din slettes, vil alle ressurser og data bli permanent slettet. Før du sletter kontoen din, vennligst last ned data eller informasjon du ønsker å beholde.
        </p>
        
        @php
            $outstanding = auth()->user()->orders()
                ->where('status', '!=', 'cancelled')
                ->get()
                ->sum(function($order) {
                    return $order->outstanding_amount;
                });
        @endphp
        
        @if($outstanding > 0)
            <div class="mt-4 p-4 bg-warning-50 border border-warning-200 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-warning-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-warning-800">
                            Utestående beløp
                        </h3>
                        <p class="mt-1 text-sm text-warning-700">
                            Du har et utestående beløp på <strong>{{ format_nok($outstanding) }}</strong>. Vennligst kontakt oss før du sletter kontoen din.
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >Slett konto</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-gray-900">
                Er du sikker på at du vil slette kontoen din?
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Når kontoen din slettes, vil alle ressurser og data bli permanent slettet. Vennligst skriv inn passordet ditt for å bekrefte at du ønsker å slette kontoen din permanent.
            </p>
            
            @if($outstanding > 0)
                <div class="mt-4 p-3 bg-warning-50 border border-warning-200 rounded">
                    <p class="text-sm text-warning-800">
                        <strong>Advarsel:</strong> Du har et utestående beløp på {{ format_nok($outstanding) }}.
                    </p>
                </div>
            @endif

            <div class="mt-6">
                <x-input-label for="password" value="Passord" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="Passord"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    Avbryt
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    Slett konto
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
