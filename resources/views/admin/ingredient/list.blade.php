@extends('admin.layouts.app')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Ingredients</h1>
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
        <div class="card">
            <form action="" method="get">
                <div class="card-header">
                    <div class="card-title">
                        <button type="button" onclick="window.location.href='{{ route('ingredients.index') }}' " class="btn btn-default btn-sm">Reset</button>
                    </div>
                    <div class="card-tools">
                        <div class="input-group input-group" style="width: 250px;">
                            <input value="{{ Request::get('keyword') }}" type="text" name="keyword" class="form-control float-right" placeholder="Search">

                            <div class="input-group-append">
                              <button type="submit" class="btn btn-default">
                                <i class="fas fa-search"></i>
                              </button>
                            </div>
                          </div>
                    </div>
                </div>
            </form>

            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Thumbnail</th>
                            <th>Change Image</th>

                            <th width="100">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($ingredients->isNotEmpty())
                            @foreach ( $ingredients as $ingredient)
                            <tr>
                                <td>{{ $ingredient->name }}</td>
                                <td>{{ $ingredient->slug }}</td>
                                <td>
                                     <!-- Display image thumbnail if ingredient->image is not empty -->
                                    @if(!empty($ingredient->image))
                                        <img src="{{ asset('uploads/ingredient/thumb/' . $ingredient->image) }}" alt="Ingredient Image" class="img-thumbnail" style="width: 50px;">
                                        @else
                                        <img src="{{ asset('admin-assets/img/default-150x150.png') }}" alt="" class="img-thumbnail mr-2" width="50">
                                    @endif
                                </td>
                                <td>
                                    <input type="file" id="imageUpload_{{ $ingredient->id }}" accept="image/*" style="display: none;" onchange="changeImage('{{ $ingredient->id }}')">
                                    <button type="button" class="btn btn-info btn-sm" onclick="document.getElementById('imageUpload_{{ $ingredient->id }}').click()">Change Image</button>
                                </td>
                                <td>
                                    <a href="#" onClick="deleteCategory('{{ $ingredient->id }}')" class="text-danger w-4 h-4 mr-1">
                                        <svg wire:loading.remove.delay="" wire:target="" class="filament-link-icon w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path	ath fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
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
                                <p>Are you sure you want to delete this ingredient?</p>
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
                {{ $ingredients->links() }}
            </div>
        </div>
    </div>
    <!-- /.card -->
</section>
<!-- /.content -->

@endsection

@section('customJs')

<script>
    function deleteCategory(id) {
        var url = '{{ route('ingredients.destroy', "ID") }}';
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
                    window.location.href = "{{ route('ingredients.index') }}";
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            });
        });
    }

    function changeImage(id) {
        var input = document.getElementById('imageUpload_' + id);
        var file = input.files[0];

        if (file) {
            var formData = new FormData();
            formData.append('image', file);

            $.ajax({
                url: '{{ route('ingredients.update', ['id' => ':id']) }}'.replace(':id', id),
                type: 'post',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    window.location.href="{{ route('ingredients.index') }}";

                },
                error: function(error) {
                    // Handle error
                    console.error('Error updating image', error);
                },
            });
        }
    }

</script>

@endsection
