<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo ($_SERVER['REQUEST_URI'] === '/' || $_SERVER['REQUEST_URI'] === '') ? 'active' : ''; ?>" href="/">
                    <span data-feather="home"></span>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/tasks') === 0 && $_SERVER['REQUEST_URI'] !== '/tasks/create') ? 'active' : ''; ?>" href="/tasks">
                    <span data-feather="list"></span>
                    Tasks
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($_SERVER['REQUEST_URI'] === '/tasks/create') ? 'active' : ''; ?>" href="/tasks/create">
                    <span data-feather="plus-circle"></span>
                    Add Task
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/categories') === 0) ? 'active' : ''; ?>" href="/categories">
                    <span data-feather="tag"></span>
                    Categories
                </a>
            </li>
        </ul>

        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>User</span>
        </h6>
        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link <?php echo ($_SERVER['REQUEST_URI'] === '/profile') ? 'active' : ''; ?>" href="/profile">
                    <span data-feather="user"></span>
                    Profile
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($_SERVER['REQUEST_URI'] === '/change-password') ? 'active' : ''; ?>" href="/change-password">
                    <span data-feather="lock"></span>
                    Change Password
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/logout">
                    <span data-feather="log-out"></span>
                    Logout
                </a>
            </li>
        </ul>
    </div>
</nav>
