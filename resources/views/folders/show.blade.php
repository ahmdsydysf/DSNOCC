@extends('layouts.app')

@section('page_title', 'Folder: ' . $folder->name)

@section('content')
<!-- TEST MODAL BUTTON AND MODAL -->
<button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#testModal">
  Launch test modal
</button>
<div class="modal fade" id="testModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Test Modal</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>This is a test modal.</p>
      </div>
    </div>
  </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Folder Details</h5>
                <a href="{{ route('folders.downloadZip', $folder) }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-file-archive"></i> Download as Zip
                </a>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr><th>Name:</th><td>{{ $folder->name }}</td></tr>
                    <tr><th>Created By:</th><td>{{ $folder->creator->name ?? 'N/A' }}</td></tr>
                    <tr><th>Created At:</th><td>{{ $folder->created_at->format('M d, Y H:i') }}</td></tr>
                    @if($folder->parent)
                    <tr><th>Parent Folder:</th><td><a href="{{ route('folders.show', $folder->parent) }}">{{ $folder->parent->name }}</a></td></tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex flex-wrap gap-3">
            <!-- Create Nested Folder Card -->
            <div class="card text-center" style="width: 180px; cursor:pointer;" data-toggle="modal" data-target="#createFolderModal">
                <div class="card-body d-flex flex-column align-items-center justify-content-center" style="height: 120px;">
                    <i class="fas fa-plus fa-2x mb-2"></i>
                    <span>Create Folder</span>
                </div>
            </div>
            <!-- Upload File Card -->
            <div class="card text-center" style="width: 180px; cursor:pointer;" data-toggle="modal" data-target="#uploadFileModal">
                <div class="card-body d-flex flex-column align-items-center justify-content-center" style="height: 120px;">
                    <i class="fas fa-upload fa-2x mb-2"></i>
                    <span>Upload File</span>
                </div>
            </div>
            <!-- Subfolder Cards -->
            @foreach($folder->children as $subfolder)
                <div class="card" style="width: 180px;">
                    <div class="card-body">
                        <h6 class="card-title">{{ $subfolder->name }}</h6>
                        <p class="mb-1"><small>By: {{ $subfolder->creator->name ?? 'N/A' }}</small></p>
                        <p class="mb-1"><small>Created: {{ $subfolder->created_at->format('M d, Y') }}</small></p>
                        <p class="mb-1"><small>Folders: {{ $subfolder->children()->count() }}</small></p>
                        <p class="mb-1"><small>Files: {{ $subfolder->files()->count() }}</small></p>
                        <div class="d-flex justify-content-between mt-2">
                            <a href="{{ route('folders.show', $subfolder) }}" class="btn btn-primary btn-sm">View</a>
                            @if(auth()->user()->is_admin)
                            <button class="btn btn-danger btn-sm delete-folder-btn" data-folder-id="{{ $subfolder->id }}"><i class="fas fa-trash"></i></button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
            <!-- File Cards -->
            @foreach($folder->files as $file)
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

<!-- Create Folder Modal -->
<div class="modal fade" id="createFolderModal" tabindex="-1" aria-labelledby="createFolderModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="createFolderForm">
        <div class="modal-header">
          <h5 class="modal-title" id="createFolderModalLabel">Create New Folder</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
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
<div class="modal fade" id="uploadFileModal" tabindex="-1" aria-labelledby="uploadFileModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="uploadFileForm" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title" id="uploadFileModalLabel">Upload File</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="fileInput" class="form-label">Choose File</label>
            <input type="file" class="form-control" id="fileInput" name="file" required>
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
document.getElementById('createFolderForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const name = document.getElementById('folderName').value;
    fetch("{{ route('folders.store', $folder->project) }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: JSON.stringify({ name, parent_id: {{ $folder->id }} })
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            location.reload();
        } else {
            alert('Error creating folder');
        }
    });
});

document.querySelectorAll('.delete-folder-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        if(confirm('Are you sure you want to delete this folder?')) {
            fetch(`/folders/${this.dataset.folderId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                }
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    location.reload();
                } else {
                    alert('Error deleting folder');
                }
            });
        }
    });
});

document.getElementById('uploadFileForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch("{{ route('files.store', $folder) }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            location.reload();
        } else {
            alert('Error uploading file');
        }
    });
});
</script>
@endsection