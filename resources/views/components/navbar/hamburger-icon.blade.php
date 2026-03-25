<!-- Collapse button (big screen) -->
<button id="sidebar-toggle" aria-expanded="true" aria-controls="logo-sidebar" class="mr-3 hidden cursor-pointer rounded-sm p-2 text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white md:inline">
    <x-heroicon-c-bars-3-bottom-left />
</button>

<!-- Hide/show button (small screen) -->
<button data-drawer-target="drawer-navigation" data-drawer-toggle="drawer-navigation" aria-controls="drawer-navigation" class="mr-2 cursor-pointer rounded-lg p-2 text-gray-600 hover:bg-gray-100 hover:text-gray-900 focus:bg-gray-100 focus:ring-2 focus:ring-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700 dark:focus:ring-gray-700 md:hidden">
    <!-- svg open sidebar -->
    <x-heroicon-c-bars-3-center-left />

    <!-- svg close sidebar -->
    <x-heroicon-s-x-mark class="hidden" />

    <span class="sr-only">Toggle sidebar</span>
</button>
