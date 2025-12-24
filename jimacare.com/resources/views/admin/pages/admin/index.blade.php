@extends('admin.template.layout')

@section('content')
    <div class="card mb-4">
        <div class="card-header pt-3">
            <div class="container mt-2">

                <div class="row">
                    <div class="form-group col-6 col-md-6 mt-2">
                        <h6 class="m-0 font-weight-bold text-primary">ADMINS</h6>
                    </div>
                </div>

            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Avatar</th>
                            <th>Type</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Member Since</th>
                            <th>Last Login</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users ?? [] as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>
                                    @if ($user->profile ?? false)
                                        <img src="{{ asset($user->profile ?? '') }}" alt="" height="40">
                                    @endif
                                </td>
                                <td>{{ $user->role->title }}</td>
                                <td>{{ $user->firstname }} {{ $user->lastname }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone }}</td>
                                <td>
                                    @if ($user->created_at ?? false)
                                        {{ $user->created_at->format('d M Y') }}
                                    @else
                                        N\A
                                    @endif
                                </td>
                                <td>
                                    @if ($user->last_login ?? false)
                                        {{ $user->last_login->diffForHumans() }}
                                    @else
                                        N\A
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('dashboard.user.status', ['user' => $user->id]) }}"
                                        method="post">
                                        @csrf
                                        <select name="status" class="custom-select status-autoupate"
                                            @if ($user->power_admin == true && auth()->user()->power_admin == false) disabled @endif>
                                            @foreach (['pending', 'review', 'active', 'block'] as $s)
                                                <option value="{{ $s }}"
                                                    @if ($s == $user->status) selected @endif>{{ ucfirst($s) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        @if ($user->power_admin == true && auth()->user()->power_admin == false)
                                        @else
                                            <a href="{{ route('dashboard.user.edit', ['user' => $user->id]) }}"
                                                class="btn btn-primary btn-edit">EDIT</a>
                                        @endif
                                        @if ($user->power_admin == false)
                                            <a href="{{ route('dashboard.user.destroy', ['user' => $user->id]) }}"
                                                class="btn btn-danger btn-delete"
                                                onclick="event.preventDefault(); if(confirm('Are you sure you want to delete?') == true) document.getElementById('user-{{ $user->id }}').submit();">DELETE</a>
                                            <form id="user-{{ $user->id }}"
                                                action="{{ route('dashboard.user.destroy', ['user' => $user->id]) }}"
                                                method="POST" class="d-none">
                                                @csrf
                                                @method('delete')
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $users->appends($_GET)->links('pagination.default') }}
            </div>
        </div>
    </div>
@endsection
