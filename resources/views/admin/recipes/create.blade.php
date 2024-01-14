@extends('admin.layouts.app')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Create Recipe</h1>
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
                                        <input type="text" name="title" id="title" class="form-control" placeholder="Title">
                                        <p></p>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="slug">Slug</label>
                                        <input readonly type="text" name="slug" id="slug" class="form-control" placeholder="Slug">
                                        <p></p>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="description">Description</label>
                                        <textarea name="description" id="description" cols="30" rows="10" class="summernote" placeholder="Description"></textarea>
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

                    </div>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h2 class="h4 mb-3">Nutrients</h2>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="price">Calories</label>
                                        <input type="float" name="calories" id="calories" class="form-control" placeholder="Calories">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="price">Protein</label>
                                        <input type="float" name="protein" id="protein" class="form-control" placeholder="Protein">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="price">Total Fat</label>
                                        <input type="float" name="total_fat" id="total_fat" class="form-control" placeholder="Total Fat">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="price">Saturated Fat</label>
                                        <input type="float" name="saturated_fat" id="saturated_fat" class="form-control" placeholder="Saturated Fat">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="price">Sodium</label>
                                        <input type="float" name="sodium" id="sodium" class="form-control" placeholder="Sodium">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h2 class="h4 mb-3">Recipe status</h2>
                            <div class="mb-3">
                                <select name="status" id="status" class="form-control">
                                    <option value="1">Active</option>
                                    <option value="0">Block</option>
                                </select>
                                <p></p>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <h2 class="h4  mb-3">Recipe category</h2>
                            <div class="mb-3">
                                <label for="category">Meal Type</label>
                                <select name="meal_type_id" id="meal_type_id" class="form-control">
                                    <option value="">Select a meal type</option>
                                    @if(!empty($mealTypes))
                                        @foreach ($mealTypes as $mealType)
                                            <option value="{{ $mealType->id }}">{{ $mealType->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <p></p>
                            </div>
                            <div class="mb-3">
                                <label for="category">Cuisine</label>
                                <select name="cuisine_id" id="cuisine_id" class="form-control">
                                    <option value="">Select a cuisine</option>
                                    @if(!empty($cuisines))
                                        @foreach ($cuisines as $cuisine)
                                            <option value="{{ $cuisine->id }}">{{ $cuisine->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <p></p>
                            </div>
                        </div>
                    </div>

                    <div id="card">
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
                <button type="submit" class="btn btn-primary">Create</button>
                <a href="{{ route('recipes.index') }}" class="btn btn-outline-dark ml-3">Cancel</a>
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

        url: "{{ route('temp-images.create') }}",
        maxFiles: 10,
        paramName: 'image',
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
        $.ajax({
            url: '{{ route('temp-images.delete') }}', // Replace with the actual URL endpoint on your server
            method: 'DELETE',
            data: { id: id },
            dataType: 'json',
            success: function (response) {
                if (response.status === true) {
                    // If deletion is successful, remove the HTML element from the DOM
                    $("#image-row-" + id).remove();
                } else {
                    // Handle errors or show a message to the user
                    alert(response.message);
                }
            },
            error: function (error) {
                // Handle AJAX errors
                console.error('AJAX request failed', error);
            }
        });
    }


    $("#recipeForm").submit(function(event){
        event.preventDefault();
        var formArray = $(this).serializeArray();
        $("button[type=submit]").prop('disabled',true);

        // Include the tags from the hidden input field in the form data
        formArray.push({ name: "tags", value: $("#tagsInput").val() });

        $.ajax({
            url: '{{ route("recipes.store") }}',
            type: 'post',
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

    $("#recipe_id").change(function(){
        var recipe_id = $(this).val();
        $.ajax({
            url: '{{ route('recipes.index') }}',
            type: 'get',
            data: {recipe_id: recipe_id},
            dataType: 'json',
            success: function(response){
                $("#sub_recipe_id").find("option").not(":first").remove();
                $.each(response['subCategories'],function(key,item){
                    $("#sub_recipe_id").append(`<option value='${item.id}'> ${item.name} </option>`)
                })
            },
            error: function (xhr, status, error) {
                console.log("AJAX Request Failed:", status, error);
            }

        })
    })

    document.addEventListener("DOMContentLoaded", function () {
    const tagContainer = document.getElementById("tag-container");
    const tagsArray = [];

    function createTagInput() {
        const outerCardDiv = document.createElement("div");
        outerCardDiv.classList.add("card", "mb-3");

        const outerCardBodyDiv = document.createElement("div");
        outerCardBodyDiv.classList.add("card-body");

        const tagInputDiv = document.createElement("div");
        tagInputDiv.classList.add("input-group", "mb-3");

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
