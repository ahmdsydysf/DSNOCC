@extends('layouts.app')

@section('page_title', 'User Details')

@section('page_actions')
    <a href="{{ route('users.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Users
    </a>
    <a href="{{ route('users.edit', $user) }}" class="btn btn-warning">
        <i class="fas fa-edit"></i> Edit User
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">User Details: {{ $user->name }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="150">ID:</th>
                                <td>{{ $user->id }}</td>
                            </tr>
                            <tr>
                                <th>Name:</th>
                                <td>{{ $user->name }}</td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td>{{ $user->email }}</td>
                            </tr>
                            <tr>
                                <th>Role:</th>
                                <td>
                                    <span class="badge badge-{{ $user->role === 'occAdmin' ? 'primary' : 'secondary' }}">
                                        {{ $user->role }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Email Verified:</th>
                                <td>
                                    @if($user->email_verified_at)
                                        <span class="badge badge-success">Verified</span>
                                        <small class="text-muted d-block">{{ $user->email_verified_at->format('M d, Y H:i') }}</small>
                                    @else
                                        <span class="badge badge-warning">Not Verified</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Created At:</th>
                                <td>{{ $user->created_at->format('M d, Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Updated At:</th>
                                <td>{{ $user->updated_at->format('M d, Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Quick Actions</h6>
                                <div class="d-grid gap-2">
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Edit User
                                    </a>
                                    @if($user->id !== Auth::id())
                                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm w-100" onclick="return confirm('Are you sure you want to delete this user?')">
                                                <i class="fas fa-trash"></i> Delete User
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-danger btn-sm w-100" disabled>
                                            <i class="fas fa-trash"></i> Cannot Delete Own Account
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
