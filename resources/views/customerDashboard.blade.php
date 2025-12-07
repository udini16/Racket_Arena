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
        <div class="flex items-center gap-4">
            <button onclick="openProfile()" class="text-gray-500 hover:text-blue-600 font-medium text-sm transition flex items-center gap-1">
                <i class="fa-solid fa-user-circle text-lg"></i> <span class="hidden md:inline">Profile</span>
            </button>
            <div class="h-4 w-px bg-gray-300"></div>
            <button onclick="logout()" class="text-gray-500 hover:text-red-600 font-medium text-sm transition">Logout</button>
        </div>
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

                <!-- 4. Auto-Calculated End Time -->
                <div class="relative">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">End Time (Auto)</label>
                    <input id="endTimeDisplay" type="text" readonly placeholder="--:-- --" class="w-full p-3 border rounded-lg bg-gray-100 text-gray-600 focus:outline-none cursor-not-allowed">
                    <i class="fa-solid fa-stopwatch absolute right-3 top-[38px] text-gray-400 pointer-events-none"></i>
                </div>

                <!-- 5. Estimated Price -->
                <div class="relative md:col-span-2">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Estimated Cost</label>
                    <div class="flex items-center">
                        <div class="bg-gray-100 border border-r-0 border-gray-300 p-3 rounded-l-lg text-gray-500 font-bold text-sm">RM</div>
                        <input id="priceDisplay" type="text" readonly value="0.00" class="w-full p-3 border border-gray-300 rounded-r-lg bg-emerald-50 text-emerald-700 font-bold focus:outline-none cursor-not-allowed">
                    </div>
                    <p id="rateInfo" class="text-[10px] text-gray-400 mt-1 italic">* Loading rate...</p>
                </div>
            </div>

            <!-- Availability Indicator -->
            <div id="availabilityPanel" class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-100 hidden">
                <div class="text-xs font-bold text-gray-500 uppercase mb-2">Availability Status</div>
                <div id="availabilityText" class="text-sm text-gray-700 flex flex-wrap gap-2"></div>
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

    <!-- PROFILE MODAL -->
    <div id="profileModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center backdrop-blur-sm transition-opacity opacity-0">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 transform scale-95 transition-transform duration-200" id="profileContent">
            <div class="bg-blue-600 p-6 rounded-t-2xl relative overflow-hidden">
                <div class="absolute top-0 right-0 -mr-4 -mt-4 w-24 h-24 bg-white/10 rounded-full"></div>
                <button onclick="closeProfile()" class="absolute top-4 right-4 text-white/80 hover:text-white transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
                <div class="flex items-center gap-4 relative z-10">
                    <div class="w-16 h-16 bg-white text-blue-600 rounded-full flex items-center justify-center text-3xl font-bold border-4 border-white/20 shadow-lg">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <div class="text-white">
                        <h2 id="pName" class="text-xl font-bold">Loading...</h2>
                        <p id="pRole" class="text-blue-100 text-sm uppercase tracking-wide font-medium">Customer</p>
                    </div>
                </div>
            </div>
            
            <div class="p-6 space-y-4">
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-400 uppercase">Email Address</label>
                    <div id="pEmail" class="text-gray-800 font-medium border-b border-gray-100 pb-2">...</div>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-400 uppercase">Member Since</label>
                    <div id="pJoined" class="text-gray-800 font-medium border-b border-gray-100 pb-2">...</div>
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
        let hourlyRate = 10;
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
            initTimeOptions();
            handleTimeChange();
        });

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

        // --- TIME HELPER: ROBUST PARSING ---
        function parseDbDate(dateStr) {
            if (!dateStr) return new Date();
            const cleanStr = dateStr.replace('T', ' ').replace('Z', '').split('.')[0];
            const [d, t] = cleanStr.split(' ');
            const [y, m, day] = d.split('-').map(Number);
            const [h, min, s] = t.split(':').map(Number);
            return new Date(y, m - 1, day, h, min, s);
        }

        // --- OVERLAP CHECK ---
        function isTimeOccupied(targetDateStr, targetHour, booking) {
            // Only count bookings that are confirmed or completed as "occupied"
            if (booking.status === 'cancelled' || booking.status === 'pending') return false;

            const startT = parseDbDate(booking.start_time).getTime();
            const endT = parseDbDate(booking.end_time).getTime();

            const [y, m, d] = targetDateStr.split('-').map(Number);
            const checkT = new Date(y, m - 1, d, targetHour, 0, 0).getTime();

            // Is the check time inside the booking range?
            return (checkT >= startT && checkT < endT);
        }

        // --- 1. Fetch Data ---
        async function loadCourts() {
            try {
                const res = await fetch('/api/courts', { headers: { 'Accept': 'application/json' }});
                const json = await res.json();
                const rawCourts = json.data || json || [];
                
                // FILTER: Only load ACTIVE courts (is_active == 1 or true)
                allCourts = rawCourts.filter(c => c.is_active == 1 || c.is_active === true);

                if (allCourts.length > 0) {
                    const activeCourt = allCourts[0];
                    if (activeCourt && activeCourt.price) {
                        hourlyRate = parseFloat(activeCourt.price);
                    }
                }
                
                document.getElementById('rateInfo').innerText = `* Rate: RM${hourlyRate.toFixed(2)} per hour`;
                updatePrice();

            } catch (e) { console.error("Error loading courts", e); }
        }

        async function loadAllBookings() {
            try {
                // FIXED: Use the Availability Endpoint (Public) instead of protected /bookings
                // This ensures customers can download the schedule to check availability
                const res = await fetch('/api/bookings/availability', { 
                    headers: { 'Accept': 'application/json' }
                });
                if (res.ok) {
                    const json = await res.json();
                    allBookings = json.data || [];
                    console.log("Loaded bookings:", allBookings.length);
                } else {
                    console.error("Failed to load availability");
                }
            } catch (e) { console.error("Error loading bookings for availability", e); }
        }

        // --- 2. Generate Options ---
        function initTimeOptions() {
            const select = document.getElementById('startHour');
            const selectedDate = dateInput.value;
            const isToday = selectedDate === todayStr;
            const currentHour = new Date().getHours();
            let html = '';
            
            for(let i=8; i<=22; i++) {
                if (isToday && i <= currentHour) continue;

                let occupiedCount = 0;
                
                // Check if ALL ACTIVE courts are full at this hour
                if (allCourts.length > 0) {
                     // Get all confirmed bookings for this time slot
                     const relevantBookings = allBookings.filter(b => isTimeOccupied(selectedDate, i, b));
                     
                     // Count unique active court IDs that are booked
                     const uniqueBookedCourts = new Set();
                     relevantBookings.forEach(b => {
                         if (b.court_id) uniqueBookedCourts.add(Number(b.court_id));
                     });
                     
                     // Only count bookings for courts that are actually in our active list
                     occupiedCount = 0;
                     uniqueBookedCourts.forEach(id => {
                         if (allCourts.some(c => c.id == id)) occupiedCount++;
                     });
                }
                
                const isFull = (allCourts.length > 0 && occupiedCount >= allCourts.length);
                
                const ampm = i >= 12 ? 'PM' : 'AM';
                const hourDisplay = i % 12 || 12; 
                const display = `${hourDisplay}:00 ${ampm}`;
                const label = isFull ? `${display} (Full)` : display;
                html += `<option value="${i}" ${isFull ? 'disabled' : ''} class="${isFull ? 'text-red-300 font-bold bg-gray-50' : ''}">${label}</option>`;
            }
            
            if (html === '') html = '<option value="" disabled selected>No slots available</option>';
            select.innerHTML = html;
        }

        // --- 3. UI Updates ---
        function handleTimeChange() {
            updateEndTime();
            updateAvailabilityDisplay();
            updatePrice();
        }

        function updatePrice() {
            const duration = parseInt(document.getElementById('duration').value) || 0;
            const totalPrice = duration * hourlyRate;
            document.getElementById('priceDisplay').value = totalPrice.toFixed(2);
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
                for (let h = startHour; h < startHour + duration; h++) {
                    const isBooked = allBookings.some(b => {
                         // Must check court_id against court.id
                         // Ensure robust comparison (Number() wrapper)
                         if (!b.court_id || Number(b.court_id) != Number(court.id)) return false;
                         return isTimeOccupied(dateStr, h, b);
                    });
                    if (isBooked) return false;
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
                    initDashboard();
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
                    const dateObj = parseDbDate(b.start_time);
                    const dateDisplay = dateObj.toLocaleDateString();
                    const timeDisplay = dateObj.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' });
                    
                    const priceDisplay = b.total_price ? `<span class="text-slate-600 font-semibold text-xs ml-2 border-l pl-2 border-slate-300">RM${Math.abs(parseFloat(b.total_price)).toFixed(2)}</span>` : '';

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
                                <div class="text-xs text-gray-500 flex items-center">
                                    ${dateDisplay} • ${timeDisplay} ${priceDisplay}
                                </div>
                            </div>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold ${getStatusColor(b.status)} capitalize">${b.status}</span>
                    </div>
                `}).join('');
            } catch(e) {}
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
                document.getElementById('pRole').innerText = user.role;
                document.getElementById('pJoined').innerText = new Date(user.created_at).toLocaleDateString();
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
            
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 200); 
        }

        function getStatusColor(status) {
            if (status === 'confirmed') return 'bg-emerald-100 text-emerald-700';
            if (status === 'cancelled') return 'bg-red-100 text-red-700';
            return 'bg-yellow-100 text-yellow-700';
        }
    </script>
</body>
</html>