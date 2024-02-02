<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
    	<title><?php echo (!empty($title)) ? 'Title-'.$title: 'HealthHub Pro'; ?></title>
    	<link rel="icon" type="image/gif" href="{{ asset('admin-assets/img/logo.gif') }}">
    	<!-- Google Font: Source Sans Pro -->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
		<!-- Font Awesome -->
		<link rel="stylesheet" href="{{asset('admin-assets/plugins/fontawesome-free/css/all.min.css')}}">
		<!-- Theme style -->
		<link rel="stylesheet" href="{{asset('admin-assets/css/adminlte.min.css')}}">
		<link rel="stylesheet" href="{{asset('admin-assets/css/custom.css')}}">
	</head>
	<body class="hold-transition login-page">
		<div class="login-box">
			<!-- /.login-logo -->
            @include('admin.message')
			<div class="card card-outline card-primary">
                <div class="card-header text-center">
                    <img src="{{ asset('admin-assets/img/new-logo.gif') }}" alt="logo" style="height: 10rem;">
                </div>

                <div class="container">

                    <div class="login-form" style="padding: 1rem;">
                        <form action="{{ route('account.resetPassword') }}" method="post">
                            @csrf
                            <h4 class="modal-title">Reset Password</h4>

                            <div class="form-group">
                                <input type="password" class="form-control" @error('password') is-invalid @enderror placeholder="Password" required="required" name="password" id="password">
                                @error('password')
                                    <p class="invalid-feedback">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="form-group">
                                <input type="password" class="form-control" @error('password_confirmation') is-invalid @enderror placeholder="Password Confirmation" required="required" name="password_confirmation" id="password_confirmation">
                                @error('password_confirmation')
                                    <p class="invalid-feedback">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="form-group">
                                <input type="text" hidden readOnly class="form-control" @error('phone_number') is-invalid @enderror placeholder="Phone" required="required" name="phone_number" id="phone_number" >
                                @error('phone_number')
                                    <p class="invalid-feedback">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="form-group">
                                <input type="text" hidden readOnly class="form-control" @error('code') is-invalid @enderror placeholder="Phone" required="required" name="code" id="code" >
                                @error('code')
                                    <p class="invalid-feedback">{{ $message }}</p>
                                @enderror
                            </div>

                            <input type="submit" class="btn btn-dark btn-block btn-lg" value="Reset">
                        </form>
                    </div>
                </div>
        </div>
        <!-- /.card -->
    </div>
    <!-- ./wrapper -->
    <!-- jQuery -->
    <script src="{{asset('admin-assets/plugins/jquery/jquery.min.js')}}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{asset('admin-assets/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <!-- AdminLTE App -->
    <script src="{{asset('admin-assets/js/adminlte.min.js')}}"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="{{asset('admin-assets/js/demo.js')}}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Function to get URL parameters by name
            function getParameterByName(name, url) {
                if (!url) url = window.location.href;
                name = name.replace(/[\[\]]/g, "\\$&");
                var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
                    results = regex.exec(url);
                if (!results) return null;
                if (!results[2]) return '';
                return decodeURIComponent(results[2].replace(/\+/g, " "));
            }

            // Get the values of phone_number and code from the URL
            var phoneNumber = getParameterByName('phone_number');
            var code = getParameterByName('code');

            document.getElementById("phone_number").value = phoneNumber;
            document.getElementById("code").value = code;


        });
    </script>
</body>
</html>

