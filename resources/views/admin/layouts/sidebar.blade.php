<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('admin.dashboard') }}" class="logo" style="display: flex; justify-content: center; align-items: center; height: 10rem;">
        <img src="{{ asset('/admin-assets/img/logo.gif') }}" style="height: 100%; opacity: 0.8;" />
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user (optional) -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
                    with font-awesome or any other icon font library -->
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('mealTypes.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-file-alt"></i>
                        <p>Meal Types</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('cuisines.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-file-alt"></i>
                        <p>Cuisines</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('recipes.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-tag"></i>
                        <p>Recipes</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('users.index') }}" class="nav-link">
                        <i class="nav-icon  fas fa-users"></i>
                        <p>Users</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admins.index') }}" class="nav-link">
                        <i class="nav-icon  fas fa-users"></i>
                        <p>Admins</p>
                    </a>
                </li>
                {{-- <li class="nav-item">
                    <a href="{{ route('products.productRatings') }}" class="nav-link">
                        <i class="nav-icon  fa fa-star"></i>
                        <p>Ratings</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('shipping.create') }}" class="nav-link">
                        <!-- <i class="nav-icon fas fa-tag"></i> -->
                        <i class="fas fa-truck nav-icon"></i>
                        <p>Shipping</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('orders.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-shopping-bag"></i>
                        <p>Orders</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('coupons.index') }}" class="nav-link">
                        <i class="nav-icon  fa fa-percent" aria-hidden="true"></i>
                        <p>Discount</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('users.index') }}" class="nav-link">
                        <i class="nav-icon  fas fa-users"></i>
                        <p>Users</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('pages.index') }}" class="nav-link">
                        <i class="nav-icon  far fa-file-alt"></i>
                        <p>Pages</p>
                    </a>
                </li> --}}
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
 </aside>
