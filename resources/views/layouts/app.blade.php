<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NexusSync AI - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 text-gray-900">
    <nav class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-2xl font-bold text-indigo-600"><i class="fa-solid fa-microchip mr-2"></i>NexusSync AI</span>
                </div>
                <div class="flex items-center space-x-4 text-sm">
                    <span class="flex items-center text-green-500"><i class="fa-solid fa-circle text-[10px] mr-1"></i> DB Connected</span>
                    <span class="flex items-center text-blue-500"><i class="fa-solid fa-circle text-[10px] mr-1"></i> Redis Active</span>
                </div>
            </div>
        </div>
    </nav>

    <main class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @yield('content')
        </div>
    </main>
</body>
</html>