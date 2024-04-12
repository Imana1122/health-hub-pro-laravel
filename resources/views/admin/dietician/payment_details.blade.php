@extends('admin.layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Dietician Details</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="profileImage">Profile Image:</label>
                        <div class="input-group">
                            @if ($dietician->image != null)
                                <img src="{{ asset('storage/uploads/dietician/profile/' . $dietician->image) }}" class="img-fluid mr-2" width="100">
                            @else
                                <img src="{{ asset('admin-assets/img/default-150x150.png') }}" alt="" class="img-fluid mr-2" width="100">
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="firstName">Name:</label>
                        <p>{{ $dietician->first_name }} {{ $dietician->last_name }}</p>
                    </div>

                    <div class="form-group">
                        <label for="email">Email:</label>
                        <p>{{ $dietician->email }}</p>
                    </div>
                    <div class="form-group">
                        <label for="phoneNumber">Phone Number:</label>
                        <p>{{ $dietician->phone_number }}</p>
                    </div>
                    <div class="form-group">
                        <label for="speciality">Speciality:</label>
                        <p>{{ $dietician->speciality }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="cv">CV:</label>
                        <a href="{{ asset('storage/uploads/dietician/cv/' . $dietician->cv) }}" target="_blank" class="btn btn-outline-primary btn-sm">View CV</a>
                    </div>
                    <div class="form-group">
                        <label for="description">Description:</label>
                        <p>{{ $dietician->description }}</p>
                    </div>
                    <div class="form-group">
                        <label for="esewaId">Esewa ID:</label>
                        <p>{{ $dietician->esewa_id }}</p>
                    </div>
                    <div class="form-group">
                        <label for="bookingAmount">Booking Amount:</label>
                        <p>{{ $dietician->booking_amount }}</p>
                    </div>
                    <div class="form-group">
                        <label for="bio">Bio:</label>
                        <p>{{ $dietician->bio }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Details</h5>
            <div class="card-tools">
                <input type="number" id="yearInput" placeholder="Enter year (YYYY)" min="1000" max="9999" required>
                <input type="number" id="monthInput" placeholder="Enter month (MM)" min="1" max="12" required>
                <button class="btn btn-primary" onclick="getPaymentDetails()">Get</button>
            </div>
        </div>
        <div  class="card-body ">
            <div class="container">
                <div class="row">
                    @foreach ($dieticianBookings as $booking)
                    <div class="col-md-4 mb-3">
                        <div class="bg-green p-4 rounded">
                            <div class="d-flex align-items-center mb-3">
                                <img src="{{ asset('storage/uploads/users/'.$booking->user->image) }}" alt="User" style="border-radius: 50%; width: 60px; height: 60px;">
                                <div class="ml-3">
                                    <div class="font-weight-bold">{{ $booking->user->name }}</div>
                                    <div class="font-weight-bold text-muted">Subscribed Date:</div>
                                    <div class="text-muted">{{ $booking->updated_at }}</div>
                                    <div class="font-weight-bold text-muted">End Date:</div>
                                    <div class="text-muted">{{ $booking->end_datetime }}</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-lightblue text-center" style="width: 20px; height: 20px;">
                                    <i class="fas fa-arrow-alt-circle-left received-message text-red"></i>
                                </div>
                                <div class="ml-2">
                                    <span>Received: {{ $booking->received_messages }}</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mt-2">
                                <div class="rounded-circle bg-lightblue text-center" style="width: 20px; height: 20px;">
                                    <i class="fas fa-arrow-alt-circle-right text-blue"></i>
                                </div>
                                <div class="ml-2">
                                    <span>Sent: {{ $booking->sent_messages }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>


        </div>
        <div class="card-footer clearfix">
            {{ $dieticianBookings->links() }}
        </div>

    </div>
    <div class="card">
        <div class="card-header">
            <h3>Payment Details</h3>
        </div>
        <div class="card-body">
            <div class="my-3">
                @if (!empty($dieticianPayment))
                <div class="form-control">Paid on: - {{ $dieticianPayment->updated_at }}</div>
                <div class="form-control">Paid Amount: - {{ $dieticianPayment->amount }}</div>

                @endif
            </div>
            @if($dieticianBookings->isNotEmpty())
            <div>
                <input type="number" id="amountInput" placeholder="Salary Amount" required/>

                <a href="#" onClick="payDietician('{{ $dietician->id }}')" class="text-danger w-4 h-4 mr-1">
                    <button class="btn btn-primary">
                        @if (!empty($dieticianPayment))
                            Update
                        @else
                            Pay
                        @endif
                    </button>
                </a>
                <!-- Add this modal for confirming deletion  -->
                <div class="modal fade" id="paymentConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="paymentConfirmationModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="paymentConfirmationModalLabel">Confirm Approval</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p>Are you sure you want to pay this dietician the amount?</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-danger" id="confirmPaymentBtn">Pay</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('customJs')

<script>
$(document).ready(function() {
    // Function to parse query parameters from URL
    function getQueryParam(name) {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(name);
    }

    // Retrieve the values from URL parameters
    var paymentYear = getQueryParam('year');
    var paymentMonth = getQueryParam('month');

    // If URL parameters are not provided, fallback to current year and month
    if (!paymentYear || !paymentMonth) {
        var currentDate = new Date();
        paymentYear = currentDate.getFullYear().toString();
        // Add 1 to month because getMonth() returns zero-based index
        paymentMonth = (currentDate.getMonth() + 1).toString();
    }

    // Set the values in the input fields
    document.getElementById('yearInput').value = paymentYear;
    document.getElementById('monthInput').value = paymentMonth;
});


    function getPaymentDetails() {
        var year = document.getElementById('yearInput').value;
        var month = document.getElementById('monthInput').value;

        var url = '{{ route('dieticians.payment-details', ['id' => $dietician->id]) }}';
        url += '?year=' + year + '&month=' + month;
        // Store the values in local storage
        localStorage.setItem('paymentYear', year);
        localStorage.setItem('paymentMonth', month);
        // Redirect to the generated URL
        window.location.href = url;
    }

    function payDietician(id) {
        var year = document.getElementById('yearInput').value;
        var month = document.getElementById('monthInput').value;
        var amount = document.getElementById('amountInput').value;
        var url = '{{ route('dieticians.payment', "ID") }}';
        var newUrl = url.replace("ID", id);

        // Show the approve confirmation modal
        $('#paymentConfirmationModal').modal('show');

        // Handle the click on the "Approve" button in the modal
        $('#confirmPaymentBtn').click(function() {
            // Close the modal
            $('#paymentConfirmationModal').modal('hide');

            // Perform the approve action
            $.ajax({
                url: newUrl,
                type: 'put',
                data: {
                    'year':year,
                    'month':month,
                    'amount':amount
                },
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
