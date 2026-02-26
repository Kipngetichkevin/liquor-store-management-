<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Liquor Management System')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>.main-content { transition: margin-left 0.3s ease-in-out; }</style>
    @stack('styles')
</head>
<body class="bg-gray-50 dark:bg-gray-900 font-sans antialiased">
    <div class="flex h-screen">
        @include('layouts.sidebar')
        <div id="mainContent" class="flex-1 flex flex-col overflow-hidden transition-all duration-300">
            @include('layouts.header')
            <main class="flex-1 overflow-y-auto pt-16">
                <div class="container mx-auto px-4 py-6">
                    @if(isset($breadcrumbs))
                    <nav class="flex mb-6" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-3">
                            @foreach($breadcrumbs as $index => $breadcrumb)
                                <li class="inline-flex items-center">
                                    @if($index > 0)<svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>@endif
                                    @if(isset($breadcrumb['url']) && !$loop->last)<a href="{{ $breadcrumb['url'] }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">{{ $breadcrumb['title'] }}</a>
                                    @else<span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $breadcrumb['title'] }}</span>@endif
                                </li>
                            @endforeach
                        </ol>
                    </nav>
                    @endif
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">@yield('page-title', 'Dashboard')</h1>
                    @hasSection('page-subtitle')<p class="text-gray-600 dark:text-gray-300 mb-6">@yield('page-subtitle')</p>@endif
                    @if(session('success'))<div class="mb-6 p-4 bg-green-100 border border-green-300 text-green-700 rounded-lg dark:bg-green-900/30 dark:border-green-800 dark:text-green-400"><div class="flex items-center"><i class="fas fa-check-circle mr-3"></i><span>{{ session('success') }}</span></div></div>@endif
                    @if(session('error'))<div class="mb-6 p-4 bg-red-100 border border-red-300 text-red-700 rounded-lg dark:bg-red-900/30 dark:border-red-800 dark:text-red-400"><div class="flex items-center"><i class="fas fa-exclamation-circle mr-3"></i><span>{{ session('error') }}</span></div></div>@endif
                    @yield('content')
                </div>
            </main>
            <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 py-4">
                <div class="container mx-auto px-4">
                    <div class="flex flex-col md:flex-row justify-between items-center">
                        <div class="text-sm text-gray-500 dark:text-gray-400">© {{ date('Y') }} Liquor Management System</div>
                        <div class="mt-2 md:mt-0 text-sm text-gray-500 dark:text-gray-400">Version 1.0.0</div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script>
        function adjustMainContent() {
            const sidebar = document.getElementById('sidebar');
            const main = document.getElementById('mainContent');
            if (window.innerWidth >= 768) {
                main.style.marginLeft = sidebar.classList.contains('-translate-x-full') ? '0' : '16rem';
            } else { main.style.marginLeft = '0'; }
        }
        window.toggleSidebar = function() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('-translate-x-full');
            adjustMainContent();
            if (window.innerWidth >= 768) localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('-translate-x-full') ? 'true' : 'false');
        };
        window.toggleDarkMode = function() {
            const html = document.documentElement;
            html.classList.toggle('dark');
            localStorage.setItem('theme', html.classList.contains('dark') ? 'dark' : 'light');
        };
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            if (localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) document.documentElement.classList.add('dark');
            if (window.innerWidth >= 768) {
                const collapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                if (collapsed) sidebar.classList.add('-translate-x-full');
                else sidebar.classList.remove('-translate-x-full');
            } else sidebar.classList.add('-translate-x-full');
            adjustMainContent();
        });
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            if (window.innerWidth < 768) sidebar.classList.add('-translate-x-full');
            else {
                const collapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                collapsed ? sidebar.classList.add('-translate-x-full') : sidebar.classList.remove('-translate-x-full');
            }
            adjustMainContent();
        });
        window.confirmDelete = function(event, msg = 'Are you sure?') { event.preventDefault(); if (confirm(msg)) event.target.closest('form').submit(); };
    </script>
    @stack('scripts')
</body>
</html>
