
<table id="datatable" class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Created At</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<!-- Scripts -->
<script>
    $(document).ready(function () {
        // Initialize DataTable with AJAX
        var table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('users.data') }}",
                type: 'GET'
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        // Open Edit Modal and Load Data
        $(document).on('click', '.editUser', function () {
            let userId = $(this).data('id');
            let userName = $(this).data('name');
            let userEmail = $(this).data('email');

            // Populate modal with data
            $('#editUserId').val(userId);
            $('#editUserName').val(userName);
            $('#editUserEmail').val(userEmail);
            $('#editUserName').focus();

            // Show modal
            $('#editUserModal').modal('show');
        });

        // Update User via AJAX
        $('#updateUserBtn').on('click', function () {
            let userId = $('#editUserId').val();
            let userName = $('#editUserName').val();
            let userEmail = $('#editUserEmail').val();

            // Clear previous errors
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            $.ajax({
                url: "{{ route('users.update', ':id') }}".replace(':id', userId),
                type: 'PUT',
                data: {
                    name: userName,
                    email: userEmail,
                    _token: "{{ csrf_token() }}"
                },
                success: function (response) {
                    if (response.success) {
                        toastr.success(response.message, 'Success');
                        $('#editUserModal').modal('hide');
                        table.ajax.reload();
                    }
                },
                error: function (response) {
                    if (response.status === 422) {
                        let errors = response.responseJSON.errors;
                        $.each(errors, function (key, value) {
                            let field = $('#editUser' + key.charAt(0).toUpperCase() + key.slice(1));
                            field.addClass('is-invalid');
                            field.after('<span class="invalid-feedback">' + value[0] + '</span>');
                        });
                        toastr.error('Validation failed', 'Error');
                    } else {
                        toastr.error('An error occurred', 'Error');
                    }
                }
            });
        });
    });
</script>
