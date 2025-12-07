<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Racket Arena</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-slate-100 font-sans">

    <!-- Sidebar & Content Layout -->
    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar -->
        <aside class="w-64 bg-slate-900 text-white hidden md:flex flex-col">
            <div class="p-6 flex items-center gap-2 border-b border-slate-800">
                <i class="fa-solid fa-shuttlecock text-emerald-400 text-xl"></i>
                <span class="font-bold text-lg tracking-wide">Racket Arena</span>
            </div>
            <nav class="flex-1 p-4 space-y-2">
                <button onclick="switchTab('dashboard')" id="nav-dashboard" class="w-full flex items-center gap-3 px-4 py-3 bg-slate-800 rounded-lg text-emerald-400 font-medium transition">
                    <i class="fa-solid fa-gauge w-6 text-center"></i> Dashboard
                </button>
                <button onclick="switchTab('employees')" id="nav-employees" class="w-full flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition">
                    <i class="fa-solid fa-users w-6 text-center"></i> Employees
                </button>
                <button onclick="switchTab('courts')" id="nav-courts" class="w-full flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition">
                    <i class="fa-solid fa-table-tennis-paddle-ball w-6 text-center"></i> Courts
                </button>
                <button onclick="switchTab('revenue')" id="nav-revenue" class="w-full flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition">
                    <i class="fa-solid fa-money-bill w-6 text-center"></i> Revenue
                </button>
            </nav>
            <div class="p-4 border-t border-slate-800">
                <button onclick="openProfile()" class="flex items-center gap-3 w-full px-4 py-3 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition">
                    <i class="fa-solid fa-user-shield w-6 text-center"></i> Admin Profile
                </button>
                <button onclick="logout()" class="flex items-center gap-3 w-full px-4 py-3 text-red-400 hover:text-red-300 hover:bg-red-900/20 rounded-lg transition mt-2">
                    <i class="fa-solid fa-right-from-bracket w-6 text-center"></i> Logout
                </button>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden relative">
            <header class="bg-white shadow-sm border-b px-6 py-4 flex justify-between items-center md:hidden">
                <span class="font-bold text-slate-800">Admin Panel</span>
                <button onclick="logout()" class="text-red-500"><i class="fa-solid fa-power-off"></i></button>
            </header>

            <main class="flex-1 overflow-y-auto p-6" id="main-container">
                
                <!-- VIEW: DASHBOARD OVERVIEW -->
                <div id="view-dashboard" class="space-y-6 animate-in fade-in duration-300">
                    <h1 class="text-2xl font-bold text-slate-800">Overview</h1>
                    <!-- Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                            <div class="text-slate-500 text-sm font-bold uppercase mb-2">Total Bookings</div>
                            <div class="text-3xl font-bold text-slate-800" id="statBookings">
                                <i class="fa-solid fa-spinner fa-spin text-lg text-slate-400"></i>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                            <div class="text-slate-500 text-sm font-bold uppercase mb-2">Active Courts</div>
                            <div class="text-3xl font-bold text-emerald-600" id="statCourts">
                                <i class="fa-solid fa-spinner fa-spin text-lg text-emerald-400"></i>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                            <div class="text-slate-500 text-sm font-bold uppercase mb-2">Total Revenue</div>
                            <div class="text-3xl font-bold text-blue-600" id="statRevenue">
                                <i class="fa-solid fa-spinner fa-spin text-lg text-blue-400"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- VIEW: COURTS MANAGEMENT -->
                <div id="view-courts" class="space-y-6 hidden animate-in fade-in duration-300">
                    <div class="flex justify-between items-center">
                        <h1 class="text-2xl font-bold text-slate-800">Court Management</h1>
                        <button onclick="openAddCourtModal()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-bold shadow-md transition flex items-center gap-2">
                            <i class="fa-solid fa-plus"></i> Add New Court
                        </button>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                        <table class="w-full text-left text-sm text-gray-600">
                            <thead class="bg-slate-50 text-xs uppercase font-bold text-slate-500">
                                <tr>
                                    <th class="p-4 border-b">ID</th>
                                    <th class="p-4 border-b">Court Name</th>
                                    <th class="p-4 border-b">Type / Description</th>
                                    <th class="p-4 border-b">Price/Hr</th>
                                    <th class="p-4 border-b">Status</th>
                                    <th class="p-4 border-b text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="courtsTableBody">
                                <!-- Populated via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Placeholder Views -->
                <div id="view-employees" class="hidden text-center py-20 text-gray-400">
                    <i class="fa-solid fa-users text-4xl mb-3"></i><p>Employee Management Coming Soon</p>
                </div>
                <div id="view-revenue" class="hidden text-center py-20 text-gray-400">
                    <i class="fa-solid fa-chart-line text-4xl mb-3"></i><p>Revenue Reports Coming Soon</p>
                </div>

            </main>
        </div>
    </div>

    <!-- MODAL: ADD COURT -->
    <div id="addCourtModal" class="fixed inset-0 bg-black/60 z-50 hidden flex items-center justify-center backdrop-blur-sm transition-opacity opacity-0">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 transform scale-95 transition-transform duration-200">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-slate-800">Add New Court</h3>
                <button onclick="closeAddCourtModal()" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Court Name</label>
                    <input id="newCourtName" type="text" placeholder="e.g. Court 5 (Grass)" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Description (Optional)</label>
                    <input id="newCourtType" type="text" placeholder="e.g. Standard Synthetic Mat" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Hourly Rate (RM)</label>
                    <input id="newCourtPrice" type="number" value="10" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
                </div>
                <button onclick="submitNewCourt()" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-lg transition shadow-lg shadow-emerald-200">
                    Create Court
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL: EDIT COURT -->
    <div id="editCourtModal" class="fixed inset-0 bg-black/60 z-50 hidden flex items-center justify-center backdrop-blur-sm transition-opacity opacity-0">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 transform scale-95 transition-transform duration-200">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-blue-50 rounded-t-xl">
                <h3 class="text-lg font-bold text-blue-800">Edit Court Details</h3>
                <button onclick="closeEditCourtModal()" class="text-blue-400 hover:text-blue-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <div class="p-6 space-y-4">
                <input type="hidden" id="editCourtId">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Court Name</label>
                    <input id="editCourtName" type="text" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Description / Type</label>
                    <input id="editCourtType" type="text" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Hourly Rate (RM)</label>
                        <input id="editCourtPrice" type="number" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Status</label>
                        <select id="editCourtStatus" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                
                <button onclick="submitEditCourt()" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg transition shadow-lg shadow-blue-200">
                    Save Changes
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL: ADMIN PROFILE -->
    <div id="profileModal" class="fixed inset-0 bg-black/60 z-50 hidden flex items-center justify-center backdrop-blur-sm transition-opacity opacity-0">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 transform scale-95 transition-transform duration-200" id="profileContent">
            <div class="bg-slate-800 p-6 rounded-t-2xl relative overflow-hidden">
                <button onclick="closeProfile()" class="absolute top-4 right-4 text-white/80 hover:text-white"><i class="fa-solid fa-xmark text-xl"></i></button>
                <div class="flex items-center gap-4 relative z-10">
                    <div class="w-16 h-16 bg-white text-slate-900 rounded-full flex items-center justify-center text-3xl font-bold border-4 border-white/20">
                        <i class="fa-solid fa-user-shield"></i>
                    </div>
                    <div class="text-white">
                        <h2 id="pName" class="text-xl font-bold">Loading...</h2>
                        <p id="pRole" class="text-emerald-400 text-sm uppercase tracking-wide font-bold">Administrator</p>
                    </div>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-400 uppercase">Admin Email</label>
                    <div id="pEmail" class="text-gray-800 font-medium border-b border-gray-100 pb-2">...</div>
                </div>
                <div class="pt-2">
                    <button onclick="closeProfile()" class="w-full py-2.5 bg-slate-800 text-white font-bold rounded-lg hover:bg-slate-700 transition">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const token = localStorage.getItem('token');
        let allCourtsData = []; 
        
        loadAdminStats();

        // --- NAVIGATION ---
        function switchTab(tabName) {
            ['dashboard', 'courts', 'employees', 'revenue'].forEach(t => {
                document.getElementById(`view-${t}`).classList.add('hidden');
                document.getElementById(`nav-${t}`).classList.remove('bg-slate-800', 'text-emerald-400');
                document.getElementById(`nav-${t}`).classList.add('text-slate-400');
            });
            document.getElementById(`view-${tabName}`).classList.remove('hidden');
            const btn = document.getElementById(`nav-${tabName}`);
            btn.classList.remove('text-slate-400');
            btn.classList.add('bg-slate-800', 'text-emerald-400');
            if(tabName === 'courts') loadCourtsTable();
        }

        // --- COURT MANAGEMENT ---
        async function loadCourtsTable() {
            const tbody = document.getElementById('courtsTableBody');
            tbody.innerHTML = '<tr><td colspan="6" class="p-4 text-center"><i class="fa-solid fa-spinner fa-spin"></i> Loading...</td></tr>';

            try {
                const res = await fetch('/api/courts', { headers: { 'Accept': 'application/json' } });
                const json = await res.json();
                allCourtsData = json.data || json || [];

                if(allCourtsData.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" class="p-8 text-center text-gray-400 italic">No courts found. Add one!</td></tr>';
                    return;
                }

                tbody.innerHTML = allCourtsData.map(c => {
                    const isActive = c.is_active == 1 || c.is_active === true;
                    const statusBadge = !isActive 
                        ? '<span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-bold uppercase">Inactive</span>'
                        : '<span class="bg-emerald-100 text-emerald-700 px-2 py-1 rounded text-xs font-bold uppercase">Active</span>';

                    return `
                    <tr class="border-b last:border-0 hover:bg-slate-50 transition group">
                        <td class="p-4 text-gray-400 font-mono text-xs">#${c.id}</td>
                        <td class="p-4 font-bold text-slate-700">${c.name}</td>
                        <td class="p-4 text-slate-500 text-sm">${c.type || '-'}</td>
                        <td class="p-4 text-slate-700 font-medium">RM${c.price || '10.00'}</td>
                        <td class="p-4">${statusBadge}</td>
                        <td class="p-4 text-right">
                            <button onclick="openEditCourtModal(${c.id})" class="text-blue-500 hover:text-blue-700 hover:bg-blue-50 p-2 rounded transition mr-1" title="Edit Court">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button onclick="deleteCourt(${c.id})" class="text-red-400 hover:text-red-600 hover:bg-red-50 p-2 rounded transition" title="Delete Court">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `}).join('');
                
            } catch(e) {
                tbody.innerHTML = '<tr><td colspan="6" class="p-4 text-center text-red-500">Error loading courts.</td></tr>';
            }
        }

        // --- EDIT MODAL LOGIC ---
        function openEditCourtModal(id) {
            const court = allCourtsData.find(c => c.id === id);
            if(!court) return;

            document.getElementById('editCourtId').value = court.id;
            document.getElementById('editCourtName').value = court.name;
            document.getElementById('editCourtType').value = court.type || '';
            document.getElementById('editCourtPrice').value = court.price || '10.00';
            
            const statusVal = (court.is_active == 1) ? 'active' : 'inactive';
            document.getElementById('editCourtStatus').value = statusVal;

            const modal = document.getElementById('editCourtModal');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.firstElementChild.classList.remove('scale-95');
                modal.firstElementChild.classList.add('scale-100');
            }, 10);
        }

        function closeEditCourtModal() {
            const modal = document.getElementById('editCourtModal');
            modal.classList.add('opacity-0');
            modal.firstElementChild.classList.remove('scale-100');
            modal.firstElementChild.classList.add('scale-95');
            setTimeout(() => { modal.classList.add('hidden'); }, 200);
        }

        async function submitEditCourt() {
            const id = document.getElementById('editCourtId').value;
            const name = document.getElementById('editCourtName').value;
            const type = document.getElementById('editCourtType').value;
            const price = document.getElementById('editCourtPrice').value;
            const status = document.getElementById('editCourtStatus').value;

            if(!name) return alert("Court name is required");

            try {
                const res = await fetch(`/api/courts/${id}`, {
                    method: 'PUT',
                    headers: { 
                        'Authorization': `Bearer ${token}`, 
                        'Content-Type': 'application/json', 
                        'Accept': 'application/json' 
                    },
                    body: JSON.stringify({ name, type, price, status })
                });

                if(res.ok) {
                    alert("Court updated successfully!");
                    closeEditCourtModal();
                    loadCourtsTable();
                    loadAdminStats(); 
                } else {
                    const err = await res.json();
                    alert("Error: " + (err.message || "Failed to update court"));
                }
            } catch(e) {
                alert("Network error");
            }
        }

        // --- ADD MODAL LOGIC ---
        function openAddCourtModal() {
            const modal = document.getElementById('addCourtModal');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.firstElementChild.classList.remove('scale-95');
                modal.firstElementChild.classList.add('scale-100');
            }, 10);
        }

        function closeAddCourtModal() {
            const modal = document.getElementById('addCourtModal');
            modal.classList.add('opacity-0');
            modal.firstElementChild.classList.remove('scale-100');
            modal.firstElementChild.classList.add('scale-95');
            setTimeout(() => { modal.classList.add('hidden'); }, 200);
        }

        async function submitNewCourt() {
            const name = document.getElementById('newCourtName').value;
            const type = document.getElementById('newCourtType').value;
            const price = document.getElementById('newCourtPrice').value;

            if(!name) return alert("Court name is required");

            try {
                const res = await fetch('/api/courts', {
                    method: 'POST',
                    headers: { 
                        'Authorization': `Bearer ${token}`, 
                        'Content-Type': 'application/json', 
                        'Accept': 'application/json' 
                    },
                    body: JSON.stringify({ name, type, price, status: 'active' })
                });

                if(res.ok) {
                    alert("Court created successfully!");
                    closeAddCourtModal();
                    loadCourtsTable();
                    loadAdminStats();
                    document.getElementById('newCourtName').value = '';
                    document.getElementById('newCourtType').value = '';
                } else {
                    const err = await res.json();
                    alert("Error: " + (err.message || "Failed to create court"));
                }
            } catch(e) {
                alert("Network error");
            }
        }

        async function deleteCourt(id) {
            if(!confirm("Are you sure you want to delete this court?")) return;
            try {
                const res = await fetch(`/api/courts/${id}`, {
                    method: 'DELETE',
                    headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
                });
                if(res.ok) {
                    loadCourtsTable();
                    loadAdminStats();
                }
            } catch(e) { alert("Network error"); }
        }

        function logout() {
            localStorage.clear();
            window.location.href = '/login';
        }

        async function openProfile() {
            const modal = document.getElementById('profileModal');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.firstElementChild.classList.add('scale-100');
            }, 10);
            try {
                const res = await fetch('/api/user', {
                    headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
                });
                const user = await res.json();
                document.getElementById('pName').innerText = user.name;
                document.getElementById('pEmail').innerText = user.email;
            } catch(e) {}
        }

        function closeProfile() {
            const modal = document.getElementById('profileModal');
            modal.classList.add('opacity-0');
            modal.firstElementChild.classList.remove('scale-100');
            setTimeout(() => { modal.classList.add('hidden'); }, 200);
        }

        // --- STATS LOGIC ---
        async function loadAdminStats() {
            try {
                // IMPORTANT: Ensure you have Route::get('/admin/stats', [DashboardController::class, 'stats']) in api.php
                const res = await fetch('/api/admin/stats', {
                    headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
                });
                
                if (res.ok) {
                    const json = await res.json();
                    if(json.data) {
                        document.getElementById('statBookings').innerText = json.data.total_bookings;
                        document.getElementById('statCourts').innerText = json.data.total_courts;
                        document.getElementById('statRevenue').innerText = 'RM' + json.data.total_revenue;
                    }
                } else {
                    document.getElementById('statBookings').innerText = '-';
                    document.getElementById('statCourts').innerText = '-';
                    document.getElementById('statRevenue').innerText = '-';
                }
            } catch(e) {
                console.error("Stats load failed", e);
            }
        }
    </script>
</body>
</html>