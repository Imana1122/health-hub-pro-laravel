@extends('admin.layouts.app')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Edit Workout</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('workouts.index') }}" class="btn btn-primary">Back</a>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
</section>
<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="container-fluid">
        <form action="" method="post" id="workoutForm" name="workoutForm">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name">Name</label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Name" value="{{ $workout->name }}">
                                <p></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="slug">Slug</label>
                                <input type="text" name="slug" readOnly id="slug" class="form-control" placeholder="Slug" value="{{ $workout->slug }}">
                                <p></p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="1" {{ $workout->status == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ $workout->status == 0 ? 'selected' : '' }}>Block</option>
                                </select>
                                <p></p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <input type="hidden" id="image_id" name="image_id" value="">
                                <label for="image">Image</label>
                                <div id="image" class="dropzone dz-clickable">
                                    <div class="dz-message needsclick">
                                        <br>Drop files here or click to upload. <br><br>
                                    </div>
                                </div>
                            </div>
                            @if(!empty($workout->image))
                            <div>
                                <img width="100" src="{{ asset('uploads/workout/thumb/'.$workout->image) }}" alt="">
                            </div>
                            @endif
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="description">Description</label>
                                <textarea name="description" class="form-control" id="description" cols="30" rows="10"  placeholder="Description">{{ $workout->description }}</textarea>
                                <p></p>
                            </div>
                        </div>

                        <div id="card" class="col-md-12">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Exercises</h2>

                                <ul id="exercise-list" class="list-group">
                                    @if ($workout->exercises != null)

                                    @foreach($workout->exercises as $index => $item)
                                        <li class="list-group-item exercise" setupSortable()>
                                            <select name="exercises[{{ $index + 1 }}]" id="exercise-select-${exerciseIndex}" class="form-control mb-2">
                                                @if(!empty($exercises))
                                                    @foreach ($exercises as $exercise)
                                                        <option value="{{ $exercise->id }}" {{ $exercise->id == $item ? 'selected' : '' }}>{{ $exercise->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-success drag-handle" onclick="createExerciseItemAfter(this.parentNode.parentNode)"><i class="fas fa-bars"></i></button>
                                                <button type="button" class="btn btn-danger" onclick="removeExerciseItem(this.parentNode.parentNode)"><i class="fas fa-trash"></i></button>
                                            </div>

                                        </li>
                                    @endforeach

                                    @endif

                                </ul>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="pb-5 pt-3">
                <button type="submit" class="btn btn-primary">Save</button>
                <a href="{{ route("workouts.index") }}" class="btn btn-outline-dark ml-3">Cancel</a>
            </div>
        </form>
    </div>
    <!-- /.card -->
</section>
<!-- /.content -->

@endsection

@section('customJs')

<script>
$("#workoutForm").submit(function(event){
    event.preventDefault();
    var element = $(this);
    $("button[type=submit]").prop('disabled',true);

    $.ajax({
        url: '{{ route("workouts.update", $workout->id) }}',
        type:'put',
        data: element.serializeArray(),
        dataType: 'json',
        success: function(response){
            $("button[type=submit]").prop('disabled',false);

            if(response["status"] == true) {

                window.location.href="{{ route('workouts.index') }}";

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



    //Exercises
    const exerciseList = document.getElementById("exercise-list");
    console.log(exerciseList);
    let exerciseIndex =1;


    if (exerciseList) {
        function createExerciseItem() {
            const exerciseItem = document.createElement("li");
            exerciseItem.classList.add("list-group-item");

            const exerciseSelect = createExerciseSelect(exerciseItem);

            const buttonGroup = createButtonGroup(exerciseItem);

            exerciseItem.appendChild(exerciseSelect);
            exerciseItem.appendChild(buttonGroup);

            exerciseList.appendChild(exerciseItem);

            setupSortable();
        }



        function createExerciseItemAfter(targetItem) {
            const exerciseItem = document.createElement("li");
            exerciseItem.classList.add("list-group-item");

            const exerciseSelect = createExerciseSelect(exerciseItem);

            const buttonGroup = createButtonGroup(exerciseItem);

            exerciseItem.appendChild(exerciseSelect);
            exerciseItem.appendChild(buttonGroup);

            exerciseList.insertBefore(exerciseItem, targetItem.nextSibling);

            updateExerciseIndices();
            setupSortable();
        }

        function createExerciseSelect(exerciseItem) {
            const exerciseSelectHTML = `
                <select name="exercises[${exerciseIndex}]" id="exercise-select-${exerciseIndex}" class="form-control mb-2">

                    @if(!empty($exercises))
                        @foreach ($exercises as $exercise)
                            <option value="{{ $exercise->id }}">{{ $exercise->name }}</option>
                        @endforeach
                    @endif
                </select>
            `;

            const tempContainer = document.createElement("div");
            tempContainer.innerHTML = exerciseSelectHTML;

            const exerciseSelect = tempContainer.querySelector("select");

            exerciseIndex++;

            return exerciseSelect;
        }

        function createButtonGroup(exerciseItem) {
            const buttonGroup = document.createElement("div");
            buttonGroup.classList.add("btn-group");

            const addButton = document.createElement("button");
            addButton.classList.add("btn", "btn-success", "drag-handle");
            addButton.setAttribute("type", "button"); // Set type to button
            addButton.innerHTML = '<i class="fas fa-bars"></i>';
            addButton.addEventListener("click", function () {
                createExerciseItemAfter(exerciseItem);
            });

            const deleteButton = document.createElement("button");
            deleteButton.classList.add("btn", "btn-danger");
            addButton.setAttribute("type", "button"); // Set type to button
            deleteButton.innerHTML = '<i class="fas fa-trash"></i>';
            deleteButton.addEventListener("click", function () {
                removeExerciseItem(exerciseItem);
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

        function removeExerciseItem(targetItem) {
            exerciseList.removeChild(targetItem);
            updateExerciseIndices();
        }

        function updateExerciseIndices() {
            const exerciseItems = Array.from(exerciseList.children);
            exerciseItems.forEach((item, index) => {
                const select = item.querySelector("select");
                if (select) {
                    select.name = `exercises[${index + 1}]`;
                    select.id = `exercise-select-${index + 1}`;
                }
            });
            exerciseIndex = exerciseItems.length + 1;
        }

        function setupSortable() {
            new Sortable(exerciseList, {
                handle: ".drag-handle",
                animation: 150,
                onEnd: updateExerciseIndices
            });
        }

        // Wait for the DOM to be ready
        document.addEventListener("DOMContentLoaded", function () {
            setupSortable();

        });


    } else {
        console.log('The item with id "exercise-list" does not exist');
    }





</script>
@endsection
