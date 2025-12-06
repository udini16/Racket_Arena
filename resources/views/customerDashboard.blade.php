<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 font-sans">

    <!-- Navbar -->
    <nav class="bg-white shadow-sm px-6 py-4 flex justify-between items-center sticky top-0 z-10">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-shuttlecock text-blue-600 text-xl"></i>
            <span class="font-bold text-lg text-gray-800">Racket Arena</span>
            <span class="bg-blue-100 text-blue-700 text-xs px-2 py-1 rounded-full uppercase font-bold tracking-wider ml-2">Customer</span>
        </div>
        <button onclick="logout()" class="text-gray-500 hover:text-red-600 font-medium text-sm transition">Logout</button>
    </nav>

    <div class="max-w-5xl mx-auto p-6 space-y-8">
        
        <!-- Booking Section -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                <i class="fa-regular fa-calendar-plus text-blue-500"></i> Request a Booking
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                
                <!-- 1. Select Date -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Date</label>
                    <input id="bookingDate" type="date" class="w-full p-3 border rounded-lg bg-gray-50 focus:border-blue-500 outline-none transition cursor-pointer">
                </div>

                <!-- 2. Select Start Hour -->
                <div class="relative">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Start Time</label>
                    <select id="startHour" onchange="handleTimeChange()" class="w-full p-3 border rounded-lg bg-gray-50 focus:border-blue-500 outline-none transition cursor-pointer appearance-none">
                        <!-- Generated via JS -->
                    </select>
                    <i class="fa-solid fa-clock absolute right-3 top-[38px] text-gray-400 pointer-events-none"></i>
                </div>

                <!-- 3. Select Duration -->
                <div class="relative">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Duration</label>
                    <select id="duration" onchange="handleTimeChange()" class="w-full p-3 border rounded-lg bg-gray-50 focus:border-blue-500 outline-none transition cursor-pointer appearance-none">
                        <option value="1">1 Hour</option>
                        <option value="2">2 Hours</option>
                        <option value="3">3 Hours</option>
                    </select>
                    <i class="fa-solid fa-hourglass-half absolute right-3 top-[38px] text-gray-400 pointer-events-none"></i>
                </div>

                <!-- 4. Auto-Calculated End Time (Visual Field) -->
                <div class="relative">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">End Time (Auto)</label>
                    <input id="endTimeDisplay" type="text" readonly placeholder="--:-- --" class="w-full p-3 border rounded-lg bg-gray-100 text-gray-600 focus:outline-none cursor-not-allowed">
                    <i class="fa-solid fa-stopwatch absolute right-3 top-[38px] text-gray-400 pointer-events-none"></i>
                </div>

            </div>

            <!-- Availability Indicator -->
            <div id="availabilityPanel" class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-100 hidden">
                <div class="text-xs font-bold text-gray-500 uppercase mb-2">Availability Status</div>
                <div id="availabilityText" class="text-sm text-gray-700 flex flex-wrap gap-2">
                    <!-- Populated by JS -->
                </div>
            </div>

            <button id="submitBtn" onclick="bookCourt()" class="bg-blue-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-blue-700 transition w-full md:w-auto shadow-md shadow-blue-200 disabled:opacity-50 disabled:cursor-not-allowed">
                Submit Request
            </button>
            <p class="text-xs text-gray-400 mt-3 italic">* Staff will assign a specific court upon confirmation.</p>
        </div>

        <!-- History Section -->
        <div>
            <h3 class="text-lg font-bold text-gray-800 mb-4">My Booking Requests</h3>
            <div id="myBookingsList" class="grid gap-3">
                <!-- Bookings populate here -->
            </div>
        </div>

    </div>

    <script>
        const token = localStorage.getItem('token');
        // Uncomment below for production
        // if (!token) window.location.href = '/login';

        // State
        let allCourts = [];
        let allBookings = [];

        // --- Init Logic ---
        const dateInput = document.getElementById('bookingDate');
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const todayStr = `${year}-${month}-${day}`;

        dateInput.min = todayStr;
        dateInput.value = todayStr;

        dateInput.addEventListener('change', () => {
            initTimeOptions(); // Re-render options based on new date's availability
            handleTimeChange();
        });

        // Start Up
        initDashboard();

        function logout() {
            localStorage.clear();
            window.location.href = '/login';
        }

        async function initDashboard() {
            await Promise.all([loadCourts(), loadAllBookings()]);
            initTimeOptions();
            handleTimeChange();
            loadMyBookings();
        }

        // --- 1. Fetch Data ---
        async function loadCourts() {
            try {
                const res = await fetch('/api/courts', { headers: { 'Accept': 'application/json' }});
                const json = await res.json();
                allCourts = json.data || json || [];
            } catch (e) { console.error("Error loading courts", e); }
        }

        async function loadAllBookings() {
            try {
                // Try to fetch all bookings to check availability.
                const res = await fetch('/api/bookings/availability', { 
                    headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
                });
                if (res.ok) {
                    const json = await res.json();
                    allBookings = json.data || [];
                }
            } catch (e) { console.error("Error loading bookings for availability", e); }
        }

        // --- Helper: Robust Date Parsing from DB String (YYYY-MM-DD HH:mm:ss or ISO) ---
        function parseDbDate(dateStr) {
            if (!dateStr) return new Date();
            // Handle ISO "T" separator and normalize to space
            // Handle "Z" (UTC) by stripping it to treat time as "Wall Clock" (Local)
            const cleanStr = dateStr.replace('T', ' ').replace('Z', '').split('.')[0];
            
            const [d, t] = cleanStr.split(' ');
            const [y, m, day] = d.split('-').map(Number);
            const [h, min, s] = t.split(':').map(Number);
            
            // Create a Local Date object for exact time comparison
            return new Date(y, m - 1, day, h, min, s);
        }

        // --- Helper: Check if a specific time slot is occupied ---
        function isTimeOccupied(targetDateStr, targetHour, booking) {
            if (booking.status !== 'confirmed') return false;

            // Use robust parsing to get "Wall Clock" times
            const startT = parseDbDate(booking.start_time).getTime();
            const endT = parseDbDate(booking.end_time).getTime();

            // Construct the check time (Local)
            const [y, m, d] = targetDateStr.split('-').map(Number);
            const checkT = new Date(y, m - 1, d, targetHour, 0, 0).getTime();

            // Is the check time inside the booking range?
            // (Start <= Check < End)
            return (checkT >= startT && checkT < endT);
        }

        // --- 2. Generate Options with Availability Check ---
        function initTimeOptions() {
            const select = document.getElementById('startHour');
            const selectedDate = dateInput.value;
            
            const isToday = selectedDate === todayStr;
            const currentHour = new Date().getHours();

            let html = '';
            
            // Operating hours: 08:00 to 22:00
            for(let i=8; i<=22; i++) {
                if (isToday && i <= currentHour) continue;

                // Check if ALL courts are full at this hour
                let occupiedCount = 0;
                if (allCourts.length > 0) {
                     // Filter bookings that cover this slot
                     const relevantBookings = allBookings.filter(b => isTimeOccupied(selectedDate, i, b));
                     // Count distinct courts booked
                     const uniqueCourts = new Set(relevantBookings.map(b => b.court_id)).size;
                     occupiedCount = uniqueCourts;
                }
                
                // If distinct booked courts >= total courts, the slot is FULL
                const isFull = (allCourts.length > 0 && occupiedCount >= allCourts.length);
                
                // Format display
                const ampm = i >= 12 ? 'PM' : 'AM';
                const hourDisplay = i % 12 || 12; 
                const display = `${hourDisplay}:00 ${ampm}`;
                const label = isFull ? `${display} (Full)` : display;
                
                html += `<option value="${i}" ${isFull ? 'disabled' : ''} class="${isFull ? 'text-red-300 font-bold bg-gray-50' : ''}">${label}</option>`;
            }

            if (html === '') {
                html = '<option value="" disabled selected>No slots available</option>';
            }

            select.innerHTML = html;
        }

        // --- 3. Update UI on Change ---
        function handleTimeChange() {
            updateEndTime();
            updateAvailabilityDisplay();
        }

        function updateEndTime() {
            const startHour = parseInt(document.getElementById('startHour').value);
            const duration = parseInt(document.getElementById('duration').value);
            const displayInput = document.getElementById('endTimeDisplay');

            if (isNaN(startHour) || isNaN(duration)) {
                displayInput.value = "--:-- --";
                return;
            }

            let endHour = startHour + duration;
            let displayEnd = endHour;
            let nextDayLabel = "";
            
            if (endHour >= 24) {
                displayEnd = endHour - 24;
                nextDayLabel = " (Next Day)";
            }

            const ampm = displayEnd >= 12 && displayEnd < 24 ? 'PM' : 'AM';
            const h = displayEnd % 12 || 12;
            
            displayInput.value = `${h}:00 ${ampm}${nextDayLabel}`;
        }

        // --- 4. Show Specific Available Courts ---
        function updateAvailabilityDisplay() {
            const dateStr = document.getElementById('bookingDate').value;
            const startHour = parseInt(document.getElementById('startHour').value);
            const duration = parseInt(document.getElementById('duration').value);
            const panel = document.getElementById('availabilityPanel');
            const text = document.getElementById('availabilityText');
            const btn = document.getElementById('submitBtn');

            if (!dateStr || isNaN(startHour)) {
                panel.classList.add('hidden');
                return;
            }

            // Find courts available for the ENTIRE duration
            const available = allCourts.filter(court => {
                // Check every hour in the duration
                for (let h = startHour; h < startHour + duration; h++) {
                    const isBooked = allBookings.some(b => {
                         if (!b.court_id || b.court_id != court.id) return false;
                         return isTimeOccupied(dateStr, h, b);
                    });
                    if (isBooked) return false; // Booked during this hour
                }
                return true;
            });

            panel.classList.remove('hidden');
            if (available.length > 0) {
                text.innerHTML = available.map(c => 
                    `<span class="bg-emerald-100 text-emerald-700 px-2 py-1 rounded text-xs font-bold border border-emerald-200"><i class="fa-solid fa-check"></i> ${c.name}</span>`
                ).join('');
                btn.disabled = false;
                btn.innerHTML = "Submit Request";
                btn.classList.remove('bg-gray-400', 'cursor-not-allowed');
                btn.classList.add('bg-blue-600', 'hover:bg-blue-700');
            } else {
                text.innerHTML = `<span class="text-red-500 font-bold"><i class="fa-solid fa-circle-exclamation"></i> No courts available for this duration.</span>`;
                btn.disabled = true;
                btn.innerHTML = "Unavailable";
                btn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                btn.classList.add('bg-gray-400', 'cursor-not-allowed');
            }
        }

        // --- 5. Submit Booking ---
        async function bookCourt() {
            const dateStr = document.getElementById('bookingDate').value;
            const hourInt = parseInt(document.getElementById('startHour').value);
            const durationInt = parseInt(document.getElementById('duration').value);

            if(!dateStr || isNaN(hourInt)) return alert("Please select date and a valid time.");

            const endHourInt = hourInt + durationInt;
            const fmt = (h) => `${h.toString().padStart(2, '0')}:00:00`;

            const payload = {
                date: dateStr,
                duration: durationInt,
                start_time: `${dateStr} ${fmt(hourInt)}`,
                end_time: `${dateStr} ${fmt(endHourInt)}`
            };

            try {
                const res = await fetch('/api/bookings', {
                    method: 'POST',
                    headers: { 
                        'Authorization': `Bearer ${token}`, 
                        'Content-Type': 'application/json', 
                        'Accept': 'application/json' 
                    },
                    body: JSON.stringify(payload)
                });
                const json = await res.json();
                
                if (res.ok) {
                    alert('Booking Request Sent!');
                    initDashboard(); // Refresh availability
                } else {
                    let msg = json.message || 'Error occurred';
                    if(json.errors) {
                        const firstKey = Object.keys(json.errors)[0];
                        msg = json.errors[firstKey][0]; 
                    }
                    alert('Error: ' + msg);
                }
            } catch (err) {
                alert("Network error.");
            }
        }

        // --- 6. Load History ---
        async function loadMyBookings() {
            try {
                const res = await fetch('/api/my-bookings', {
                    headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
                });
                const json = await res.json();
                const list = document.getElementById('myBookingsList');
                
                if(!json.data || json.data.length === 0) {
                    list.innerHTML = '<p class="text-gray-400 italic">No bookings found.</p>';
                    return;
                }

                list.innerHTML = json.data.map(b => {
                    // Force UTC display
                    const dateObj = new Date(b.start_time);
                    const dateDisplay = dateObj.toLocaleDateString(undefined, { timeZone: 'UTC' });
                    const timeDisplay = dateObj.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit', timeZone: 'UTC' });

                    return `
                    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 flex justify-between items-center transition hover:shadow-md">
                        <div class="flex items-center gap-4">
                            <div class="h-10 w-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                                <i class="fa-solid fa-clock"></i>
                            </div>
                            <div>
                                <div class="font-bold text-gray-800">
                                    ${b.court ? b.court.name : `Booking #${b.id}`}
                                </div>
                                <div class="text-xs text-gray-500">
                                    ${dateDisplay} • ${timeDisplay}
                                </div>
                            </div>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold ${getStatusColor(b.status)} capitalize">${b.status}</span>
                    </div>
                `}).join('');
            } catch(e) {}
        }

        function getStatusColor(status) {
            if (status === 'confirmed') return 'bg-emerald-100 text-emerald-700';
            if (status === 'cancelled') return 'bg-red-100 text-red-700';
            return 'bg-yellow-100 text-yellow-700';
        }
    </script>
</body>
</html>