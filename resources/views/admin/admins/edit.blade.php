@extends('admin.layouts.app')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Edit Admin</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('admins.index') }}" class="btn btn-primary">Back</a>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
</section>
<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="container-fluid">
        <form action="" id="adminForm" name="adminForm">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name">Name</label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Name" value="{{ $admin->name }}">
                                <p></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone_number">Phone Number</label>
                                <input type="number" name="phone_number" id="phone_number" class="form-control" placeholder="Phone" value="{{ $admin->phone_number }}">
                                <p></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email">Email</label>
                                <input type="text" name="email" id="email" class="form-control" placeholder="Email" value="{{ $admin->email }}">
                                <p></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="1" {{ ($admin->status == 1) ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ ($admin->status == 0) ? 'selected' : '' }}>Block</option>
                                </select>
                                <p></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="role">Status</label>
                                <select name="role" id="role" class="form-control">
                                    <option value="1" {{ ($admin->role == 1) ? 'selected' : '' }}>Super Admin</option>
                                    <option value="2" {{ ($admin->role == 2) ? 'selected' : '' }}>Admin</option>
                                </select>
                                <p></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pb-5 pt-3">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('admins.index') }}" class="btn btn-outline-dark ml-3">Cancel</a>
            </div>
        </form>
    </div>
    <!-- /.card -->
</section>
<!-- /.content -->

@endsection

@section('customJs')
<script>

$("#adminForm").submit(function(event){
    event.preventDefault();
    var element = $("#adminForm");
    $("button[type=submit]").prop('disabled',true);

    $.ajax({
        url:'{{ route('admins.update',$admin->id) }}',
        type:'put',
        data: element.serializeArray(),
        dataType: 'json',
        success: function(response){
            $("button[type=submit]").prop('disabled',false);

            if(response["status"] == true) {

                window.location.href="{{ route('admins.index') }}";

                $("#name").removeClass('is-invalid')
                .siblings('p')
                .removeClass('invalid-feedback')
                .html("");

                $("#phone_number").removeClass('is-invalid')
                .siblings('p')
                .removeClass('invalid-feedback')
                .html("");
                $("#email").removeClass('is-invalid')
                .siblings('p')
                .removeClass('invalid-feedback')
                .html("");

                $("#password").removeClass('is-invalid')
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


                if(errors['phone_number']){
                    $("#phone_number").addClass('is-invalid')
                    .siblings('p')
                    .addClass('invalid-feedback')
                    .html(errors['phone_number']);
                }else{
                    $("#phone_number").removeClass('is-invalid')
                    .siblings('p')
                    .removeClass('invalid-feedback')
                    .html("");
                }

                if(errors['email']){
                    $("#email").addClass('is-invalid')
                    .siblings('p')
                    .addClass('invalid-feedback')
                    .html(errors['email']);
                }else{
                    $("#email").removeClass('is-invalid')
                    .siblings('p')
                    .removeClass('invalid-feedback')
                    .html("");
                }

                if(errors['password']){
                    $("#password").addClass('is-invalid')
                    .siblings('p')
                    .addClass('invalid-feedback')
                    .html(errors['password']);
                }else{
                    $("#password").removeClass('is-invalid')
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

</script>
@endsection
