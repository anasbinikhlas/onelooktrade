{{-- resources/views/dashboard.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            🐋 Crypto Whale Movement Dashboard
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Stats Row --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">BTC Price</h3>
                    <p class="text-2xl font-bold text-yellow-500">$65,430</p>
                </div>
                <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">ETH Price</h3>
                    <p class="text-2xl font-bold text-indigo-500">$3,240</p>
                </div>
                <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Whale Inflow</h3>
                    <p class="text-2xl font-bold text-green-500">+4,250 BTC</p>
                </div>
                <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Whale Outflow</h3>
                    <p class="text-2xl font-bold text-red-500">-2,180 BTC</p>
                </div>
            </div>

            {{-- Charts Row --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
                    <h3 class="text-lg font-bold mb-4 text-gray-700 dark:text-gray-300">Whale Movement (Last 7 Days)</h3>
                    <canvas id="whaleChart"></canvas>
                </div>
                <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
                    <h3 class="text-lg font-bold mb-4 text-gray-700 dark:text-gray-300">Market Sentiment</h3>
                    <canvas id="sentimentChart"></canvas>
                </div>
            </div>

            {{-- Latest Whale Transactions --}}
            <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
                <h3 class="text-lg font-bold mb-4 text-gray-700 dark:text-gray-300">Latest Whale Transactions</h3>
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-700">
                            <th class="p-2">Wallet</th>
                            <th class="p-2">Token</th>
                            <th class="p-2">Amount</th>
                            <th class="p-2">Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b dark:border-gray-700">
                            <td class="p-2">0xAb12...89Cd</td>
                            <td class="p-2">BTC</td>
                            <td class="p-2 text-green-500">+500</td>
                            <td class="p-2">Inflow</td>
                        </tr>
                        <tr class="border-b dark:border-gray-700">
                            <td class="p-2">0xEf34...67Gh</td>
                            <td class="p-2">ETH</td>
                            <td class="p-2 text-red-500">-1,200</td>
                            <td class="p-2">Outflow</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const whaleCtx = document.getElementById('whaleChart').getContext('2d');
        new Chart(whaleCtx, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Inflow',
                    data: [500, 700, 600, 900, 1200, 800, 950],
                    borderColor: 'green',
                    fill: false
                }, {
                    label: 'Outflow',
                    data: [300, 400, 500, 600, 700, 650, 500],
                    borderColor: 'red',
                    fill: false
                }]
            }
        });

        const sentimentCtx = document.getElementById('sentimentChart').getContext('2d');
        new Chart(sentimentCtx, {
            type: 'doughnut',
            data: {
                labels: ['Bullish', 'Bearish', 'Neutral'],
                datasets: [{
                    data: [55, 30, 15],
                    backgroundColor: ['#22c55e', '#ef4444', '#eab308']
                }]
            }
        });
    </script>
</x-app-layout>
