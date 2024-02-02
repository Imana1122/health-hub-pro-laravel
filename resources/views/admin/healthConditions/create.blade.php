@extends('admin.layouts.app')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Create HealthCondition</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="healthConditions.index" class="btn btn-primary">Back</a>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
</section>
<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="container-fluid">
        <form action="" name="healthConditionForm" id="healthConditionForm">
            <div class="card">
                <div class="card-body">
                    <div class="row">

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name">Name</label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Name">
                                 <p></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="slug">Slug</label>
                                <input type="text" readonly name="slug" id="slug" class="form-control" placeholder="Slug">
                                <p></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="1">Active</option>
                                    <option value="0">Block</option>
                                </select>
                                <p></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pb-5 pt-3">
                <button type="submit" class="btn btn-primary">Create</button>
                <a href="healthConditions.index" class="btn btn-outline-dark ml-3">Cancel</a>
            </div>
        </form>

    </div>
    <!-- /.card -->
</section>
<!-- /.content -->

@endsection

@section('customJs')
<script>

$("#name").change(function(){
    $("#slug").removeClass('is-invalid')
        .siblings('p')
        .removeClass('invalid-feedback')
        .html("");
    element = $(this);
    $("button[type=submit]").prop('disabled',true);
    $.ajax({
        url:'{{ route("getSlug") }}',
        type:'get',
        data: {title: element.val()},
        dataType: 'json',
        success: function(response){
            $("button[type=submit]").prop('disabled',false);
            if(response["status"] == true){
                $("#slug").val(response["slug"]);
            }
        }

    });
});

$("#healthConditionForm").submit(function(event){
    event.preventDefault();
    var element = $("#healthConditionForm");
    $("button[type=submit]").prop('disabled',true);

    $.ajax({
        url:'{{ route('healthConditions.store') }}',
        type:'post',
        data: element.serializeArray(),
        dataType: 'json',
        success: function(response){
            $("button[type=submit]").prop('disabled', false);

            if (response["status"] == true) {
                window.location.href = "{{ route('healthConditions.index') }}";
                resetFormErrors(["healthCondition_id", "name", "slug", "status"]);
            } else {
                var errors = response['errors'];

                // Define an array of field names
                var fields = ["healthCondition_id", "name", "slug", "status"];

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
