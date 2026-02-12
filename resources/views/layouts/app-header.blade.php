<!-- Header -->
<nav class="bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700 fixed top-0 right-0 left-0 z-30 px-4 py-3">
    <div class="flex items-center justify-between">
        <!-- Left: Mobile menu button and brand -->
        <div class="flex items-center">
            <!-- Mobile menu button -->
            <button type="button" class="p-2 text-gray-500 rounded-lg hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 md:hidden" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Logo/Brand -->
            <a href="{{ route('dashboard') }}" class="flex items-center ml-2 md:ml-0">
                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-wine-bottle text-white text-sm"></i>
                </div>
                <span class="text-xl font-bold text-gray-800 dark:text-white hidden md:inline">LiquorMS</span>
            </a>
        </div>

        <!-- Center: Search bar (desktop only) -->
        <div class="hidden md:flex items-center flex-1 max-w-xl mx-4">
            <div class="relative w-full">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="search" id="search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search products, categories..." />
            </div>
        </div>

        <!-- Right: User menu and notifications -->
        <div class="flex items-center space-x-3">
            <!-- Search button (mobile only) -->
            <button type="button" class="md:hidden p-2 text-gray-500 rounded-lg hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700">
                <i class="fas fa-search"></i>
            </button>

            <!-- Notifications -->
            <div class="relative">
                <button type="button" class="p-2 text-gray-500 rounded-lg hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700" onclick="toggleNotifications()">
                    <i class="fas fa-bell"></i>
                    <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">
                        0
                    </span>
                </button>
            </div>

            <!-- User dropdown -->
            <div class="relative">
                <button type="button" class="flex items-center space-x-3 text-sm bg-gray-100 dark:bg-gray-700 rounded-full p-1" onclick="toggleUserMenu()">
                    <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white font-semibold">
                        A
                    </div>
                    <div class="hidden md:block text-left">
                        <div class="font-medium text-gray-900 dark:text-white">Admin User</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Administrator</div>
                    </div>
                    <i class="fas fa-chevron-down text-gray-500 dark:text-gray-400 hidden md:block"></i>
                </button>

                <!-- Dropdown menu -->
                <div id="user-menu" class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50">
                    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Admin User</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">admin@liquorms.test</p>
                    </div>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">
                        <i class="fas fa-user mr-2"></i> My Profile
                    </a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">
                        <i class="fas fa-cog mr-2"></i> Settings
                    </a>
                    <div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>
                    <a href="#" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100 dark:text-red-400 dark:hover:bg-gray-700">
                        <i class="fas fa-sign-out-alt mr-2"></i> Sign Out
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
    // Toggle sidebar on mobile
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('-translate-x-full');
    }

    // Toggle user menu
    function toggleUserMenu() {
        const menu = document.getElementById('user-menu');
        menu.classList.toggle('hidden');
        
        // Close notifications if open
        const notifications = document.getElementById('notifications-dropdown');
        if (notifications) notifications.classList.add('hidden');
    }

    // Toggle notifications
    function toggleNotifications() {
        const notifications = document.getElementById('notifications-dropdown');
        if (notifications) notifications.classList.toggle('hidden');
        
        // Close user menu if open
        const userMenu = document.getElementById('user-menu');
        if (userMenu) userMenu.classList.add('hidden');
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        const userMenuButton = document.querySelector('button[onclick="toggleUserMenu()"]');
        const userMenu = document.getElementById('user-menu');
        const notificationsButton = document.querySelector('button[onclick="toggleNotifications()"]');
        const notifications = document.getElementById('notifications-dropdown');

        // Close user menu if clicking outside
        if (userMenu && userMenuButton && !userMenu.contains(event.target) && !userMenuButton.contains(event.target)) {
            userMenu.classList.add('hidden');
        }

        // Close notifications if clicking outside
        if (notifications && notificationsButton && !notifications.contains(event.target) && !notificationsButton.contains(event.target)) {
            notifications.classList.add('hidden');
        }
    });

    // Search functionality
    document.getElementById('search')?.addEventListener('keyup', function(event) {
        if (event.key === 'Enter') {
            const query = this.value.trim();
            if (query) {
                // You can implement search logic here
                console.log('Searching for:', query);
                // Example: window.location.href = `/search?q=${encodeURIComponent(query)}`;
            }
        }
    });
</script>