<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Racket Arena</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome for Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            /* Your Custom Purple Gradient */
            background: #6a11cb;
            background: -webkit-linear-gradient(to right, rgba(106, 17, 203, 1), rgba(37, 117, 252, 1));
            background: linear-gradient(to right, rgba(106, 17, 203, 1), rgba(37, 117, 252, 1));
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        /* Custom Input Underline Style */
        .custom-input {
            background: transparent;
            border: none;
            border-bottom: 1px solid rgba(255,255,255,0.5);
            color: white;
            transition: border-color 0.3s;
        }
        .custom-input:focus {
            outline: none;
            border-bottom: 2px solid white;
        }
        /* Animations */
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .hidden { display: none !important; }
    </style>
</head>
<body>

    <div class="w-full max-w-md px-4">
        <!-- Dark Card -->
        <div class="bg-gray-900 text-white rounded-2xl shadow-2xl p-8 md:p-10 text-center relative overflow-hidden" style="border-radius: 1rem;">
            
            <!-- Error Message Container (Shared) -->
            <p id="errorMsg" class="text-red-400 text-sm mb-4 hidden font-bold bg-red-900/30 p-2 rounded fade-in"></p>

            <!-- ================= LOGIN VIEW ================= -->
            <div id="loginView" class="fade-in">
                <h2 class="text-3xl font-bold mb-2 uppercase tracking-wider">Login</h2>
                <p class="text-gray-400 mb-8 text-sm">Please enter your login and password!</p>

                <!-- Email -->
                <div class="mb-6 text-left">
                    <label class="block text-gray-400 text-xs font-bold mb-1 ml-1 uppercase">Email address</label>
                    <input id="login_email" type="email" value="izzuddin0@gmail.com" class="custom-input w-full py-2 px-1 text-lg">
                </div>

                <!-- Password -->
                <div class="mb-8 text-left">
                    <label class="block text-gray-400 text-xs font-bold mb-1 ml-1 uppercase">Password</label>
                    <input id="login_password" type="password" class="custom-input w-full py-2 px-1 text-lg">
                </div>

                <p class="text-sm mb-8">
                    <a href="#!" class="text-gray-400 hover:text-white transition">Forgot password?</a>
                </p>

                <!-- Login Button -->
                <button onclick="login()" class="border border-white text-white px-10 py-3 rounded-full font-bold uppercase tracking-wide hover:bg-white hover:text-gray-900 transition-all duration-300 transform hover:scale-105 mb-6">
                    Login
                </button>

                <!-- Social Media Icons -->
                <div class="flex justify-center gap-6 mb-8">
                    <a href="#!" class="text-white hover:text-blue-500 transition transform hover:scale-110"><i class="fab fa-facebook-f fa-lg"></i></a>
                    <a href="#!" class="text-white hover:text-blue-400 transition transform hover:scale-110"><i class="fab fa-twitter fa-lg"></i></a>
                    <a href="#!" class="text-white hover:text-red-500 transition transform hover:scale-110"><i class="fab fa-google fa-lg"></i></a>
                </div>

                <div class="text-sm">
                    <p class="mb-0">Don't have an account? <button onclick="toggleView('register')" class="text-white font-bold hover:underline bg-transparent border-0 cursor-pointer">Sign Up</button></p>
                </div>
            </div>

            <!-- ================= REGISTER VIEW ================= -->
            <div id="registerView" class="hidden fade-in">
                <h2 class="text-3xl font-bold mb-2 uppercase tracking-wider">Join Us</h2>
                <p class="text-gray-400 mb-6 text-sm">Create your customer account today!</p>

                <!-- Name -->
                <div class="mb-4 text-left">
                    <label class="block text-gray-400 text-xs font-bold mb-1 ml-1 uppercase">Full Name</label>
                    <input id="reg_name" type="text" class="custom-input w-full py-2 px-1 text-lg">
                </div>

                <!-- Email -->
                <div class="mb-4 text-left">
                    <label class="block text-gray-400 text-xs font-bold mb-1 ml-1 uppercase">Email Address</label>
                    <input id="reg_email" type="email" class="custom-input w-full py-2 px-1 text-lg">
                </div>

                <!-- Password -->
                <div class="mb-4 text-left">
                    <label class="block text-gray-400 text-xs font-bold mb-1 ml-1 uppercase">Password</label>
                    <input id="reg_password" type="password" class="custom-input w-full py-2 px-1 text-lg">
                </div>

                <!-- Confirm Password -->
                <div class="mb-8 text-left">
                    <label class="block text-gray-400 text-xs font-bold mb-1 ml-1 uppercase">Confirm Password</label>
                    <input id="reg_password_confirmation" type="password" class="custom-input w-full py-2 px-1 text-lg">
                </div>

                <!-- Register Button -->
                <button onclick="register()" class="bg-blue-600 border border-blue-600 text-white px-10 py-3 rounded-full font-bold uppercase tracking-wide hover:bg-blue-700 transition-all duration-300 transform hover:scale-105 mb-6 shadow-lg shadow-blue-500/50">
                    Sign Up
                </button>

                <div class="text-sm">
                    <p class="mb-0">Already have an account? <button onclick="toggleView('login')" class="text-white font-bold hover:underline bg-transparent border-0 cursor-pointer">Login</button></p>
                </div>
            </div>

        </div>
    </div>

    <!-- JavaScript Logic -->
    <script>
        // Check for existing session
        const token = localStorage.getItem('token');
        const role = localStorage.getItem('role');
        if (token && role) {
            redirectUser(role);
        }

        // --- Toggle Views ---
        function toggleView(view) {
            const loginView = document.getElementById('loginView');
            const registerView = document.getElementById('registerView');
            const errorMsg = document.getElementById('errorMsg');
            
            // Clear errors
            errorMsg.classList.add('hidden');
            errorMsg.innerText = '';

            if (view === 'register') {
                loginView.classList.add('hidden');
                registerView.classList.remove('hidden');
            } else {
                registerView.classList.add('hidden');
                loginView.classList.remove('hidden');
            }
        }

        // --- Login Logic ---
        async function login() {
            const email = document.getElementById('login_email').value;
            const password = document.getElementById('login_password').value;
            
            showError(''); // Clear error

            try {
                const res = await fetch('/api/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ email, password })
                });
                const data = await res.json();

                if (data.token) {
                    handleSuccess(data);
                } else {
                    showError(data.message || 'Invalid Email or Password');
                }
            } catch (e) {
                console.error(e);
                showError('Unable to connect to server.');
            }
        }

        // --- Register Logic ---
        async function register() {
            const name = document.getElementById('reg_name').value;
            const email = document.getElementById('reg_email').value;
            const password = document.getElementById('reg_password').value;
            const password_confirmation = document.getElementById('reg_password_confirmation').value;

            showError(''); // Clear error

            try {
                const res = await fetch('/api/register', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ name, email, password, password_confirmation })
                });
                const data = await res.json();

                if (data.token) {
                    handleSuccess(data);
                } else {
                    // Show validation errors if they exist, otherwise generic message
                    const msg = data.message || 'Registration failed.';
                    showError(msg);
                }
            } catch (e) {
                console.error(e);
                showError('Unable to connect to server.');
            }
        }

        // --- Helpers ---
        function handleSuccess(data) {
            localStorage.setItem('token', data.token);
            localStorage.setItem('role', data.user.role);
            redirectUser(data.user.role);
        }

        function showError(msg) {
            const el = document.getElementById('errorMsg');
            if (msg) {
                el.innerText = msg;
                el.classList.remove('hidden');
            } else {
                el.classList.add('hidden');
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