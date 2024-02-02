@extends('admin.layouts.app')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Update Contact</h1>
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
        <form action="" method="put" id="contactForm" name="contactForm">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                         <div class="col-md-4">
                            <div class="mb-3">
                                <input type="text" name="corporate_office" id="corporate_office" class="form-control" value="{{ $contact->corporate_office }}" placeholder="Corporate Office">
                                <p></p>
                            </div>
                        </div>
                         <div class="col-md-4">
                            <div class="mb-3">
                                <input type="text" name="email" id="email" class="form-control" value="{{ $contact->email }}" placeholder="Email">
                                <p></p>
                            </div>
                        </div>
                         <div class="col-md-4">
                            <div class="mb-3">
                                <input type="text" name="phone_number" id="phone_number" class="form-control" value="{{ $contact->phone_number }}" placeholder="Phone Number">
                                <p></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <input type="text" name="mobile_number" id="mobile_number" class="form-control" value="{{ $contact->mobile_number }}" placeholder="Mobile Number">
                                <p></p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="m-auto">
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-striped">
                            <tr>
                                <th>Corporate Office</th>
                                <th>Email</th>
                                <th>Phone Number</th>
                                <th>Mobile Number</th>
                            </tr>

                            @if ($contact != null)
                            <tr>
                                <td>
                                    {{ $contact->corporate_office}}
                                </td>
                                <td>{{ $contact->email }}</td>
                                <td>{{ $contact->phone_number }}</td>
                                <td>{{ $contact->mobile_number }}</td>
                                
                            </tr>

                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Add this modal for confirming deletion  -->
        <div class="modal fade" id="updateConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="updateConfirmationModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateConfirmationModalLabel">Confirm Update</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to update?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmUpdateBtn">Update</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.card -->
</section>
<!-- /.content -->

@endsection

@section('customJs')
<script>
$("#contactForm").submit(function(event) {
    event.preventDefault();
    var element = $(this);
    $("button[type=submit]").prop('disabled', true);

    // Show the update confirmation modal
    $('#updateConfirmationModal').modal('show');

    // Handle the click on the "Update" button in the modal
    $('#confirmUpdateBtn').click(function() {
        // Close the modal
        $('#updateConfirmationModal').modal('hide');

        // Proceed with the AJAX request
        $.ajax({
            url: '{{ route('contact.update') }}',
            type: 'put',
            data: element.serializeArray(),
            dataType: 'json',
            success: function(response) {
                $("button[type=submit]").prop('disabled', false);

                if (response.status == true) {

                    // Redirect to the specified route on success
                    window.location.href = "{{ route('contact.index') }}";

                    // Clear any validation messages
                    $("#corporate_office").removeClass('is-invalid')
                        .siblings('p')
                        .removeClass('invalid-feedback')
                        .html("");

                    $("#email").removeClass('is-invalid')
                        .siblings('p')
                        .removeClass('invalid-feedback')
                        .html("");
                    $("#phone_number").removeClass('is-invalid')
                        .siblings('p')
                        .removeClass('invalid-feedback')
                        .html("");
                    $("#mobile_number").removeClass('is-invalid')
                        .siblings('p')
                        .removeClass('invalid-feedback')
                        .html("");
                } else {

                    // Handle validation errors
                    var errors = response.errors;
                    
                    if (errors['corporate_office']) {
                        $("#corporate_office").addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback')
                            .html(errors['corporate_office']);
                    } else {
                        $("#corporate_office").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback')
                            .html("");
                    }
                    
                    if (errors['email']) {
                        $("#email").addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback')
                            .html(errors['email']);
                    } else {
                        $("#email").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback')
                            .html("");
                    }
                    
                    if (errors['phone_number']) {
                        $("#phone_number").addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback')
                            .html(errors['phone_number']);
                    } else {
                        $("#phone_number").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback')
                            .html("");
                    }
                    
                    if (errors['mobile_number']) {
                        $("#mobile_number").addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback')
                            .html(errors['mobile_number']);
                    } else {
                        $("#mobile_number").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback')
                            .html("");
                    }

                    

                }
            },
            error: function(jqXHR, exception) {
                console.log("Something went wrong!");
            }
        });
    });
});
</script>
@endsection
