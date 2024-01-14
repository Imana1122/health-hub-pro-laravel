@extends('admin.layouts.app')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Create Recipe Steps</h1>
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
    <form action="" name="recipeStepsForm" id="recipeStepsForm">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="h4  mb-3">Recipe Steps</h2>
                            <div class="mb-3">
                                <label for="category">Recipe</label>
                                <select name="meal_type_id" id="meal_type_id" class="form-control">
                                    <option value="">Select a Recipe</option>
                                    @if(!empty($recipes))
                                        @foreach ($recipes as $recipe)
                                            <option value="{{ $recipe->id }}">{{ $recipe->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <p></p>
                            </div>
                        </div>
                    </div>

                    <div id="card">
                        <div class="card-body">
                            <h2 class="h4 mb-3">Recipe Steps</h2>
                            <div id="recipe-step-container">
                                <input type="hidden" name="steps" id="stepsInput" value="">
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

    $("#recipeStepsForm").submit(function(event){
        event.preventDefault();
        var formArray = $(this).serializeArray();
        $("button[type=submit]").prop('disabled',true);

        // Include the steps from the hidden input field in the form data
        formArray.push({ name: "steps", value: $("#stepsInput").val() });

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
    const recipeStepContainer = document.getElementById("recipe-step-container");
    const stepsArray = [];

    function createRecipeStepInput() {
        const outerCardDiv = document.createElement("div");
        outerCardDiv.classList.add("card", "mb-3");

        const outerCardBodyDiv = document.createElement("div");
        outerCardBodyDiv.classList.add("card-body");

        const recipeStepInputDiv = document.createElement("div");
        recipeStepInputDiv.classList.add("input-group", "mb-3");

        const recipeStepInput = document.createElement("input");
        recipeStepInput.classList.add("form-control");
        recipeStepInput.setAttribute("type", "text");
        recipeStepInput.setAttribute("placeholder", "Enter recipeStep");
        recipeStepInput.addEventListener("input", function () {
            this.value = this.value.toLowerCase(); // Convert to lowercase
        });

        const addRecipeStepIconContainer = document.createElement("div");
        addRecipeStepIconContainer.classList.add("input-group-append");

        const addRecipeStepIcon = document.createElement("button");
        addRecipeStepIcon.classList.add("btn", "btn-success", "rounded-circle", "ml-2");
        addRecipeStepIcon.type = "button";
        addRecipeStepIcon.innerHTML = '<i class="fas fa-plus"></i>';
        addRecipeStepIcon.addEventListener("click", function () {
            const recipeStepValue = recipeStepInput.value.trim();
            if (recipeStepValue !== "") {
                stepsArray.push(recipeStepValue);
                createRecipeStepElement(recipeStepValue);
            }
            recipeStepInput.value = "";
        });

        recipeStepInputDiv.appendChild(recipeStepInput);
        addRecipeStepIconContainer.appendChild(addRecipeStepIcon);
        recipeStepInputDiv.appendChild(addRecipeStepIconContainer);
        outerCardBodyDiv.appendChild(recipeStepInputDiv);
        outerCardDiv.appendChild(outerCardBodyDiv);
        recipeStepContainer.appendChild(outerCardDiv);
    }

    function createRecipeStepElement(recipeStepValue) {
        const recipeStepElement = document.createElement("div");
        recipeStepElement.classList.add("input-group", "mb-2");

        const recipeStepSpan = document.createElement("span");
        recipeStepSpan.classList.add("form-control");
        recipeStepSpan.textContent = recipeStepValue;

        const deleteRecipeStepIconContainer = document.createElement("div");
        deleteRecipeStepIconContainer.classList.add("input-group-append");

        const deleteRecipeStepIcon = document.createElement("button");
        deleteRecipeStepIcon.classList.add("btn", "btn-danger", "rounded-circle", "ml-2");
        deleteRecipeStepIcon.type = "button";
        deleteRecipeStepIcon.innerHTML = '<i class="fas fa-times"></i>';
        deleteRecipeStepIcon.addEventListener("click", function () {
            const index = stepsArray.indexOf(recipeStepValue);
            if (index !== -1) {
                stepsArray.splice(index, 1);
                updateRecipeStepsInput(); // Update the hidden input field
            }
            recipeStepElement.remove();
        });

        recipeStepElement.appendChild(recipeStepSpan);
        deleteRecipeStepIconContainer.appendChild(deleteRecipeStepIcon);
        recipeStepElement.appendChild(deleteRecipeStepIconContainer);

        recipeStepContainer.appendChild(recipeStepElement);

        // Update the hidden input field when a new recipeStep is added
        updateRecipeStepsInput();
    }

    function updateRecipeStepsInput() {
        const stepsInput = document.getElementById("stepsInput");
        stepsInput.value = stepsArray.join(","); // Convert array to a comma-separated string
    }

    createRecipeStepInput(); // Initial recipeStep input
});


</script>

@endsection
