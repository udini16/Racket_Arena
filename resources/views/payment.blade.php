<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FPX Payment Gateway - Mock</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">

    <div class="bg-white p-8 rounded-xl shadow-2xl max-w-md w-full border-t-8 border-blue-400">
        <!-- FPX Logo Area -->
        <div class="flex justify-between items-center mb-8">
            <div class="font-bold text-2xl text-slate-800 italic">FPX <span class="text-xs not-italic text-gray-400 block font-normal">Online Banking</span></div>
            <div class="text-right">
                <div class="text-xs text-gray-500 uppercase">Merchant</div>
                <div class="font-bold text-slate-700">Racket Arena Sdn Bhd</div>
            </div>
        </div>

        <!-- Transaction Details -->
        <div class="bg-slate-50 p-4 rounded-lg border border-slate-200 mb-6">
            <div class="flex justify-between mb-2">
                <span class="text-sm text-gray-500">Order ID</span>
                <span class="font-mono font-bold text-slate-700">#{{ $booking->id }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-500">Amount</span>
                <span class="text-2xl font-bold text-emerald-600">RM{{ number_format($booking->total_price, 2) }}</span>
            </div>
        </div>

        <!-- Bank Selection -->
        <p class="text-sm font-bold text-slate-700 mb-3">Select Bank:</p>
        <div class="grid grid-cols-2 gap-3 mb-8">
            <button onclick="selectBank(this)" class="bank-btn border border-gray-300 rounded-lg p-3 hover:border-yellow-400 hover:bg-yellow-50 transition flex items-center justify-center gap-2 group">
                <div class="w-4 h-4 rounded-full bg-yellow-500"></div> <span class="text-sm font-bold text-slate-600 group-hover:text-slate-800">Maybank2u</span>
            </button>
            <button onclick="selectBank(this)" class="bank-btn border border-gray-300 rounded-lg p-3 hover:border-red-500 hover:bg-red-50 transition flex items-center justify-center gap-2 group">
                <div class="w-4 h-4 rounded-full bg-red-600"></div> <span class="text-sm font-bold text-slate-600 group-hover:text-slate-800">CIMB Clicks</span>
            </button>
            <button onclick="selectBank(this)" class="bank-btn border border-gray-300 rounded-lg p-3 hover:border-blue-400 hover:bg-blue-50 transition flex items-center justify-center gap-2 group">
                <div class="w-4 h-4 rounded-full bg-blue-500"></div> <span class="text-sm font-bold text-slate-600 group-hover:text-slate-800">RHB Now</span>
            </button>
            <button onclick="selectBank(this)" class="bank-btn border border-gray-300 rounded-lg p-3 hover:border-orange-400 hover:bg-orange-50 transition flex items-center justify-center gap-2 group">
                <div class="w-4 h-4 rounded-full bg-orange-500"></div> <span class="text-sm font-bold text-slate-600 group-hover:text-slate-800">Bank Islam</span>
            </button>
        </div>

        <!-- Actions -->
        <form action="{{ url('/payment/process/'.$booking->id) }}" method="POST" id="payForm">
            @csrf
            <button type="submit" id="payBtn" disabled class="w-full bg-slate-300 text-white font-bold py-3 rounded-lg shadow-sm cursor-not-allowed transition mb-3">
                Pay RM{{ number_format($booking->total_price, 2) }}
            </button>
        </form>
        
        <a href="{{ url('/customer-dashboard') }}" class="block text-center text-sm text-gray-400 hover:text-gray-600">Cancel Transaction</a>
    </div>

    <script>
        function selectBank(btn) {
            // Reset others
            document.querySelectorAll('.bank-btn').forEach(b => {
                b.classList.remove('ring-2', 'ring-offset-1', 'border-transparent');
                b.classList.add('border-gray-300');
            });

            // Highlight selected
            btn.classList.remove('border-gray-300');
            btn.classList.add('ring-2', 'ring-offset-1', 'ring-indigo-500', 'border-transparent');

            // Enable Pay Button
            const payBtn = document.getElementById('payBtn');
            payBtn.disabled = false;
            payBtn.classList.remove('bg-slate-300', 'cursor-not-allowed');
            payBtn.classList.add('bg-blue-600', 'hover:bg-blue-700', 'shadow-lg');
        }
    </script>
</body>
</html>