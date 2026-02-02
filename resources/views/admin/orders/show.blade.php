<x-admin-layout>
    <x-slot name="title">Ordre {{ $order->order_number }}</x-slot>

    <!-- Header -->
    <div class="flex justify-between items-start mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900">Ordre {{ $order->order_number }}</h2>
            <p class="mt-1 text-sm text-gray-600">
                Kunde: <a href="{{ route('admin.customers.show', $order->customer) }}" class="text-primary-600 hover:text-primary-700">{{ $order->customer->name }}</a>
            </p>
            @if($order->status === 'open')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-success-100 text-success-800 mt-2">
                    Åpen
                </span>
            @elseif($order->status === 'closed')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 mt-2">
                    Lukket
                </span>
            @else
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-danger-100 text-danger-800 mt-2">
                    Kansellert
                </span>
            @endif
            @if($isOverpaid)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-warning-100 text-warning-800 mt-2 ml-2">
                    Overbetalt
                </span>
            @endif
        </div>
        @if($order->status === 'open')
            <div class="flex space-x-3">
                <button onclick="confirmCloseOrder()" 
                        class="px-4 py-2 bg-success-600 text-white font-medium rounded-lg hover:bg-success-700 focus:ring-4 focus:ring-success-200 transition-colors duration-150">
                    Lukk ordre
                </button>
                <button onclick="confirmCancelOrder()" 
                        class="px-4 py-2 bg-danger-600 text-white font-medium rounded-lg hover:bg-danger-700 focus:ring-4 focus:ring-danger-200 transition-colors duration-150">
                    Kanseller ordre
                </button>
            </div>
        @endif
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 gap-6 mb-8 sm:grid-cols-3">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-500">Total</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ format_nok($order->total_amount) }}</p>
            @if($order->total_amount !== $autoCalculatedTotal)
                <p class="text-xs text-gray-500 mt-1">Auto: {{ format_nok($autoCalculatedTotal) }}</p>
            @endif
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-500">Betalt</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ format_nok($order->paid_amount) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-500">Utestående</p>
            <p class="text-2xl font-bold {{ $isOverpaid ? 'text-warning-600' : 'text-gray-900' }} mt-1">{{ format_nok($outstanding) }}</p>
        </div>
    </div>

    <!-- Manual Total Override -->
    @if($order->status === 'open')
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Overstyr totalbeløp</h3>
            <form method="POST" action="{{ route('admin.orders.update-total', $order) }}" class="flex items-end space-x-4">
                @csrf
                @method('PATCH')
                <div class="flex-1">
                    <label for="total_amount" class="block text-sm font-medium text-gray-700 mb-2">
                        Totalbeløp (øre)
                    </label>
                    <input type="number" 
                           id="total_amount" 
                           name="total_amount" 
                           value="{{ $order->total_amount }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                           required>
                </div>
                <button type="submit" 
                        class="px-4 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 focus:ring-4 focus:ring-primary-200 transition-colors duration-150">
                    Oppdater
                </button>
            </form>
        </div>
    @endif

    <!-- Order Lines -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Ordrelinjer</h3>
            @if($order->status === 'open')
                <button onclick="openAddItemModal()" 
                        class="px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 focus:ring-4 focus:ring-primary-200 transition-colors duration-150">
                    Legg til vare
                </button>
            @endif
        </div>
        
        @if($order->orderLines->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Vare
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Enhetspris
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Antall
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total
                            </th>
                            @if($order->status === 'open')
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Handlinger
                                </th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($order->orderLines as $line)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $line->item->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="text-sm text-gray-900">{{ format_nok($line->unit_price) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="text-sm text-gray-900">{{ $line->quantity }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="text-sm font-semibold text-gray-900">{{ format_nok($line->total) }}</div>
                                </td>
                                @if($order->status === 'open')
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button onclick="confirmDeleteLine({{ $line->id }})" 
                                                class="text-danger-600 hover:text-danger-700">
                                            Slett
                                        </button>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Ingen varer ennå</h3>
                <p class="mt-1 text-sm text-gray-500">Legg til varer i denne ordren.</p>
            </div>
        @endif
    </div>

    <!-- Payments -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Betalinger</h3>
            @if($order->status === 'open')
                <button onclick="openAddPaymentModal()" 
                        class="px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 focus:ring-4 focus:ring-primary-200 transition-colors duration-150">
                    Registrer betaling
                </button>
            @endif
        </div>
        
        @if($order->payments->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Dato
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Beløp
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Metode
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Notat
                            </th>
                            @if($order->status === 'open')
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Handlinger
                                </th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($order->payments as $payment)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $payment->paid_at->format('d.m.Y') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="text-sm font-semibold text-gray-900">{{ format_nok($payment->amount) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $payment->payment_method ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-500">{{ $payment->note ?? '-' }}</div>
                                </td>
                                @if($order->status === 'open')
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button onclick="confirmDeletePayment({{ $payment->id }})" 
                                                class="text-danger-600 hover:text-danger-700">
                                            Slett
                                        </button>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Ingen betalinger ennå</h3>
                <p class="mt-1 text-sm text-gray-500">Registrer betalinger for denne ordren.</p>
            </div>
        @endif
    </div>

    <!-- Add Item Modal -->
    <div id="addItemModal" class="fixed inset-0 z-50 overflow-y-auto hidden" x-data="{ open: false }" x-show="open" style="display: none;">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" @click="open = false"></div>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Legg til vare</h3>
                <form method="POST" action="{{ route('admin.orders.lines.store', $order) }}">
                    @csrf
                    <div class="mb-4">
                        <label for="item_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Vare <span class="text-danger-600">*</span>
                        </label>
                        <select id="item_id" 
                                name="item_id" 
                                onchange="updateUnitPrice()"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                required>
                            <option value="">Velg vare</option>
                            @foreach($availableItems as $item)
                                <option value="{{ $item->id }}" data-price="{{ $item->target_price }}">
                                    {{ $item->name }} ({{ format_nok($item->target_price) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">
                            Antall <span class="text-danger-600">*</span>
                        </label>
                        <input type="number" 
                               id="quantity" 
                               name="quantity" 
                               value="1"
                               min="1"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                               required>
                    </div>
                    <div class="mb-6">
                        <label for="unit_price" class="block text-sm font-medium text-gray-700 mb-2">
                            Enhetspris (øre) <span class="text-danger-600">*</span>
                        </label>
                        <input type="number" 
                               id="unit_price" 
                               name="unit_price" 
                               value="0"
                               min="0"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                               required>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" 
                                @click="open = false" 
                                class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
                            Avbryt
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                            Legg til
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Payment Modal -->
    <div id="addPaymentModal" class="fixed inset-0 z-50 overflow-y-auto hidden" x-data="{ open: false }" x-show="open" style="display: none;">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" @click="open = false"></div>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Registrer betaling</h3>
                <form method="POST" action="{{ route('admin.orders.payments.store', $order) }}">
                    @csrf
                    <div class="mb-4">
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                            Beløp (øre) <span class="text-danger-600">*</span>
                        </label>
                        <input type="number" 
                               id="amount" 
                               name="amount" 
                               value="{{ max(0, $outstanding) }}"
                               min="1"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                               required>
                    </div>
                    <div class="mb-4">
                        <label for="paid_at" class="block text-sm font-medium text-gray-700 mb-2">
                            Betalingsdato <span class="text-danger-600">*</span>
                        </label>
                        <input type="date" 
                               id="paid_at" 
                               name="paid_at" 
                               value="{{ date('Y-m-d') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                               required>
                    </div>
                    <div class="mb-4">
                        <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">
                            Betalingsmetode
                        </label>
                        <input type="text" 
                               id="payment_method" 
                               name="payment_method" 
                               placeholder="F.eks. Vipps, Kontant, Bankoverføring"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div class="mb-6">
                        <label for="note" class="block text-sm font-medium text-gray-700 mb-2">
                            Notat
                        </label>
                        <textarea id="note" 
                                  name="note" 
                                  rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                  placeholder="Valgfritt notat..."></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" 
                                @click="open = false" 
                                class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
                            Avbryt
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                            Registrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Order Line Modal -->
    <div id="deleteLineModal" class="fixed inset-0 z-50 overflow-y-auto hidden" x-data="{ open: false, lineId: null }" x-show="open" style="display: none;">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" @click="open = false"></div>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Slett ordrelinje?</h3>
                <p class="text-sm text-gray-600 mb-6">
                    Er du sikker på at du vil fjerne denne varen fra ordren? Varen vil bli tilgjengelig igjen.
                </p>
                <div class="flex justify-end space-x-3">
                    <button @click="open = false" 
                            class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Avbryt
                    </button>
                    <form :action="`{{ route('admin.orders.show', $order) }}/lines/${lineId}`" method="POST">
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

    <!-- Delete Payment Modal -->
    <div id="deletePaymentModal" class="fixed inset-0 z-50 overflow-y-auto hidden" x-data="{ open: false, paymentId: null }" x-show="open" style="display: none;">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" @click="open = false"></div>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Slett betaling?</h3>
                <p class="text-sm text-gray-600 mb-6">
                    Er du sikker på at du vil slette denne betalingen?
                </p>
                <div class="flex justify-end space-x-3">
                    <button @click="open = false" 
                            class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Avbryt
                    </button>
                    <form :action="`{{ route('admin.orders.show', $order) }}/payments/${paymentId}`" method="POST">
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

    <!-- Close Order Modal -->
    <div id="closeOrderModal" class="fixed inset-0 z-50 overflow-y-auto hidden" x-data="{ open: false }" x-show="open" style="display: none;">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" @click="open = false"></div>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Lukk ordre?</h3>
                <p class="text-sm text-gray-600 mb-6">
                    Er du sikker på at du vil lukke denne ordren? Alle varer vil bli merket som solgt.
                </p>
                <div class="flex justify-end space-x-3">
                    <button @click="open = false" 
                            class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Avbryt
                    </button>
                    <form method="POST" action="{{ route('admin.orders.close', $order) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" 
                                class="px-4 py-2 bg-success-600 text-white rounded-lg hover:bg-success-700">
                            Lukk ordre
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel Order Modal -->
    <div id="cancelOrderModal" class="fixed inset-0 z-50 overflow-y-auto hidden" x-data="{ open: false }" x-show="open" style="display: none;">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" @click="open = false"></div>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Kanseller ordre?</h3>
                <p class="text-sm text-gray-600 mb-6">
                    Er du sikker på at du vil kansellere denne ordren? Dette vil fjerne alle linjer og betalinger, og alle varer vil bli tilgjengelige igjen.
                </p>
                <div class="flex justify-end space-x-3">
                    <button @click="open = false" 
                            class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Avbryt
                    </button>
                    <form method="POST" action="{{ route('admin.orders.cancel', $order) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" 
                                class="px-4 py-2 bg-danger-600 text-white rounded-lg hover:bg-danger-700">
                            Kanseller ordre
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openAddItemModal() {
            const modal = document.getElementById('addItemModal');
            modal.classList.remove('hidden');
            modal.setAttribute('x-data', '{ open: true }');
        }

        function openAddPaymentModal() {
            const modal = document.getElementById('addPaymentModal');
            modal.classList.remove('hidden');
            modal.setAttribute('x-data', '{ open: true }');
        }

        function confirmDeleteLine(lineId) {
            const modal = document.getElementById('deleteLineModal');
            modal.classList.remove('hidden');
            modal.setAttribute('x-data', `{ open: true, lineId: ${lineId} }`);
        }

        function confirmDeletePayment(paymentId) {
            const modal = document.getElementById('deletePaymentModal');
            modal.classList.remove('hidden');
            modal.setAttribute('x-data', `{ open: true, paymentId: ${paymentId} }`);
        }

        function confirmCloseOrder() {
            const modal = document.getElementById('closeOrderModal');
            modal.classList.remove('hidden');
            modal.setAttribute('x-data', '{ open: true }');
        }

        function confirmCancelOrder() {
            const modal = document.getElementById('cancelOrderModal');
            modal.classList.remove('hidden');
            modal.setAttribute('x-data', '{ open: true }');
        }

        function updateUnitPrice() {
            const select = document.getElementById('item_id');
            const priceInput = document.getElementById('unit_price');
            const selectedOption = select.options[select.selectedIndex];
            const price = selectedOption.getAttribute('data-price');
            if (price) {
                priceInput.value = price;
            }
        }
    </script>
</x-admin-layout>
