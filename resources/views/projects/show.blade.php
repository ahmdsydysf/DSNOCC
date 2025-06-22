@extends('layouts.app')

@section('page_title', 'Project Details')

@section('page_actions')
    <a href="{{ route('projects.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Projects
    </a>
    <a href="{{ route('projects.edit', $project) }}" class="btn btn-warning">
        <i class="fas fa-edit"></i> Edit Project
    </a>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Project Details</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr><th>Name:</th><td>{{ $project->name }}</td></tr>
                    <tr><th>Code:</th><td>{{ $project->code ?? 'Not specified' }}</td></tr>
                    <tr><th>Created At:</th><td>{{ $project->created_at->format('M d, Y H:i') }}</td></tr>
                    <tr><th>Created By:</th><td>{{ $project->creator->name ?? 'N/A' }}</td></tr>
                    <tr><th>Last Updated:</th><td>{{ $project->updated_at->format('M d, Y H:i') }}</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex flex-wrap gap-3">
            <!-- Create Folder Card -->
            <div class="card text-center" style="width: 180px; cursor:pointer;" data-toggle="modal" data-target="#createFolderModal">
                <div class="card-body d-flex flex-column align-items-center justify-content-center" style="height: 120px;">
                    <i class="fas fa-plus fa-2x mb-2"></i>
                    <span>Create Folder</span>
                </div>
            </div>
            <!-- Upload File Card -->
            <div class="card text-center" style="width: 180px; cursor:pointer;" data-toggle="modal" data-target="#uploadProjectFileModal">
                <div class="card-body d-flex flex-column align-items-center justify-content-center" style="height: 120px;">
                    <i class="fas fa-upload fa-2x mb-2"></i>
                    <span>Upload File</span>
                </div>
            </div>
            <!-- Folder Cards -->
            @foreach($project->folders()->whereNull('parent_id')->get() as $folder)
                <div class="card" style="width: 180px;">
                    <div class="card-body">
                        <h6 class="card-title">{{ $folder->name }}</h6>
                        <p class="mb-1"><small>By: {{ $folder->creator->name ?? 'N/A' }}</small></p>
                        <p class="mb-1"><small>Created: {{ $folder->created_at->format('M d, Y') }}</small></p>
                        <p class="mb-1"><small>Folders: {{ $folder->children()->count() }}</small></p>
                        <p class="mb-1"><small>Files: {{ $folder->files()->count() }}</small></p>
                        <div class="d-flex justify-content-between mt-2">
                            <a href="{{ route('folders.show', $folder) }}" class="btn btn-primary btn-sm">View</a>
                            @if(auth()->user()->is_admin)
                            <button class="btn btn-danger btn-sm delete-folder-btn" data-folder-id="{{ $folder->id }}"><i class="fas fa-trash"></i></button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Project Files Section -->
@if($project->files->count())
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Project Files</h5>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-3">
                    @foreach($project->files as $file)
                        <div class="card border-info" style="width: 180px;">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fas fa-file"></i> {{ $file->name }}</h6>
                                <p class="mb-1"><small>By: {{ $file->creator->name ?? 'N/A' }}</small></p>
                                <p class="mb-1"><small>Uploaded: {{ $file->created_at->format('M d, Y') }}</small></p>
                                <a href="{{ route('files.download', $file) }}" class="btn btn-success btn-sm">Download</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Create Folder Modal -->
<div class="modal fade" id="createFolderModal" tabindex="-1" role="dialog" aria-labelledby="createFolderModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="createFolderForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="createFolderModalLabel">Create New Folder</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="folderName" class="form-label">Folder Name</label>
                        <input type="text" class="form-control" id="folderName" name="name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Upload File Modal -->
