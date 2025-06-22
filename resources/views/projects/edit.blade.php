@extends('layouts.app')

@section('page_title', 'Edit Project')

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
                <h5 class="card-title">Edit Project: {{ $project->name }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('projects.update', $project) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="name">Project Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name', $project->name) }}" required
                               placeholder="Enter project name">
                        <small class="form-text text-muted">
                            Changing the project name will also rename the project folder from
                            <code>{{ $project->project_path }}</code> to <code>public/projects/[new-name]</code>
                        </small>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="code">Project Code</label>
                        <input type="text" class="form-control @error('code') is-invalid @enderror"
                               id="code" name="code" value="{{ old('code', $project->code) }}"
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
                            <i class="fas fa-save"></i> Update Project
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
