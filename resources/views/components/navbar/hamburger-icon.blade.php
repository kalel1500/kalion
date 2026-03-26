@php($colorClases = 'text-body hover:bg-neutral-tertiary-medium hover:text-heading')

<!-- Collapse button (big screen) -->
<button id="sidebar-toggle" aria-expanded="true" aria-controls="logo-sidebar" class="hidden md:inline mr-3 cursor-pointer rounded-sm p-2 {{ $colorClases }}">
    <span class="sr-only">Toggle sidebar</span>
    <x-heroicon-c-bars-3-bottom-left />
</button>

<!-- Hide/show button (small screen) -->
<button data-drawer-target="drawer-navigation" data-drawer-toggle="drawer-navigation" aria-controls="drawer-navigation" class="md:hidden mr-2 cursor-pointer rounded-sm p-2 {{ $colorClases }}">
    <span class="sr-only">Open sidebar</span>

    <!-- svg open sidebar -->
    <x-heroicon-c-bars-3-center-left />
    <!-- svg close sidebar -->
    <x-heroicon-s-x-mark class="hidden" />
</button>
