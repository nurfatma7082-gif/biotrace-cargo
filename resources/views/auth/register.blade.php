<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - BioTrace</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-900 via-teal-800 to-cyan-900 flex items-center justify-center p-4">
    <div class="w-full max-w-lg">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-teal-500 rounded-full mb-4 shadow-lg">
                <i class="fas fa-dna text-white text-2xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-white">BioTrace</h1>
            <p class="text-teal-200 mt-1 text-sm">Cold-Chain Biospecimen Tracking Platform</p>
        </div>

        <!-- Register Card -->
        <div class="bg-white/10 backdrop-blur-md rounded-2xl shadow-2xl border border-white/20 p-8">
            <h2 class="text-xl font-semibold text-white mb-6 text-center">Create Account</h2>

            @if($errors->any())
                <div class="mb-4 p-4 bg-red-500/20 border border-red-400/50 rounded-xl">
                    @foreach($errors->all() as $error)
                        <p class="text-red-200 text-sm"><i class="fas fa-exclamation-circle mr-1"></i>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <!-- Name -->
                <div>
                    <label class="block text-teal-200 text-sm font-medium mb-1">Full Name</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-teal-300">
                            <i class="fas fa-user text-sm"></i>
                        </span>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="w-full pl-10 pr-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition"
                            placeholder="Dr. John Doe">
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-teal-200 text-sm font-medium mb-1">Email Address</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-teal-300">
                            <i class="fas fa-envelope text-sm"></i>
                        </span>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full pl-10 pr-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition"
                            placeholder="you@hospital.com">
                    </div>
                </div>

                <!-- Organization -->
                <div>
                    <label class="block text-teal-200 text-sm font-medium mb-1">Organization</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-teal-300">
                            <i class="fas fa-hospital text-sm"></i>
                        </span>
                        <input type="text" name="organization" value="{{ old('organization') }}"
                            class="w-full pl-10 pr-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition"
                            placeholder="RSCM / Biolab / Research Center">
                    </div>
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-teal-200 text-sm font-medium mb-1">Phone (Optional)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-teal-300">
                            <i class="fas fa-phone text-sm"></i>
                        </span>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                            class="w-full pl-10 pr-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition"
                            placeholder="+62 812 xxxx xxxx">
                    </div>
                </div>

                <!-- Role -->
                <div>
                    <label class="block text-teal-200 text-sm font-medium mb-1">Account Role</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-teal-300">
                            <i class="fas fa-user-tag text-sm"></i>
                        </span>
                        <select name="role"
                            class="w-full pl-10 pr-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition appearance-none">
                            <option value="CUSTOMER" {{ old('role') == 'CUSTOMER' ? 'selected' : '' }} class="text-gray-900">Customer (Hospital / Lab)</option>
                            <option value="OPERATOR" {{ old('role') == 'OPERATOR' ? 'selected' : '' }} class="text-gray-900">Operator (Logistics Staff)</option>
                        </select>
                    </div>
                    <p class="text-teal-300 text-xs mt-1"><i class="fas fa-info-circle mr-1"></i>Admin accounts are created by existing admins only.</p>
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-teal-200 text-sm font-medium mb-1">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-teal-300">
                            <i class="fas fa-lock text-sm"></i>
                        </span>
                        <input type="password" name="password" required
                            class="w-full pl-10 pr-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition"
                            placeholder="Min. 8 characters">
                    </div>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label class="block text-teal-200 text-sm font-medium mb-1">Confirm Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-teal-300">
                            <i class="fas fa-lock text-sm"></i>
                        </span>
                        <input type="password" name="password_confirmation" required
                            class="w-full pl-10 pr-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition"
                            placeholder="Repeat password">
                    </div>
                </div>

                <!-- Submit -->
                <button type="submit"
                    class="w-full py-3 px-4 bg-teal-500 hover:bg-teal-400 text-white font-semibold rounded-xl transition duration-200 shadow-lg mt-2">
                    <i class="fas fa-user-plus mr-2"></i>Create Account
                </button>
            </form>

            <!-- Login link -->
            <p class="text-center text-teal-200 text-sm mt-6">
                Already have an account?
                <a href="{{ route('login') }}" class="text-white font-semibold hover:text-teal-200 underline underline-offset-2">Sign In</a>
            </p>
        </div>

        <p class="text-center text-white/40 text-xs mt-6">BioTrace &copy; {{ date('Y') }} &bull; Biomedical Cold-Chain Logistics</p>
    </div>
</body>
</html>

