<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Racket Arena</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans min-h-screen flex items-center justify-center">

    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md">
        <h1 class="text-3xl font-bold text-center mb-2 text-blue-600">🏸 Racket Arena</h1>
        <p class="text-center text-gray-500 mb-6">Sign in to your account</p>
        
        <div class="space-y-4">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Email Address</label>
                <input id="email" type="email" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" value="izzuddin0@gmail.com">
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                <input id="password" type="password" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
            </div>
            <button onclick="login()" class="w-full bg-blue-600 text-white p-3 rounded-lg font-bold hover:bg-blue-700 transition duration-200">Sign In</button>
        </div>
        <p id="loginError" class="text-red-500 text-center mt-4 hidden text-sm font-semibold"></p>
    </div>

    <script>
        // If already logged in, redirect based on stored role
        const token = localStorage.getItem('token');
        const role = localStorage.getItem('role');
        if (token && role) {
            redirectUser(role);
        }

        async function login() {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const errorText = document.getElementById('loginError');

            try {
                const res = await fetch('/api/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ email, password })
                });
                const data = await res.json();

                if (data.token) {
                    // Save Token & Role
                    localStorage.setItem('token', data.token);
                    localStorage.setItem('role', data.user.role);
                    
                    // Redirect
                    redirectUser(data.user.role);
                } else {
                    errorText.innerText = data.message || 'Login failed';
                    errorText.classList.remove('hidden');
                }
            } catch (e) {
                errorText.innerText = 'Connection Error';
                errorText.classList.remove('hidden');
            }
        }

        function redirectUser(role) {
            if (role === 'admin') window.location.href = '/admin-dashboard';
            else if (role === 'employee') window.location.href = '/employee-dashboard';
            else window.location.href = '/customer-dashboard';
        }
    </script>
</body>
</html>