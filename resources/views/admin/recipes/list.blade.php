@extends('admin.layouts.app')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Recipes</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('recipes.create') }}" class="btn btn-primary">New Recipe</a>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="container-fluid">
        @include('admin.message')
        <div class="card bg-light">
            <form action="{{ route('recipes.index') }}" method="get">
                <div class="card-header">
                    <div class="card-title">
                        <button type="button" onclick="window.location.href='{{ route('recipes.index') }}' " class="btn btn-default btn-sm">Reset</button>
                    </div>
                    <div class="card-tools">
                        <div class="input-group input-group" style="width: 250px;">
                            <input value="{{ Request::get('keyword') }}" type="text" name="keyword" class="form-control float-right" placeholder="Search">

                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Apply Filters
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="meal_type">Meal Type</label>
                                <select class="form-control" name="meal_type">
                                    <option value="" class="font-italic">All</option>
                                    @foreach($mealTypes as $mealType)
                                        <option value="{{ $mealType->id }}" {{ Request::get('meal_type') == $mealType->id ? 'selected' : '' }}>{{ $mealType->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="category">Category</label>
                                <select class="form-control" name="category">
                                    <option value="" class="font-italic">All</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ Request::get('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="cuisine">Cuisine:</label>
                                <select class="form-control" name="cuisine">
                                    <option value="" class="font-italic">All</option>
                                    @foreach($cuisines as $cuisine)
                                        <option value="{{ $cuisine->id }}" {{ Request::get('cuisine') == $cuisine->id ? 'selected' : '' }}>{{ $cuisine->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="allergen">Allergen:</label>
                                <select class="form-control" name="allergen">
                                    <option value="" class="font-italic">All</option>
                                    @foreach($allergens as $allergen)
                                        <option value="{{ $allergen->id }}" {{ Request::get('allergen') == $allergen->id ? 'selected' : '' }}>{{ $allergen->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="health_condition" class="font-weight-bold">Health Condition:</label>
                                <select class="form-control" name="health_condition">
                                    <option value="" selected class="font-italic">All</option>
                                    @foreach($healthConditions as $healthCondition)
                                        <option value="{{ $healthCondition->id }}" {{ Request::get('health_condition') == $healthCondition->id ? 'selected' : '' }}>{{ $healthCondition->name }}</option>
                                    @endforeach
                                </select>
                            </div>


                        </div>
                    </div>
                </div>
            </form>
        </div>


        <div class="card">
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Image</th>

                            <th>Cuisine</th>
                            <th>Meal Type</th>
                            <th>Calories</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (!empty($recipes))
                            @foreach ($recipes as $recipe)
                                <tr>
                                    <td><a href="#">{{ $recipe->title }}</a></td>
                                    <td>
                                        <div class="d-flex">
                                            @if (!empty($recipe->images) && count($recipe->images) > 0)
                                                    <img src="{{ asset('uploads/recipes/small/' . $recipe->images->last()->image) }}" class="img-thumbnail mr-2" width="50">
                                            @else
                                                <img src="{{ asset('admin-assets/img/default-150x150.png') }}" alt="" class="img-thumbnail mr-2" width="50">
                                            @endif

                                        </div>
                                    </td>
                                    <td>{{ $recipe->cuisine->name }}</td>
                                    <td>{{ $recipe->meal_type->name }}</td>
                                    <td>{{ $recipe->calories }}</td>

                                    <td>
                                        @if ($recipe->status == 1)
                                            <svg class="text-success-500 h-6 w-6 text-success" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        @else
                                            <svg class="text-danger h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('recipes.edit', $recipe->id) }}">
                                            <svg class="filament-link-icon w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                                            </svg>
                                        </a>
                                        <a href="#" onClick="deleteRecipe('{{ $recipe->id }}')" class="text-danger w-4 h-4 mr-1">
                                            <svg wire:loading.remove.delay="" wire:target="" class="filament-link-icon w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5">Records not found</td>
                            </tr>
                        @endif
                    </tbody>
                </table>

                <!-- Add this modal for confirming deletion  -->
                <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteConfirmationModalLabel">Confirm Deletion</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p>Are you sure you want to delete this recipe?</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer clearfix">
                {{ $recipes->links() }}
            </div>
        </div>
    </div>
    <!-- /.card -->
</section>
<!-- /.content -->

@endsection

@section('customJs')

<script>
    function deleteRecipe(id) {
        var url = '{{ route('recipes.destroy', "ID") }}';
        var newUrl = url.replace("ID", id);

        // Show the delete confirmation modal
        $('#deleteConfirmationModal').modal('show');

        // Handle the click on the "Delete" button in the modal
        $('#confirmDeleteBtn').click(function() {
            // Close the modal
            $('#deleteConfirmationModal').modal('hide');

            // Perform the delete action
            $.ajax({
                url: newUrl,
                type: 'delete',
                data: {},
                dataType: 'json',
                success: function(response) {
                    $("button[type=submit]").prop('disabled', false);
                    window.location.href = "{{ route('recipes.index') }}";
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            });
        });
    }
</script>

@endsection
