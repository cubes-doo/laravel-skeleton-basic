<div class="sidebar" data-color="purple" data-background-color="black">
    <!--
    Tip 1: You can change the color of the sidebar using: data-color="purple | azure | green | orange | danger"

    Tip 2: you can also add an image using data-image tag
    -->
    <div class="logo">
        <a href="/" class="simple-text logo-mini">
            CT
        </a>
        <a href="/" class="simple-text logo-normal">
            Cubes
        </a>
    </div>
    <div class="sidebar-wrapper">
        <ul class="nav">
            <li class="nav-item @active('dashboard', 'active')">
                <a class="nav-link" href="@route('dashboard')">
                    <i class="material-icons">dashboard</i>
                    <p>Dashboard</p>
                </a>
            </li>
            <li class="nav-item @active('entities', 'active') @active('entities/*', 'active')">
                <a class="nav-link" href="@route('entities.list')">
                    <i class="material-icons">waves</i>
                    <p>Entities</p>
                </a>
            </li>
            <!-- your sidebar here -->
        </ul>
    </div>
</div>

