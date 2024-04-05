@extends('admin.layouts.app')
@section('content')
<div class="container mt-5">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">User Details</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="profileImage">Profile Image:</label>
                        <div class="input-group">
                            @if ($dietician->image != null)
                                <img src="{{ asset('storage/uploads/dietician/profile/' . $dietician->image) }}" class="img-fluid mr-2" width="50">
                            @else
                                <img src="{{ asset('admin-assets/img/default-150x150.png') }}" alt="" class="img-fluid mr-2" width="50">
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="firstName">First Name:</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                            </div>
                            <input type="text" id="firstName" class="form-control" value="{{ $dietician->first_name }}" disabled>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="lastName">Last Name:</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                            </div>
                            <input type="text" id="lastName" class="form-control" value="{{ $dietician->last_name }}" disabled>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            </div>
                            <input type="email" id="email" class="form-control" value="{{ $dietician->email }}" disabled>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="phoneNumber">Phone Number:</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                            </div>
                            <input type="text" id="phoneNumber" class="form-control" value="{{ $dietician->phone_number }}" disabled>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="speciality">Speciality:</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-user-md"></i></span>
                            </div>
                            <input type="text" id="speciality" class="form-control" value="{{ $dietician->speciality }}" disabled>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="cv">CV:</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-file-pdf"></i></span>
                            </div>
                            <a href="{{ asset('storage/uploads/dietician/cv/' . $dietician->cv) }}" target="_blank" class="btn btn-outline-primary btn-sm">View CV</a>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description">Description:</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-info"></i></span>
                            </div>
                            <textarea id="description" class="form-control" disabled>{{ $dietician->description }}</textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="esewaId">Esewa ID:</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-money-bill"></i></span>
                            </div>
                            <input type="text" id="esewaId" class="form-control" value="{{ $dietician->esewa_id }}" disabled>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="bookingAmount">Booking Amount:</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-money-bill"></i></span>
                            </div>
                            <input type="text" id="bookingAmount" class="form-control" value="{{ $dietician->booking_amount }}" disabled>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="bio">Bio:</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-info"></i></span>
                            </div>
                            <textarea id="bio" class="form-control" disabled>{{ $dietician->bio }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                @if($dietician->approved_status != 1)
                <a href="#" onClick="approveDietician('{{ $dietician->id }}')" class="text-danger w-4 h-4 mr-1">
                    <button class="btn btn-primary">Approve</button>
                </a>
                <!-- Add this modal for confirming deletion  -->
                <div class="modal fade" id="approveConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="approveConfirmationModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="approveConfirmationModalLabel">Confirm Approval</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p>Are you sure you want to approve this dietician?</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-danger" id="confirmApproveBtn">Approve</button>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>


        </div>
    </div>
</div>
@endsection

@section('customJs')

<script>
    function approveDietician(id) {
        var url = '{{ route('dieticians.approveStatus', "ID") }}';
        var newUrl = url.replace("ID", id);

        // Show the approve confirmation modal
        $('#approveConfirmationModal').modal('show');

        // Handle the click on the "Approve" button in the modal
        $('#confirmApproveBtn').click(function() {
            // Close the modal
            $('#approveConfirmationModal').modal('hide');

            // Perform the approve action
            $.ajax({
                url: newUrl,
                type: 'put',
                data: {},
                dataType: 'json',
                success: function(response) {
                    $("button[type=submit]").prop('disabled', false);
                    window.location.href = "{{ route('dieticians.index') }}";
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            });
        });
    }

</script>

@endsection
