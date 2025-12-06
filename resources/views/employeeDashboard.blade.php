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
            <span class="text-sm text-gray-500 hidden md:block">Employee Panel</span>
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

    <script>
        const token = localStorage.getItem('token');
        // Uncomment below for production
        // if (!token) window.location.href = '/login';

        let availableCourts = [];

        // Initialize Data Loading
        initDashboard();

        function logout() {
            localStorage.clear();
            window.location.href = '/login';
        }

        async function initDashboard() {
            loadStats();
            await loadCourts(); // Wait for courts before loading bookings
            loadAllBookings();
        }

        // 1. Fetch Courts from Database
        async function loadCourts() {
            try {
                const res = await fetch('/api/courts', {
                    headers: { 'Accept': 'application/json' }
                });
                const json = await res.json();
                availableCourts = json.data || json || [];
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
                
                if(!json.data || json.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="8" class="p-8 text-center text-gray-400 italic">No booking requests found.</td></tr>';
                    return;
                }

                tbody.innerHTML = json.data.map(b => {
                    // --- Timezone Handling (UTC) ---
                    const startObj = new Date(b.start_time);
                    const endObj = new Date(b.end_time);

                    const dateStr = startObj.toLocaleDateString(undefined, { timeZone: 'UTC' });
                    const startTimeStr = startObj.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit', timeZone: 'UTC'});
                    const endTimeStr = endObj.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit', timeZone: 'UTC'});
                    
                    // --- Overlap Detection Logic ---
                    const currentStart = startObj.getTime();
                    const currentEnd = endObj.getTime();
                    
                    const occupiedCourtIds = json.data
                        .filter(other => {
                            if (other.id === b.id) return false;
                            if (other.status !== 'confirmed') return false;
                            if (!other.court_id) return false;

                            const oStart = new Date(other.start_time).getTime();
                            const oEnd = new Date(other.end_time).getTime();
                            return (currentStart < oEnd && currentEnd > oStart);
                        })
                        .map(o => o.court_id);


                    // --- Court Dropdown / Display Logic ---
                    
                    // ERROR RECOVERY: If status is 'confirmed' BUT no court_id, treat it like 'pending' so user can fix it
                    const isMissingCourt = (b.status === 'confirmed' && !b.court_id);
                    const showDropdown = (b.status === 'pending' || isMissingCourt);

                    let courtDisplay = '';
                    
                    if (showDropdown) {
                        // Generate Dropdown with Occupied Courts Disabled
                        const options = availableCourts.map(c => {
                            const isTaken = occupiedCourtIds.includes(c.id);
                            const label = isTaken ? `${c.name} (Booked)` : c.name;
                            return `<option value="${c.id}" ${isTaken ? 'disabled' : ''} class="${isTaken ? 'text-red-300' : ''}">${label}</option>`;
                        }).join('');

                        courtDisplay = `
                            <div class="relative group">
                                <select id="court-select-${b.id}" class="w-full p-2 bg-white border border-gray-300 rounded text-xs focus:border-purple-500 focus:ring-1 focus:ring-purple-500 outline-none cursor-pointer">
                                    <option value="" disabled selected>${isMissingCourt ? '⚠️ Re-assign Court...' : 'Select Court...'}</option>
                                    ${options}
                                </select>
                            </div>
                        `;
                    } else {
                        // Confirmed Status Display
                        let courtName = '';
                        if (b.court) {
                            courtName = b.court.name;
                        } else if (b.court_id) {
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

                    // Status Badge Logic
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

            // Logic: Require Court ID only when Confirming a Pending booking
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
                    initDashboard(); // Refresh UI fully
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