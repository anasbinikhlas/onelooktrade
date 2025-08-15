<x-app-layout>
  <x-slot name="header"><h2 class="text-xl">Alerts</h2></x-slot>
  <div class="p-6 max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
      <h3 class="font-semibold mb-4">Create Alert</h3>
      <form id="alert-form" class="grid grid-cols-1 md:grid-cols-5 gap-4">
        @csrf
        <input type="text" name="symbol" placeholder="BTC" class="border rounded p-2 dark:bg-gray-900 dark:border-gray-700">
        <select name="condition" class="border rounded p-2 dark:bg-gray-900 dark:border-gray-700">
          <option value="above">Above</option>
          <option value="below">Below</option>
        </select>
        <input type="number" step="0.00000001" name="threshold" placeholder="50000" class="border rounded p-2 dark:bg-gray-900 dark:border-gray-700">
        <select name="channel" class="border rounded p-2 dark:bg-gray-900 dark:border-gray-700">
          <option value="">None</option>
          <option value="email">Email</option>
          <option value="telegram">Telegram</option>
        </select>
        <button type="submit" class="px-4 py-2 rounded bg-indigo-600 text-white">Save</button>
      </form>
      <p id="alert-msg" class="text-sm mt-2 text-gray-500"></p>
    </div>

    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
      <h3 class="font-semibold mb-4">Your Alerts</h3>
      @isset($alerts)
        <ul class="space-y-2">
          @forelse($alerts as $a)
            <li class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
              <span>{{ $a->symbol }} — {{ $a->condition }} {{ $a->threshold }} ({{ $a->active ? 'active':'off' }})</span>
              <small class="text-gray-500">
                Last triggered: {{ $a->last_triggered_at?->diffForHumans() ?? 'never' }}
              </small>
            </li>
          @empty
            <li class="text-gray-500">No alerts yet.</li>
          @endforelse
        </ul>
      @endisset
    </div>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <script>
    const form = document.getElementById('alert-form');
    form?.addEventListener('submit', async (e) => {
      e.preventDefault();
      const data = new FormData(form);
      try {
        await axios.post('{{ route("alerts.store") }}', data);
        document.getElementById('alert-msg').textContent = 'Alert saved!';
        setTimeout(() => location.reload(), 700);
      } catch (err) {
        document.getElementById('alert-msg').textContent = 'Failed to save alert.';
        console.error(err);
      }
    });
  </script>
</x-app-layout>
