@extends('admin.layouts.app')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Edit Recipe</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('recipes.index') }}" class="btn btn-primary">Back</a>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
</section>
<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <form action="" name="recipeForm" id="recipeForm">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="title">Title</label>
                                        <input type="text" name="title" id="title" class="form-control" placeholder="Title" value="{{ $recipe->title }}">
                                        <p></p>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="slug">Slug</label>
                                        <input readonly type="text" name="slug" id="slug" class="form-control" placeholder="Slug" value="{{ $recipe->slug }}">
                                        <p></p>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="description">Description</label>
                                        <textarea name="description" id="description" cols="30" rows="10" class="summernote" placeholder="Description">{{ $recipe->description }}</textarea>
                                        <p></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h2 class="h4 mb-3">Media</h2>
                            <div id="image" class="dropzone dz-clickable">
                                <div class="dz-message needsclick">
                                    <br>Drop files here or click to upload.<br><br>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="recipe-gallery">
                        @if ($recipe->images->isNotEmpty())
                            @foreach ($recipe->images as $img )
                                <div class="col-md-3" id="image-row-{{ $img->id }}">
                                    <input type="hidden" name="image_array[]" value="{{ $img->id }}">
                                    <div class="card" >
                                        <img src="{{ asset('uploads/recipes/small/'.$img->image) }}" class="card-img-top" alt="...">
                                        <div class="card-body">
                                            <a href="javascript:void(0)" onClick="deleteImage({{ $img->id }})" class="btn btn-danger">Delete</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                </div>
                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h2 class="h4 mb-3">Recipe status</h2>
                            <div class="mb-3">
                                <select name="status" id="status" class="form-control">
                                    <option {{ ($recipe->status == 1) ? 'selected' : '' }} value="1">Active</option>
                                    <option {{ ($recipe->status == 0) ? 'selected' : '' }} value="0">Block</option>
                                </select>
                                <p></p>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <h2 class="h4  mb-3">Recipe cuisine</h2>
                            <div class="mb-3">
                                <label for="cuisine">Category</label>
                                <select name="cuisine_id" id="cuisine_id" class="form-control">
                                    <option value="">Select a cuisine</option>
                                    @if(!empty($cuisines))                                        @foreach ($cuisines as $cuisine)
                                            <option {{ ($recipe->cuisine_id == $cuisine->id) ? 'selected' : '' }} value="{{ $cuisine->id }}">{{ $cuisine->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <p></p>
                            </div>
                            <div class="mb-3">
                                <label for="cuisine">Sub cuisine</label>
                                <select name="meal_type_id" id="meal_type_id" class="form-control">
                                    <option value="">Select a sub cuisine</option>
                                    @if(!empty($mealTypes))
                                    @foreach ($mealTypes as $meal_type)
                                            <option {{ ($recipe->meal_type_id == $meal_type->id) ? 'selected' : '' }} value="{{ $meal_type->id }}">{{ $meal_type->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <p></p>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <h2 class="h4 mb-3">Tags</h2>
                            <div id="tag-container">
                                <input type="hidden" name="tags" id="tagsInput" value="">
                            </div>
                        </div>
                    </div>


                </div>
            </div>

            <div class="pb-5 pt-3">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('recipes.index') }}" class="btn btn-outline-dark ml-3">Cancel</a>
            </div>

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
                            <p>Are you sure you want to delete this image?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <!-- /.card -->
</section>
<!-- /.content -->

@endsection


@section('customJs')

<script>

    $("#title").change(function(){
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
        url: "{{ route('recipe-images.update') }}",
        maxFiles: 10,
        paramName: 'image',
        params: {'recipe_id': '{{ $recipe->id }}'},
        addRemoveLinks: true,
        acceptedFiles: "image/jpeg,image/png,image/gif",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(file, response){
            //$("#image_id"). val(response.image_id);

            var html = `<div class="col-md-3" id="image-row-${response.image_id}">
                        <input type="hidden" name="image_array[]" value="${response.image_id}">
                        <div class="card" >
                            <img src="${response['imagePath']}" class="card-img-top" alt="...">
                            <div class="card-body">
                                <a href="javascript:void(0)" onClick="deleteImage(${response.image_id})" class="btn btn-danger">Delete</a>
                            </div>
                        </div>
                        </div>`;


            $("#recipe-gallery").append(html);

        },
        complete: function(file){
            this.removeFile(file);
        }
    });

    function deleteImage(id) {
        // Make an AJAX request to delete the image
       // Show the delete confirmation modal
       $('#deleteConfirmationModal').modal('show');

        // Handle the click on the "Delete" button in the modal
        $('#confirmDeleteBtn').click(function() {
            // Close the modal
            $('#deleteConfirmationModal').modal('hide');
            $.ajax({
                url: '{{ route('recipe-images.delete') }}', // Replace with the actual URL endpoint on your server
                method: 'DELETE',
                data: { id: id },
                dataType: 'json',
                success: function (response) {
                    if (response.status === true) {
                        // If deletion is successful, remove the HTML element from the DOM
                        $("#image-row-" + id).remove();
                        alert(response.message);
                    } else {
                        // Handle errors or show a message to the user
                        alert(response.message);
                    }
                },
                error: function (error) {
                    // Handle AJAX errors
                    console.error('AJAX request failed', error);
                    alert('Image not deleted');
                }
            });
       });
    }


    $("#recipeForm").submit(function(event){
        event.preventDefault();
        var formArray = $(this).serializeArray();
        $("button[type=submit]").prop('disabled',true);

        $.ajax({
            url: '{{ route("recipes.update", $recipe->id) }}',
            type: 'put',
            data: formArray,
            dataType: 'json',
            success: function(response){
                $("button[type=submit]").prop('disabled', false);

                if (response["status"] == true) {
                    $(".error").removeClass('invalid-feedback');
                    $('input[type="text"], select').removeClass('is-invalid');
                    window.location.href = "{{ route('recipes.index') }}";


                } else {
                    var errors = response['errors'];
                    $(".error").removeClass('invalid-feedback');
                    $('input[type="text"], select').removeClass('is-invalid');
                    $.each(errors, function(key, value){
                        $(`#${key}`).addClass('is-invalid')
                        .siblings('p')
                        .addClass('invalid-feedback')
                        .html(value);
                    })


                }

            },
            error: function (xhr, status, error) {
                console.log("AJAX Request Failed:", status, error);
            }

        })
    })


    // Wait for the DOM to be ready
    $(document).ready(function () {
        // Attach a change event listener to the track_qty checkbox
        $('#track_qty').change(function () {
            // Enable/disable the qty input based on the checkbox state
            $('#qty').prop('disabled', !this.checked);
        }).change(); // Trigger the change event once to handle the initial state
    });

    document.addEventListener("DOMContentLoaded", function () {
    const tagContainer = document.getElementById("tag-container");
    const tagsArray = [];

    function createTagInput() {
        const outerCardDiv = document.createElement("div");
        outerCardDiv.classList.add("card", "mb-1");

        const outerCardBodyDiv = document.createElement("div");
        outerCardBodyDiv.classList.add("card-body");

        const tagInputDiv = document.createElement("div");
        tagInputDiv.classList.add("input-group", "mb-1");

        const tagInput = document.createElement("input");
        tagInput.classList.add("form-control");
        tagInput.setAttribute("type", "text");
        tagInput.setAttribute("placeholder", "Enter tag");
        tagInput.addEventListener("input", function () {
            this.value = this.value.toLowerCase(); // Convert to lowercase
        });

        const addTagIconContainer = document.createElement("div");
        addTagIconContainer.classList.add("input-group-append");

        const addTagIcon = document.createElement("button");
        addTagIcon.classList.add("btn", "btn-success", "rounded-circle", "ml-2");
        addTagIcon.type = "button";
        addTagIcon.innerHTML = '<i class="fas fa-plus"></i>';
        addTagIcon.addEventListener("click", function () {
            const tagValue = tagInput.value.trim();
            if (tagValue !== "") {
                tagsArray.push(tagValue);
                createTagElement(tagValue);
            }
            tagInput.value = "";
        });

        tagInputDiv.appendChild(tagInput);
        addTagIconContainer.appendChild(addTagIcon);
        tagInputDiv.appendChild(addTagIconContainer);
        outerCardBodyDiv.appendChild(tagInputDiv);
        outerCardDiv.appendChild(outerCardBodyDiv);
        tagContainer.appendChild(outerCardDiv);
    }

    function createTagElement(tagValue) {
        const tagElement = document.createElement("div");
        tagElement.classList.add("input-group", "mb-2");

        const tagSpan = document.createElement("span");
        tagSpan.classList.add("form-control");
        tagSpan.textContent = tagValue;

        const deleteTagIconContainer = document.createElement("div");
        deleteTagIconContainer.classList.add("input-group-append");

        const deleteTagIcon = document.createElement("button");
        deleteTagIcon.classList.add("btn", "btn-danger", "rounded-circle", "ml-2");
        deleteTagIcon.type = "button";
        deleteTagIcon.innerHTML = '<i class="fas fa-times"></i>';
        deleteTagIcon.addEventListener("click", function () {
            const index = tagsArray.indexOf(tagValue);
            if (index !== -1) {
                tagsArray.splice(index, 1);
                updateTagsInput(); // Update the hidden input field
            }
            tagElement.remove();
        });

        tagElement.appendChild(tagSpan);
        deleteTagIconContainer.appendChild(deleteTagIcon);
        tagElement.appendChild(deleteTagIconContainer);

        tagContainer.appendChild(tagElement);

        // Update the hidden input field when a new tag is added
        updateTagsInput();
    }

    function updateTagsInput() {
        const tagsInput = document.getElementById("tagsInput");
        tagsInput.value = tagsArray.join(","); // Convert array to a comma-separated string
    }

    createTagInput(); // Initial tag input
});


</script>

@endsection
