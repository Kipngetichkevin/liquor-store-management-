<!-- Sidebar -->
<aside id="sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen pt-16 transition-all duration-300 -translate-x-full bg-white border-r border-gray-200 md:translate-x-0 dark:bg-gray-800 dark:border-gray-700">
    <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-800">
        <ul class="space-y-2 font-medium">
            <!-- Dashboard -->
            <li>
                <a href="{{ route('dashboard') }}" class="flex items-center p-3 text-gray-900 rounded-lg hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 group {{ request()->routeIs('dashboard') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                    <i class="fas fa-tachometer-alt w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white"></i>
                    <span class="ml-3 sidebar-text">Dashboard</span>
                </a>
            </li>

            <!-- Products -->
            <li>
                <a href="{{ route('products.index') }}" class="flex items-center p-3 text-gray-900 rounded-lg hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 group {{ request()->routeIs('products.*') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                    <i class="fas fa-wine-bottle w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white"></i>
                    <span class="ml-3 sidebar-text">Products</span>
                </a>
            </li>

            <!-- Categories -->
            <li>
                <a href="{{ route('categories.index') }}" class="flex items-center p-3 text-gray-900 rounded-lg hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 group {{ request()->routeIs('categories.*') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                    <i class="fas fa-tags w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white"></i>
                    <span class="ml-3 sidebar-text">Categories</span>
                </a>
            </li>

            <!-- Divider -->
            <li class="pt-4 mt-4 border-t border-gray-200 dark:border-gray-700">
                <span class="px-3 text-xs font-semibold text-gray-500 uppercase dark:text-gray-400 sidebar-text">Coming Soon</span>
            </li>

            <!-- Suppliers (disabled for now) -->
            <li>
                <a href="#" class="flex items-center p-3 text-gray-400 rounded-lg cursor-not-allowed group">
                    <i class="fas fa-truck w-5 h-5 text-gray-400"></i>
                    <span class="ml-3 sidebar-text">Suppliers</span>
                    <span class="ml-auto text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full dark:bg-yellow-900 dark:text-yellow-300">Soon</span>
                </a>
            </li>

            <!-- Inventory (disabled) -->
            <li>
                <a href="#" class="flex items-center p-3 text-gray-400 rounded-lg cursor-not-allowed group">
                    <i class="fas fa-boxes w-5 h-5 text-gray-400"></i>
                    <span class="ml-3 sidebar-text">Inventory</span>
                    <span class="ml-auto text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full dark:bg-yellow-900 dark:text-yellow-300">Soon</span>
                </a>
            </li>

            <!-- Sales (disabled) -->
            <li>
                <a href="#" class="flex items-center p-3 text-gray-400 rounded-lg cursor-not-allowed group">
                    <i class="fas fa-shopping-cart w-5 h-5 text-gray-400"></i>
                    <span class="ml-3 sidebar-text">Sales</span>
                    <span class="ml-auto text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full dark:bg-yellow-900 dark:text-yellow-300">Soon</span>
                </a>
            </li>

            <!-- Customers (disabled) -->
            <li>
                <a href="#" class="flex items-center p-3 text-gray-400 rounded-lg cursor-not-allowed group">
                    <i class="fas fa-users w-5 h-5 text-gray-400"></i>
                    <span class="ml-3 sidebar-text">Customers</span>
                    <span class="ml-auto text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full dark:bg-yellow-900 dark:text-yellow-300">Soon</span>
                </a>
            </li>

            <!-- Reports (disabled) -->
            <li>
                <a href="#" class="flex items-center p-3 text-gray-400 rounded-lg cursor-not-allowed group">
                    <i class="fas fa-chart-bar w-5 h-5 text-gray-400"></i>
                    <span class="ml-3 sidebar-text">Reports</span>
                    <span class="ml-auto text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full dark:bg-yellow-900 dark:text-yellow-300">Soon</span>
                </a>
            </li>

            <!-- System Info at bottom -->
            <li class="fixed bottom-0 left-0 w-64 p-4 bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 transition-all duration-300" style="width: inherit;">
                <div class="text-xs text-gray-500 dark:text-gray-400">
                    <div class="flex items-center mb-1">
                        <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                        <span class="sidebar-text">System Operational</span>
                    </div>
                    <div class="text-gray-400 dark:text-gray-500 sidebar-text">Version 1.0.0</div>
                    <button onclick="toggleDarkMode()" class="mt-2 w-full flex items-center justify-center p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white bg-gray-100 dark:bg-gray-700 rounded-lg">
                        <i class="fas fa-moon dark:hidden mr-2"></i>
                        <i class="fas fa-sun hidden dark:block mr-2"></i>
                        <span class="sidebar-text">Toggle Theme</span>
                    </button>
                </div>
            </li>
        </ul>
    </div>
</aside>