@extends('admin.layouts.admin_master')

@section('title', 'Standard Icons')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4 class="card-title">List</h4>
                <div class="action-btn">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createModal" id="openModalBtn">
                        <i class="bi bi-plus-circle"></i> Add New Icon
                    </button>
                </div>
            </div>

            <!-- Search and Filter Section -->
            <div class="card-body border-bottom">
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group mb-3">
                            <input type="text" id="searchInput" class="form-control" placeholder="Search icons by name..." aria-label="Search icons">
                            <button class="btn btn-outline-secondary" type="button" id="searchButton">
                                <i class="bi bi-search"></i> Search
                            </button>
                            <button class="btn btn-outline-danger" type="button" id="resetSearchButton" style="display: none;">
                                <i class="bi bi-x-circle"></i> Clear
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select id="categoryFilter" class="form-select">
                            <option value="">All Categories</option>
                            @if(isset($iconcategory) && !empty($iconcategory))
                                @foreach($iconcategory as $ic)
                                    <option value="{{ $ic['id'] }}">{{ $ic['category_name'] }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select id="paginationLimit" class="form-select">
                            <option value="5" {{ request('per_page', 10) == 5 ? 'selected' : '' }}>5 per page</option>
                            <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10 per page</option>
                            <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50 per page</option>
                            <option value="all">All</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Create Icon Modal -->
            <div id="createModal" class="modal fade" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
                <!-- Modal content remains the same as before -->
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="createModalLabel">Upload New Icon</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="createIconForm" action="{{ route('icons.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Icon Name:</label>
                                    <input type="text" id="createName" name="name" class="form-control" required>
                                    <div class="invalid-feedback" id="nameError"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Category:</label>
                                    <select name="category_id" id="category_id" class="form-control" required>
                                        <option value="" disabled selected>Select Category</option>
                                        @if(isset($iconcategory) && !empty($iconcategory))
                                            @foreach($iconcategory as $ic)
                                                <option value="{{ $ic['id'] }}">{{ $ic['category_name'] }}</option>
                                            @endforeach
                                        @else
                                            <option value="" disabled>No categories available</option>
                                        @endif
                                    </select>
                                    <div class="invalid-feedback" id="categoryError"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="icon" class="form-label">Icon File:</label>
                                    <input type="file" id="createIcon" name="icon" accept="image/*" class="form-control" required>
                                    <div class="invalid-feedback" id="iconError"></div>
                                    <div class="form-text">Accepted formats: PNG, JPG, JPEG, GIF, SVG. Max size: 2MB</div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" id="createSubmitBtn">Upload Icon</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Edit Icon Modal -->
            <div id="editModal" class="modal fade" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                <!-- Modal content remains the same as before -->
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel">Edit Icon</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="editIconForm" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('POST')
                            <input type="hidden" id="edit_icon_id" name="id">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="editName" class="form-label">Icon Name:</label>
                                    <input type="text" id="editName" name="name" class="form-control" required>
                                    <div class="invalid-feedback" id="editNameError"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_category_id" class="form-label">Category:</label>
                                    <select name="category_id" id="edit_category_id" class="form-control" required>
                                        <option value="" disabled selected>Select Category</option>
                                        @if(isset($iconcategory) && !empty($iconcategory))
                                            @foreach($iconcategory as $ic)
                                                <option value="{{ $ic['id'] }}">{{ $ic['category_name'] }}</option>
                                            @endforeach
                                        @else
                                            <option value="" disabled>No categories available</option>
                                        @endif
                                    </select>
                                    <div class="invalid-feedback" id="editCategoryError"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="editIcon" class="form-label">Icon File (Optional):</label>
                                    <input type="file" id="editIcon" name="icon" accept="image/*" class="form-control">
                                    <div class="invalid-feedback" id="editIconError"></div>
                                    <div class="form-text">Leave empty to keep current icon</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Current Icon:</label>
                                    <div id="currentIconPreview" class="mt-2"></div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" id="editSubmitBtn">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div id="loadingSpinner" class="text-center" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading icons...</p>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-borderless table-primary align-middle" id="allDataTable">
                        <thead class="table-light">
                            <tr>
                                <th>Sl No</th>
                                <th>Icon Name</th>
                                <th>Icon Category</th>
                                <th>Icon Image</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="iconsTableBody">
                            @include('admin.standardicon.icons_table', ['icons' => $icons])
                        </tbody>
                    </table>
                </div>
               <div id="paginationContainer" class="d-flex flex-column flex-md-row justify-content-between align-items-center my-3">
    <!-- Pagination Info -->
    <div id="paginationInfo" class="text-muted mb-2 mb-md-0">
        @if($icons->count() > 0)
            Showing <strong>{{ $icons->firstItem() }}</strong> to <strong>{{ $icons->lastItem() }}</strong> of <strong>{{ $icons->total() }}</strong> entries
        @else
            <span class="text-danger">No entries found</span>
        @endif
    </div>

    <!-- Pagination Links -->
    <div>
        {{ $icons->appends(['per_page' => request('per_page', 10)])->links('pagination::bootstrap-5') }}
    </div>
</div>

                
                <div id="noResults" class="alert alert-warning text-center" style="display: none;">
                    No icons found matching your search criteria.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        let currentPage = 1;
        let searchQuery = '';
        let categoryFilter = '';
        let paginationLimit = $('#paginationLimit').val();
        
        // CSRF Token setup
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // Function to load icons with filters and pagination
        function loadIcons(page = 1, search = '', category = '', limit = paginationLimit) {
            $('#loadingSpinner').show();
            $('#iconsTableBody').hide();
            $('#paginationContainer').hide();
            $('#noResults').hide();
            
            $.ajax({
                url: "{{ route('icons.index') }}",
                type: 'GET',
                data: {
                    page: page,
                    search: search,
                    category: category,
                    per_page: limit,
                    ajax: true
                },
                success: function(response) {
                    $('#iconsTableBody').html(response.icons_html);
                    $('#paginationContainer').html(response.pagination_html);
                    
                    $('#loadingSpinner').hide();
                    $('#iconsTableBody').show();
                    $('#paginationContainer').show();
                    
                    if (response.icons_count === 0) {
                        $('#noResults').show();
                    }
                    
                    // Update pagination info
                    $('#paginationInfo').text(
                        `Showing ${response.from} to ${response.to} of ${response.total} entries`
                    );
                    
                    // Update current page and filters
                    currentPage = page;
                    searchQuery = search;
                    categoryFilter = category;
                    paginationLimit = limit;
                },
                error: function(xhr) {
                    $('#loadingSpinner').hide();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to load icons. Please try again.'
                    });
                }
            });
        }
        
        // Search functionality
        $('#searchButton').on('click', function() {
            const searchValue = $('#searchInput').val().trim();
            loadIcons(1, searchValue, $('#categoryFilter').val(), paginationLimit);
            
            if (searchValue) {
                $('#resetSearchButton').show();
            }
        });
        
        // Category filter change
        $('#categoryFilter').on('change', function() {
            loadIcons(1, $('#searchInput').val().trim(), $(this).val(), paginationLimit);
            
            if ($(this).val()) {
                $('#resetSearchButton').show();
            }
        });
        
        // Pagination limit change
        $('#paginationLimit').on('change', function() {
            const limit = $(this).val();
            loadIcons(1, $('#searchInput').val().trim(), $('#categoryFilter').val(), limit);
        });
        
        // Reset search and filters
        $('#resetSearchButton').on('click', function() {
            $('#searchInput').val('');
            $('#categoryFilter').val('');
            loadIcons(1, '', '', paginationLimit);
            $(this).hide();
        });
        
        // Handle pagination clicks
        $(document).on('click', '.pagination a', function(e) {
            e.preventDefault();
            const page = $(this).attr('href').split('page=')[1];
            loadIcons(page, searchQuery, categoryFilter, paginationLimit);
        });
        
        // Create Icon Form Submission
        $('#createIconForm').on('submit', function(e) {
            e.preventDefault();
            
            // Reset validation
            $('.invalid-feedback').hide();
            $('.is-invalid').removeClass('is-invalid');
            
            // Disable submit button
            $('#createSubmitBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Uploading...');
            
            var formData = new FormData(this);
            
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        // Reload the icons with current filters
                        loadIcons(currentPage, searchQuery, categoryFilter, paginationLimit);
                        
                        $('#createModal').modal('hide');
                        $('#createIconForm')[0].reset();
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message || 'Icon uploaded successfully.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON.errors;
                    if (errors) {
                        $.each(errors, function(key, value) {
                            $('#' + key + 'Error').text(value[0]).show();
                            $('#' + key).addClass('is-invalid');
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'An error occurred while uploading the icon.'
                        });
                    }
                },
                complete: function() {
                    $('#createSubmitBtn').prop('disabled', false).html('Upload Icon');
                }
            });
        });

        // Edit Icon - Open Modal
        $(document).on('click', '.edit-icon-btn', function() {
            var iconId = $(this).data('icon-id');
            var url = "{{ route('icons.edit', ':id') }}".replace(':id', iconId);
            
            // Reset validation
            $('#editIconForm .invalid-feedback').hide();
            $('#editIconForm .is-invalid').removeClass('is-invalid');
            
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        $('#edit_icon_id').val(response.icon.id);
                        $('#editName').val(response.icon.name);
                        $('#edit_category_id').val(response.icon.category_id);
                        
                        // Show current icon preview
                        var iconUrl = "{{ Storage::exists('placeholder') ? Storage::url('placeholder') : asset('storage/placeholder') }}"
                            .replace('placeholder', response.icon.path);
                        
                        $('#currentIconPreview').html(`
                            <img src="${iconUrl}" width="50" height="50" class="img-thumbnail" alt="${response.icon.name}">
                        `);
                        
                        // Set form action
                        $('#editIconForm').attr('action', "{{ route('icons.update', ':id') }}".replace(':id', response.icon.id));
                        
                        $('#editModal').modal('show');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message || 'Failed to fetch icon details.'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to fetch icon details. Please try again.'
                    });
                }
            });
        });

        // Edit Icon Form Submission
        $('#editIconForm').on('submit', function(e) {
            e.preventDefault();
            
            // Reset validation
            $('.invalid-feedback').hide();
            $('.is-invalid').removeClass('is-invalid');
            
            // Disable submit button
            $('#editSubmitBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
            
            var formData = new FormData(this);
            var iconId = $('#edit_icon_id').val();
            var url = "{{ route('icons.update', ':id') }}".replace(':id', iconId);
            
            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        // Reload the icons with current filters
                        loadIcons(currentPage, searchQuery, categoryFilter, paginationLimit);
                        
                        $('#editModal').modal('hide');
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message || 'Icon updated successfully.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON.errors;
                    if (errors) {
                        $.each(errors, function(key, value) {
                            $('#edit' + key.charAt(0).toUpperCase() + key.slice(1) + 'Error').text(value[0]).show();
                            $('#edit' + key.charAt(0).toUpperCase() + key.slice(1)).addClass('is-invalid');
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'An error occurred while updating the icon.'
                        });
                    }
                },
                complete: function() {
                    $('#editSubmitBtn').prop('disabled', false).html('Save Changes');
                }
            });
        });

        // Delete Icon
        
        $(document).on('click', '.delete-icon-btn', function() {
            var iconId = $(this).data('icon-id');
            var url = "{{ route('icons.destroy', ':id') }}".replace(':id', iconId);
            
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        success: function(response) {
                            if (response.success) {
                                // Reload the icons with current filters
                                loadIcons(currentPage, searchQuery, categoryFilter, paginationLimit);
                                
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: response.message || 'Icon has been deleted.',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: response.message || 'Failed to delete the icon.'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Failed to delete the icon. Please try again.'
                            });
                        }
                    });
                }
            });
        });

        // Reset forms when modals are closed
        $('#createModal, #editModal').on('hidden.bs.modal', function() {
            $(this).find('form')[0].reset();
            $(this).find('.invalid-feedback').hide();
            $(this).find('.is-invalid').removeClass('is-invalid');
        });
        
        // Show reset button if there's text in search input
        $('#searchInput').on('input', function() {
            if ($(this).val().trim()) {
                $('#resetSearchButton').show();
            } else {
                $('#resetSearchButton').hide();
            }
        });
        
        // Enable search on pressing Enter key
        $('#searchInput').on('keypress', function(e) {
            if (e.which === 13) {
                $('#searchButton').click();
            }
        });
    });
</script>

@endsection