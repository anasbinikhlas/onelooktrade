<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            The Trading Floor — Dashboard
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Stats Row --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">BTC Price</h3>
                    <p class="text-2xl font-bold text-yellow-500 btc-price">$65,430</p>
                </div>
                <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">ETH Price</h3>
                    <p class="text-2xl font-bold text-indigo-500 eth-price">$3,240</p>
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
                <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow" style="height: 320px;">
                    <h3 class="text-lg font-bold mb-4 text-gray-700 dark:text-gray-300">Whale Movement (Last 7 Days)</h3>
                    <canvas id="whaleChart" style="max-height:240px;"></canvas>
                </div>
                <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow" style="height: 320px;">
                    <h3 class="text-lg font-bold mb-4 text-gray-700 dark:text-gray-300">Market Sentiment</h3>
                    <canvas id="sentimentChart" style="max-height:240px;"></canvas>
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
                            <th class="p-2">Time</th>
                        </tr>
                    </thead>
                    <tbody id="whale-table-body">
                        <!-- rows injected by JS -->
                    </tbody>
                </table>
            </div>

            {{-- News & Alerts --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
                    <h3 class="text-lg font-bold mb-4 text-gray-700 dark:text-gray-300">📰 Latest Crypto News</h3>
                    <ul id="news-list" class="text-sm text-gray-700 dark:text-gray-200">
                        <!-- injected by JS -->
                    </ul>
                </div>

                <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
                    <h3 class="text-lg font-bold mb-4 text-gray-700 dark:text-gray-300">🔔 Alerts</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        No alerts set. Use the <a class="text-indigo-600 hover:underline" href="{{ route('alerts') }}">Alerts</a> page to create notifications.
                    </p>
                </div>
            </div>

        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    {{-- axios (CDN) --}}
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const whaleCtx = document.getElementById('whaleChart').getContext('2d');
        const sentimentCtx = document.getElementById('sentimentChart').getContext('2d');

        const whaleChart = new Chart(whaleCtx, {
            type: 'line',
            data: { labels: [], datasets: [
                { label: 'Inflow', data: [], borderColor: 'green', fill: false, tension: 0.3 },
                { label: 'Outflow', data: [], borderColor: 'red', fill: false, tension: 0.3 }
            ]},
            options: { responsive: true, maintainAspectRatio: false }
        });

        const sentimentChart = new Chart(sentimentCtx, {
            type: 'doughnut',
            data: { labels: ['Bullish','Bearish','Neutral'], datasets:[{ data:[50,30,20], backgroundColor:['#22c55e','#ef4444','#eab308'] }]},
            options: { responsive: true, maintainAspectRatio: false }
        });

        const btcPriceEl = document.querySelector('.btc-price');
        const ethPriceEl = document.querySelector('.eth-price');
        const whaleTableBody = document.querySelector('#whale-table-body');
        const newsList = document.querySelector('#news-list');

        function updateMarket() {
            axios.get('{{ route("market.data") }}')
            .then(res => {
                const d = res.data || {};
                if (d.btc?.price && btcPriceEl) btcPriceEl.textContent = '$' + Number(d.btc.price).toLocaleString();
                if (d.eth?.price && ethPriceEl) ethPriceEl.textContent = '$' + Number(d.eth.price).toLocaleString();

                // quick visualization using top coin changes (placeholder logic)
                if (Array.isArray(d.top)) {
                    const labels = d.top.map(t => t.symbol);
                    const inflowData = d.top.map(t => Math.max(0, Math.round((t.change || 0) * 10)));
                    const outflowData = d.top.map(t => Math.max(0, Math.round(-(t.change || 0) * 8)));

                    whaleChart.data.labels = labels;
                    whaleChart.data.datasets[0].data = inflowData;
                    whaleChart.data.datasets[1].data = outflowData;
                    whaleChart.update();
                }
            })
            .catch(err => console.error('market-data error', err));
        }

        function updateWhales() {
            axios.get('{{ route("whales.data") }}')
            .then(res => {
                const payload = res.data || {};
                const rows = payload.latest || payload || [];
                if (!whaleTableBody) return;
                whaleTableBody.innerHTML = '';
                rows.forEach(r => {
                    const tr = document.createElement('tr');
                    tr.className = 'border-b dark:border-gray-700';
                    const amt = (r.amount !== undefined && r.amount !== null)
                        ? (typeof r.amount === 'number' ? r.amount.toLocaleString() : r.amount)
                        : '';
                    tr.innerHTML = `
                        <td class="p-2 font-mono text-xs">${r.wallet || ''}</td>
                        <td class="p-2">${r.token || ''}</td>
                        <td class="p-2 ${r.type === 'inflow' ? 'text-green-500' : 'text-red-500'}">
                            ${r.type === 'inflow' ? '+' : (r.type === 'outflow' ? '-' : '')}${amt}
                        </td>
                        <td class="p-2">${r.type || ''}</td>
                        <td class="p-2 text-xs text-gray-400">${r.time || ''}</td>
                    `;
                    whaleTableBody.appendChild(tr);
                });
            })
            .catch(err => console.error('whales-data error', err));
        }

        function updateNews() {
            axios.get('{{ route("news.data") }}')
            .then(res => {
                const items = res.data || [];
                if (!newsList) return;
                newsList.innerHTML = '';
                items.forEach(n => {
                    const li = document.createElement('li');
                    li.className = 'border-b py-2';
                    const safeUrl = n.url || '#';
                    li.innerHTML = `<a href="${safeUrl}" target="_blank" rel="noopener" class="font-medium hover:underline">${n.title || ''}</a>
                                    <br><small class="text-gray-500">${(n.source || 'Unknown')} ${n.time ? '— ' + n.time : ''}</small>`;
                    newsList.appendChild(li);
                });
            })
            .catch(err => console.error('news-data error', err));
        }

        updateMarket();
        updateWhales();
        updateNews();

        setInterval(updateMarket, 30_000);
        setInterval(updateWhales, 25_000);
        setInterval(updateNews, 60_000);
    });
    </script>
</x-app-layout>
