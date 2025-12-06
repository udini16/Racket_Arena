<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 font-sans">

    <nav class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-user-tie text-purple-600 text-xl"></i>
            <span class="font-bold text-lg text-gray-800">Racket Arena <span class="bg-purple-100 text-purple-700 text-xs px-2 py-1 rounded ml-1">STAFF</span></span>
        </div>
        <button onclick="logout()" class="text-gray-500 hover:text-red-600 text-sm font-medium">Logout</button>
    </nav>

    <div class="max-w-6xl mx-auto p-6">
        
        <!-- Stats Row -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6" id="statsContainer"></div>

        <!-- Schedule -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="font-bold text-gray-800">Booking Schedule</h2>
                <button onclick="loadAllBookings()" class="text-purple-600 text-sm">Refresh</button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 text-xs uppercase font-medium text-gray-500">
                        <tr>
                            <th class="p-4">ID</th>
                            <th class="p-4">Court</th>
                            <th class="p-4">Time</th>
                            <th class="p-4">Status</th>
                            <th class="p-4">Action</th>
                        </tr>
                    </thead>
                    <tbody id="bookingsTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        const token = localStorage.getItem('token');
        if (!token) window.location.href = '/login';

        loadStats();
        loadAllBookings();

        function logout() {
            localStorage.clear();
            window.location.href = '/login';
        }

        async function loadStats() {
            try {
                // Use employee specific stats endpoint
                const res = await fetch('/api/employee/stats', {
                    headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
                });
                const json = await res.json();
                const container = document.getElementById('statsContainer');
                
                let html = '';
                for (const [key, value] of Object.entries(json.data)) {
                    html += `
                        <div class="bg-white p-4 rounded-lg border border-gray-200">
                            <div class="text-xs text-gray-400 uppercase font-bold mb-1">${key.replace('_', ' ')}</div>
                            <div class="text-xl font-bold text-gray-800">${value}</div>
                        </div>
                    `;
                }
                container.innerHTML = html;
            } catch(e) {}
        }

        async function loadAllBookings() {
            const res = await fetch('/api/bookings', {
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });
            const json = await res.json();
            const tbody = document.getElementById('bookingsTableBody');
            
            tbody.innerHTML = json.data.map(b => `
                <tr class="border-b last:border-0 hover:bg-gray-50">
                    <td class="p-4 text-gray-400">#${b.id}</td>
                    <td class="p-4 font-bold text-gray-700">${b.court.name}</td>
                    <td class="p-4">${new Date(b.start_time).toLocaleString()}</td>
                    <td class="p-4"><span class="px-2 py-1 rounded text-xs font-bold ${getStatusColor(b.status)} uppercase">${b.status}</span></td>
                    <td class="p-4">
                        ${b.status === 'pending' ? `
                            <button onclick="updateStatus(${b.id}, 'confirmed')" class="text-emerald-600 hover:text-emerald-800 mr-2 font-bold text-xs">ACCEPT</button>
                            <button onclick="updateStatus(${b.id}, 'cancelled')" class="text-red-600 hover:text-red-800 font-bold text-xs">REJECT</button>
                        ` : '<span class="text-gray-300 text-xs">LOCKED</span>'}
                    </td>
                </tr>
            `).join('');
        }

        async function updateStatus(id, status) {
            if(!confirm(`Confirm ${status}?`)) return;
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