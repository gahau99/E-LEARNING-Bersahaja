<nav class="bg-blue-600 text-white shadow-md">
    <div class="container mx-auto px-4 py-3 flex justify-between items-center">
        <!-- Logo / Brand -->
        <a href="../dashboard.php" 
            class="font-bold text-xl text-green-500 hover:text-green-700 transition-colors">
            Bersahaja
        </a>

        <!-- Menu (Desktop) -->
        <ul class="hidden md:flex space-x-6">
            
        </ul>

        <!-- User Dropdown -->
        <div class="relative">
            <button id="userMenuBtn" class="flex items-center space-x-2 focus:outline-none">
                <span><?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M19 9l-7 7-7-7"/>
                </svg>
                
            </button>
            <div id="userMenu" class="absolute right-0 mt-2 w-40 bg-white text-gray-800 rounded shadow-lg hidden">
                <a href="logout.php" class="block px-4 py-2 hover:bg-gray-100">Logout</a>
            </div>
        </div>

        <!-- Mobile menu button -->
        <button id="mobileBtn" class="md:hidden ml-4 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>

    <!-- Mobile Menu -->
    <ul id="mobileMenu" class="md:hidden hidden bg-blue-500 px-4 py-2 space-y-2">
       
        <li><a href="../logout.php" class="block text-white hover:text-gray-200">Logout</a></li>
        <li><a href="../dashboard.php" class="block text-white hover:text-gray-200">Dashboard</a></li>
    </ul>
</nav>

<script>
    // Toggle user dropdown
    document.getElementById('userMenuBtn').addEventListener('click', () => {
        const menu = document.getElementById('userMenu');
        menu.classList.toggle('hidden');
    });

    // Toggle mobile menu
    document.getElementById('mobileBtn').addEventListener('click', () => {
        const menu = document.getElementById('mobileMenu');
        menu.classList.toggle('hidden');
    });
</script>
