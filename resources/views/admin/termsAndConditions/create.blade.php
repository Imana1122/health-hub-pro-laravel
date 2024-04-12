@extends('admin.layouts.app')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Create Term and condition</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="termsAndConditions.index" class="btn btn-primary">Back</a>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
</section>
<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="container-fluid">
        <form action="" name="termsAndConditionForm" id="termsAndConditionForm">
            <div class="card">
                <div class="card-body">
                    <div class="row">

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="content">Content</label>
                                <input type="text" name="content" id="content" class="form-control" placeholder="Content">
                                 <p></p>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
            <div class="pb-5 pt-3">
                <button type="submit" class="btn btn-primary">Create</button>
                <a href="termsAndConditions.index" class="btn btn-outline-dark ml-3">Cancel</a>
            </div>
        </form>

    </div>
    <!-- /.card -->
</section>
<!-- /.content -->

@endsection

@section('customJs')
<script>


$("#termsAndConditionForm").submit(function(event){
    event.preventDefault();
    var element = $("#termsAndConditionForm");
    $("button[type=submit]").prop('disabled',true);

    $.ajax({
        url:'{{ route('termsAndConditions.store') }}',
        type:'post',
        data: element.serializeArray(),
        dataType: 'json',
        success: function(response){
            $("button[type=submit]").prop('disabled', false);

            if (response["status"] == true) {
                window.location.href = "{{ route('termsAndConditions.index') }}";
                resetFormErrors([ "content"]);
            } else {
                var errors = response['errors'];

                // Define an array of field names
                var fields = [ "content"];

                // Loop through the fields array
                fields.forEach(function (field) {
                    // Get the element and error message for the current field
                    var element = $("#" + field);
                    var errorMessage = errors[field];

                    // Add or remove the 'is-invalid' class based on the presence of errors
                    if (errorMessage) {
                        element.addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback')
                            .html(errorMessage);
                    } else {
                        element.removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback')
                            .html("");
                    }
                });
            }

            // Function to reset form errors
            function resetFormErrors(fields) {
                fields.forEach(function (field) {
                    $("#" + field).removeClass('is-invalid')
                        .siblings('p')
                        .removeClass('invalid-feedback')
                        .html("");
                });
            }

        },
        error: function(jqXHR, exception){
            console.log("Something went wrong!");
        }
    })
});

</script>
@endsection
