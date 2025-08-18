<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Alerts') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Create Alert Card --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form id="alertForm" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        @csrf
                        <div>
                            <label class="block text-sm mb-1">Symbol</label>
                            <input name="symbol" class="w-full rounded border-gray-300 dark:bg-gray-700" placeholder="BTC" required>
                        </div>

                        <div>
                            <label class="block text-sm mb-1">Condition</label>
                            <select name="condition" class="w-full rounded border-gray-300 dark:bg-gray-700" required>
                                <option value="above">Above</option>
                                <option value="below">Below</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm mb-1">Threshold</label>
                            <input name="threshold" type="number" step="0.00000001" class="w-full rounded border-gray-300 dark:bg-gray-700" placeholder="65000" required>
                        </div>

                        <div>
                            <label class="block text-sm mb-1">Channel</label>
                            <select name="channel" class="w-full rounded border-gray-300 dark:bg-gray-700">
                                <option value="">None</option>
                                <option value="email">Email</option>
                                <option value="telegram">Telegram</option>
                            </select>
                        </div>

                        <div class="flex items-end">
                            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded">
                                Add Alert
                            </button>
                        </div>
                    </form>

                    <p id="alertStatus" class="text-sm mt-3"></p>
                </div>
            </div>

            {{-- List Alerts --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="font-semibold mb-4">Your Alerts</h3>

                    @if($alerts->isEmpty())
                        <p class="text-sm text-gray-500">No alerts yet. Create one above.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-left border-b border-gray-700/20">
                                        <th class="py-2 pr-4">Symbol</th>
                                        <th class="py-2 pr-4">Condition</th>
                                        <th class="py-2 pr-4">Threshold</th>
                                        <th class="py-2 pr-4">Channel</th>
                                        <th class="py-2 pr-4">Active</th>
                                        <th class="py-2 pr-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($alerts as $a)
                                        <tr class="border-b border-gray-700/10">
                                            <td class="py-2 pr-4">{{ $a->symbol }}</td>
                                            <td class="py-2 pr-4">{{ ucfirst($a->condition) }}</td>
                                            <td class="py-2 pr-4">{{ rtrim(rtrim(number_format($a->threshold, 8, '.', ''), '0'), '.') }}</td>
                                            <td class="py-2 pr-4">{{ $a->channel ?: '—' }}</td>
                                            <td class="py-2 pr-4">{{ $a->active ? 'Yes' : 'No' }}</td>
                                            <td class="py-2 pr-4">
                                                <form method="POST" action="{{ route('alerts.destroy', $a) }}" onsubmit="return confirm('Delete this alert?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="px-2 py-1 bg-red-600 hover:bg-red-700 text-white rounded">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    {{-- Axios + CSRF --}}
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        document.getElementById('alertForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            try {
                await axios.post('{{ route("alerts.store") }}', data, {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                document.getElementById('alertStatus').textContent = '✅ Alert created';
                document.getElementById('alertStatus').className = 'text-green-600 text-sm mt-3';
                // simple reload to reflect new alert in the list
                setTimeout(() => location.reload(), 600);
            } catch (err) {
                document.getElementById('alertStatus').textContent = '❌ Failed to create alert';
                document.getElementById('alertStatus').className = 'text-red-600 text-sm mt-3';
                console.error(err);
            }
        });
    </script>
</x-app-layout>