<div class="modal fade" id="uploadProjectFileModal" tabindex="-1" role="dialog" aria-labelledby="uploadProjectFileModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="uploadProjectFileForm" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadProjectFileModalLabel">Upload File</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="projectFileInput" class="form-label">Choose File</label>
                        <input type="file" class="form-control-file" id="projectFileInput" name="file" required>
                        <small class="form-text text-muted">Server upload limit: <span id="serverLimit">Checking...</span></small>
                        <div id="fileInfo" class="mt-2" style="display: none;">
                            <small class="text-info">Selected file: <span id="fileName"></span> (<span id="fileSize"></span>)</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Create Folder Form Submission
    document.getElementById('createFolderForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const name = document.getElementById('folderName').value;

        // Disable submit button to prevent double submission
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';

        fetch("{{ route('folders.store', $project) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ name: name })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                // Close modal and reload page
                $('#createFolderModal').modal('hide');
                location.reload();
            } else {
                alert('Error creating folder: ' + (data.message || 'Unknown error'));
                // Re-enable submit button
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Create';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error creating folder. Please try again.');
            // Re-enable submit button
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Create';
        });
    });

    // Upload File Form Submission
    document.getElementById('uploadProjectFileForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        // Disable submit button to prevent double submission
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';

        fetch("{{ route('projects.files.store', $project) }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                // Close modal and reload page
                $('#uploadProjectFileModal').modal('hide');
                location.reload();
            } else {
                alert('Error uploading file: ' + (data.message || 'Unknown error'));
                // Re-enable submit button
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Upload';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error uploading file. Please check the console for details.');
            // Re-enable submit button
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Upload';
        });
    });

    // Delete Folder Event Listeners
    document.querySelectorAll('.delete-folder-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if(confirm('Are you sure you want to delete this folder?')) {
                const folderId = this.dataset.folderId;

                // Disable button to prevent double submission
                this.disabled = true;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                fetch(`/folders/${folderId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        location.reload();
                    } else {
                        alert('Error deleting folder: ' + (data.message || 'Unknown error'));
                        // Re-enable button
                        this.disabled = false;
                        this.innerHTML = '<i class="fas fa-trash"></i>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting folder. Please try again.');
                    // Re-enable button
                    this.disabled = false;
                    this.innerHTML = '<i class="fas fa-trash"></i>';
                });
            }
        });
    });

    // Reset forms when modals are closed
    $('#createFolderModal').on('hidden.bs.modal', function () {
        document.getElementById('createFolderForm').reset();
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Create';
    });

    $('#uploadProjectFileModal').on('hidden.bs.modal', function () {
        document.getElementById('uploadProjectFileForm').reset();
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Upload';
        // Hide file info
        document.getElementById('fileInfo').style.display = 'none';
    });

    // Show file information when file is selected
    document.getElementById('projectFileInput').addEventListener('change', function() {
        const file = this.files[0];
        const fileInfo = document.getElementById('fileInfo');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');

        if (file) {
            fileName.textContent = file.name;
            fileSize.textContent = formatFileSize(file.size);
            fileInfo.style.display = 'block';
        } else {
            fileInfo.style.display = 'none';
        }
    });

    // Check server upload limits
    function checkServerLimits() {
        fetch("{{ route('projects.files.store', $project) }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: new FormData() // Empty form data to trigger the limit check
        })
        .then(response => response.json())
        .then(data => {
            if (data.message && data.message.includes('upload_max_filesize')) {
                // Extract limit from error message
                const match = data.message.match(/upload_max_filesize=([^,]+)/);
                if (match) {
                    document.getElementById('serverLimit').textContent = match[1];
                } else {
                    document.getElementById('serverLimit').textContent = 'Unknown';
                }
            } else {
                document.getElementById('serverLimit').textContent = '500MB (configured)';
            }
        })
        .catch(error => {
            document.getElementById('serverLimit').textContent = 'Error checking limits';
        });
    }

    // Check limits when modal opens
    $('#uploadProjectFileModal').on('show.bs.modal', function () {
        checkServerLimits();
    });

    // Helper function to format file size
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
});
</script>
@endsection
