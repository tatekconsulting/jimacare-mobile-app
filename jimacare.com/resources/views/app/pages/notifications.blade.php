@extends('app.template.layout-profile')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>🔔 Notifications</h2>
                @if($notifications->where('is_read', false)->count() > 0)
                    <form action="{{ route('notifications.readAll') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-primary btn-sm">
                            Mark All as Read
                        </button>
                    </form>
                @endif
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if($notifications->isEmpty())
                <div class="card">
                    <div class="card-body text-center py-5">
                        <h4 class="text-muted">No notifications yet</h4>
                        <p class="text-muted">You'll see notifications here when there's activity on your account.</p>
                    </div>
                </div>
            @else
                <div class="list-group">
                    @foreach($notifications as $notification)
                        <div class="list-group-item list-group-item-action {{ !$notification->is_read ? 'bg-light' : '' }}" style="border-left: 4px solid {{ !$notification->is_read ? '#667eea' : '#e0e0e0' }};">
                            <div class="d-flex w-100 justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-1">
                                        @if(!$notification->is_read)
                                            <span class="badge badge-primary mr-2">NEW</span>
                                        @endif
                                        <h6 class="mb-0 font-weight-bold">{{ $notification->title }}</h6>
                                    </div>
                                    <p class="mb-1 text-dark">{{ $notification->message }}</p>
                                    <small class="text-muted">
                                        <i class="fa fa-clock-o"></i> {{ $notification->created_at->diffForHumans() }}
                                        @if($notification->type)
                                            <span class="badge badge-light ml-2">{{ ucwords(str_replace('_', ' ', $notification->type)) }}</span>
                                        @endif
                                    </small>
                                </div>
                                <div class="ml-3">
                                    @if($notification->action_url && !$notification->is_read)
                                        <form action="{{ route('notifications.read', $notification->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-primary btn-sm">View</button>
                                        </form>
                                    @elseif($notification->action_url)
                                        <a href="{{ $notification->action_url }}" class="btn btn-outline-secondary btn-sm">View</a>
                                    @elseif(!$notification->is_read)
                                        <form action="{{ route('notifications.read', $notification->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-secondary btn-sm">Mark Read</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.list-group-item {
    transition: all 0.2s ease;
}
.list-group-item:hover {
    transform: translateX(5px);
}
</style>
@endsection

