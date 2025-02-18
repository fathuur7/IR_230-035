<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SearchBook</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Custom CSS -->
    <style>
        .gradient-background {
            background: linear-gradient(111.4deg, rgba(122,192,233,1) 18.8%, rgba(4,161,255,1) 100.2%);
        }
    </style>
</head>
<body class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header>
        <nav class="gradient-background shadow-lg">
            <div class="container mx-auto px-4 py-4">
                <a class="text-2xl font-bold text-white hover:text-blue-100 transition-colors duration-200" href="#">
                    Book Searching
                </a>
            </div>
        </nav>
    </header>

    <!-- Main Search Section -->
    <main class="gradient-background">
        <div class="container mx-auto px-4 py-12">
            <form id="searchForm" class="max-w-3xl mx-auto" onsubmit="return false">
                <div class="flex flex-col sm:flex-row gap-4">
                    <!-- Search Input -->
                    <input 
                        type="text" 
                        class="flex-1 px-4 py-2 rounded-lg border-0 shadow-md focus:ring-2 focus:ring-blue-300 outline-none"
                        placeholder="Search the Book" 
                        name="q" 
                        id="cari"
                    >
                    
                    <!-- Results Per Page Select -->
                    <select 
                        class="w-24 px-3 py-2 rounded-lg border-0 shadow-md focus:ring-2 focus:ring-blue-300 outline-none bg-white"
                        name="rank" 
                        id="rank"
                    >
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="20">20</option>
                    </select>
                    
                    <!-- Search Button -->
                    <button 
                        class="px-6 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200 shadow-md"
                        id="search" 
                        type="submit"
                    >
                        Search
                    </button>
                </div>
            </form>
        </div>
    </main>

    <!-- Results Section -->
    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6" id="content">
            <!-- Results will be loaded here -->
        </div>
    </div>

    <!-- Script -->
    <script>
        // Menjadi (karena Anda menggunakan ID "search" pada button):
        $(document).ready(function() {
    $("#search").click(function() {  // Ganti dari submit ke click karena kita menggunakan button
        const cari = $("#cari").val().trim();
        const rank = $("#rank").val();

        if (!cari) {
            alert("Please enter a search query.");
            return;
        }

        $('#content').html('<div class="text-center">Loading...</div>');
        
        $.ajax({
            url: '/search',  
            method: 'GET',
            data: {
                q: cari,
                rank: rank
            },
            dataType: 'json',
            success: function(response) {
                // Karena response adalah object dengan numeric keys
                const results = Object.values(response);
                $('#content').html(results.join(''));
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                $('#content').html('<div class="text-center text-red-500">Error occurred while searching</div>');
            }
        });
    });
});    
    </script>
</body>
</html>