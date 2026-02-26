<!-- Sidebar -->
<aside id="sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen pt-16 transition-all duration-300 -translate-x-full bg-white border-r border-gray-200 md:translate-x-0 dark:bg-gray-800 dark:border-gray-700">
    <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-800">
        <ul class="space-y-2 font-medium">
            <!-- Dashboard (everyone) -->
            <li>
                <a href="{{ route('dashboard') }}" class="flex items-center p-3 text-gray-900 rounded-lg hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 group {{ request()->routeIs('dashboard') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                    <i class="fas fa-tachometer-alt w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white"></i>
                    <span class="ml-3 sidebar-text">Dashboard</span>
                </a>
            </li>

            <!-- Products – admin, manager, stock keeper -->
            @if(in_array(auth()->user()->role, ['admin', 'manager', 'stock_keeper']))
            <li>
                <a href="{{ route('products.index') }}" class="flex items-center p-3 text-gray-900 rounded-lg hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 group {{ request()->routeIs('products.*') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                    <i class="fas fa-wine-bottle w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white"></i>
                    <span class="ml-3 sidebar-text">Products</span>
                    @if(request()->routeIs('products.*'))
                        <span class="ml-auto w-2 h-2 bg-blue-600 rounded-full"></span>
                    @endif
                </a>
            </li>
            @endif

            <!-- Categories – admin, manager, stock keeper -->
            @if(in_array(auth()->user()->role, ['admin', 'manager', 'stock_keeper']))
            <li>
                <a href="{{ route('categories.index') }}" class="flex items-center p-3 text-gray-900 rounded-lg hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 group {{ request()->routeIs('categories.*') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                    <i class="fas fa-tags w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white"></i>
                    <span class="ml-3 sidebar-text">Categories</span>
                    @if(request()->routeIs('categories.*'))
                        <span class="ml-auto w-2 h-2 bg-blue-600 rounded-full"></span>
                    @endif
                </a>
            </li>
            @endif

            <!-- Suppliers – admin, manager, stock keeper -->
            @if(in_array(auth()->user()->role, ['admin', 'manager', 'stock_keeper']))
            <li>
                <a href="{{ route('suppliers.index') }}" class="flex items-center p-3 text-gray-900 rounded-lg hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 group {{ request()->routeIs('suppliers.*') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                    <i class="fas fa-truck w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white"></i>
                    <span class="ml-3 sidebar-text">Suppliers</span>
                    @if(request()->routeIs('suppliers.*'))
                        <span class="ml-auto w-2 h-2 bg-blue-600 rounded-full"></span>
                    @endif
                </a>
            </li>
            @endif

            <!-- Inventory – admin, manager, stock keeper -->
            @if(in_array(auth()->user()->role, ['admin', 'manager', 'stock_keeper']))
            <li>
                <a href="{{ route('inventory.index') }}" class="flex items-center p-3 text-gray-900 rounded-lg hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 group {{ request()->routeIs('inventory.*') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                    <i class="fas fa-boxes w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white"></i>
                    <span class="ml-3 sidebar-text">Inventory</span>
                    @if(request()->routeIs('inventory.*'))
                        <span class="ml-auto w-2 h-2 bg-blue-600 rounded-full"></span>
                    @endif
                </a>
            </li>
            @endif

            <!-- Purchase Orders – admin, manager, stock keeper -->
            @if(in_array(auth()->user()->role, ['admin', 'manager', 'stock_keeper']))
            <li>
                <a href="{{ route('purchase-orders.index') }}" class="flex items-center p-3 text-gray-900 rounded-lg hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 group {{ request()->routeIs('purchase-orders.*') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                    <i class="fas fa-shopping-cart w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white"></i>
                    <span class="ml-3 sidebar-text">Purchase Orders</span>
                    @if(request()->routeIs('purchase-orders.*'))
                        <span class="ml-auto w-2 h-2 bg-blue-600 rounded-full"></span>
                    @endif
                </a>
            </li>
            @endif

            <!-- POS – admin, manager, cashier -->
            @if(in_array(auth()->user()->role, ['admin', 'manager', 'cashier']))
            <li>
                <a href="{{ route('pos.index') }}" class="flex items-center p-3 text-gray-900 rounded-lg hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 group {{ request()->routeIs('pos.*') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                    <i class="fas fa-shopping-cart w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white"></i>
                    <span class="ml-3 sidebar-text">Point of Sale</span>
                    @if(request()->routeIs('pos.*'))
                        <span class="ml-auto w-2 h-2 bg-blue-600 rounded-full"></span>
                    @endif
                </a>
            </li>
            @endif

            <!-- Customers – admin, manager, cashier -->
            @if(in_array(auth()->user()->role, ['admin', 'manager', 'cashier']))
            <li>
                <a href="{{ route('customers.index') }}" class="flex items-center p-3 text-gray-900 rounded-lg hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 group {{ request()->routeIs('customers.*') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                    <i class="fas fa-users w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white"></i>
                    <span class="ml-3 sidebar-text">Customers</span>
                    @if(request()->routeIs('customers.*'))
                        <span class="ml-auto w-2 h-2 bg-blue-600 rounded-full"></span>
                    @endif
                </a>
            </li>
            @endif

            <!-- Users – admin, manager -->
            @if(in_array(auth()->user()->role, ['admin', 'manager']))
            <li>
                <a href="{{ route('users.index') }}" class="flex items-center p-3 text-gray-900 rounded-lg hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 group {{ request()->routeIs('users.*') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                    <i class="fas fa-users-cog w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white"></i>
                    <span class="ml-3 sidebar-text">Users</span>
                    @if(request()->routeIs('users.*'))
                        <span class="ml-auto w-2 h-2 bg-blue-600 rounded-full"></span>
                    @endif
                </a>
            </li>
            @endif

            <!-- Sales Reports (existing dropdown) – admin, manager -->
            @if(in_array(auth()->user()->role, ['admin', 'manager']))
            <li x-data="{ open: {{ request()->routeIs('sales.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="flex items-center w-full p-3 text-gray-900 rounded-lg hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 group">
                    <i class="fas fa-chart-line w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white"></i>
                    <span class="flex-1 ml-3 text-left sidebar-text">Sales Reports</span>
                    <i class="fas fa-chevron-down text-gray-500 dark:text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                </button>
                <ul x-show="open" x-collapse class="pl-4 mt-1 space-y-1">
                    <li>
                        <a href="{{ route('sales.index') }}" class="flex items-center p-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 {{ request()->routeIs('sales.index') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                            <i class="fas fa-list w-4 h-4 mr-2"></i> All Sales
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('sales.daily') }}" class="flex items-center p-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 {{ request()->routeIs('sales.daily') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                            <i class="fas fa-calendar-day w-4 h-4 mr-2"></i> Daily Report
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('sales.weekly') }}" class="flex items-center p-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 {{ request()->routeIs('sales.weekly') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                            <i class="fas fa-calendar-week w-4 h-4 mr-2"></i> Weekly Report
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('sales.monthly') }}" class="flex items-center p-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 {{ request()->routeIs('sales.monthly') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                            <i class="fas fa-calendar-alt w-4 h-4 mr-2"></i> Monthly Report
                        </a>
                    </li>
                    <!-- IMPORT LINK -->
                    <li>
                        <a href="{{ route('sales.import.form') }}" class="flex items-center p-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 {{ request()->routeIs('sales.import*') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                            <i class="fas fa-upload w-4 h-4 mr-2"></i> Import Sales Data
                        </a>
                    </li>
                </ul>
            </li>
            @endif

            <!-- 👇 NEW REPORTS DROPDOWN – admin, manager -->
            @if(in_array(auth()->user()->role, ['admin', 'manager']))
            <li x-data="{ open: {{ request()->routeIs('reports.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="flex items-center w-full p-3 text-gray-900 rounded-lg hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 group">
                    <i class="fas fa-chart-bar w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white"></i>
                    <span class="flex-1 ml-3 text-left sidebar-text">Reports</span>
                    <i class="fas fa-chevron-down text-gray-500 dark:text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                </button>
                <ul x-show="open" x-collapse class="pl-4 mt-1 space-y-1">
                    <li>
                        <a href="{{ route('reports.products') }}" class="flex items-center p-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 {{ request()->routeIs('reports.products') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                            <i class="fas fa-cube w-4 h-4 mr-2"></i> Product Sales
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('reports.profit') }}" class="flex items-center p-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 {{ request()->routeIs('reports.profit') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                            <i class="fas fa-coins w-4 h-4 mr-2"></i> Profit & Loss
                        </a>
                    </li>
                </ul>
            </li>
            @endif

            <!-- Divider -->
            <li class="pt-4 mt-4 border-t border-gray-200 dark:border-gray-700"></li>

            <!-- Logout (everyone) -->
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center p-3 text-gray-900 rounded-lg hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 group">
                        <i class="fas fa-sign-out-alt w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white"></i>
                        <span class="ml-3 sidebar-text">Logout</span>
                    </button>
                </form>
            </li>
        </ul>
    </div>
</aside>

<style>
    /* Sidebar collapsed state styling */
    .sidebar-collapsed #sidebar {
        width: 5rem !important;
    }
    .sidebar-collapsed #sidebar .sidebar-text {
        display: none;
    }
    .sidebar-collapsed #sidebar .ml-auto {
        display: none;
    }
    .sidebar-collapsed #sidebar .fixed.bottom-0 {
        width: 5rem !important;
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
    .sidebar-collapsed #sidebar .fixed.bottom-0 .sidebar-text {
        display: none;
    }
    .sidebar-collapsed #sidebar .fa-chevron-down {
        display: none;
    }
</style>

<!-- Alpine.js for dropdowns -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<script>
    // Dark mode toggle (must be globally accessible)
    window.toggleDarkMode = function() {
        const html = document.documentElement;
        if (html.classList.contains('dark')) {
            html.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        } else {
            html.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        }
    };

    // Set initial theme
    document.addEventListener('DOMContentLoaded', function() {
        const theme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
        }
    });
</script>