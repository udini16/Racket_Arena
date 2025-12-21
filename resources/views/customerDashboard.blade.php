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
        
        <!-- Payment Success Message -->
        <div id="paymentSuccess" class="hidden bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded relative animate-bounce" role="alert">
            <strong class="font-bold">Payment Successful!</strong>
            <span class="block sm:inline">Your booking is now complete. See you on the court! 🏸</span>
        </div>

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

    <!-- PROFILE MODAL (Same as before) -->
    <div id="profileModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center backdrop-blur-sm transition-opacity opacity-0">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 transform scale-95 transition-transform duration-200" id="profileContent">
            <div class="bg-blue-600 p-6 rounded-t-2xl relative overflow-hidden">
                <button onclick="closeProfile()" class="absolute top-4 right-4 text-white/80 hover:text-white transition"><i class="fa-solid fa-xmark text-xl"></i></button>
                <div class="flex items-center gap-4 relative z-10">
                    <div class="w-16 h-16 bg-white text-blue-600 rounded-full flex items-center justify-center text-3xl font-bold border-4 border-white/20"><i class="fa-solid fa-user"></i></div>
                    <div class="text-white"><h2 id="pName" class="text-xl font-bold">Loading...</h2><p id="pRole" class="text-blue-100 text-sm uppercase">Customer</p></div>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div class="space-y-1"><label class="text-xs font-bold text-gray-400 uppercase">Email</label><div id="pEmail" class="text-gray-800 font-medium border-b border-gray-100 pb-2">...</div></div>
                <div class="space-y-1"><label class="text-xs font-bold text-gray-400 uppercase">Member Since</label><div id="pJoined" class="text-gray-800 font-medium border-b border-gray-100 pb-2">...</div></div>
                <div class="pt-2"><button onclick="closeProfile()" class="w-full py-2.5 bg-gray-100 text-gray-600 font-bold rounded-lg hover:bg-gray-200 transition">Close</button></div>
            </div>
        </div>
    </div>

    <script>
        const token = localStorage.getItem('token');
        let hourlyRate = 10;
        let allCourts = [];
        let allBookings = [];

        // Check for payment success param in URL
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('paid') === 'true') {
            document.getElementById('paymentSuccess').classList.remove('hidden');
            window.history.replaceState({}, document.title, window.location.pathname);
        }

        const dateInput = document.getElementById('bookingDate');
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const todayStr = `${year}-${month}-${day}`;

        dateInput.min = todayStr;
        dateInput.value = todayStr;

        dateInput.addEventListener('change', () => { initTimeOptions(); handleTimeChange(); });
        initDashboard();

        function logout() { localStorage.clear(); window.location.href = '/login'; }
        async function initDashboard() { await Promise.all([loadCourts(), loadAllBookings()]); initTimeOptions(); handleTimeChange(); loadMyBookings(); }
        
        // --- FIXED DATE PARSER ---
        function parseDbDate(dateStr) { 
            if (!dateStr) return new Date(); 
            // Normalize separator to ensure space
            const cleanStr = dateStr.replace('T', ' ').replace('Z', '').split('.')[0]; 
            const [d, t] = cleanStr.split(' '); 
            const [y, m, day] = d.split('-').map(Number); 
            const [h, min, s] = t.split(':').map(Number); 
            // Return Local Date Object
            return new Date(y, m - 1, day, h, min, s); 
        }

        // --- FIXED OVERLAP CHECK ---
        function isTimeOccupied(targetDateStr, targetHour, booking) { 
            // Filter out cancelled bookings
            if (booking.status !== 'confirmed' && booking.status !== 'completed') return false; 
            
            const startT = parseDbDate(booking.start_time).getTime(); 
            const endT = parseDbDate(booking.end_time).getTime(); 
            
            // Build comparison date for the slot
            // targetDateStr comes from input type='date' (YYYY-MM-DD)
            const [y, m, d] = targetDateStr.split('-').map(Number);
            const checkT = new Date(y, m - 1, d, targetHour, 0, 0).getTime(); 
            
            // Check if slot falls within booking (Start <= Slot < End)
            return (checkT >= startT && checkT < endT); 
        }

        async function loadCourts() { 
            try { 
                const res = await fetch('/api/courts', { headers: { 'Accept': 'application/json' }}); 
                const json = await res.json(); 
                const rawCourts = json.data || json || []; 
                
                // FILTER: Only load ACTIVE courts
                // Handle NULL by checking != 0 and != false, assuming NULL is active in legacy data, OR require strict 1.
                // Based on your screenshot, 1 is active, 0 is inactive, NULL is... questionable. 
                // Let's assume strict 1 for safety, OR update your DB to set 1.
                // Fix: Accept '1' (string) or 1 (number).
                allCourts = rawCourts.filter(c => c.is_active == 1);

                if (allCourts.length > 0) { 
                    const activeCourt = allCourts[0]; 
                    if (activeCourt && activeCourt.price) { 
                        hourlyRate = parseFloat(activeCourt.price); 
                    } 
                } 
                document.getElementById('rateInfo').innerText = `* Rate: RM${hourlyRate.toFixed(2)} per hour`; 
                updatePrice(); 
            } catch (e) {} 
        }

        async function loadAllBookings() { 
            try { 
                const res = await fetch('/api/bookings/availability', { headers: { 'Accept': 'application/json' } }); 
                if (res.ok) { 
                    const json = await res.json(); 
                    allBookings = json.data || []; 
                } 
            } catch (e) {} 
        }

        // --- 6. Load History ---
        async function loadMyBookings() {
            try {
                const res = await fetch('/api/my-bookings', { headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' } });
                const json = await res.json();
                const list = document.getElementById('myBookingsList');
                
                if(!json.data || json.data.length === 0) { list.innerHTML = '<p class="text-gray-400 italic">No bookings found.</p>'; return; }

                list.innerHTML = json.data.map(b => {
                    const dateObj = parseDbDate(b.start_time);
                    const dateDisplay = dateObj.toLocaleDateString();
                    const timeDisplay = dateObj.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' });
                    const priceDisplay = b.total_price ? `RM${Math.abs(parseFloat(b.total_price)).toFixed(2)}` : 'RM0.00';

                    let statusBadge = '';
                    let actionHtml = '';
                    let mainText = '';

                    if (b.status === 'pending') {
                        statusBadge = `<span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-bold capitalize">Pending Payment</span>`;
                        actionHtml = `
                            <button onclick="payWithFPX(${b.id})" class="bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded shadow-sm text-xs font-bold transition flex items-center gap-2 animate-pulse">
                                <i class="fa-regular fa-credit-card"></i> Pay Now
                            </button>
                        `;
                        mainText = `<span class="text-slate-600 italic">Booking #${b.id}</span>`;
                    } 
                    else if (b.status === 'confirmed') {
                        statusBadge = `<span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-xs font-bold capitalize">Paid</span>`;
                        if (b.court) {
                            mainText = `<span class="text-gray-800">${b.court.name}</span>`;
                            actionHtml = `<span class="text-xs text-emerald-600 font-bold"><i class="fa-solid fa-check-circle"></i> Booked</span>`;
                        } else {
                            mainText = `<span class="text-orange-500 italic"><i class="fa-solid fa-hourglass-half"></i> Awaiting Court Assignment</span>`;
                            actionHtml = `<span class="text-xs text-gray-400">Staff is reviewing</span>`;
                        }
                    } 
                    else if (b.status === 'completed') {
                        statusBadge = `<span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold capitalize">Completed</span>`;
                        mainText = b.court ? b.court.name : 'Unknown Court';
                        actionHtml = `<span class="text-xs text-blue-600 font-bold">Done</span>`;
                    } 
                    else {
                        statusBadge = `<span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-bold capitalize">Cancelled</span>`;
                        mainText = `Booking #${b.id}`;
                    }

                    return `
                    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4 transition hover:shadow-md">
                        <div class="flex items-center gap-4 w-full">
                            <div class="h-12 w-12 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 shrink-0">
                                <i class="fa-solid fa-clock text-xl"></i>
                            </div>
                            <div>
                                <div class="font-bold text-lg">
                                    ${mainText}
                                </div>
                                <div class="text-sm text-gray-500 flex items-center gap-2">
                                    <span>${dateDisplay}</span>
                                    <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                                    <span>${timeDisplay}</span>
                                    <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                                    <span class="font-semibold text-slate-700">${priceDisplay}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 w-full md:w-auto justify-end">
                            ${actionHtml}
                            ${statusBadge}
                        </div>
                    </div>
                `}).join('');
            } catch(e) {}
        }

        async function payWithFPX(id) {
            try {
                const res = await fetch(`/api/bookings/${id}/pay`, {
                    method: 'POST',
                    headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
                });
                const json = await res.json();
                
                if (json.payment_url) {
                    window.location.href = json.payment_url;
                } else {
                    alert("Payment initiation failed: " + (json.message || "Unknown error"));
                }
            } catch (e) {
                alert("Payment network error.");
            }
        }

        function initTimeOptions() { const select = document.getElementById('startHour'); const selectedDate = dateInput.value; const isToday = selectedDate === todayStr; const currentHour = new Date().getHours(); let html = ''; for(let i=8; i<=22; i++) { if (isToday && i <= currentHour) continue; let occupiedCount = 0; if (allCourts.length > 0) { const relevantBookings = allBookings.filter(b => isTimeOccupied(selectedDate, i, b)); const uniqueCourts = new Set(relevantBookings.map(b => b.court_id)); occupiedCount = 0; uniqueCourts.forEach(id => { if (allCourts.some(c => c.id == id)) occupiedCount++; }); } const isFull = (allCourts.length > 0 && occupiedCount >= allCourts.length); const ampm = i >= 12 ? 'PM' : 'AM'; const hourDisplay = i % 12 || 12; const display = `${hourDisplay}:00 ${ampm}`; const label = isFull ? `${display} (Full)` : display; html += `<option value="${i}" ${isFull ? 'disabled' : ''} class="${isFull ? 'text-red-300 font-bold bg-gray-50' : ''}">${label}</option>`; } if (html === '') html = '<option value="" disabled selected>No slots available</option>'; select.innerHTML = html; }
        function handleTimeChange() { updateEndTime(); updateAvailabilityDisplay(); updatePrice(); }
        function updatePrice() { const duration = parseInt(document.getElementById('duration').value) || 0; const totalPrice = duration * hourlyRate; document.getElementById('priceDisplay').value = totalPrice.toFixed(2); }
        function updateEndTime() { const startHour = parseInt(document.getElementById('startHour').value); const duration = parseInt(document.getElementById('duration').value); const displayInput = document.getElementById('endTimeDisplay'); if (isNaN(startHour) || isNaN(duration)) { displayInput.value = "--:-- --"; return; } let endHour = startHour + duration; let displayEnd = endHour; let nextDayLabel = ""; if (endHour >= 24) { displayEnd = endHour - 24; nextDayLabel = " (Next Day)"; } const ampm = displayEnd >= 12 && displayEnd < 24 ? 'PM' : 'AM'; const h = displayEnd % 12 || 12; displayInput.value = `${h}:00 ${ampm}${nextDayLabel}`; }
        
        function updateAvailabilityDisplay() { 
            const dateStr = document.getElementById('bookingDate').value; 
            const startHour = parseInt(document.getElementById('startHour').value); 
            const duration = parseInt(document.getElementById('duration').value); 
            const panel = document.getElementById('availabilityPanel'); 
            const text = document.getElementById('availabilityText'); 
            const btn = document.getElementById('submitBtn'); 
            
            if (!dateStr || isNaN(startHour)) { panel.classList.add('hidden'); return; } 
            
            const available = allCourts.filter(court => { 
                for (let h = startHour; h < startHour + duration; h++) { 
                    const isBooked = allBookings.some(b => { 
                        // Strict check: Is this specific court ID occupied at this hour?
                        // Also make sure we parse court_id as integer
                        if (!b.court_id || parseInt(b.court_id) !== parseInt(court.id)) return false; 
                        return isTimeOccupied(dateStr, h, b); 
                    }); 
                    if (isBooked) return false; 
                } 
                return true; 
            }); 
            
            panel.classList.remove('hidden'); 
            if (available.length > 0) { 
                text.innerHTML = available.map(c => `<span class="bg-emerald-100 text-emerald-700 px-2 py-1 rounded text-xs font-bold border border-emerald-200"><i class="fa-solid fa-check"></i> ${c.name}</span>`).join(''); 
                btn.disabled = false; btn.innerHTML = "Submit Request"; btn.classList.remove('bg-gray-400', 'cursor-not-allowed'); btn.classList.add('bg-blue-600', 'hover:bg-blue-700'); 
            } else { 
                text.innerHTML = `<span class="text-red-500 font-bold"><i class="fa-solid fa-circle-exclamation"></i> No courts available for this duration.</span>`; 
                btn.disabled = true; btn.innerHTML = "Unavailable"; btn.classList.remove('bg-blue-600', 'hover:bg-blue-700'); btn.classList.add('bg-gray-400', 'cursor-not-allowed'); 
            } 
        }

        async function bookCourt() { const dateStr = document.getElementById('bookingDate').value; const hourInt = parseInt(document.getElementById('startHour').value); const durationInt = parseInt(document.getElementById('duration').value); if(!dateStr || isNaN(hourInt)) return alert("Please select date and a valid time."); const endHourInt = hourInt + durationInt; const fmt = (h) => `${h.toString().padStart(2, '0')}:00:00`; const payload = { date: dateStr, duration: durationInt, start_time: `${dateStr} ${fmt(hourInt)}`, end_time: `${dateStr} ${fmt(endHourInt)}` }; try { const res = await fetch('/api/bookings', { method: 'POST', headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json', 'Accept': 'application/json' }, body: JSON.stringify(payload) }); const json = await res.json(); if (res.ok) { alert('Request Created! Redirecting to payment...'); payWithFPX(json.data.id); } else { let msg = json.message || 'Error occurred'; if(json.errors) { const firstKey = Object.keys(json.errors)[0]; msg = json.errors[firstKey][0]; } alert('Error: ' + msg); } } catch (err) { alert("Network error."); } }
        async function openProfile() { const modal = document.getElementById('profileModal'); const content = document.getElementById('profileContent'); modal.classList.remove('hidden'); setTimeout(() => { modal.classList.remove('opacity-0'); content.classList.remove('scale-95'); content.classList.add('scale-100'); }, 10); try { const res = await fetch('/api/user', { headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' } }); const user = await res.json(); document.getElementById('pName').innerText = user.name; document.getElementById('pEmail').innerText = user.email; document.getElementById('pRole').innerText = user.role; document.getElementById('pJoined').innerText = new Date(user.created_at).toLocaleDateString(); } catch(e) { console.error("Failed to load profile"); } }
        function closeProfile() { const modal = document.getElementById('profileModal'); const content = document.getElementById('profileContent'); modal.classList.add('opacity-0'); content.classList.remove('scale-100'); content.classList.add('scale-95'); setTimeout(() => { modal.classList.add('hidden'); }, 200); }
    </script>
</body>
</html>