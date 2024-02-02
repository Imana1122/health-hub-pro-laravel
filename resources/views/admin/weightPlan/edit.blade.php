@extends('admin.layouts.app')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Edit Weight Plan</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('weightPlans.index') }}" class="btn btn-primary">Back</a>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
</section>
<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="container-fluid">
        <form action="" method="post" id="weightPlanForm" name="weightPlanForm">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="title">Name</label>
                                <input type="text" name="title" id="title" class="form-control" placeholder="Title" value="{{ $weightPlan->title }}">
                                <p></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="slug">Slug</label>
                                <input type="text" name="slug" readOnly id="slug" class="form-control" placeholder="Slug" value="{{ $weightPlan->slug }}">
                                <p></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="subtitle">SubTitle</label>
                                <input type="text" name="subtitle" id="subtitle" class="form-control" placeholder="SubTitle" value="{{ $weightPlan->subtitle }}">
                                <p></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option {{ ($weightPlan->status == 1) ? 'selected' : '' }} value="1">Active</option>
                                    <option {{ ($weightPlan->status == 0) ? 'selected' : '' }} value="0">Block</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <input type="hidden" id="image_id" name="image_id" value="" >
                                <label for="image">Image</label>
                                <div id="image" class="dropzone dz-clickable">
                                    <div class="dz-message needsclick">
                                        <br>Drop files here or click to upload. <br><br>
                                    </div>
                                </div>
                            </div>
                            @if(!empty($weightPlan->image))
                            <div>
                                <img width="250" src="{{ asset('uploads/weightPlan/thumb/'.$weightPlan->image) }}" alt="">
                            </div>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
            <div class="pb-5 pt-3">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route("weightPlans.index") }}" class="btn btn-outline-dark ml-3">Cancel</a>
            </div>
        </form>
    </div>
    <!-- /.card -->
</section>
<!-- /.content -->

@endsection

@section('customJs')
<script>
$("#weightPlanForm").submit(function(event){
    event.preventDefault();
    var element = $(this);
    $("button[type=submit]").prop('disabled',true);

    $.ajax({
        url:'{{ route('weightPlans.update', $weightPlan->id) }}',
        type:'put',
        data: element.serializeArray(),
        dataType: 'json',
        success: function(response){
            $("button[type=submit]").prop('disabled',false);

            if(response["status"] == true) {

                window.location.href="{{ route('weightPlans.index') }}";

                $("#title").removeClass('is-invalid')
                .siblings('p')
                .removeClass('invalid-feedback')
                .html("");

                $("#slug").removeClass('is-invalid')
                .siblings('p')
                .removeClass('invalid-feedback')
                .html("");
            }else{
                if(response['notFound'] == true){
                    window.location.href="{{ route('weightPlans.index') }}";
                }

                var errors=response['errors'];
                if(errors['title']){
                    $("#title").addClass('is-invalid')
                    .siblings('p')
                    .addClass('invalid-feedback')
                    .html(errors['title']);
                }else{
                    $("#title").removeClass('is-invalid')
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
