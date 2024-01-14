@extends('admin.layouts.app')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="{{ route('admin.profile') }}">My Profile</a></li>
                    <li class="breadcrumb-item">Change Password</li>
                </ol>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">Back</a>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
</section>


<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="container-fluid row">
        <div class="col-md-12">
            @include('admin.message')
        </div>
        <div class="col-md-3">
            @include('admin.account.sidebar')
        </div>
        <div class="col-md-9">
            <form action="" method="post" id="changePasswordForm" name="changePasswordForm">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="old_password">Old Password</label>
                                    <input type="password" name="old_password" id="old_password" class="form-control" placeholder="Old Password">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password">New Password</label>
                                    <input type="password" name="password" id="password" class="form-control" placeholder="New Password">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password_confirmation">New Password Confirmation</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="New Password Confirmation">
                                    <p></p>
                                </div>
                            </div>
    
                        </div>
                    </div>
                </div>
                <div class="pb-5 pt-3">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route("admin.dashboard") }}" class="btn btn-outline-dark ml-3">Cancel</a>
                </div>
            </form>
        </div>
    </div>
    <!-- /.card -->
    <!-- Add this modal for confirming update  -->
    <div class="modal fade" id="updateConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="updateConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateConfirmationModalLabel">Confirm Password Change</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to change your password?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmUpdateBtn">Update</button>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- /.content -->

@endsection

@section('customJs')
<script>
     $("#changePasswordForm").submit(function(event){
        event.preventDefault();
        var element = $(this);
        $("button[type=submit]").prop('disabled', true);

        // Show the delete confirmation modal
        $('#updateConfirmationModal').modal('show');

        // Handle the click on the "Delete" button in the modal
        $('#confirmUpdateBtn').click(function() {
            // Close the modal
            $('#updateConfirmationModal').modal('hide');

            $.ajax({
                url: '{{ route('admin.changePassword') }}',
                type: 'post',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response){
                    $("button[type=submit]").prop('disabled', false);

                    $(".error").removeClass('invalid-feedback');
                    $('input[type="password"]').removeClass('is-invalid');

                    if (response.status == true) {
                        window.location.href = "{{ route('admin.showChangePasswordForm') }}";

                    } else {
                        var errors = response.errors;
                        $.each(errors, function(key, value){
                            $(`#${key}`).addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback')
                            .html(value);
                        })
                    }
                }
            })
        });
    })

</script>
@endsection
