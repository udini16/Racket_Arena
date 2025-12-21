<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Dashboard - Racket Arena</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Chart.js for Graphs -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-slate-100 font-sans">

    <!-- Layout Container -->
    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar -->
        <aside class="w-64 bg-slate-900 text-white hidden md:flex flex-col">
            <div class="p-6 flex items-center gap-2 border-b border-slate-800">
                <i class="fa-solid fa-user-tie text-purple-500 text-xl"></i>
                <span class="font-bold text-lg tracking-wide">Racket Arena <span class="bg-purple-600 text-white text-[10px] px-1.5 py-0.5 rounded ml-1 align-top">STAFF</span></span>
            </div>
            <nav class="flex-1 p-4 space-y-2">
                <button onclick="switchTab('dashboard')" id="nav-dashboard" class="w-full flex items-center gap-3 px-4 py-3 bg-slate-800 rounded-lg text-purple-400 font-medium transition">
                    <i class="fa-solid fa-gauge w-6 text-center"></i> Dashboard
                </button>
                <button onclick="switchTab('bookings')" id="nav-bookings" class="w-full flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition">
                    <i class="fa-regular fa-calendar-check w-6 text-center"></i> Bookings
                </button>
            </nav>
            <div class="p-4 border-t border-slate-800">
                <button onclick="openProfile()" class="flex items-center gap-3 w-full px-4 py-3 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition">
                    <i class="fa-solid fa-user-gear w-6 text-center"></i> Profile
                </button>
                <button onclick="logout()" class="flex items-center gap-3 w-full px-4 py-3 text-red-400 hover:text-red-300 hover:bg-red-900/20 rounded-lg transition mt-2">
                    <i class="fa-solid fa-right-from-bracket w-6 text-center"></i> Logout
                </button>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden relative">
            
            <!-- Mobile Header -->
            <header class="bg-white shadow-sm border-b px-6 py-4 flex justify-between items-center md:hidden">
                <span class="font-bold text-slate-800">Staff Panel</span>
                <button onclick="logout()" class="text-red-500"><i class="fa-solid fa-power-off"></i></button>
            </header>

            <main class="flex-1 overflow-y-auto p-6" id="main-container">

                <!-- VIEW 1: DASHBOARD OVERVIEW -->
                <div id="view-dashboard" class="space-y-6 animate-in fade-in duration-300">
                    <h1 class="text-2xl font-bold text-slate-800">Overview</h1>
                    
                    <!-- Stats Row -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6" id="statsContainer">
                        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                            <div class="text-slate-500 text-sm font-bold uppercase mb-2">Total Bookings</div>
                            <div class="text-3xl font-bold text-slate-800" id="statBookings">
                                <i class="fa-solid fa-spinner fa-spin text-lg text-slate-400"></i>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                            <div class="text-slate-500 text-sm font-bold uppercase mb-2">Total Courts</div>
                            <div class="text-3xl font-bold text-purple-600" id="statCourts">
                                <i class="fa-solid fa-spinner fa-spin text-lg text-purple-400"></i>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                            <div class="text-slate-500 text-sm font-bold uppercase mb-2">Total Revenue</div>
                            <div class="text-3xl font-bold text-blue-600" id="statRevenue">
                                <i class="fa-solid fa-spinner fa-spin text-lg text-blue-400"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Graphs Section -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                            <h3 class="font-bold text-slate-700 mb-4">Bookings by Status</h3>
                            <div class="h-64 relative"><canvas id="bookingStatusChart"></canvas></div>
                        </div>
                        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                            <h3 class="font-bold text-slate-700 mb-4">Court Availability Status</h3>
                            <div class="h-64 relative flex justify-center"><canvas id="courtStatusChart"></canvas></div>
                        </div>
                    </div>
                </div>

                <!-- VIEW 2: BOOKING MANAGEMENT -->
                <div id="view-bookings" class="space-y-6 hidden animate-in fade-in duration-300">
                    <div class="flex justify-between items-center">
                        <h1 class="text-2xl font-bold text-slate-800">Booking Management</h1>
                        <button onclick="initDashboard()" class="text-purple-600 text-sm hover:underline hover:text-purple-800 flex items-center gap-1 transition">
                            <i class="fa-solid fa-rotate"></i> Refresh List
                        </button>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                        <table class="w-full text-left text-sm text-gray-600">
                            <thead class="bg-gray-100 text-xs uppercase font-medium text-gray-500 border-b">
                                <tr>
                                    <th class="p-4 whitespace-nowrap">ID</th>
                                    <th class="p-4 whitespace-nowrap">Customer</th>
                                    <th class="p-4 whitespace-nowrap">Date</th>
                                    <th class="p-4 whitespace-nowrap">Time</th>
                                    <th class="p-4 w-72 whitespace-nowrap">Assigned Court</th>
                                    <th class="p-4 whitespace-nowrap">Status</th>
                                    <th class="p-4 text-right whitespace-nowrap">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="bookingsTableBody" class="divide-y divide-gray-100"></tbody>
                        </table>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <!-- STAFF PROFILE MODAL -->
    <div id="profileModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center backdrop-blur-sm transition-opacity opacity-0">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 transform scale-95 transition-transform duration-200" id="profileContent">
            <div class="bg-purple-600 p-6 rounded-t-2xl relative overflow-hidden">
                <button onclick="closeProfile()" class="absolute top-4 right-4 text-white/80 hover:text-white transition"><i class="fa-solid fa-xmark text-xl"></i></button>
                <div class="flex items-center gap-4 relative z-10">
                    <div class="w-16 h-16 bg-white text-purple-600 rounded-full flex items-center justify-center text-3xl font-bold border-4 border-white/20"><i class="fa-solid fa-user-tie"></i></div>
                    <div class="text-white">
                        <h2 id="pName" class="text-xl font-bold text-white">Loading...</h2>
                        <p id="pRole" class="text-purple-100 text-sm uppercase tracking-wide font-medium">Staff Member</p>
                    </div>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div class="space-y-1"><label class="text-xs font-bold text-gray-400 uppercase">Work Email</label><div id="pEmail" class="text-gray-800 font-medium border-b border-gray-100 pb-2">...</div></div>
                <div class="space-y-1"><label class="text-xs font-bold text-gray-400 uppercase">Employee ID</label><div id="pId" class="text-gray-800 font-medium border-b border-gray-100 pb-2">...</div></div>
                <div class="pt-2"><button onclick="closeProfile()" class="w-full py-2.5 bg-gray-100 text-gray-600 font-bold rounded-lg hover:bg-gray-200 transition">Close</button></div>
            </div>
        </div>
    </div>

    <script>
        const token = localStorage.getItem('token');
        let availableCourts = [];
        let allBookingsData = [];
        let editingRows = new Set();
        let bookingChartInstance = null;
        let courtChartInstance = null;

        initDashboard();

        function logout() { localStorage.clear(); window.location.href = '/login'; }

        // --- NAVIGATION ---
        function switchTab(tabName) {
            ['dashboard', 'bookings'].forEach(t => {
                document.getElementById(`view-${t}`).classList.add('hidden');
                document.getElementById(`nav-${t}`).classList.remove('bg-slate-800', 'text-purple-400');
                document.getElementById(`nav-${t}`).classList.add('text-slate-400');
            });
            document.getElementById(`view-${tabName}`).classList.remove('hidden');
            document.getElementById(`nav-${tabName}`).classList.remove('text-slate-400');
            document.getElementById(`nav-${tabName}`).classList.add('bg-slate-800', 'text-purple-400');
            if (tabName === 'dashboard') loadStats(); 
        }

        async function initDashboard() {
            await Promise.all([loadStats(), loadCourts()]);
            await loadAllBookings();
        }

        function parseDbDate(dateStr) {
            if (!dateStr) return new Date();
            const cleanStr = dateStr.replace('T', ' ').replace('Z', '').split('.')[0];
            const [d, t] = cleanStr.split(' ');
            const [y, m, day] = d.split('-').map(Number);
            const [h, min, s] = t.split(':').map(Number);
            return new Date(y, m - 1, day, h, min, s);
        }

        async function loadCourts() {
            try {
                const res = await fetch('/api/courts', { headers: { 'Accept': 'application/json' }});
                const json = await res.json();
                const raw = json.data || json || [];
                availableCourts = raw.filter(c => c.is_active == 1 || c.is_active === true);
            } catch (e) { availableCourts = []; }
        }

        async function loadStats() {
            try {
                const res = await fetch('/api/employee/stats', { headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' } });
                const json = await res.json();
                const d = json.data;
                if(d) {
                    document.getElementById('statBookings').innerText = d.total_bookings;
                    document.getElementById('statCourts').innerText = d.total_courts;
                    document.getElementById('statRevenue').innerText = 'RM' + d.total_revenue;

                    if (d.booking_graph) {
                        const ctxB = document.getElementById('bookingStatusChart').getContext('2d');
                        if (bookingChartInstance) bookingChartInstance.destroy();
                        bookingChartInstance = new Chart(ctxB, {
                            type: 'bar',
                            data: {
                                labels: ['Completed', 'Confirmed', 'Pending', 'Cancelled'],
                                datasets: [{ label: 'Bookings', data: [d.booking_graph.completed, d.booking_graph.confirmed, d.booking_graph.pending, d.booking_graph.cancelled], backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444'], borderWidth: 1 }]
                            },
                            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
                        });
                    }

                    if (d.court_graph) {
                        const ctxC = document.getElementById('courtStatusChart').getContext('2d');
                        if (courtChartInstance) courtChartInstance.destroy();
                        courtChartInstance = new Chart(ctxC, {
                            type: 'doughnut',
                            data: { labels: ['Active', 'Inactive'], datasets: [{ data: [d.court_graph.active, d.court_graph.inactive], backgroundColor: ['#10b981', '#ef4444'] }] },
                            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right' } } }
                        });
                    }
                }
            } catch(e) {}
        }

        async function loadAllBookings() {
            try {
                const res = await fetch('/api/bookings', { headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' } });
                const json = await res.json();
                const tbody = document.getElementById('bookingsTableBody');
                allBookingsData = json.data || []; 
                
                if(allBookingsData.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="7" class="p-8 text-center text-gray-400 italic">No bookings found.</td></tr>';
                    return;
                }

                tbody.innerHTML = allBookingsData.map(b => {
                    const startObj = parseDbDate(b.start_time);
                    const endObj = parseDbDate(b.end_time);
                    const dateStr = startObj.toLocaleDateString();
                    const timeStr = `${startObj.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})} - ${endObj.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}`;
                    
                    // Conflict Detection Logic
                    const curStart = startObj.getTime();
                    const curEnd = endObj.getTime();
                    const takenIds = new Set();
                    allBookingsData.forEach(o => {
                        if (o.id !== b.id && o.status === 'confirmed' && o.court_id) {
                            if (curStart < parseDbDate(o.end_time).getTime() && curEnd > parseDbDate(o.start_time).getTime()) takenIds.add(Number(o.court_id));
                        }
                    });

                    const isPending = (b.status === 'pending');
                    const isConfirmed = (b.status === 'confirmed');
                    const hasCourt = (b.court_id !== null);
                    const isEditing = editingRows.has(b.id);

                    let courtCol = '';
                    let actionCol = '';

                    if (isPending) {
                        courtCol = `<span class="text-orange-400 text-xs italic">Waiting for Payment...</span>`;
                        actionCol = `<span class="text-gray-400 text-xs font-bold uppercase tracking-widest">Unpaid</span>`;
                    } 
                    else if (isEditing || (isConfirmed && !hasCourt)) {
                        // SUGGEST AUTO-ASSIGNMENT OR MANUAL DROPDOWN
                        let autoCourt = availableCourts.find(c => !takenIds.has(Number(c.id)));
                        
                        const options = availableCourts.map(c => {
                            const isTaken = takenIds.has(Number(c.id));
                            if (isTaken && Number(c.id) !== Number(b.court_id)) return `<option value="${c.id}" disabled class="text-red-300">${c.name} (Full)</option>`;
                            const isSelected = (Number(c.id) === Number(b.court_id)) || (!b.court_id && c.id === autoCourt?.id);
                            return `<option value="${c.id}" ${isSelected ? 'selected' : ''}>${c.name} ${isSelected && !b.court_id ? '(Auto)' : ''}</option>`;
                        }).join('');

                        courtCol = `
                            <div class="flex items-center gap-2">
                                <select id="court-select-${b.id}" class="flex-1 p-1.5 bg-white border border-purple-300 rounded text-xs outline-none focus:border-purple-600 transition cursor-pointer">
                                    <option value="" disabled ${!b.court_id && !autoCourt ? 'selected' : ''}>Select Court...</option>
                                    ${options}
                                </select>
                            </div>
                        `;
                        actionCol = `
                            <div class="flex justify-end gap-2">
                                <button onclick="saveCourtUpdate(${b.id})" class="bg-emerald-600 text-white px-3 py-1.5 rounded text-xs font-bold hover:bg-emerald-700 shadow-sm transition">Confirm</button>
                                ${hasCourt ? `<button onclick="cancelEdit(${b.id})" class="bg-gray-100 text-gray-500 px-3 py-1.5 rounded text-xs font-bold hover:bg-gray-200 transition">Cancel</button>` : ''}
                            </div>
                        `;
                    } else {
                        // DISPLAY STATIC TEXT + CHANGE BUTTON
                        let courtName = b.court ? b.court.name : (b.court_id ? `Court #${b.court_id}` : 'Unassigned');
                        courtCol = `
                            <div class="flex items-center justify-between group/cell">
                                <span class="font-bold text-slate-700"><i class="fa-solid fa-table-tennis-paddle-ball text-slate-300 mr-2"></i>${courtName}</span>
                                ${isConfirmed ? `<button onclick="enterEdit(${b.id})" class="text-blue-500 text-[10px] uppercase font-bold opacity-0 group-hover/cell:opacity-100 transition hover:underline">Change</button>` : ''}
                            </div>
                        `;
                        actionCol = `
                            <div class="flex justify-end gap-2">
                                ${isConfirmed ? `
                                    <button onclick="processBooking(${b.id}, 'completed')" class="text-blue-600 hover:text-blue-800 text-xs font-bold underline decoration-dotted">Complete</button>
                                    <button onclick="processBooking(${b.id}, 'cancelled')" class="text-red-400 hover:text-red-600 text-xs font-bold underline decoration-dotted ml-2">Cancel</button>
                                ` : `<span class="text-gray-300 text-xs italic">Finished</span>`}
                            </div>
                        `;
                    }

                    return `
                        <tr class="hover:bg-slate-50 transition duration-150">
                            <td class="p-4 text-gray-400 font-mono text-xs">#${b.id}</td>
                            <td class="p-4">
                                <div class="font-bold text-gray-800">${b.user?.name || 'Guest'}</div>
                                <div class="text-[10px] text-gray-400">${b.user?.email || ''}</div>
                            </td>
                            <td class="p-4 text-gray-700 whitespace-nowrap">${dateStr}</td>
                            <td class="p-4 text-gray-700 whitespace-nowrap font-medium">${timeStr}</td>
                            <td class="p-4">${courtCol}</td>
                            <td class="p-4"><span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border ${getStatusColor(b.status)}">${b.status}</span></td>
                            <td class="p-4 text-right">${actionCol}</td>
                        </tr>
                    `;
                }).join('');
            } catch(e) {}
        }

        // --- ACTIONS ---
        function enterEdit(id) { editingRows.add(id); loadAllBookings(); }
        function cancelEdit(id) { editingRows.delete(id); loadAllBookings(); }

        async function saveCourtUpdate(id) {
            const courtId = document.getElementById(`court-select-${id}`).value;
            if (!courtId) return alert("Select a court first.");
            try {
                const res = await fetch(`/api/bookings/${id}/status`, {
                    method: 'PUT',
                    headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ status: 'confirmed', court_id: courtId })
                });
                if(res.ok) { editingRows.delete(id); loadAllBookings(); } else { alert("Failed to assign court."); }
            } catch(e) { alert("Network error"); }
        }

        async function processBooking(id, status) {
            if(!confirm(`Mark booking #${id} as ${status}?`)) return;
            try {
                const res = await fetch(`/api/bookings/${id}/status`, {
                    method: 'PUT',
                    headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ status: status })
                });
                if(res.ok) loadAllBookings(); else alert("Update failed.");
            } catch(e) { alert("Network error"); }
        }

        async function openProfile() { 
            const m = document.getElementById('profileModal'); 
            m.classList.remove('hidden'); 
            setTimeout(() => { m.classList.remove('opacity-0'); document.getElementById('profileContent').classList.add('scale-100'); }, 10);
            try {
                const res = await fetch('/api/user', { headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' } });
                const u = await res.json();
                document.getElementById('pName').innerText = u.name;
                document.getElementById('pEmail').innerText = u.email;
                document.getElementById('pRole').innerText = u.role.toUpperCase();
                document.getElementById('pId').innerText = `EMP-${u.id.toString().padStart(4, '0')}`;
            } catch(e) {}
        }
        function closeProfile() { const m = document.getElementById('profileModal'); m.classList.add('opacity-0'); document.getElementById('profileContent').classList.remove('scale-100'); setTimeout(() => m.classList.add('hidden'), 200); }
        function getStatusColor(status) { if (status === 'confirmed') return 'bg-emerald-50 text-emerald-700 border-emerald-100'; if (status === 'completed') return 'bg-blue-50 text-blue-700 border-blue-100'; if (status === 'cancelled') return 'bg-red-50 text-red-700 border-red-100'; return 'bg-yellow-50 text-yellow-700 border-yellow-100'; }
    </script>
</body>
</html>