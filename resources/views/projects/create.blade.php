@extends('layouts.app')

@section('page_title', 'Create New Project')

@section('page_actions')
    <a href="{{ route('projects.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Projects
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Create New Project</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('projects.store') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="name">Project Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name') }}" required
                               placeholder="Enter project name">
                        <small class="form-text text-muted">
                            The project folder will be automatically created as: <code>public/projects/[project-name]</code>
                        </small>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="code">Project Code</label>
                        <input type="text" class="form-control @error('code') is-invalid @enderror"
                               id="code" name="code" value="{{ old('code') }}"
                               placeholder="Enter project code (optional)">
                        <small class="form-text text-muted">
                            Optional project code or identifier
                        </small>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Project
                        </button>
                        <a href="{{ route('projects.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
