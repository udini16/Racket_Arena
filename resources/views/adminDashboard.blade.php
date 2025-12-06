<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">

    <nav class="bg-gray-900 text-white px-6 py-4 flex justify-between items-center shadow-lg">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-shield-halved text-emerald-400 text-xl"></i>
            <span class="font-bold text-lg">Racket Arena <span class="text-gray-400 font-normal">| Admin</span></span>
        </div>
        <button onclick="logout()" class="text-gray-300 hover:text-white text-sm">Logout</button>
    </nav>

    <div class="max-w-7xl mx-auto p-6">
        
        <!-- Stats Section -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8" id="statsContainer">
            <!-- Loaded via JS -->
            <div class="bg-white p-4 rounded shadow animate-pulse h-24"></div>
            <div class="bg-white p-4 rounded shadow animate-pulse h-24"></div>
            <div class="bg-white p-4 rounded shadow animate-pulse h-24"></div>
            <div class="bg-white p-4 rounded shadow animate-pulse h-24"></div>
        </div>

        <!-- Management Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                <h2 class="text-lg font-bold text-gray-800">All Bookings</h2>
                <button onclick="loadAllBookings()" class="text-blue-600 text-sm hover:underline"><i class="fa-solid fa-rotate-right"></i> Refresh</button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-100 text-xs uppercase font-medium text-gray-500">
                        <tr>
                            <th class="p-4">ID</th>
                            <th class="p-4">Customer</th>
                            <th class="p-4">Court</th>
                            <th class="p-4">Time</th>
                            <th class="p-4">Total</th>
                            <th class="p-4">Status</th>
                            <th class="p-4">Action</th>
                        </tr>
                    </thead>
                    <tbody id="bookingsTableBody" class="divide-y divide-gray-100"></tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        const token = localStorage.getItem('token');
        if (!token) window.location.href = '/login';

        // Load Data
        loadStats();
        loadAllBookings();

        function logout() {
            localStorage.clear();
            window.location.href = '/login';
        }

        async function loadStats() {
            try {
                const res = await fetch('/api/admin/stats', {
                    headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
                });
                const json = await res.json();
                const stats = json.data;
                const container = document.getElementById('statsContainer');
                
                let html = '';
                for (const [key, value] of Object.entries(stats)) {
                    html += `
                        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                            <div class="text-xs text-gray-500 uppercase font-bold tracking-wider mb-1">${key.replace('_', ' ')}</div>
                            <div class="text-2xl font-bold text-gray-800">${value}</div>
                        </div>
                    `;
                }
                container.innerHTML = html;
            } catch(e) { console.error('Stats error', e); }
        }

        async function loadAllBookings() {
            const res = await fetch('/api/bookings', {
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });
            const json = await res.json();
            const tbody = document.getElementById('bookingsTableBody');
            
            tbody.innerHTML = json.data.map(b => `
                <tr class="hover:bg-gray-50 transition">
                    <td class="p-4 text-gray-400">#${b.id}</td>
                    <td class="p-4 font-medium text-gray-800">${b.user ? b.user.name : 'Unknown'}</td>
                    <td class="p-4">${b.court ? b.court.name : 'Unknown'}</td>
                    <td class="p-4">${new Date(b.start_time).toLocaleDateString()} ${new Date(b.start_time).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</td>
                    <td class="p-4 font-mono">$${b.total_price}</td>
                    <td class="p-4"><span class="px-2 py-1 rounded text-xs font-bold ${getStatusColor(b.status)} uppercase">${b.status}</span></td>
                    <td class="p-4">
                        ${b.status === 'pending' || b.status === 'confirmed' ? `
                            <button onclick="updateStatus(${b.id}, 'confirmed')" class="text-emerald-500 hover:text-emerald-700 mr-3" title="Approve"><i class="fa-solid fa-check"></i></button>
                            <button onclick="updateStatus(${b.id}, 'cancelled')" class="text-red-500 hover:text-red-700" title="Reject"><i class="fa-solid fa-xmark"></i></button>
                        ` : '<span class="text-gray-300">-</span>'}
                    </td>
                </tr>
            `).join('');
        }

        async function updateStatus(id, status) {
            if(!confirm(`Mark booking #${id} as ${status}?`)) return;
            await fetch(`/api/bookings/${id}/status`, {
                method: 'PUT',
                headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ status })
            });
            loadAllBookings();
        }

        function getStatusColor(status) {
            if (status === 'confirmed') return 'bg-emerald-100 text-emerald-700';
            if (status === 'cancelled') return 'bg-red-100 text-red-700';
            return 'bg-yellow-100 text-yellow-700';
        }
    </script>
</body>
</html>