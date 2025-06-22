@extends('layouts.app')

@section('page_title', 'Projects Management')

@section('page_actions')
    <a href="{{ route('projects.display') }}" class="btn btn-info">
        <i class="fas fa-th"></i> Gallery View
    </a>
    <a href="{{ route('projects.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Project
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Projects List</h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Project Path</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($projects as $project)
                                <tr>
                                    <td>{{ $project->id }}</td>
                                    <td>
                                        <strong>{{ $project->name }}</strong>
                                    </td>
                                    <td>
                                        @if($project->code)
                                            <span class="badge badge-info">{{ $project->code }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $project->project_path }}</small>
                                    </td>
                                    <td>{{ $project->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('projects.edit', $project) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('projects.destroy', $project) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this project? This will also delete the project folder.')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No projects found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center">
                    {{ $projects->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
