<nav class="bg-white shadow-md relative">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <!-- Logo -->
            <div class="flex items-center w-1/4">
                <a href="#" class="text-xl font-bold text-gray-800">
                    <img src="{{asset('./assets/img/logo.webp')}}">
                </a>
            </div>

            <!-- Search Bar -->
            {{-- <div class="hidden md:flex items-center flex-1 mx-8">
                <input 
                    type="text" 
                    placeholder="Search for products..." 
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div> --}}

            <!-- Navigation Links -->
            <div class="flex items-center space-x-6">
                <a href="{{route('homepage')}}" class="text-gray-700 hover:text-indigo-600">Home</a>
                <a href="#" class="text-gray-700 hover:text-indigo-600">Create an account</a>
                <a href="#" class="text-gray-700 hover:text-indigo-600">Login</a>
            </div>
        </div>

        <!-- Mobile Menu Button -->
        <div class="flex md:hidden justify-between items-center py-2">
            <button id="menuButton" class="text-gray-700 focus:outline-none focus:text-indigo-600">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobileMenu" class="hidden md:hidden">
        <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Home</a>
        <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Shop</a>
        <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">About</a>
        <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Contact</a>
    </div>
</nav>

<script>
    const menuButton = document.getElementById('menuButton');
    const mobileMenu = document.getElementById('mobileMenu');

    menuButton.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
    });
</script>