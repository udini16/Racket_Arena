<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Racket Arena</title>
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        @import url('https://fonts.googleapis.com/css?family=Montserrat:400,800');

        * {
            box-sizing: border-box;
        }

        body {
            background: #f6f5f7;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            font-family: 'Montserrat', sans-serif;
            height: 100vh;
            margin: -20px 0 50px;
        }

        h1 {
            font-weight: bold;
            margin: 0;
        }

        h2 {
            text-align: center;
        }

        p {
            font-size: 14px;
            font-weight: 100;
            line-height: 20px;
            letter-spacing: 0.5px;
            margin: 20px 0 30px;
        }

        span {
            font-size: 12px;
        }

        a {
            color: #333;
            font-size: 14px;
            text-decoration: none;
            margin: 15px 0;
        }

        button {
            border-radius: 20px;
            border: 1px solid #007bff;
            background-color: #007bff;
            color: #FFFFFF;
            font-size: 12px;
            font-weight: bold;
            padding: 12px 45px;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: transform 80ms ease-in;
            cursor: pointer;
        }

        button:active {
            transform: scale(0.95);
        }

        button:focus {
            outline: none;
        }

        button:disabled {
            background-color: #80bdff;
            border-color: #80bdff;
            cursor: not-allowed;
        }

        button.ghost {
            background-color: transparent;
            border-color: #FFFFFF;
        }

        form {
            background-color: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 0 50px;
            height: 100%;
            text-align: center;
        }

        input {
            background-color: #eee;
            border: none;
            padding: 12px 15px;
            margin: 8px 0;
            width: 100%;
        }

        .container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 14px 28px rgba(0,0,0,0.25), 
                        0 10px 10px rgba(0,0,0,0.22);
            position: relative;
            overflow: hidden;
            width: 768px;
            max-width: 100%;
            min-height: 550px; /* Increased slightly for extra fields */
        }

        .form-container {
            position: absolute;
            top: 0;
            height: 100%;
            transition: all 0.6s ease-in-out;
        }

        .sign-in-container {
            left: 0;
            width: 50%;
            z-index: 2;
        }

        .container.right-panel-active .sign-in-container {
            transform: translateX(100%);
        }

        .sign-up-container {
            left: 0;
            width: 50%;
            opacity: 0;
            z-index: 1;
        }

        .container.right-panel-active .sign-up-container {
            transform: translateX(100%);
            opacity: 1;
            z-index: 5;
            animation: show 0.6s;
        }

        @keyframes show {
            0%, 49.99% {
                opacity: 0;
                z-index: 1;
            }
            
            50%, 100% {
                opacity: 1;
                z-index: 5;
            }
        }

        .overlay-container {
            position: absolute;
            top: 0;
            left: 50%;
            width: 50%;
            height: 100%;
            overflow: hidden;
            transition: transform 0.6s ease-in-out;
            z-index: 100;
        }

        .container.right-panel-active .overlay-container{
            transform: translateX(-100%);
        }

        .overlay {
            background: #0056b3;
            background: -webkit-linear-gradient(to right, #007bff, #0056b3);
            background: linear-gradient(to right, #007bff, #0056b3);
            background-repeat: no-repeat;
            background-size: cover;
            background-position: 0 0;
            color: #FFFFFF;
            position: relative;
            left: -100%;
            height: 100%;
            width: 200%;
            transform: translateX(0);
            transition: transform 0.6s ease-in-out;
        }

        .container.right-panel-active .overlay {
            transform: translateX(50%);
        }

        .overlay-panel {
            position: absolute;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 0 40px;
            text-align: center;
            top: 0;
            height: 100%;
            width: 50%;
            transform: translateX(0);
            transition: transform 0.6s ease-in-out;
        }

        .overlay-left {
            transform: translateX(-20%);
        }

        .container.right-panel-active .overlay-left {
            transform: translateX(0);
        }

        .overlay-right {
            right: 0;
            transform: translateX(0);
        }

        .container.right-panel-active .overlay-right {
            transform: translateX(20%);
        }

        .social-container {
            margin: 20px 0;
        }

        .social-container a {
            border: 1px solid #DDDDDD;
            border-radius: 50%;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            margin: 0 5px;
            height: 40px;
            width: 40px;
        }

        footer {
            background-color: #222;
            color: #fff;
            font-size: 14px;
            bottom: 0;
            position: fixed;
            left: 0;
            right: 0;
            text-align: center;
            z-index: 999;
        }

        footer p {
            margin: 10px 0;
        }

        footer i {
            color: #007bff;
        }

        footer a {
            color: #3c97bf;
            text-decoration: none;
        }

        /* Helper for error messages */
        .error-text {
            color: #e74c3c;
            font-size: 11px;
            margin-top: 5px;
            font-weight: bold;
            display: none;
        }

        /* Home Button Style */
        .home-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            background-color: #fff;
            color: #007bff;
            border: 1px solid #007bff;
            border-radius: 50%;
            width: 45px;
            height: 45px;
            display: flex;
            justify-content: center;
            align-items: center;
            text-decoration: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            z-index: 1000;
            font-size: 18px;
            margin: 0; /* Override generic anchor margin */
        }

        .home-btn:hover {
            transform: scale(1.1);
            background-color: #007bff;
            color: #fff;
            box-shadow: 0 6px 8px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>

<!-- Home Button -->
<a href="/" class="home-btn" title="Back to Homepage">
    <i class="fa-solid fa-house"></i>
</a>

<div class="container" id="container">
    <!-- Sign Up Container -->
    <div class="form-container sign-up-container">
        <form onsubmit="event.preventDefault(); register()">
            <h1>Create Account</h1>
            <div class="social-container">
                <a href="#" class="social"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="social"><i class="fab fa-google-plus-g"></i></a>
                <a href="#" class="social"><i class="fab fa-linkedin-in"></i></a>
            </div>
            <span>or use your email for registration</span>
            <input type="text" id="reg_name" placeholder="Name" required />
            <input type="email" id="reg_email" placeholder="Email" required />
            <input type="password" id="reg_password" placeholder="Password" required />
            <input type="password" id="reg_password_confirmation" placeholder="Confirm Password" required />
            <p id="regError" class="error-text"></p>
            <button id="btnSignUp" type="submit">Sign Up</button>
        </form>
    </div>

    <!-- Sign In Container -->
    <div class="form-container sign-in-container">
        <form onsubmit="event.preventDefault(); login()">
            <h1>Sign in</h1>
            <div class="social-container">
                <a href="#" class="social"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="social"><i class="fab fa-google-plus-g"></i></a>
                <a href="#" class="social"><i class="fab fa-linkedin-in"></i></a>
            </div>
            <span>or use your account</span>
            <input type="email" id="login_email" placeholder="Email" value="izzuddin0@gmail.com" required />
            <input type="password" id="login_password" placeholder="Password" required />
            <a href="#">Forgot your password?</a>
            <p id="loginError" class="error-text"></p>
            <button id="btnLogin" type="submit">Sign In</button>
        </form>
    </div>

    <!-- Overlay / Slider -->
    <div class="overlay-container">
        <div class="overlay">
            <div class="overlay-panel overlay-left">
                <h1>Welcome Back!</h1>
                <p>To keep connected with us please login with your personal info</p>
                <button class="ghost" id="signIn">Sign In</button>
            </div>
            <div class="overlay-panel overlay-right">
                <h1>Hello, Friend!</h1>
                <p>Enter your personal details and start journey with us</p>
                <button class="ghost" id="signUp">Sign Up</button>
            </div>
        </div>
    </div>
</div>

<footer>
    <p>
        Created with <i class="fa fa-heart"></i> by
        <a target="_blank" href="https://florin-pop.com">Florin Pop</a>
        - Modified for Racket Arena
    </p>
</footer>

<script>
    // --- Animation / Slider Logic ---
    const signUpButton = document.getElementById('signUp');
    const signInButton = document.getElementById('signIn');
    const container = document.getElementById('container');

    signUpButton.addEventListener('click', () => {
        container.classList.add("right-panel-active");
        clearErrors();
    });

    signInButton.addEventListener('click', () => {
        container.classList.remove("right-panel-active");
        clearErrors();
    });

    function clearErrors() {
        document.getElementById('loginError').style.display = 'none';
        document.getElementById('regError').style.display = 'none';
    }

    // --- Authentication Logic ---

    // Check for existing session
    const token = localStorage.getItem('token');
    const role = localStorage.getItem('role');
    if (token && role) {
        redirectUser(role);
    }

    async function login() {
        const email = document.getElementById('login_email').value;
        const password = document.getElementById('login_password').value;
        const errorMsg = document.getElementById('loginError');
        const btn = document.getElementById('btnLogin');

        errorMsg.style.display = 'none';
        btn.disabled = true;
        btn.innerText = 'Signing In...';

        try {
            const res = await fetch('/api/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ email, password })
            });
            const data = await res.json();

            if (res.ok && data.token) {
                handleSuccess(data);
            } else {
                errorMsg.innerText = data.message || 'Invalid Email or Password';
                errorMsg.style.display = 'block';
            }
        } catch (e) {
            console.error(e);
            errorMsg.innerText = 'Unable to connect to server.';
            errorMsg.style.display = 'block';
        } finally {
            btn.disabled = false;
            btn.innerText = 'Sign In';
        }
    }

    async function register() {
        const name = document.getElementById('reg_name').value;
        const email = document.getElementById('reg_email').value;
        const password = document.getElementById('reg_password').value;
        const password_confirmation = document.getElementById('reg_password_confirmation').value;
        const errorMsg = document.getElementById('regError');
        const btn = document.getElementById('btnSignUp');

        errorMsg.style.display = 'none';
        btn.disabled = true;
        btn.innerText = 'Creating...';

        try {
            const res = await fetch('/api/register', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ name, email, password, password_confirmation })
            });
            const data = await res.json();

            if (res.ok && data.token) {
                handleSuccess(data);
            } else {
                // If backend sends specific field errors, just show the general message for now
                errorMsg.innerText = data.message || 'Registration failed. Check inputs.';
                errorMsg.style.display = 'block';
            }
        } catch (e) {
            console.error(e);
            errorMsg.innerText = 'Unable to connect to server.';
            errorMsg.style.display = 'block';
        } finally {
            btn.disabled = false;
            btn.innerText = 'Sign Up';
        }
    }

    function handleSuccess(data) {
        localStorage.setItem('token', data.token);
        localStorage.setItem('role', data.user.role);
        redirectUser(data.user.role);
    }

    function redirectUser(role) {
        if (role === 'admin') window.location.href = '/admin-dashboard';
        else if (role === 'employee') window.location.href = '/employee-dashboard';
        else window.location.href = '/customer-dashboard';
    }
</script>

</body>
</html>