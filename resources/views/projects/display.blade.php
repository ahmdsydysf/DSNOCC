@extends('layouts.app')

@section('page_title', 'Projects Gallery')

@section('page_actions')
    <a href="{{ route('projects.index') }}" class="btn btn-secondary">
        <i class="fas fa-list"></i> List View
    </a>
@endsection

@section('head_styles')
<style>
    .projects-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
        padding: 1rem 0;
    }

    .project-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        cursor: pointer;
        border: 2px solid transparent;
    }

    .project-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        border-color: var(--primary-color);
    }

    .project-card-header {
        height: 120px;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .project-card-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, var(--card-color-1), var(--card-color-2));
        opacity: 0.9;
    }

    .project-card-icon {
        position: relative;
        z-index: 2;
        font-size: 3rem;
        color: white;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    .project-card-image {
        position: relative;
        z-index: 2;
        width: 80px;
        height: 80px;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .project-card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .project-card-image-fallback {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--card-color-1), var(--card-color-2));
        color: white;
        font-size: 1.5rem;
        font-weight: 700;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    .project-card-body {
        padding: 1.5rem;
    }

    .project-name {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
        line-height: 1.3;
    }

    .project-code {
        display: inline-block;
        background: var(--card-color-1);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .project-meta {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
        font-size: 0.875rem;
        color: var(--text-secondary);
    }

    .project-meta-item {
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .project-actions {
        display: flex;
        gap: 0.5rem;
        margin-top: 1rem;
    }

    .project-action-btn {
        flex: 1;
        padding: 0.5rem;
        border: none;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.25rem;
    }

    .project-action-btn.view {
        background: var(--light-surface);
        color: var(--text-secondary);
    }

    .project-action-btn.view:hover {
        background: var(--primary-color);
        color: white;
        text-decoration: none;
    }

    .project-action-btn.edit {
        background: #fbbf24;
        color: white;
    }

    .project-action-btn.edit:hover {
        background: #f59e0b;
        text-decoration: none;
        color: white;
    }

    .project-action-btn.delete {
        background: #ef4444;
        color: white;
    }

    .project-action-btn.delete:hover {
        background: #dc2626;
        text-decoration: none;
        color: white;
    }

    /* Add Project Card */
    .add-project-card {
        background: linear-gradient(135deg, #f8fafc, #e2e8f0);
        border: 2px dashed #cbd5e1;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 200px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .add-project-card:hover {
        background: linear-gradient(135deg, #e0e7ff, #c7d2fe);
        border-color: var(--primary-color);
        transform: translateY(-4px);
    }

    .add-project-content {
        text-align: center;
        color: var(--text-secondary);
    }

    .add-project-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
        color: var(--primary-color);
    }

    .add-project-text {
        font-size: 1.125rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .add-project-subtext {
        font-size: 0.875rem;
        opacity: 0.8;
    }

    /* Modal Styles */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 2000;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .modal-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    .modal-content {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        width: 90%;
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
        transform: scale(0.9);
        transition: all 0.3s ease;
    }

    .modal-overlay.active .modal-content {
        transform: scale(1);
    }

    .modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--border-color);
    }

    .modal-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: var(--text-secondary);
        cursor: pointer;
        padding: 0.5rem;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .modal-close:hover {
        background: var(--light-surface);
        color: var(--text-primary);
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .form-input {
        width: 100%;
        padding: 0.75rem;
        border: 2px solid var(--border-color);
        border-radius: 8px;
        font-size: 1rem;
        transition: all 0.2s ease;
        background: white;
    }

    .form-input:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    .form-input.error {
        border-color: #ef4444;
    }

    .error-message {
        color: #ef4444;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .modal-actions {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-primary {
        background: var(--primary-color);
        color: white;
    }

    .btn-primary:hover {
        background: var(--primary-dark);
        text-decoration: none;
        color: white;
    }

    .btn-secondary {
        background: var(--light-surface);
        color: var(--text-secondary);
    }

    .btn-secondary:hover {
        background: var(--border-color);
        text-decoration: none;
        color: var(--text-primary);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .projects-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .modal-content {
            width: 95%;
            padding: 1.5rem;
        }

        .modal-actions {
            flex-direction: column;
        }
    }
        --card-color-2: #65a30d;
    }

    .project-card[data-color="8"] {
        --card-color-1: #f97316;
        --card-color-2: #ea580c;
    }
</style>
@endsection

@section('content')
<div class="projects-grid">
    <!-- Add Project Card -->
    <div class="project-card add-project-card" onclick="openCreateModal()">
        <div class="add-project-content">
            <div class="add-project-icon">
                <i class="fas fa-plus"></i>
            </div>
            <div class="add-project-text">Add New Project</div>
            <div class="add-project-subtext">Create a new project</div>
        </div>
    </div>

    <!-- Project Cards -->
    @forelse($projects as $project)
        @php
            $colors = $project->getProjectColors();
            $colorIndex = ($loop->index % 8) + 1;
        @endphp

        <div class="project-card" data-color="{{ $colorIndex }}" style="--card-color-1: {{ $colors[0] }}; --card-color-2: {{ $colors[1] }};">
            <div class="project-card-header">
                <div class="project-card-image">
                    <img src="{{ $project->image_url }}"
                         alt="{{ $project->name }}"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="project-card-image-fallback" style="display: none;">
                        {{ $project->getProjectInitials() }}
                    </div>
                </div>
            </div>
            <div class="project-card-body">
                <h3 class="project-name">{{ $project->name }}</h3>

                @if($project->code)
                    <div class="project-code">{{ $project->code }}</div>
                @endif

                <div class="project-meta">
                    <div class="project-meta-item">
                        <i class="fas fa-calendar"></i>
                        <span>{{ $project->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="project-meta-item">
                        <i class="fas fa-folder"></i>
                        <span>{{ $project->id }}</span>
                    </div>
                </div>

                <div class="project-actions">
                    <a href="{{ route('projects.show', $project) }}" class="project-action-btn view">
                        <i class="fas fa-eye"></i>
                        <span>View</span>
                    </a>
                    <a href="{{ route('projects.edit', $project) }}" class="project-action-btn edit">
                        <i class="fas fa-edit"></i>
                        <span>Edit</span>
                    </a>
                    <button class="project-action-btn delete" onclick="deleteProject({{ $project->id }}, '{{ $project->name }}')">
                        <i class="fas fa-trash"></i>
                        <span>Delete</span>
                    </button>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <div class="empty-state">
                <i class="fas fa-project-diagram" style="font-size: 4rem; color: var(--text-secondary); margin-bottom: 1rem;"></i>
                <h3 style="color: var(--text-secondary); margin-bottom: 0.5rem;">No Projects Yet</h3>
                <p style="color: var(--text-secondary);">Get started by creating your first project.</p>
            </div>
        </div>
    @endforelse
</div>

<!-- Create Project Modal -->
<div class="modal-overlay" id="createProjectModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Create New Project</h3>
            <button class="modal-close" onclick="closeCreateModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="createProjectForm" action="{{ route('projects.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="project_name" class="form-label">Project Name <span style="color: #ef4444;">*</span></label>
                <input type="text" id="project_name" name="name" class="form-input" required
                       placeholder="Enter project name">
                <div class="error-message" id="name_error" style="display: none;"></div>
            </div>

            <div class="form-group">
                <label for="project_code" class="form-label">Project Code</label>
                <input type="text" id="project_code" name="code" class="form-input"
                       placeholder="Enter project code (optional)">
                <div class="error-message" id="code_error" style="display: none;"></div>
            </div>

            <div class="form-group">
                <label class="form-label">Project Image Preview</label>
                <div id="projectImagePreview" style="display: flex; align-items: center; gap: 1rem; margin-top: 0.5rem;">
                    <div style="width: 60px; height: 60px; border-radius: 12px; overflow: hidden; background: #f8fafc; display: flex; align-items: center; justify-content: center; border: 2px dashed #cbd5e1;">
                        <span style="color: #64748b; font-size: 0.875rem;">Preview</span>
                    </div>
                    <div style="flex: 1;">
                        <small style="color: var(--text-secondary);">A unique image will be generated based on your project code</small>
                    </div>
                </div>
            </div>

            <div class="modal-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Create Project
                </button>
                <button type="button" class="btn btn-secondary" onclick="closeCreateModal()">
                    <i class="fas fa-times"></i>
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal-overlay" id="deleteProjectModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Delete Project</h3>
            <button class="modal-close" onclick="closeDeleteModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <p>Are you sure you want to delete the project "<strong id="deleteProjectName"></strong>"?</p>
            <p style="color: #ef4444; font-size: 0.875rem;">
                <i class="fas fa-exclamation-triangle"></i>
                This action cannot be undone. The project folder and all its contents will be permanently deleted.
            </p>
        </div>

        <form id="deleteProjectForm" method="POST">
            @csrf
            @method('DELETE')

            <div class="modal-actions">
                <button type="submit" class="btn btn-danger" style="background: #ef4444;">
                    <i class="fas fa-trash"></i>
                    Delete Project
                </button>
                <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">
                    <i class="fas fa-times"></i>
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Modal functions
    function openCreateModal() {
        document.getElementById('createProjectModal').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeCreateModal() {
        document.getElementById('createProjectModal').classList.remove('active');
        document.body.style.overflow = 'auto';
        // Reset form
        document.getElementById('createProjectForm').reset();
        // Clear error messages
        document.querySelectorAll('.error-message').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.form-input').forEach(el => el.classList.remove('error'));
    }

    function openDeleteModal(projectId, projectName) {
        document.getElementById('deleteProjectName').textContent = projectName;
        document.getElementById('deleteProjectForm').action = `/projects/${projectId}`;
        document.getElementById('deleteProjectModal').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeDeleteModal() {
        document.getElementById('deleteProjectModal').classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    function deleteProject(projectId, projectName) {
        openDeleteModal(projectId, projectName);
    }

    // Close modals when clicking outside
    document.querySelectorAll('.modal-overlay').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                if (modal.id === 'createProjectModal') {
                    closeCreateModal();
                } else if (modal.id === 'deleteProjectModal') {
                    closeDeleteModal();
                }
            }
        });
    });

    // Handle form submission with AJAX
    document.getElementById('createProjectForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        // Show loading state
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';
        submitBtn.disabled = true;

        // Clear previous errors
        document.querySelectorAll('.error-message').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.form-input').forEach(el => el.classList.remove('error'));

        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message and reload page
                showToast('success', 'Project created successfully!');
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                // Show validation errors
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        const input = document.getElementById(`project_${field}`);
                        const errorDiv = document.getElementById(`${field}_error`);
                        if (input && errorDiv) {
                            input.classList.add('error');
                            errorDiv.textContent = data.errors[field][0];
                            errorDiv.style.display = 'block';
                        }
                    });
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('error', 'An error occurred while creating the project.');
        })
        .finally(() => {
            // Reset button state
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });

    // Handle delete form submission
    document.getElementById('deleteProjectForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        // Show loading state
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
        submitBtn.disabled = true;

        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('success', 'Project deleted successfully!');
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showToast('error', data.message || 'An error occurred while deleting the project.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('error', 'An error occurred while deleting the project.');
        })
        .finally(() => {
            // Reset button state
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            closeDeleteModal();
        });
    });

    // Toast notification function
    function showToast(type, message) {
        const toastClass = type === 'success' ? 'success' : 'error';
        const icon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';

        $.toast({
            text: message,
            position: 'top-right',
            loaderBg: '#ff6849',
            icon: icon,
            hideAfter: 3500,
            stack: 6
        });
    }

    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCreateModal();
            closeDeleteModal();
        }
    });

    // Update project image preview in real-time
    function updateProjectImagePreview() {
        const projectName = document.getElementById('project_name').value;
        const projectCode = document.getElementById('project_code').value;
        const previewContainer = document.getElementById('projectImagePreview');

        if (!projectName && !projectCode) {
            previewContainer.innerHTML = `
                <div style="width: 60px; height: 60px; border-radius: 12px; overflow: hidden; background: #f8fafc; display: flex; align-items: center; justify-content: center; border: 2px dashed #cbd5e1;">
                    <span style="color: #64748b; font-size: 0.875rem;">Preview</span>
                </div>
                <div style="flex: 1;">
                    <small style="color: var(--text-secondary);">A unique image will be generated based on your project code</small>
                </div>
            `;
            return;
        }

        const seed = projectCode || projectName;
        const hash = btoa(seed).replace(/[^a-zA-Z0-9]/g, '').substring(0, 16);
        const backgroundColor = '#' + hash.substring(0, 6);
        const patterns = ['geometric', 'abstract', 'shapes', 'pixel'];
        const pattern = patterns[hash.charCodeAt(0) % patterns.length];

        const imageUrl = `https://api.dicebear.com/7.x/${pattern}/svg?seed=${encodeURIComponent(seed)}&size=120&backgroundColor=${backgroundColor}&colorLevel=2&radius=20`;

        previewContainer.innerHTML = `
            <div style="width: 60px; height: 60px; border-radius: 12px; overflow: hidden; background: white; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <img src="${imageUrl}" alt="Project Preview" style="width: 100%; height: 100%; object-fit: cover;"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div style="display: none; width: 100%; height: 100%; background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white; font-size: 1rem; font-weight: 700; align-items: center; justify-content: center;">
                    ${(projectCode || projectName).substring(0, 2).toUpperCase()}
                </div>
            </div>
            <div style="flex: 1;">
                <small style="color: var(--text-secondary);">Preview of your project's unique generated image</small>
            </div>
        `;
    }

    // Add event listeners for real-time preview
    document.getElementById('project_name').addEventListener('input', updateProjectImagePreview);
    document.getElementById('project_code').addEventListener('input', updateProjectImagePreview);
</script>
@endsection
