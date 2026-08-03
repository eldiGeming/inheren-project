<nav class="nxl-navigation">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="{{ url('/') }}" class="b-brand">
                <img src="{{ asset('assets/images/logo-full.png') }}" class="logo logo-lg">
                <img src="{{ asset('assets/images/logo-abbr.png') }}" class="logo logo-sm">
            </a>
        </div>

        <div class="navbar-content">
            <ul class="nxl-navbar">
                <li class="nxl-item">
                    <a href="{{ url('/company') }}" 
                    class="nxl-link {{ request()->is('company*') ? 'active' : '' }}">
                        <span class="nxl-micon">
                            <i class="bi bi-buildings-fill"></i>
                        </span>
                        <span class="nxl-mtext">Company</span>
                    </a>
                </li>

                <li class="nxl-item">
                    <a href="{{ url('/type-certificate') }}" 
                    class="nxl-link {{ request()->is('certificateType*') ? 'active' : '' }}">
                        <span class="nxl-micon">
                            <i class="bi bi-clipboard2-check-fill"></i>
                        </span>
                        <span class="nxl-mtext">Standard</span>
                    </a>
                </li>

                <li class="nxl-item">
                    <a href="{{ url('/certification') }}" 
                    class="nxl-link {{ request()->is('certification*') ? 'active' : '' }}">
                        <span class="nxl-micon">
                            <i class="bi bi-award-fill"></i>
                        </span>
                        <span class="nxl-mtext">Certification</span>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</nav>