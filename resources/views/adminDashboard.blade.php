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

    <!-- Navbar -->
    <nav class="bg-gray-900 text-white px-6 py-4 flex justify-between items-center shadow-lg sticky top-0 z-50">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-shield-halved text-emerald-400 text-xl"></i>
            <span class="font-bold text-lg">Racket Arena <span class="text-gray-400 font-normal">| Admin</span></span>
        </div>
        <button onclick="logout()" class="text-gray-300 hover:text-white text-sm transition">Logout</button>
    </nav>

    <div class="max-w-7xl mx-auto p-6">
        
        <!-- Header & Actions -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-bold text-gray-800">Dashboard Overview</h1>
            <button onclick="openCourtModal()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2 rounded-lg font-bold shadow-md transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Add New Court
            </button>
        </div>

        <!-- Stats Section -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8" id="statsContainer">
            <!-- Loading Skeletons -->
            <div class="bg-white p-4 rounded shadow animate-pulse h-24"></div>
            <div class="bg-white p-4 rounded shadow animate-pulse h-24"></div>
            <div class="bg-white p-4 rounded shadow animate-pulse h-24"></div>
            <div class="bg-white p-4 rounded shadow animate-pulse h-24"></div>
        </div>

        <!-- Courts List Section -->
        <div class="mb-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-800">🏸 Existing Courts</h2>
                <button onclick="loadCourts()" class="text-blue-600 text-sm hover:underline"><i class="fa-solid fa-rotate-right"></i> Refresh</button>
            </div>
            <div id="courtListContainer" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <!-- Courts loaded via JS -->
            </div>
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

    <!-- Add Court Modal -->
    <div id="addCourtModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all scale-95 opacity-0" id="modalContent">
            <div class="bg-emerald-600 p-4 flex justify-between items-center">
                <h3 class="text-white font-bold text-lg"><i class="fa-solid fa-table-tennis-paddle-ball mr-2"></i> Add New Court</h3>
                <button onclick="closeCourtModal()" class="text-emerald-200 hover:text-white"><i class="fa-solid fa-xmark fa-lg"></i></button>
            </div>
            
            <div class="p-6">
                <label class="block text-sm font-bold text-gray-700 mb-2">Court Name</label>
                <input type="text" id="newCourtName" placeholder="e.g. Pro Court 1" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
                <p class="text-xs text-gray-500 mt-2">New courts are set to "Active" by default.</p>
            </div>

            <div class="p-4 bg-gray-50 flex justify-end gap-3 border-t border-gray-100">
                <button onclick="closeCourtModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium">Cancel</button>
                <button onclick="submitNewCourt()" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold transition shadow-sm">Save Court</button>
            </div>
        </div>
    </div>

    <script>
        const token = localStorage.getItem('token');
        if (!token) window.location.href = '/login';

        // Load Data
        loadStats();
        loadCourts();
        loadAllBookings();

        function logout() {
            localStorage.clear();
            window.location.href = '/login';
        }

        // --- Court Management Logic ---
        async function loadCourts() {
            try {
                const res = await fetch('/api/courts');
                const json = await res.json();
                const container = document.getElementById('courtListContainer');
                
                if (json.data.length === 0) {
                    container.innerHTML = '<p class="text-gray-500 italic col-span-full">No courts found.</p>';
                    return;
                }

                container.innerHTML = json.data.map(court => `
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 flex justify-between items-center group hover:shadow-md transition">
                        <div>
                            <div class="font-bold text-gray-800">${court.name}</div>
                            <div class="text-xs text-gray-500">ID: ${court.id} • <span class="text-emerald-600 font-semibold">Active</span></div>
                        </div>
                        <button onclick="deleteCourt(${court.id})" class="text-gray-300 hover:text-red-500 transition p-2 bg-gray-50 hover:bg-red-50 rounded-full" title="Delete Court">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </div>
                `).join('');
            } catch(e) { console.error('Courts error', e); }
        }

        async function deleteCourt(id) {
            if(!confirm('Are you sure you want to delete this court? This action cannot be undone.')) return;
            
            try {
                const res = await fetch(`/api/courts/${id}`, {
                    method: 'DELETE',
                    headers: { 
                        'Authorization': `Bearer ${token}`, 
                        'Accept': 'application/json' 
                    }
                });
                
                if(res.ok) {
                    loadCourts(); // Refresh list
                    loadStats(); // Update stats
                } else {
                    alert('Failed to delete court');
                }
            } catch(e) {
                alert('Connection error');
            }
        }

        // --- Modal Logic ---
        function openCourtModal() {
            const modal = document.getElementById('addCourtModal');
            const content = document.getElementById('modalContent');
            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeCourtModal() {
            const modal = document.getElementById('addCourtModal');
            const content = document.getElementById('modalContent');
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
                document.getElementById('newCourtName').value = ''; 
            }, 200); 
        }

        async function submitNewCourt() {
            const name = document.getElementById('newCourtName').value;
            if (!name) return alert("Please enter a court name");

            try {
                const res = await fetch('/api/courts', {
                    method: 'POST',
                    headers: { 
                        'Authorization': `Bearer ${token}`, 
                        'Content-Type': 'application/json', 
                        'Accept': 'application/json' 
                    },
                    body: JSON.stringify({ name: name })
                });
                
                const json = await res.json();
                
                if (res.ok) {
                    alert('Court Added Successfully!');
                    closeCourtModal();
                    loadCourts(); // Refresh court list
                    loadStats();  // Refresh stats
                } else {
                    alert('Error: ' + (json.message || 'Failed to add court'));
                }
            } catch (e) {
                alert('Connection Error');
            }
        }

        // --- Existing Stats & Booking Logic ---
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
                        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-center items-center">
                            <div class="text-xs text-gray-500 uppercase font-bold tracking-wider mb-1">${key.replace('_', ' ')}</div>
                            <div class="text-3xl font-bold text-gray-800">${value}</div>
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
                    <td class="p-4 text-gray-400 text-xs">#${b.id}</td>
                    <td class="p-4 font-medium text-gray-800">${b.user ? b.user.name : 'Unknown'}</td>
                    <td class="p-4"><span class="bg-blue-50 text-blue-700 px-2 py-1 rounded text-xs font-bold">${b.court ? b.court.name : 'Unknown'}</span></td>
                    <td class="p-4 text-sm text-gray-600">
                        <div>${new Date(b.start_time).toLocaleDateString()}</div>
                        <div class="text-xs text-gray-400">${new Date(b.start_time).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</div>
                    </td>
                    <td class="p-4 font-mono text-sm font-bold">$${b.total_price}</td>
                    <td class="p-4"><span class="px-2 py-1 rounded text-xs font-bold ${getStatusColor(b.status)} uppercase">${b.status}</span></td>
                    <td class="p-4">
                        ${b.status === 'pending' || b.status === 'confirmed' ? `
                            <button onclick="updateStatus(${b.id}, 'confirmed')" class="text-emerald-500 hover:bg-emerald-50 p-2 rounded-full transition" title="Approve"><i class="fa-solid fa-check"></i></button>
                            <button onclick="updateStatus(${b.id}, 'cancelled')" class="text-red-500 hover:bg-red-50 p-2 rounded-full transition" title="Reject"><i class="fa-solid fa-xmark"></i></button>
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