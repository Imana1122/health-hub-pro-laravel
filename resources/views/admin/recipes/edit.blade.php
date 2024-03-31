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
                                        <textarea name="description" class="form-control" id="description" cols="30" rows="10"  placeholder="Description">{{ $recipe->description }}</textarea>
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
                                        <img src="{{ asset('storage/uploads/recipes/small/'.$img->image) }}" class="card-img-top" alt="...">
                                        <div class="card-body">
                                            <a href="javascript:void(0)" onClick="deleteImage('{{ $img->id }}')" class="btn btn-danger">Delete</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <div class="card mb-3">
                        <div class="card-body">
                            <h2 class="h4 mb-3">Nutrients</h2>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="calories">Calories</label>
                                        <input type="float" name="calories" id="calories" class="form-control" placeholder="Calories" value="{{ $recipe->calories }}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="protein">Protein</label>
                                        <input type="float" name="protein" id="protein" class="form-control" placeholder="Protein" value="{{ $recipe->protein }}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="total_fat">Total Fat</label>
                                        <input type="float" name="total_fat" id="total_fat" class="form-control" placeholder="Total Fat" value="{{ $recipe->total_fat }}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="saturated_fat">Saturated Fat</label>
                                        <input type="float" name="saturated_fat" id="saturated_fat" class="form-control" placeholder="Saturated Fat" value="{{ $recipe->saturated_fat }}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="sodium">Sodium</label>
                                        <input type="float" name="sodium" id="sodium" class="form-control" placeholder="Sodium" value="{{ $recipe->sodium }}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="sugar">Sugar</label>
                                        <input type="float" name="sugar" id="sugar" class="form-control" placeholder="Sugar" value="{{ $recipe->sugar }}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="carbohydrates">Carbohydrates</label>
                                        <input type="float" name="carbohydrates" id="carbohydrates" class="form-control" placeholder="Carbohydrates" value="{{ $recipe->carbohydrates }}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="protein">Protein</label>
                                        <input type="float" name="protein" id="protein" class="form-control" placeholder="Protein" value="{{ $recipe->protein }}">
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>

                    <div id="card">
                        <div class="card-body">
                            <h2 class="h4 mb-3">Steps</h2>
                            <ul id="step-list" class="list-group">
                                @foreach($recipe->steps as $index => $step)
                                    <li class="list-group-item">
                                        <textarea name="steps[{{ $index + 1 }}]" class="form-control mb-2" placeholder="Enter step">{{ $step }}</textarea>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-success drag-handle" onclick="createStepItemAfter(this.parentNode.parentNode)"><i class="fas fa-bars"></i></button>
                                            <button type="button" class="btn btn-danger" onclick="removeStepItem(this.parentNode.parentNode)"><i class="fas fa-trash"></i></button>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                </div>
                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h2 class="h4 mb-3">Recipe status</h2>
                            <div class="mb-3">
                                <select name="status" id="status" class="form-control">
                                    <option value="1" {{ $recipe->status == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ $recipe->status == 0 ? 'selected' : '' }}>Block</option>
                                </select>
                                <p></p>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <h2 class="h4  mb-3">Recipe category</h2>
                            <div class="mb-3">
                                <label for="meal_type_id">Meal Type</label>
                                <select name="meal_type_id" id="meal_type_id" class="form-control">
                                    <option value="">Select a meal type</option>
                                    @if(!empty($mealTypes))
                                        @foreach ($mealTypes as $mealType)
                                            <option {{ $recipe->meal_type_id == $mealType->id ? 'selected' : '' }} value="{{ $mealType->id }}">{{ $mealType->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <p></p>
                            </div>
                            <div class="mb-3">
                                <label for="cuisine_id">Cuisine</label>
                                <select name="cuisine_id" id="cuisine_id" class="form-control">
                                    <option value="">Select a cuisine</option>
                                    @if(!empty($cuisines))
                                        @foreach ($cuisines as $cuisine)
                                            <option {{ $recipe->cuisine_id == $cuisine->id ? 'selected' : '' }} value="{{ $cuisine->id }}">{{ $cuisine->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <p></p>
                            </div>
                            <div class="mb-3">
                                <label for="category_id">Category</label>
                                <select name="category_id" id="category_id" class="form-control">
                                    <option value="">Select a category</option>
                                    @if(!empty($categories))
                                        @foreach ($categories as $category)
                                            <option {{ $recipe->category_id == $category->id ? 'selected' : '' }} value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <p></p>
                            </div>
                        </div>
                    </div>

                    <!-- Allergen Recommendation -->
                    <div class="card">
                        <div class="card-body">
                            <h2 class="h4 mb-3">Allergen Recommendation</h2>
                            <div class="mb-3">
                                <label for="allergens">Allergens</label>

                                <!-- Loop through allergens and create checkbox inputs -->
                                @if(!empty($allergens))
                                    @foreach ($allergens as $allergen)
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <input type="checkbox" id="allergen{{ $allergen->id }}" name="allergens[]" value="{{ $allergen->id }}" {{ in_array($allergen->id, $recipe->allergenRecipes->pluck('allergen_id')->toArray()) ? 'checked' : '' }} aria-label="Checkbox for following text input">
                                                </div>
                                            </div>
                                            <label class="form-control" for="allergen{{ $allergen->id }}">{{ $allergen->name }}</label>
                                        </div>
                                    @endforeach
                                @endif

                                <p></p>
                            </div>
                        </div>
                    </div>

                    <!-- Health Condition Recommendation -->
                    <div class="card">
                        <div class="card-body">
                            <h2 class="h4 mb-3">Health Condition Recommendation</h2>
                            <div class="mb-3">
                                <label for="healthConditions">Health Conditions</label>

                                <!-- Loop through healthConditions and create checkbox inputs -->
                                @if(!empty($healthConditions))
                                    @foreach ($healthConditions as $healthCondition)
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <input type="checkbox" id="healthCondition{{ $healthCondition->id }}" name="healthConditions[]" value="{{ $healthCondition->id }}" {{ in_array($healthCondition->id, $recipe->healthConditionRecipes->pluck('health_condition_id')->toArray()) ? 'checked' : '' }} aria-label="Checkbox for following text input">
                                                </div>
                                            </div>
                                            <label class="form-control" for="healthCondition{{ $healthCondition->id }}">{{ $healthCondition->name }}</label>
                                        </div>
                                    @endforeach
                                @endif

                                <p></p>
                            </div>
                        </div>
                    </div>
                    <div id="card">
                        <div class="card-body">
                            <h2 class="h4 mb-3">Tags</h2>
                            <ul id="tag-list" class="list-group">
                                @if ($recipe->tags != null)


                                @foreach($recipe->tags as $index => $tag)
                                    <li class="list-group-item">
                                        <input type="text" name="tags[{{ $index + 1 }}]" class="form-control mb-2" placeholder="Enter tag" value="{{ $tag }}">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-success" onclick="createTagItemAfter(this.parentNode.parentNode)"><i class="fas fa-bars"></i></button>
                                            <button type="button" class="btn btn-danger" onclick="removeTagItem(this.parentNode.parentNode)"><i class="fas fa-trash"></i></button>
                                        </div>
                                    </li>
                                @endforeach

                                @endif
                            </ul>
                        </div>
                    </div>
                    <div id="card" class="col-md-12">
                        <div class="card-body">
                            <h2 class="h4 mb-3">Ingredients</h2>

                            <ul id="ingredient-list" class="list-group">
                                @if ($recipe->recipeIngredients != null)

                                @foreach($recipe->recipeIngredients as $index => $item)
                                    <li class="list-group-item ingredient" setupSortable()>
                                        <select name="ingredients[{{ $index + 1 }}]" id="ingredient-select-${ingredientIndex}" class="form-control mb-2">
                                            @if(!empty($ingredients))
                                                @foreach ($ingredients as $ingredient)
                                                    <option value="{{ $ingredient->id }}" {{ $ingredient->id == $item->ingredient_id ? 'selected' : '' }}>{{ $ingredient->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-success drag-handle" onclick="createIngredientItemAfter(this.parentNode.parentNode)"><i class="fas fa-bars"></i></button>
                                            <button type="button" class="btn btn-danger" onclick="removeIngredientItem(this.parentNode.parentNode)"><i class="fas fa-trash"></i></button>
                                        </div>

                                    </li>
                                @endforeach

                                @endif

                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pb-5 pt-3">
                <button type="submit" class="btn btn-primary">Save</button>
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

    //Ingredients
    const ingredientList = document.getElementById("ingredient-list");
    console.log(ingredientList);
    let ingredientIndex =1;


    if (ingredientList) {
        function createIngredientItem() {
            const ingredientItem = document.createElement("li");
            ingredientItem.classList.add("list-group-item");

            const ingredientSelect = createIngredientSelect(ingredientItem);

            const buttonGroup = createButtonGroup(ingredientItem);

            ingredientItem.appendChild(ingredientSelect);
            ingredientItem.appendChild(buttonGroup);

            ingredientList.appendChild(ingredientItem);

            setupSortable();
        }

        function createIngredientItemAfter(targetItem) {
            const ingredientItem = document.createElement("li");
            ingredientItem.classList.add("list-group-item");

            const ingredientSelect = createIngredientSelect(ingredientItem);

            const buttonGroup = createButtonGroup(ingredientItem);

            ingredientItem.appendChild(ingredientSelect);
            ingredientItem.appendChild(buttonGroup);

            ingredientList.insertBefore(ingredientItem, targetItem.nextSibling);

            updateIngredientIndices();
            setupSortable();
        }

        function createIngredientSelect(ingredientItem) {
            const ingredientSelectHTML = `
                <select name="ingredients[${ingredientIndex}]" id="ingredient-select-${ingredientIndex}" class="form-control mb-2">

                    @if(!empty($ingredients))
                        @foreach ($ingredients as $ingredient)
                            <option value="{{ $ingredient->id }}">{{ $ingredient->name }}</option>
                        @endforeach
                    @endif
                </select>
            `;

            const tempContainer = document.createElement("div");
            tempContainer.innerHTML = ingredientSelectHTML;

            const ingredientSelect = tempContainer.querySelector("select");

            ingredientIndex++;

            return ingredientSelect;
        }

        function createButtonGroup(ingredientItem) {
            const buttonGroup = document.createElement("div");
            buttonGroup.classList.add("btn-group");

            const addButton = document.createElement("button");
            addButton.classList.add("btn", "btn-success", "drag-handle");
            addButton.setAttribute("type", "button"); // Set type to button
            addButton.innerHTML = '<i class="fas fa-bars"></i>';
            addButton.addEventListener("click", function () {
                createIngredientItemAfter(ingredientItem);
            });

            const deleteButton = document.createElement("button");
            deleteButton.classList.add("btn", "btn-danger");
            addButton.setAttribute("type", "button"); // Set type to button
            deleteButton.innerHTML = '<i class="fas fa-trash"></i>';
            deleteButton.addEventListener("click", function () {
                removeIngredientItem(ingredientItem);
            });

            buttonGroup.appendChild(addButton);
            buttonGroup.appendChild(deleteButton);

            return buttonGroup;


        }


        function createButton(innerHtml, clickHandler) {
            const button = document.createElement("button");
            button.classList.add("btn");
            button.setAttribute("type", "button");
            button.innerHTML = innerHtml;
            button.addEventListener("click", clickHandler);
            return button;
        }

        function removeIngredientItem(targetItem) {
            ingredientList.removeChild(targetItem);
            updateIngredientIndices();
        }

        function updateIngredientIndices() {
            const ingredientItems = Array.from(ingredientList.children);
            ingredientItems.forEach((item, index) => {
                const select = item.querySelector("select");
                if (select) {
                    select.name = `ingredients[${index + 1}]`;
                    select.id = `ingredient-select-${index + 1}`;
                }
            });
            ingredientIndex = ingredientItems.length + 1;
        }

        function setupSortable() {
            new Sortable(ingredientList, {
                handle: ".drag-handle",
                animation: 150,
                onEnd: updateIngredientIndices
            });
        }

        // Wait for the DOM to be ready
        document.addEventListener("DOMContentLoaded", function () {
            setupSortable();

        });


    } else {
        console.log('The item with id "ingredient-list" does not exist');
    }

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
                <div class="card">
                    <img src="${response['imagePath']}" class="card-img-top" alt="...">
                    <div class="card-body">
                        <button type="button" onclick="deleteImage('${response.image_id}')" class="btn btn-danger">Delete</button>
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
        console.log("okay");
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

    //Steps
    const stepList = document.getElementById("step-list");
        let stepIndex = 1;

        if(stepList){

        function createStepItem() {
            const stepItem = document.createElement("li");
            stepItem.classList.add("list-group-item");

            const stepTextarea = document.createElement("textarea");
            stepTextarea.classList.add("form-control", "mb-2");
            stepTextarea.setAttribute("name", `steps[${stepIndex}]`);
            stepTextarea.setAttribute("placeholder", "Enter step");
            stepIndex++;

            const buttonGroup = document.createElement("div");
            buttonGroup.classList.add("btn-group");

            const addButton = document.createElement("button");
            addButton.classList.add("btn", "btn-success", "drag-handle");
            addButton.setAttribute("type", "button"); // Set type to button
            addButton.innerHTML = '<i class="fas fa-bars"></i>';
            addButton.addEventListener("click", function () {
                createStepItemAfter(stepItem);
            });

            const deleteButton = document.createElement("button");
            addButton.setAttribute("type", "button"); // Set type to button
            deleteButton.classList.add("btn", "btn-danger");
            deleteButton.innerHTML = '<i class="fas fa-trash"></i>';
            deleteButton.addEventListener("click", function () {
                removeStepItem(stepItem);
            });

            buttonGroup.appendChild(addButton);
            buttonGroup.appendChild(deleteButton);

            stepItem.appendChild(stepTextarea);
            stepItem.appendChild(buttonGroup);

            stepList.appendChild(stepItem);

            setupSortable();
        }

        function createStepItemAfter(targetItem) {
            const stepItem = document.createElement("li");
            stepItem.classList.add("list-group-item");

            const stepTextarea = document.createElement("textarea");
            stepTextarea.classList.add("form-control", "mb-2");
            stepTextarea.setAttribute("name", `steps[${stepIndex}]`);
            stepTextarea.setAttribute("placeholder", "Enter step");
            stepIndex++;

            const buttonGroup = document.createElement("div");
            buttonGroup.classList.add("btn-group");

            const addButton = document.createElement("button");
            addButton.classList.add("btn", "btn-success", "drag-handle");
            addButton.setAttribute("type", "button"); // Set type to button
            addButton.innerHTML = '<i class="fas fa-bars"></i>';
            addButton.addEventListener("click", function () {
                createStepItemAfter(stepItem);
            });

            const deleteButton = document.createElement("button");
            deleteButton.classList.add("btn", "btn-danger");
            addButton.setAttribute("type", "button"); // Set type to button
            deleteButton.innerHTML = '<i class="fas fa-trash"></i>';
            deleteButton.addEventListener("click", function () {
                removeStepItem(stepItem);
            });

            buttonGroup.appendChild(addButton);
            buttonGroup.appendChild(deleteButton);

            stepItem.appendChild(stepTextarea);
            stepItem.appendChild(buttonGroup);

            // Insert new step item after the target item
            stepList.insertBefore(stepItem, targetItem.nextSibling);

            // Update indices and setup sortable
            updateStepIndices();
            setupSortable();
        }


        function removeStepItem(targetItem) {
            stepList.removeChild(targetItem);
            updateStepIndices();
        }

        function updateStepIndices() {
            const stepItems = Array.from(stepList.children);
            stepItems.forEach((item, index) => {
                const textarea = item.querySelector("textarea");
                if (textarea) {
                    textarea.setAttribute("name", `steps[${index + 1}]`);
                }
            });
            stepIndex = stepItems.length + 1;
        }

        function setupSortable() {
            new Sortable(stepList, {
                handle: ".drag-handle",
                animation: 150,
                onEnd: updateStepIndices
            });
        }

        // Wait for the DOM to be ready
        document.addEventListener("DOMContentLoaded", function () {
            setupSortable();

        });
    }else{
        console.log('The item with id does not exist');
    }



    //Tags
        const tagList = document.getElementById("tag-list");
        let tagIndex = {{ count($recipe->tags) ?? 1 }}; // Set the initial index based on existing tags
        if(tagList){

        function createTagItem() {
            const tagItem = document.createElement("li");
            tagItem.classList.add("list-group-item");

            const tagInput = document.createElement("input");
            tagInput.setAttribute("type", "text"); // Set type to text
            tagInput.classList.add("form-control", "mb-2");
            tagInput.setAttribute("name", `tags[${tagIndex}]`);
            tagInput.setAttribute("placeholder", "Enter tag");
            tagIndex++;

            const buttonGroup = document.createElement("div");
            buttonGroup.classList.add("btn-group");

            const addButton = document.createElement("button");
            addButton.classList.add("btn", "btn-success", "drag-handle");
            addButton.setAttribute("type", "button"); // Set type to button
            addButton.innerHTML = '<i class="fas fa-bars"></i>';
            addButton.addEventListener("click", function () {
                createTagItemAfter(tagItem);
            });

            const deleteButton = document.createElement("button");
            addButton.setAttribute("type", "button"); // Set type to button
            deleteButton.classList.add("btn", "btn-danger");
            deleteButton.innerHTML = '<i class="fas fa-trash"></i>';
            deleteButton.addEventListener("click", function () {
                removeTagItem(tagItem);
            });

            buttonGroup.appendChild(addButton);
            buttonGroup.appendChild(deleteButton);

            tagItem.appendChild(tagInput);
            tagItem.appendChild(buttonGroup);

            tagList.appendChild(tagItem);

            setupSortable();
        }

        function createTagItemAfter(targetItem) {
            const tagItem = document.createElement("li");
            tagItem.classList.add("list-group-item");

            const tagInput = document.createElement("input");
            tagInput.setAttribute("type", "text"); // Set type to text
            tagInput.classList.add("form-control", "mb-2");
            tagInput.setAttribute("name", `tags[${tagIndex}]`);
            tagInput.setAttribute("placeholder", "Enter tag");
            tagIndex++;

            const buttonGroup = document.createElement("div");
            buttonGroup.classList.add("btn-group");

            const addButton = document.createElement("button");
            addButton.classList.add("btn", "btn-success", "drag-handle");
            addButton.setAttribute("type", "button"); // Set type to button
            addButton.innerHTML = '<i class="fas fa-bars"></i>';
            addButton.addEventListener("click", function () {
                createTagItemAfter(tagItem);
            });

            const deleteButton = document.createElement("button");
            deleteButton.classList.add("btn", "btn-danger");
            addButton.setAttribute("type", "button"); // Set type to button
            deleteButton.innerHTML = '<i class="fas fa-trash"></i>';
            deleteButton.addEventListener("click", function () {
                removeTagItem(tagItem);
            });

            buttonGroup.appendChild(addButton);
            buttonGroup.appendChild(deleteButton);

            tagItem.appendChild(tagInput);
            tagItem.appendChild(buttonGroup);

            // Insert new tag item after the target item
            tagList.insertBefore(tagItem, targetItem.nextSibling);

            // Update indices and setup sortable
            updateTagIndices();
            setupSortable();
        }


        function removeTagItem(targetItem) {
            tagList.removeChild(targetItem);
            updateTagIndices();
        }

        function updateTagIndices() {
            const tagItems = Array.from(tagList.children);
            tagItems.forEach((item, index) => {
                const input = item.querySelector("input");
                if (input) {
                    input.setAttribute("name", `tags[${index + 1}]`);
                }
            });
            tagIndex = tagItems.length + 1;
        }

        function setupSortable() {
            new Sortable(tagList, {
                handle: ".drag-handle",
                animation: 150,
                onEnd: updateTagIndices
            });
        }

        // Wait for the DOM to be ready
        document.addEventListener("DOMContentLoaded", function () {
            setupSortable();

        });
        if (tagList.children.length === 0) {
            createTagItem();
        }

    }else{
        console.log('The item with taglist doesnot exist');
    }



</script>

@endsection
