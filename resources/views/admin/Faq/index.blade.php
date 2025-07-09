@extends('admin.layouts.admin_master')

@section('title', 'FAQ Management')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4 class="card-title">FAQ List</h4>
                <div class="action-btn">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createModal" id="openModalBtn"><i class="bi bi-plus-circle"></i></button>
                </div>
            </div>

            <!-- Create FAQ Modal -->
            <div id="createModal" class="modal fade" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="createModalLabel">Create FAQ</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="createFaqForm" action="{{ route('admin.Faq.store') }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="question">Question:</label>
                                    <input type="text" id="createQuestion" name="question" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="category_id">Category:</label>
                                    <select name="category_id" id="category_id" class="form-control" required>
                                        <option value="" disabled selected>Select Category</option>
                                        @foreach($iconcategory as $ic)
                                            <option value="{{ $ic['id'] }}">{{ $ic['category_name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="answer">Answer:</label>
                                    <!-- Removed the textarea completely and replaced it with a div for CKEditor -->
                                    <div id="createAnswerEditor" class="form-control" style="min-height: 150px;"></div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Save FAQ</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Edit FAQ Modal -->
            <div id="editModal" class="modal fade" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel">Edit FAQ</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="editFaqForm" method="POST">
                            @csrf
                            @method('POST') <!-- Change method here to POST -->
                            <input type="hidden" id="faq_id" name="id">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="question">Question:</label>
                                    <input type="text" id="editQuestion" name="question" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label for="category_id">Category:</label>
                                    <select name="category_id" id="editCategoryId" class="form-control" required>
                                        <option value="" disabled selected>Select Category</option>
                                        @foreach($iconcategory as $ic)
                                            <option value="{{ $ic['id'] }}">{{ $ic['category_name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="answer">Answer:</label>
                                    <!-- Same approach here for Edit: Replace textarea with div for CKEditor -->
                                    <div id="editAnswerEditor" class="form-control" style="min-height: 150px;"></div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-borderless table-primary align-middle" id="allDataTable">
                        <thead class="table-light">
                            <tr>
                                <th>Sl No</th>
                                <th>Question</th>
                                <th>Category</th>
                                <th>Answer</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="faqTableBody">
                            @foreach($icons as $index => $faq)
                                <tr id="faq-row-{{ $faq->id }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $faq->question }}</td>
                                    <td>{{ getFaqCategory($faq->category_id) }}</td>
                                    <td>{!! $faq->answer !!}</td>
                                    <td>
                                        <button class="edit-faq-btn btn btn-warning btn-sm" data-faq-id="{{ $faq->id }}" data-question="{{ $faq->question }}" data-answer="{{ $faq->answer }}" data-category="{{ $faq->category_id }}">Edit</button>
                                        <form action="{{ route('admin.Faq.destroy', $faq->id) }}" method="POST" class="delete-faq-form" data-faq-id="{{ $faq->id }}" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm delete-btn">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>

<script>
    // Declare CKEditor instances for create and edit modals
    let createEditor, editEditor;

    // Initialize CKEditor for Create Modal
    ClassicEditor
        .create(document.querySelector('#createAnswerEditor'))
        .then(editor => {
            createEditor = editor;
        })
        .catch(error => {
            console.error(error);
        });

    // Initialize CKEditor for Edit Modal
    ClassicEditor
        .create(document.querySelector('#editAnswerEditor'))
        .then(editor => {
            editEditor = editor;
        })
        .catch(error => {
            console.error(error);
        });

    // Handle Create FAQ form submission
    $('#createFaqForm').submit(function(e) {
        e.preventDefault();
        
        // Before form submission, create hidden input to carry CKEditor data
        var answerData = createEditor.getData();
        var answerInput = $('<input>').attr({
            type: 'hidden',
            name: 'answer',
            value: answerData
        });
        $(this).append(answerInput);  // Append the hidden input with CKEditor data

        var formData = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    var newFaq = response.faq;
                    var newRow = '<tr id="faq-row-' + newFaq.id + '">' +
                                 '<td>' + ($('#faqTableBody tr').length + 1) + '</td>' +
                                 '<td>' + newFaq.question + '</td>' +
                                 '<td>' + newFaq.category_name + '</td>' +
                                 '<td>' + newFaq.answer + '</td>' +
                                 '<td><button class="edit-faq-btn btn btn-warning btn-sm" data-faq-id="' + newFaq.id + '">Edit</button>' +
                                 '<form action="/admin/faq/' + newFaq.id + '" method="POST" class="delete-faq-form" data-faq-id="' + newFaq.id + '" style="display:inline-block;">@csrf @method('DELETE')<button type="submit" class="btn btn-danger btn-sm">Delete</button></form></td>' +
                                 '</tr>';
                    $('#faqTableBody').append(newRow);
                    $('#createModal').modal('hide');

                    // SweetAlert Success message
                    Swal.fire({
                        icon: 'success',
                        title: 'FAQ Added!',
                        text: 'The FAQ has been successfully created.',
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Error creating the FAQ. Please try again.',
                });
            }
        });
    });

    // Handle Edit FAQ form submission
    $('#editFaqForm').submit(function(e) {
        e.preventDefault();

        // Before form submission, create hidden input to carry CKEditor data
        var answerData = editEditor.getData();
        var answerInput = $('<input>').attr({
            type: 'hidden',
            name: 'answer',
            value: answerData
        });
        $(this).append(answerInput);  // Append the hidden input with CKEditor data

        var formData = new FormData(this);

        var faqId = $('#faq_id').val(); // Retrieve the ID from the hidden input

        $.ajax({
            url: '/admin/faq/update/' + faqId,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    var faq = response.faq;
                    $('#faq-row-' + faq.id + ' td:nth-child(2)').text(faq.question);
                    $('#faq-row-' + faq.id + ' td:nth-child(3)').text(faq.category_name);
                    $('#faq-row-' + faq.id + ' td:nth-child(4)').html(faq.answer);
                    $('#editModal').modal('hide');

                    // SweetAlert Success message
                    Swal.fire({
                        icon: 'success',
                        title: 'FAQ Updated!',
                        text: 'The FAQ has been successfully updated.',
                    });

                    // Refresh the page to reflect updated data
                    location.reload();
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Error updating the FAQ. Please try again.',
                });
            }
        });
    });

    // Delete FAQ with SweetAlert confirmation
    $(document).on('click', '.delete-btn', function(e) {
        e.preventDefault();
        var form = $(this).closest('form');
        Swal.fire({
            title: 'Are you sure?',
            text: 'You won\'t be able to revert this!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    // Populate Edit Modal with FAQ data
    $(document).on('click', '.edit-faq-btn', function() {
        var faqId = $(this).data('faq-id');
        var question = $(this).data('question');
        var answer = $(this).data('answer');
        var category = $(this).data('category');

        $('#faq_id').val(faqId);
        $('#editQuestion').val(question);
        editEditor.setData(answer);
        $('#editCategoryId').val(category); // Ensure category is selected in the edit modal
        $('#editModal').modal('show');
    });

</script>

@endsection
