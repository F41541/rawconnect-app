<!-- Header -->
<div class="app-header d-flex align-items-center justify-content-center px-3">
    <button
        class="menu-btn btn d-md-none position-absolute start-0"
        onclick="toggleSidebar()"> <i class="bi bi-list"></i>
    </button>
    <span class="fw-bold" style="font-size: 1.5rem;">
        {{ $slot }}
    </span>
</div>