<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Dashboard - Racket Arena</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 font-sans">

    <!-- Navbar -->
    <nav class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center sticky top-0 z-10">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-user-tie text-purple-600 text-xl"></i>
            <span class="font-bold text-lg text-gray-800">Racket Arena <span class="bg-purple-100 text-purple-700 text-xs px-2 py-1 rounded ml-1">STAFF</span></span>
        </div>
        <div class="flex items-center gap-4">
            <button onclick="openProfile()" class="text-gray-500 hover:text-purple-600 font-medium text-sm transition flex items-center gap-1">
                <i class="fa-solid fa-user-gear"></i> Profile
            </button>
            <div class="h-4 w-px bg-gray-300"></div>
            <button onclick="logout()" class="text-gray-500 hover:text-red-600 text-sm font-medium flex items-center gap-1">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </button>
        </div>
    </nav>

    <div class="max-w-[90rem] mx-auto p-6">
        
        <!-- Stats Row -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6" id="statsContainer">
            <div class="bg-white p-4 rounded-lg border border-gray-200 animate-pulse h-24"></div>
            <div class="bg-white p-4 rounded-lg border border-gray-200 animate-pulse h-24"></div>
            <div class="bg-white p-4 rounded-lg border border-gray-200 animate-pulse h-24"></div>
            <div class="bg-white p-4 rounded-lg border border-gray-200 animate-pulse h-24"></div>
        </div>

        <!-- Schedule & Assignment -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-4 border-b border-gray-200 flex justify-between items-center bg-gray-50 rounded-t-lg">
                <h2 class="font-bold text-gray-800 flex items-center gap-2">
                    <i class="fa-regular fa-calendar-check text-purple-600"></i> Booking Management
                </h2>
                <button onclick="initDashboard()" class="text-purple-600 text-sm hover:underline hover:text-purple-800 flex items-center gap-1 transition">
                    <i class="fa-solid fa-rotate"></i> Refresh List
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-100 text-xs uppercase font-medium text-gray-500">
                        <tr>
                            <th class="p-4 whitespace-nowrap">ID</th>
                            <th class="p-4 whitespace-nowrap">Customer</th>
                            <th class="p-4 whitespace-nowrap">Date</th>
                            <th class="p-4 whitespace-nowrap">Start Time</th>
                            <th class="p-4 whitespace-nowrap">End Time</th>
                            <th class="p-4 w-64 whitespace-nowrap">Assign Court</th>
                            <th class="p-4 whitespace-nowrap">Status</th>
                            <th class="p-4 text-right whitespace-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="bookingsTableBody" class="divide-y divide-gray-100"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- STAFF PROFILE MODAL -->
    <div id="profileModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center backdrop-blur-sm transition-opacity opacity-0">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 transform scale-95 transition-transform duration-200" id="profileContent">
            <div class="bg-purple-600 p-6 rounded-t-2xl relative overflow-hidden">
                <div class="absolute top-0 right-0 -mr-4 -mt-4 w-24 h-24 bg-white/10 rounded-full"></div>
                <button onclick="closeProfile()" class="absolute top-4 right-4 text-white/80 hover:text-white transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
                <div class="flex items-center gap-4 relative z-10">
                    <div class="w-16 h-16 bg-white text-purple-600 rounded-full flex items-center justify-center text-3xl font-bold border-4 border-white/20 shadow-lg">
                        <i class="fa-solid fa-user-tie"></i>
                    </div>
                    <div class="text-white">
                        <h2 id="pName" class="text-xl font-bold">Loading...</h2>
                        <p id="pRole" class="text-purple-100 text-sm uppercase tracking-wide font-medium">Staff Member</p>
                    </div>
                </div>
            </div>
            
            <div class="p-6 space-y-4">
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-400 uppercase">Work Email</label>
                    <div id="pEmail" class="text-gray-800 font-medium border-b border-gray-100 pb-2">...</div>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-400 uppercase">Employee ID</label>
                    <div id="pId" class="text-gray-800 font-medium border-b border-gray-100 pb-2">...</div>
                </div>
                <div class="pt-2">
                    <button onclick="closeProfile()" class="w-full py-2.5 bg-gray-100 text-gray-600 font-bold rounded-lg hover:bg-gray-200 transition">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const token = localStorage.getItem('token');
        let availableCourts = []; // Filtered list of ACTIVE courts
        let allBookingsData = []; // Full list of bookings

        // Execute main startup function
        initDashboard();

        function logout() {
            localStorage.clear();
            window.location.href = '/login';
        }

        // --- TIME HELPER FUNCTION ---
        // Generates a UTC timestamp number from a DB time string
        function getUtcTime(dbTime) {
            return new Date(dbTime).getTime(); 
        }

        // --- MAIN DASHBOARD INITIALIZATION (ASYNC) ---
        async function initDashboard() {
            // Load essential data concurrently
            await Promise.all([loadStats(), loadCourts()]);
            // Load bookings after courts are loaded
            await loadAllBookings();
        }

        // --- PROFILE LOGIC ---
        async function openProfile() {
            const modal = document.getElementById('profileModal');
            const content = document.getElementById('profileContent');
            
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                content.classList.remove('scale-95');
                content.classList.add('scale-100');
            }, 10);

            try {
                const res = await fetch('/api/user', {
                    headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
                });
                const user = await res.json();
                
                document.getElementById('pName').innerText = user.name;
                document.getElementById('pEmail').innerText = user.email;
                document.getElementById('pRole').innerText = user.role.toUpperCase();
                document.getElementById('pId').innerText = `EMP-${user.id.toString().padStart(4, '0')}`;
            } catch(e) {
                console.error("Failed to load profile");
            }
        }

        function closeProfile() {
            const modal = document.getElementById('profileModal');
            const content = document.getElementById('profileContent');
            modal.classList.add('opacity-0');
            content.classList.remove('scale-100');
            content.classList.add('scale-95');
            setTimeout(() => { modal.classList.add('hidden'); }, 200);
        }

        // 1. Fetch Courts from Database (and filter only active ones for assignment)
        async function loadCourts() {
            try {
                const res = await fetch('/api/courts', {
                    headers: { 'Accept': 'application/json' }
                });
                const json = await res.json();
                const allCourts = json.data || json || [];
                
                // FIX 1: Filter out Inactive courts. 
                availableCourts = allCourts
                    .filter(c => c.is_active == 1 || c.is_active === true); 
                
                console.log("Active Courts for Assignment:", availableCourts.map(c => c.name));
                
            } catch (e) {
                console.error("Failed to load courts", e);
                availableCourts = [];
            }
        }

        async function loadStats() {
            try {
                const res = await fetch('/api/employee/stats', {
                    headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
                });
                const json = await res.json();
                const container = document.getElementById('statsContainer');
                
                let html = '';
                if(json.data) {
                    for (const [key, value] of Object.entries(json.data)) {
                        html += `
                            <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm transition hover:shadow-md">
                                <div class="text-xs text-gray-400 uppercase font-bold mb-1 tracking-wider">${key.replace(/_/g, ' ')}</div>
                                <div class="text-2xl font-bold text-purple-700">${value}</div>
                            </div>
                        `;
                    }
                    container.innerHTML = html;
                }
            } catch(e) {
                console.warn("Stats load failed", e);
            }
        }

        async function loadAllBookings() {
            try {
                const res = await fetch('/api/bookings', {
                    headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
                });
                const json = await res.json();
                const tbody = document.getElementById('bookingsTableBody');
                allBookingsData = json.data || []; 
                
                if(allBookingsData.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="8" class="p-8 text-center text-gray-400 italic">No booking requests found.</td></tr>';
                    return;
                }

                tbody.innerHTML = allBookingsData.map(b => {
                    const startObj = new Date(b.start_time);
                    const endObj = new Date(b.end_time);

                    const dateStr = startObj.toLocaleDateString(undefined, { timeZone: 'UTC' });
                    const startTimeStr = startObj.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit', timeZone: 'UTC'});
                    const endTimeStr = endObj.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit', timeZone: 'UTC'});
                    
                    // --- Overlap Detection Logic ---
                    const currentStart = getUtcTime(b.start_time);
                    const currentEnd = getUtcTime(b.end_time);
                    
                    // FIX 2: Identify ALL courts booked by confirmed bookings that overlap with THIS slot
                    const occupiedCourtIds = new Set();

                    allBookingsData.forEach(other => {
                        if (other.id === b.id) return; // Ignore self
                        if (other.status !== 'confirmed') return; // Only check confirmed bookings
                        if (!other.court_id) return; // Must have an assigned court

                        const oStart = getUtcTime(other.start_time);
                        const oEnd = getUtcTime(other.end_time);
                        
                        // Overlap condition: (StartA < EndB) and (EndA > StartB)
                        if (currentStart < oEnd && currentEnd > oStart) {
                            occupiedCourtIds.add(other.court_id);
                        }
                    });


                    // --- Court Dropdown / Display Logic ---
                    const isMissingCourt = (b.status === 'confirmed' && !b.court_id);
                    const showDropdown = (b.status === 'pending' || isMissingCourt);

                    let courtDisplay = '';
                    
                    if (showDropdown) {
                        const options = availableCourts.map(c => {
                            // Check against the OCCUPIED IDs Set
                            const isTaken = occupiedCourtIds.has(c.id);
                            
                            // If Booked: Disable it and color red
                            if (isTaken) {
                                return `<option value="${c.id}" disabled class="text-red-300 font-medium">${c.name} (Booked)</option>`;
                            }

                            // If not booked, it's active and available
                            return `<option value="${c.id}" class="text-gray-800 font-medium">${c.name}</option>`;
                        }).join('');
                        
                        // If there are no active courts, show a disabled message
                        if (availableCourts.length === 0) {
                             courtDisplay = `<span class="text-red-500 text-xs font-bold">No Active Courts Available</span>`;
                        } else {
                            courtDisplay = `
                                <div class="relative group">
                                    <select id="court-select-${b.id}" class="w-full p-2 bg-white border border-gray-300 rounded text-xs focus:border-purple-500 focus:ring-1 focus:ring-purple-500 outline-none cursor-pointer">
                                        <option value="" disabled selected>${isMissingCourt ? '⚠️ Re-assign Court...' : 'Select Court...'}</option>
                                        ${options}
                                    </select>
                                </div>
                            `;
                        }
                    } else {
                        // Display confirmed court name
                        let courtName = '';
                        if (b.court) {
                            courtName = b.court.name;
                        } else if (b.court_id) {
                            // The error was here: We cannot use AWAIT in this synchronous map loop.
                            // Since the array 'availableCourts' is loaded globally, we use that instead of fetching again.
                            const found = availableCourts.find(c => c.id == b.court_id);
                            courtName = found ? found.name : 'Unknown Court';
                        } else {
                            courtName = '<span class="text-red-400 italic">Deleted Court</span>';
                        }

                        courtDisplay = `
                            <span class="font-bold text-gray-700 flex items-center gap-2">
                                <i class="fa-solid fa-table-tennis-paddle-ball text-gray-400 text-xs"></i>
                                ${courtName}
                            </span>
                        `;
                    }

                    // --- Action Buttons Logic ---
                    let actionsHtml = '';
                    if (showDropdown) {
                        actionsHtml = `
                            <div class="flex justify-end gap-2">
                                <button onclick="processBooking(${b.id}, 'confirmed')" 
                                    class="bg-emerald-600 text-white hover:bg-emerald-700 px-3 py-1.5 rounded shadow-sm text-xs font-bold transition flex items-center gap-1">
                                    <i class="fa-solid fa-check"></i> ${isMissingCourt ? 'Retry' : 'Assign'}
                                </button>
                                <button onclick="processBooking(${b.id}, 'cancelled')" 
                                    class="bg-white text-red-600 hover:bg-red-50 border border-red-200 px-3 py-1.5 rounded shadow-sm text-xs font-bold transition" title="Reject Request">
                                    <i class="fa-solid fa-xmark"></i> Reject
                                </button>
                            </div>
                        `;
                    } else {
                        actionsHtml = `
                            <div class="flex justify-end gap-2">
                                ${b.status === 'confirmed' ? `
                                    <button onclick="processBooking(${b.id}, 'completed')" class="text-blue-600 hover:text-blue-800 text-xs font-bold underline decoration-dotted">Complete</button>
                                    <span class="text-gray-300">|</span>
                                    <button onclick="processBooking(${b.id}, 'cancelled')" class="text-red-400 hover:text-red-600 text-xs font-bold underline decoration-dotted">Cancel</button>
                                ` : `
                                    <span class="text-gray-300 text-xs italic">Archived</span>
                                `}
                            </div>
                        `;
                    }

                    let statusLabel = b.status;
                    let statusColor = getStatusColor(b.status);
                    
                    if (isMissingCourt) {
                        statusLabel = "Error: No Court";
                        statusColor = "bg-red-100 text-red-700 border-red-200 animate-pulse";
                    }

                    return `
                        <tr class="hover:bg-gray-50 transition duration-150 group">
                            <td class="p-4 text-gray-400 font-mono text-xs">#${b.id}</td>
                            <td class="p-4">
                                <div class="font-bold text-gray-800">${b.user ? b.user.name : 'Guest'}</div>
                                <div class="text-xs text-gray-400">${b.user ? b.user.email : ''}</div>
                            </td>
                            <td class="p-4 text-gray-700 whitespace-nowrap">${dateStr}</td>
                            <td class="p-4 text-gray-700 whitespace-nowrap font-medium text-emerald-700">${startTimeStr}</td>
                            <td class="p-4 text-gray-700 whitespace-nowrap font-medium text-red-700">${endTimeStr}</td>
                            <td class="p-4">${courtDisplay}</td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide border ${statusColor}">
                                    ${statusLabel}
                                </span>
                            </td>
                            <td class="p-4 text-right">
                                ${actionsHtml}
                            </td>
                        </tr>
                    `;
                }).join('');
            } catch(e) {
                console.error(e);
            }
        }

        async function processBooking(id, status) {
            const payload = { status: status };

            if (status === 'confirmed') {
                const selectEl = document.getElementById(`court-select-${id}`);
                if (selectEl) {
                    const courtId = selectEl.value;
                    if (!courtId) {
                        alert("⚠️ Please assign a court from the dropdown before confirming.");
                        selectEl.focus();
                        selectEl.classList.add('border-red-500', 'ring-1', 'ring-red-500');
                        return;
                    }
                    payload.court_id = courtId;
                }
            }

            const actionVerb = status === 'confirmed' ? 'Assign Court & Confirm' : (status === 'completed' ? 'Mark as Complete' : 'Cancel');

            if(!confirm(`Are you sure you want to ${actionVerb} for booking #${id}?`)) return;

            try {
                const res = await fetch(`/api/bookings/${id}/status`, {
                    method: 'PUT',
                    headers: { 
                        'Authorization': `Bearer ${token}`, 
                        'Content-Type': 'application/json', 
                        'Accept': 'application/json' 
                    },
                    body: JSON.stringify(payload)
                });

                if(res.ok) {
                    initDashboard();
                } else {
                    const err = await res.json();
                    alert("Error: " + (err.message || "Failed to update"));
                }
            } catch(e) {
                alert("Network error.");
            }
        }

        function getStatusColor(status) {
            if (status === 'confirmed') return 'bg-emerald-50 text-emerald-700 border-emerald-200';
            if (status === 'completed') return 'bg-blue-50 text-blue-700 border-blue-200';
            if (status === 'cancelled') return 'bg-red-50 text-red-700 border-red-200';
            return 'bg-yellow-50 text-yellow-700 border-yellow-200';
        }
    </script>
</body>
</html>