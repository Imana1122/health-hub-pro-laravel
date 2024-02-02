@extends('admin.layouts.app')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Create Exercise</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('exercises.index') }}" class="btn btn-primary">Back</a>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
</section>
<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="container-fluid">
        <form action="" method="post" id="exerciseForm" name="exerciseForm">
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
                                <input type="text" name="slug" readOnly id="slug" class="form-control" placeholder="Slug">
                                <p></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="metabolic_equivalent">Metabolic Equivalent</label>
                                <input type="text" name="metabolic_equivalent" id="metabolic_equivalent" class="form-control" placeholder="Metabolic Equivalent">
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
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <input type="hidden" id="image_id" name="image_id" value="">
                                <label for="image">Image</label>
                                <div id="image" class="dropzone dz-clickable">
                                    <div class="dz-message needsclick">
                                        <br>Drop files here or click to upload. <br><br>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="description">Description</label>
                                <textarea name="description" id="description" class="form-control" rows="5" placeholder="Description"></textarea>
                                <p></p>
                            </div>
                        </div>



                    </div>
                </div>
            </div>
            <div class="pb-5 pt-3">
                <button type="submit" class="btn btn-primary">Create</button>
                <a href="{{ route("exercises.index") }}" class="btn btn-outline-dark ml-3">Cancel</a>
            </div>
        </form>
    </div>
    <!-- /.card -->
</section>
<!-- /.content -->

@endsection

@section('customJs')
<script>
$("#exerciseForm").submit(function(event){
    event.preventDefault();
    var element = $(this);
    $("button[type=submit]").prop('disabled',true);

    $.ajax({
        url:'{{ route('exercises.store') }}',
        type:'post',
        data: element.serializeArray(),
        dataType: 'json',
        success: function(response){
            $("button[type=submit]").prop('disabled',false);

            if(response["status"] == true) {

                window.location.href="{{ route('exercises.index') }}";

                $("#name").removeClass('is-invalid')
                .siblings('p')
                .removeClass('invalid-feedback')
                .html("");

                $("#slug").removeClass('is-invalid')
                .siblings('p')
                .removeClass('invalid-feedback')
                .html("");
                $("#metabolic_equivalent").removeClass('is-invalid')
                .siblings('p')
                .removeClass('invalid-feedback')
                .html("");
                $("#description").removeClass('is-invalid')
                .siblings('p')
                .removeClass('invalid-feedback')
                .html("");
            }else{

                var errors=response['errors'];
                if(errors['name']){
                    $("#name").addClass('is-invalid')
                    .siblings('p')
                    .addClass('invalid-feedback')
                    .html(errors['name']);
                }else{
                    $("#name").removeClass('is-invalid')
                    .siblings('p')
                    .removeClass('invalid-feedback')
                    .html("");
                }


                if(errors['slug']){
                    $("#slug").addClass('is-invalid')
                    .siblings('p')
                    .addClass('invalid-feedback')
                    .html(errors['slug']);
                }else{
                    $("#slug").removeClass('is-invalid')
                    .siblings('p')
                    .removeClass('invalid-feedback')
                    .html("");
                }

                if(errors['metabolic_equivalent']){
                    $("#metabolic_equivalent").addClass('is-invalid')
                    .siblings('p')
                    .addClass('invalid-feedback')
                    .html(errors['metabolic_equivalent']);
                }else{
                    $("#metabolic_equivalent").removeClass('is-invalid')
                    .siblings('p')
                    .removeClass('invalid-feedback')
                    .html("");
                }

                if(errors['description']){
                    $("#description").addClass('is-invalid')
                    .siblings('p')
                    .addClass('invalid-feedback')
                    .html(errors['description']);
                }else{
                    $("#description").removeClass('is-invalid')
                    .siblings('p')
                    .removeClass('invalid-feedback')
                    .html("");
                }
            }
        },
        error: function(jqXHR, exception){
            console.log("Something went wrong!");
        }
    })
});

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

Dropzone.autoDiscover = false;
const dropzone = $("#image").dropzone({
    init: function() {
        this.on('addedfile', function(file) {
            if (this.files.length > 1) {
                this.removeFile(this.files[0]);
            }
        })
    },
    url: "{{ route('temp-images.create') }}",
    maxFiles: 1,
    paramName: 'image',
    addRemoveLinks: true,
    acceptedFiles: "image/jpeg,image/png,image/gif",
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    success: function(file, response){
        $("#image_id"). val(response.image_id);
    }
});




</script>
@endsection
