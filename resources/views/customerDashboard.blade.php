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
    <nav class="bg-white shadow-sm px-6 py-4 flex justify-between items-center">
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
                <i class="fa-regular fa-calendar-plus text-blue-500"></i> Book a Court
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Select Court</label>
                    <select id="courtSelect" class="w-full p-3 border rounded-lg bg-gray-50 focus:border-blue-500 outline-none"></select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Start Time</label>
                    <input id="startTime" type="datetime-local" class="w-full p-3 border rounded-lg bg-gray-50 focus:border-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">End Time</label>
                    <input id="endTime" type="datetime-local" class="w-full p-3 border rounded-lg bg-gray-50 focus:border-blue-500 outline-none">
                </div>
            </div>
            <button onclick="bookCourt()" class="bg-blue-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-blue-700 transition w-full md:w-auto shadow-md shadow-blue-200">
                Confirm Booking
            </button>
        </div>

        <!-- History Section -->
        <div>
            <h3 class="text-lg font-bold text-gray-800 mb-4">My Booking History</h3>
            <div id="myBookingsList" class="grid gap-3">
                <!-- Bookings populate here -->
            </div>
        </div>

    </div>

    <script>
        const token = localStorage.getItem('token');
        if (!token) window.location.href = '/login';

        // Load Data on Start
        loadCourts();
        loadMyBookings();

        function logout() {
            localStorage.clear();
            window.location.href = '/login';
        }

        async function loadCourts() {
            const res = await fetch('/api/courts');
            const json = await res.json();
            const select = document.getElementById('courtSelect');
            select.innerHTML = json.data.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
        }

        async function bookCourt() {
            const courtId = document.getElementById('courtSelect').value;
            const start = document.getElementById('startTime').value;
            const end = document.getElementById('endTime').value;

            const res = await fetch('/api/bookings', {
                method: 'POST',
                headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ court_id: courtId, start_time: start, end_time: end })
            });
            const json = await res.json();
            if (json.status === 'success') {
                alert('Booking Request Sent!');
                loadMyBookings();
            } else {
                alert('Error: ' + (json.message || 'Check your selection'));
            }
        }

        async function loadMyBookings() {
            const res = await fetch('/api/my-bookings', {
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });
            const json = await res.json();
            const list = document.getElementById('myBookingsList');
            
            if(json.data.length === 0) {
                list.innerHTML = '<p class="text-gray-400 italic">No bookings found.</p>';
                return;
            }

            list.innerHTML = json.data.map(b => `
                <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 flex justify-between items-center transition hover:shadow-md">
                    <div class="flex items-center gap-4">
                        <div class="h-10 w-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <div>
                            <div class="font-bold text-gray-800">${b.court.name}</div>
                            <div class="text-xs text-gray-500">${new Date(b.start_time).toLocaleString()}</div>
                        </div>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold ${getStatusColor(b.status)} capitalize">${b.status}</span>
                </div>
            `).join('');
        }

        function getStatusColor(status) {
            if (status === 'confirmed') return 'bg-emerald-100 text-emerald-700';
            if (status === 'cancelled') return 'bg-red-100 text-red-700';
            return 'bg-yellow-100 text-yellow-700';
        }
    </script>
</body>
</html>