<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
        <div class="sidebar-brand-icon">
            <i class="fa fa-dashboard"></i>
        </div>
        <div class="sidebar-brand-text mx-3">ADMIN PRO</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item active">
        <a class="nav-link" href="#">
            <i class="fa fa-fw fa-tachometer"></i>
            <span>DASHBOARD</span></a>
    </li>
    <!-- Nav Item - Dashboard -->
    @if (auth()->user()->power_admin == true)
        <li class="nav-item">
            <a class="nav-link" href="{{ route('dashboard.admin.index') }}">
                <i class="fa fa-fw fa-cog"></i>
                <span>ADMINS</span></a>
        </li>
    @endif

    <li class="nav-item">
        <a class="nav-link" href="{{ route('dashboard.language.index') }}">
            <i class="fa fa-fw fa-cog"></i>
            <span>LANGUAGES</span>
        </a>
    </li>
    @if (auth()->user()->role->slug == 'client' || auth()->user()->role->slug == 'admin')
        <li class="nav-item dropdown">

            <p id="dLabel" class="nav-link" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                <i class="fa fa-fw fa-cog"></i>
                <span>POST A JOB</span>
            </p>
            <div class="dropdown-menu ml-4" aria-labelledby="dLabel">
                @foreach (\App\Models\Role::where('seller', true)->get() as $r)
                    <a class="dropdown-item"
                        href="{{ route('contract.create', ['type' => $r->slug]) . '?tab=requirements' }}">For
                        {{ $r->title }}</a>
                @endforeach
            </div>

        </li>
    @endif


    <li class="nav-item">
        <a class="nav-link" href="{{ route('dashboard.type.index') }}">
            <i class="fa fa-fw fa-cog"></i>
            <span>SERVICE TYPES</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="{{ route('dashboard.education.index') }}">
            <i class="fa fa-fw fa-cog"></i>
            <span>EDUCATIONS</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="{{ route('dashboard.experience.index') }}">
            <i class="fa fa-fw fa-cog"></i>
            <span>EXPERIENCES</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="{{ route('dashboard.skill.index') }}">
            <i class="fa fa-fw fa-cog"></i>
            <span>SKILLS</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="{{ route('dashboard.interest.index') }}">
            <i class="fa fa-fw fa-cog"></i>
            <span>INTERESTS</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="{{ route('dashboard.post.index') }}">
            <i class="fa fa-fw fa-cog"></i>
            <span>POSTS</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('dashboard.faq.index') }}">
            <i class="fa fa-fw fa-cog"></i>
            <span>FAQS</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="{{ route('dashboard.user.index') }}">
            <i class="fa fa-fw fa-cog"></i>
            <span>USERS</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('dashboard.contract.index') }}">
            <i class="fa fa-fw fa-cog"></i>
            <span>JOBS</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="{{ route('dashboard.order.index') }}">
            <i class="fa fa-fw fa-cog"></i>
            <span>ORDERS</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- New Features Section -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('dashboard.timesheets.index') }}">
            <i class="fa fa-fw fa-clock-o"></i>
            <span>TIMESHEETS</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('dashboard.job-applications.index') }}">
            <i class="fa fa-fw fa-file-text"></i>
            <span>JOB APPLICATIONS</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('dashboard.timesheet-payments.index') }}">
            <i class="fa fa-fw fa-credit-card"></i>
            <span>TIMESHEET PAYMENTS</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>
