<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>Welcome, {{ $user->name }}!</h1>
    <p>Your Branch: {{ $branch->branch_name ?? 'Not assigned' }}</p>
            <div class="p-4 border-t border-indigo-700">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
</body>
</html>
