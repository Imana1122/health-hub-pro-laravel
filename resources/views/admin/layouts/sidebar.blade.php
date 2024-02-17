<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('admin.dashboard') }}" class="brand-link">
        <img src="{{asset('/admin-assets/img/logo.gif') }}" style="width: 14rem; opacity: .8"/>
    </a>
    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user (optional) -->
        <nav class="mt-2">
            <div class="accordion" id="accordionExample">

               <!-- Dashboard Section -->
               <div class="card bg-dark">
                <div class="card-header" id="headingDashboard">
                    <h2 class="mb-0">
                        <button class="btn btn-link {{ Route::is('admin.dashboard') ? '' : 'collapsed' }}" type="button" data-toggle="collapse" data-target="#collapseDashboard" aria-expanded="{{ Route::is('admin.dashboard') ? 'true' : 'false' }}" aria-controls="collapseDashboard">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            Dashboard
                        </button>
                    </h2>
                </div>

                <div id="collapseDashboard" class="collapse {{ Route::is('admin.dashboard') ? 'show' : '' }}" aria-labelledby="headingDashboard" data-parent="#accordionExample">
                    <div class="card-body">
                        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                            <li class="nav-item">
                                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ Route::is('admin.dashboard') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-tachometer-alt"></i>
                                    <p>Dashboard</p>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

                <!-- Recipe Section -->
                <div class="card bg-dark">
                    <div class="card-header" id="headingRecipe">
                        <h2 class="mb-0">
                            <button class="btn btn-link {{ Route::is('recipes.*') || Route::is('recipeCategories.*') || Route::is('cuisines.*') || Route::is('mealTypes.*') || Route::is('allergens.*') || Route::is('healthConditions.*') ? '' : 'collapsed' }}" type="button" data-toggle="collapse" data-target="#collapseRecipe" aria-expanded="{{ Route::is('recipes.*') || Route::is('recipeCategories.*') || Route::is('cuisines.*') || Route::is('mealTypes.*') || Route::is('allergens.*') || Route::is('healthConditions.*') ? 'true' : 'false' }}" aria-controls="collapseRecipe">
                                <i class="nav-icon fas fa-utensils"></i>
                                Recipe
                            </button>
                        </h2>
                    </div>

                    <div id="collapseRecipe" class="collapse {{ Route::is('recipes.*') || Route::is('recipeCategories.*') || Route::is('ingredients.*') || Route::is('cuisines.*') || Route::is('mealTypes.*') || Route::is('allergens.*') || Route::is('healthConditions.*') ? 'show' : '' }}" aria-labelledby="headingRecipe" data-parent="#accordionExample">
                        <div class="card-body">
                            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                                <li class="nav-item">
                                    <a href="{{ route('recipes.index') }}" class="nav-link {{ Route::is('recipes.*') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-list-alt"></i>
                                        <p>Recipes</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('recipeCategories.index') }}" class="nav-link {{ Route::is('recipeCategories.*') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-folder"></i>
                                        <p>Recipe Category</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('ingredients.index') }}" class="nav-link {{ Route::is('ingredients.*') ? 'active' : '' }}">
                                        <i class=" nav-icon fas fa-egg"></i>
                                        <p>Ingredients</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('mealTypes.index') }}" class="nav-link {{ Route::is('mealTypes.*') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-clock"></i> <!-- Meal Types -->
                                        <p>Meal Types</p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="{{ route('cuisines.index') }}" class="nav-link {{ Route::is('cuisines.*') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-globe"></i> <!-- Cuisines -->
                                        <p>Cuisines</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('allergens.index') }}" class="nav-link {{ Route::is('allergen.*') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-allergies"></i> <!-- Allergens -->
                                        <p>Allergens</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('healthConditions.index') }}" class="nav-link {{ Route::is('healthConditions.*') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-heartbeat"></i> <!-- Health Conditions -->
                                        <p>Health Conditions</p>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Exercises Section -->
                <div class="card bg-dark">
                    <div class="card-header" id="headingExercises">
                        <h2 class="mb-0">
                            <button class="btn btn-link {{ Route::is('exercises.*') || Route::is('workouts.*') ? '' : 'collapsed' }}" type="button" data-toggle="collapse" data-target="#collapseExercises" aria-expanded="{{ Route::is('exercises.*') || Route::is('workouts.*') ? 'true' : 'false' }}" aria-controls="collapseExercises">
                                <i class="nav-icon fas fa-dumbbell"></i>
                                Exercises
                            </button>
                        </h2>
                    </div>

                    <div id="collapseExercises" class="collapse {{ Route::is('exercises.*') || Route::is('workouts.*') ? 'show' : '' }}" aria-labelledby="headingExercises" data-parent="#accordionExample">
                        <div class="card-body">
                            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                                <li class="nav-item">
                                    <a href="{{ route('exercises.index') }}" class="nav-link {{ Route::is('exercises.*') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-walking"></i> <!-- Exercises -->
                                        <p>Exercises</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('workouts.index') }}" class="nav-link {{ Route::is('workouts.*') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-dumbbell"></i> <!-- Workouts -->
                                        <p>Workouts</p>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Configure Section -->
                <div class="card bg-dark">
                    <div class="card-header" id="headingConfigure">
                        <h2 class="mb-0">
                            <button class="btn btn-link {{ Route::is('weightPlans.*') || Route::is('users.*') || Route::is('dieticians.*') || Route::is('contact.*') ? '' : 'collapsed' }}" type="button" data-toggle="collapse" data-target="#collapseConfigure" aria-expanded="{{ Route::is('weightPlans.*') || Route::is('dieticians.*') || Route::is('users.*') || Route::is('contact.*') ? 'true' : 'false' }}" aria-controls="collapseConfigure">
                                <i class="nav-icon fas fa-cogs"></i>
                                Configure
                            </button>
                        </h2>
                    </div>

                    <div id="collapseConfigure" class="collapse {{ Route::is('weightPlans.*') || Route::is('users.*') || Route::is('dieticians.*') || Route::is('contact.*') ? 'show' : '' }}" aria-labelledby="headingConfigure" data-parent="#accordionExample">
                        <div class="card-body">
                            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                                <li class="nav-item">
                                    <a href="{{ route('weightPlans.index') }}" class="nav-link {{ Route::is('weightPlans.*') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-weight"></i> <!-- Weight Plans -->
                                        <p>Weight Plans</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('users.index') }}" class="nav-link {{ Route::is('users.*') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-users"></i> <!-- Users -->
                                        <p>Users</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('dieticians.index') }}" class="nav-link {{ Route::is('dieticians.*') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-users"></i> <!-- Users -->
                                        <p>Dieticians</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('contact.index') }}" class="nav-link {{ Route::is('contact.*') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-address-card"></i> <!-- Contact -->
                                        <p>Contact</p>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>



                <!-- Setting Section -->
                <div class="card bg-dark">
                    <div class="card-header" id="headingSetting">
                        <h2 class="mb-0">
                            <button class="btn btn-link {{ Route::is('admins.*') ? '' : 'collapsed' }}" type="button" data-toggle="collapse" data-target="#collapseSetting" aria-expanded="{{ Route::is('admins.*') ? 'true' : 'false' }}" aria-controls="collapseSetting">
                                <i class="nav-icon fas fa-cogs"></i>
                                Setting
                            </button>
                        </h2>
                    </div>

                    <div id="collapseSetting" class="collapse {{ Route::is('admins.*') ? 'show' : '' }}" aria-labelledby="headingSetting" data-parent="#accordionExample">
                        <div class="card-body">
                            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                                <li class="nav-item">
                                    <a href="{{ route('admins.index') }}" class="nav-link {{ Route::is('admins.*') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-users-cog"></i> <!-- Admins -->
                                        <p>Admins</p>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
