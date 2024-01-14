@extends('admin.layouts.app')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="{{ route('admin.profile') }}">My Profile</a></li>
                    <li class="breadcrumb-item">Settings</li>
                </ol>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">Back</a>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
</section>

<section class=" section-11 ">
    <div class="container  mt-5">
        <div class="row">
            <div class="col-md-12">
                @include('admin.message')
            </div>
            <div class="col-md-3">
                @include('admin.account.sidebar')
            </div>
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">
                        <h2 class="h5 mb-0 pt-2 pb-2">Personal Information</h2>
                    </div>
                    <form action="" name="profileForm" id="profileForm">
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="col-sm-6 mb-3">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" id="name" placeholder="Enter Your Name" class="form-control" value="{{ $user->name }}">
                                    <p></p>
                                </div>
                                <div class="col-sm-6 mb-3">
                                    <label for="phone_number">Phone</label>
                                    <input type="number" name="phone_number" id="phone_number" placeholder="Enter Your Phone" class="form-control" value="{{ $user->phone_number }}">
                                    <p></p>
                                </div>
                                <div class="d-flex">
                                    <button class="btn btn-dark">Update</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h2 class="h5 mb-0 pt-2 pb-2">Delete Account</h2>
                    </div>
                    <div class="d-flex  p-3">
                    <button type="button" class="btn btn-danger" onclick="confirmDeleteAccount()">Delete Account</button>
                    </div>
                </div>
                <!-- Add this modal for confirming update  -->
                <div class="modal fade" id="updateConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="updateConfirmationModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="updateConfirmationModalLabel">Confirm Profile Update</h5>
                                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p>Are you sure you want to update your profile?</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-danger" id="confirmUpdateBtn">Update</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add this modal for confirming deleteAccount -->
                <div class="modal fade" id="deleteAccountConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="deleteAccountConfirmationModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteAccountConfirmationModalLabel">Confirm Profile Deletion</h5>
                                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p>Are you sure you want to delete your account?</p>
                                <form id="deleteAccountForm" action="{{ route('account.deleteAccount') }}" method="delete">
                                    @csrf
                                    <div class="form-group">
                                        <label for="password">Password</label>
                                        <input type="password" class="form-control" name="password" id="password" required>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-danger" id="confirmDeleteAccountBtn">Delete Account</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('customJs')

<script>
    $("#profileForm").submit(function(event){
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
                url: '{{ route('admin.updateProfile') }}',
                type: 'post',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response){
                    $("button[type=submit]").prop('disabled', false);

                    $(".error").removeClass('invalid-feedback');
                    $('input[type="text"], input[type="number"]').removeClass('is-invalid');
                    if (response.status == true) {
                        window.location.href = "{{ route('admin.profile') }}";

                    } else {
                        console.log('error');
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


    
    $(document).ready(function(){
        $("#confirmDeleteAccountBtn").click(function(){
            // Submit the delete account form
            $("#deleteAccountForm").submit();
        });
    });
    
    function confirmDeleteAccount() {
        // Show the delete account confirmation modal
        $('#deleteAccountConfirmationModal').modal('show');
    }
    
</script>

@endsection


