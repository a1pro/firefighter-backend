@extends('admin.layouts.admin_master')

@section('title', 'Upload CSV Users')

@section('content')

<style>
    .form-container {
        padding: 20px;
        margin: 0 auto;
        max-width: 600px;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .form-container .form-group {
        margin-bottom: 15px;
    }

    .form-container label {
        font-weight: 500;
        margin-bottom: 8px;
    }

    .form-container select,
    .form-container input {
        height: 45px;
        font-size: 1rem;
    }

    .form-container button {
        margin-top: 15px;
        font-size: 1rem;
        padding: 10px 20px;
        border-radius: 5px;
    }

    .card-title {
        font-weight: 600;
        color: #333;
    }

    #progressBar {
        width: 0%;
        height: 10px;
        background-color: green;
        margin-top: 15px;
    }
</style>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card form-container">
            <div class="card-header bg-primary text-white text-center">
                <h4 class="card-title mb-0">Upload CSV Users </h4>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                <form id="csvForm" action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Agency Selection -->
                    <div class="form-group">
                        <label for="agency">Select Agency</label>
                        <select name="agency" id="agency" class="form-control" required>
                            <option value="">-- Select Agency --</option>
                            @foreach ($agencies as $agency)
                                <option value="{{ $agency->id }}">{{ $agency->name }}</option>
                            @endforeach
                        </select>
                    </div>

<!-- Sample CSV Download -->
<div class="form-group text-center">
    <a href="{{ asset('csv/sample_users.csv') }}" class="btn btn-info" download>
        <i class="fas fa-download me-2"></i> Download Sample CSV
    </a>
</div>
                    <!-- CSV File Input -->
                    <div class="form-group">
                        <label for="csv_file">Choose CSV File</label>
                        <input type="file" name="csv_file" id="csv_file" class="form-control" accept=".csv" required>
                    </div>

                    <!-- Submit Button -->
                    <div class="text-center">
                        <button type="submit" class="btn btn-success" id="submitBtn">
                            <i class="fas fa-upload me-2"></i> Upload CSV
                        </button>
                    </div>

                    <!-- Progress Bar -->
                    <div id="progressContainer" style="display:none;">
                        <div id="progressBar"></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')

<script type="text/javascript">
$(document).ready(function() {
    // Initialize select2 for agency dropdown
    $('#agency').select2({
        placeholder: "Select Agency",
        allowClear: true,
        width: "100%",
    });

    // SweetAlert2 confirmation before form submission

    


});
$(document).ready(function() {
    // Disable the submit button and show progress bar on form submit
    $('#csvForm').on('submit', function(e) {
        e.preventDefault();

        // Disable the submit button to prevent multiple submissions
        $('#submitBtn').prop('disabled', true);

        // Show progress bar
        $('#progressContainer').show();
        var progress = 0;
        var interval = setInterval(function() {
            progress += 10;
            $('#progressBar').css('width', progress + '%');
            if (progress >= 100) {
                clearInterval(interval);
            }
        }, 500);

        // Create a FormData object
        var formData = new FormData(this);

        // Make AJAX request to import CSV
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                $('#progressContainer').hide();
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: response.message,
                    }).then(() => {
                        // Clear the agency and CSV file fields
                        $('#agency').val('').trigger('change'); // Clear the agency dropdown
                        $('#csv_file').val(''); // Clear the CSV file input
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: response.message,
                    });
                }
                $('#submitBtn').prop('disabled', false);
            },
            error: function(xhr, status, error) {
                $('#progressContainer').hide();
                Swal.fire({
                    icon: 'error',
                    title: 'An error occurred during upload!',
                });
                $('#submitBtn').prop('disabled', false);
            }
        });
    });
});
</script>
@endsection

