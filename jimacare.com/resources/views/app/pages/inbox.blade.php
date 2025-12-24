@extends('app.template.layout-profile')

@section('content')
<style>
    .inbox-container {
        background: #f7fafc;
        min-height: calc(100vh - 200px);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    }
    
    .conversations-sidebar {
        background: white;
        border-right: 1px solid #e2e8f0;
        height: calc(100vh - 200px);
        overflow-y: auto;
    }
    
    .conversations-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem;
        display: flex;
        align-items: center;
    }
    
    .conversations-header img {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        border: 2px solid rgba(255,255,255,0.3);
        margin-right: 1rem;
    }
    
    .conversation-item {
        display: flex;
        align-items: center;
        padding: 1.25rem;
        border-bottom: 1px solid #e2e8f0;
        transition: all 0.3s ease;
        cursor: pointer;
        text-decoration: none;
        color: inherit;
    }
    
    .conversation-item:hover {
        background: #f7fafc;
        text-decoration: none;
        color: inherit;
    }
    
    .conversation-item.active {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        border-left: 4px solid #667eea;
    }
    
    .conversation-avatar {
        width: 55px;
        height: 55px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 1rem;
        border: 2px solid #e2e8f0;
    }
    
    .conversation-info {
        flex: 1;
        min-width: 0;
    }
    
    .conversation-name {
        font-weight: 600;
        font-size: 1rem;
        color: #2d3748;
        margin-bottom: 0.25rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .conversation-preview {
        font-size: 0.875rem;
        color: #718096;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .messages-area {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 200px);
        background: #f7fafc;
    }
    
    .messages-header {
        background: white;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .messages-header-user {
        display: flex;
        align-items: center;
    }
    
    .messages-header-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 1rem;
        border: 2px solid #e2e8f0;
    }
    
    .messages-header-info h4 {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2d3748;
        margin: 0;
    }
    
    .messages-header-info small {
        color: #718096;
        font-size: 0.85rem;
    }
    
    .messages-list {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 1.5rem;
        background: #f7fafc;
        -webkit-overflow-scrolling: touch; /* Smooth scrolling on iOS */
        min-height: 0; /* Important for flex children */
    }
    
    .message-bubble {
        display: flex;
        margin-bottom: 1.25rem;
        animation: fadeIn 0.3s ease;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .message-bubble.sent {
        justify-content: flex-end;
    }
    
    .message-bubble.received {
        justify-content: flex-start;
    }
    
    .message-content {
        max-width: 70%;
        display: flex;
        align-items: flex-end;
    }
    
    .message-bubble.sent .message-content {
        flex-direction: row-reverse;
    }
    
    .message-avatar {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        object-fit: cover;
        margin: 0 0.75rem;
        border: 2px solid #e2e8f0;
    }
    
    .message-bubble.sent .message-avatar {
        margin-left: 0.75rem;
        margin-right: 0;
    }
    
    .message-bubble.received .message-avatar {
        margin-right: 0.75rem;
        margin-left: 0;
    }
    
    .message-text {
        padding: 0.875rem 1.25rem;
        border-radius: 18px;
        position: relative;
        word-wrap: break-word;
    }
    
    .message-bubble.sent .message-text {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-bottom-right-radius: 4px;
    }
    
    .message-bubble.received .message-text {
        background: white;
        color: #2d3748;
        border-bottom-left-radius: 4px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    
    .message-time {
        font-size: 0.75rem;
        color: #a0aec0;
        margin-top: 0.5rem;
        text-align: right;
    }
    
    .message-bubble.received .message-time {
        text-align: left;
    }
    
    /* Toast Notification Styles */
    .toast-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        z-index: 10000;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        min-width: 280px;
        max-width: 400px;
        animation: slideInRight 0.3s ease-out, fadeOut 0.3s ease-in 2.7s forwards;
        transform: translateX(0);
    }
    
    @keyframes slideInRight {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes fadeOut {
        to {
            opacity: 0;
            transform: translateX(400px);
        }
    }
    
    .toast-notification.success {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
    }
    
    .toast-notification.error {
        background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
    }
    
    .toast-notification-icon {
        font-size: 1.5rem;
        flex-shrink: 0;
    }
    
    .toast-notification-content {
        flex: 1;
    }
    
    .toast-notification-title {
        font-weight: 600;
        font-size: 1rem;
        margin-bottom: 0.25rem;
    }
    
    .toast-notification-message {
        font-size: 0.875rem;
        opacity: 0.95;
    }
    
    .toast-notification-close {
        background: rgba(255,255,255,0.2);
        border: none;
        color: white;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: background 0.2s;
    }
    
    .toast-notification-close:hover {
        background: rgba(255,255,255,0.3);
    }
    
    @media (max-width: 768px) {
        .toast-notification {
            top: 10px;
            right: 10px;
            left: 10px;
            min-width: auto;
            max-width: none;
        }
        
        @keyframes slideInRight {
            from {
                transform: translateY(-100px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        @keyframes fadeOut {
            to {
                opacity: 0;
                transform: translateY(-100px);
            }
        }
    }
    
    .invoice-message {
        background: white;
        border: 2px solid #667eea;
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.1);
    }
    
    .invoice-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .invoice-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2d3748;
    }
    
    .invoice-price {
        font-size: 1.3rem;
        font-weight: 700;
        color: #667eea;
    }
    
    .message-input-area {
        background: white;
        padding: 1.25rem 1.5rem;
        border-top: 1px solid #e2e8f0;
        box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
    }
    
    .message-input-form {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .message-input {
        flex: 1;
        border: 2px solid #e2e8f0;
        border-radius: 25px;
        padding: 0.875rem 1.5rem;
        font-size: 1rem;
        transition: all 0.3s ease;
        resize: none;
    }
    
    .message-input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    
    .btn-send {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }
    
    .btn-send:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        color: white;
    }
    
    .btn-video-call {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        border: none;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(72, 187, 120, 0.3);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
        cursor: pointer;
    }
    
    .btn-video-call::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.25);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }
    
    .btn-video-call:hover::before {
        width: 300px;
        height: 300px;
    }
    
    .btn-video-call:hover {
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 8px 25px rgba(72, 187, 120, 0.5);
        color: white;
    }
    
    .btn-video-call:active {
        transform: translateY(-1px) scale(1.02);
    }
    
    .btn-video-call i {
        margin-right: 0.5rem;
        font-size: 1.15rem;
        position: relative;
        z-index: 1;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.1);
        }
    }
    
    .btn-video-call span {
        position: relative;
        z-index: 1;
    }
    
    .btn-video-call:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }
    
    .btn-video-call:disabled i {
        animation: none;
    }
    
    .btn-invoice {
        background: linear-gradient(135deg, #f6ad55 0%, #ed8936 100%);
        border: none;
        color: white;
        padding: 0.625rem 1.25rem;
        border-radius: 25px;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(246, 173, 85, 0.3);
    }
    
    .btn-invoice:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(246, 173, 85, 0.4);
        color: white;
    }
    
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        padding: 3rem;
        text-align: center;
    }
    
    .empty-state-icon {
        font-size: 5rem;
        color: #cbd5e0;
        margin-bottom: 1.5rem;
    }
    
    .empty-state h3 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 0.5rem;
    }
    
    .empty-state p {
        color: #718096;
        font-size: 1.1rem;
    }
    
    @media (max-width: 768px) {
        .inbox-container {
            min-height: calc(100vh - 120px);
        }
        
        .conversations-sidebar {
            position: absolute;
            left: -100%;
            z-index: 1000;
            width: 80%;
            transition: left 0.3s ease;
            height: calc(100vh - 120px);
        }
        
        .conversations-sidebar.show {
            left: 0;
        }
        
        .messages-area {
            height: calc(100vh - 120px);
            min-height: 500px;
        }
        
        .messages-header {
            padding: 1rem;
            flex-wrap: wrap;
        }
        
        .messages-list {
            padding: 1rem;
            height: 100%;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .message-bubble {
            margin-bottom: 1rem;
        }
        
        .message-content {
            max-width: 85% !important;
        }
    }
</style>

<div class="container-fluid py-4">
    <div class="inbox-container">
        <div class="row no-gutters h-100">
            <!-- Conversations Sidebar -->
            <div class="col-12 col-md-4 conversations-sidebar" id="conversationsSidebar">
                <div class="conversations-header">
                    <img src="{{ asset($auth->profile ?? 'img/undraw_profile.svg') }}" alt="">
                    <div>
                        <div style="font-weight: 600; font-size: 1.1rem;">{{ $auth->firstname ?? '' }} {{ $auth->lastname[0] ?? '' }}</div>
                        <small style="opacity: 0.9;">Your Messages</small>
                    </div>
                </div>
                
                <div class="conversation-list">
                    @forelse($inboxes as $ib)
                        @php 
                            $u = $ib->{$ib->client_id == $auth->id ? 'seller' : 'client'};
                            $latestMessage = $ib->messages()->latest()->first();
                            $isActive = (isset($user) && $user && isset($u) && $u) ? ($user->id == $u->id) : false;
                        @endphp
                        @if (isset($u) && $u)
                            <a class="conversation-item {{ $isActive ? 'active' : '' }}" 
                               href="{{ route('inbox.show', ['user' => $u->id]) }}">
                                <img src="{{ asset($u->profile ?? 'img/undraw_profile.svg') }}" 
                                     alt="" class="conversation-avatar">
                                <div class="conversation-info">
                                    <div class="conversation-name">
                                        {{ $u->firstname ?? '' }} {{ $u->lastname ?? '' }}
                                    </div>
                                    <div class="conversation-preview">
                                        {{ $latestMessage ? \Illuminate\Support\Str::limit($latestMessage->message, 40) : 'No messages yet' }}
                                    </div>
                                </div>
                            </a>
                        @endif
                    @empty
                        <div class="text-center py-5">
                            <i class="fa fa-inbox" style="font-size: 3rem; color: #cbd5e0; margin-bottom: 1rem;"></i>
                            <p style="color: #718096;">No conversations yet</p>
                        </div>
                    @endforelse
                </div>
            </div>
            
            <!-- Messages Area -->
            <div class="col-12 col-md-8 messages-area">
                @if (($inbox ?? false) && isset($user) && $user)
                    <!-- Messages Header -->
                    <div class="messages-header">
                        <div class="messages-header-user">
                            <button class="btn btn-link d-md-none mr-2" id="toggleSidebar" style="color: #667eea;">
                                <i class="fa fa-bars"></i>
                            </button>
                            <img src="{{ asset($user->profile ?? 'img/undraw_profile.svg') }}" 
                                 alt="" class="messages-header-avatar">
                            <div class="messages-header-info">
                                <h4>{{ $user->firstname ?? '' }} {{ $user->lastname ?? '' }}</h4>
                                <small>
                                    @if(isset($user->role_id))
                                        @if($user->role_id == 2)
                                            Client
                                        @elseif($user->role_id == 3)
                                            Carer
                                        @elseif($user->role_id == 4)
                                            Childminder
                                        @elseif($user->role_id == 5)
                                            Housekeeper
                                        @endif
                                    @endif
                                </small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            @if (isset($user) && $user)
                                <button class="btn btn-video-call" 
                                        data-user-id="{{ $user->id }}"
                                        data-user-name="{{ $user->firstname ?? '' }} {{ $user->lastname ?? '' }}">
                                    <i class="fa fa-video-camera"></i>
                                    <span>Video Call</span>
                                </button>
                            @endif
                            @if (auth()->user()->role_id > 2)
                                <button class="btn btn-invoice" data-toggle="modal" data-target="#send-invoice-model">
                                    <i class="fa fa-file-text-o mr-2"></i>Send Invoice
                                </button>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Messages List -->
                    <div class="messages-list" id="messagesList">
                        @if($messages && $messages->count() > 0)
                        @foreach ($messages as $message)
                            <div class="message-bubble {{ $message->from_id == $auth->id ? 'sent' : 'received' }}">
                                <div class="message-content">
                                    @if($message->type == 'invoice')
                                        <div class="invoice-message" style="max-width: 100%;">
                                            <div class="invoice-header">
                                                <div class="invoice-title">
                                                    <i class="fa fa-file-text-o mr-2"></i>Custom Invoice
                                                </div>
                                                <div class="invoice-price">£{{ $message->invoice->price ?? 0 }}</div>
                                            </div>
                                            <p style="color: #4a5568; margin-bottom: 1rem;">{{ $message->message ?? '' }}</p>
                                            <div class="d-flex gap-2 flex-wrap">
                                                @if ($message->from_id != $auth->id)
                                                    @if ($message->invoice->status == 'active')
                                                        <a href="{{ route('invoice.pay', ['invoice' => $message->invoice->id]) }}" 
                                                           class="btn btn-primary" style="border-radius: 25px;">
                                                            <i class="fa fa-check mr-2"></i>Pay Now
                                                        </a>
                                                        <a href="{{ route('invoice.reject', ['invoice' => $message->invoice->id]) }}" 
                                                           class="btn btn-outline-danger reject-invoice-request" style="border-radius: 25px;">
                                                            <i class="fa fa-times mr-2"></i>Reject
                                                        </a>
                                                    @elseif($message->invoice->status == 'paid')
                                                        <button class="btn btn-success" disabled style="border-radius: 25px;">
                                                            <i class="fa fa-check-circle mr-2"></i>Paid
                                                        </button>
                                                        @if($message->invoice->order ?? false)
                                                            <a href="{{ route('order.show', ['order' => $message->invoice->order->id]) }}" 
                                                               class="btn btn-outline-primary" style="border-radius: 25px;">
                                                                <i class="fa fa-eye mr-2"></i>View Order
                                                            </a>
                                                        @endif
                                                    @else
                                                        <button class="btn btn-secondary" disabled style="border-radius: 25px;">
                                                            {{ ucwords($message->invoice->status) }}
                                                        </button>
                                                    @endif
                                                @else
                                                    @if ($message->invoice->status == 'active')
                                                        <a href="{{ route('invoice.cancel', ['invoice' => $message->invoice->id]) }}" 
                                                           class="btn btn-outline-warning cancel-invoice-request" style="border-radius: 25px;">
                                                            <i class="fa fa-ban mr-2"></i>Cancel Invoice
                                                        </a>
                                                    @elseif($message->invoice->status == 'paid')
                                                        <button class="btn btn-success" disabled style="border-radius: 25px;">
                                                            <i class="fa fa-check-circle mr-2"></i>Paid
                                                        </button>
                                                        @if($message->invoice->order ?? false)
                                                            <a href="{{ route('order.show', ['order' => $message->invoice->order->id]) }}" 
                                                               class="btn btn-outline-primary" style="border-radius: 25px;">
                                                                <i class="fa fa-eye mr-2"></i>View Order
                                                            </a>
                                                        @endif
                                                    @else
                                                        <button class="btn btn-secondary" disabled style="border-radius: 25px;">
                                                            {{ ucwords($message->invoice->status) }}
                                                        </button>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        @if($message->from)
                                            <img src="{{ asset($message->from->profile ?? 'img/undraw_profile.svg') }}" 
                                                 alt="" class="message-avatar" 
                                                 onerror="this.src='{{ asset('img/undraw_profile.svg') }}'">
                                        @else
                                            <img src="{{ asset('img/undraw_profile.svg') }}" 
                                                 alt="" class="message-avatar">
                                        @endif
                                        <div>
                                            <div class="message-text">
                                                {{ $message->message ?? '' }}
                                            </div>
                                            <div class="message-time">
                                                <i class="fa fa-clock-o mr-1"></i>
                                                {{ $message->created_at ? $message->created_at->format('M d, Y \a\t H:i') : '' }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                        @else
                            <div class="empty-state" style="text-align: center; padding: 3rem 1rem;">
                                <div class="empty-state-icon" style="font-size: 3rem; color: #cbd5e0; margin-bottom: 1rem;">
                                    <i class="fa fa-comments"></i>
                                </div>
                                <h3 style="color: #2d3748; margin-bottom: 0.5rem;">No messages yet</h3>
                                <p style="color: #718096;">Start the conversation by sending a message below</p>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Message Input -->
                    @if(isset($user) && $user)
                    <div class="message-input-area">
                        <form method="POST" action="{{ route('message', ['user' => $user->id]) }}" class="message-input-form" id="messageForm">
                            @csrf
                            <input type="text" name="message" id="message" class="message-input" 
                                   placeholder="Type your message..." autocomplete="off" required>
                            <button type="submit" class="btn-send">
                                <i class="fa fa-paper-plane"></i>
                            </button>
                        </form>
                    </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fa fa-comments"></i>
                        </div>
                        <h3>Select a Conversation</h3>
                        <p>Choose a conversation from the list to start messaging</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Send Invoice Modal -->
@if (($inbox ?? false) && isset($user) && $user && auth()->user()->role_id > 2)
    <div class="modal fade" id="send-invoice-model" role="dialog" aria-labelledby="send-invoice-model-label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="{{ route('message.invoice', ['user' => $user->id]) }}" class="modal-content" style="border-radius: 16px; overflow: hidden;">
                @csrf
                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;">
                    <h5 class="modal-title" id="send-invoice-model-label" style="font-weight: 600;">
                        <i class="fa fa-file-text-o mr-2"></i>Send Invoice
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 0.9;">
                        <span aria-hidden="true" style="font-size: 1.5rem;">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="padding: 2rem;">
                    <div class="form-group">
                        <label for="invoice-message" class="form-label" style="font-weight: 600; color: #2d3748;">
                            Describe your offer
                        </label>
                        <textarea name="message" class="form-control" id="invoice-message" 
                                  placeholder="Describe the services and what you're invoicing for..." 
                                  rows="4" required style="border-radius: 12px; border: 2px solid #e2e8f0;"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="invoice-price" class="form-label" style="font-weight: 600; color: #2d3748;">
                            Price (£)
                        </label>
                        <input type="number" name="price" class="form-control" id="invoice-price"
                               placeholder="0.00" step="0.01" min="0" required 
                               style="border-radius: 12px; border: 2px solid #e2e8f0;">
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #e2e8f0; padding: 1.5rem 2rem;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 25px;">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-invoice" style="border-radius: 25px;">
                        <i class="fa fa-paper-plane mr-2"></i>Send Invoice
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif

<script>
    // Helper function to escape HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Auto-scroll to bottom of messages
    document.addEventListener('DOMContentLoaded', function() {
        const messagesList = document.getElementById('messagesList');
        if (messagesList) {
            // Use requestAnimationFrame for better mobile browser support
            requestAnimationFrame(function() {
                messagesList.scrollTop = messagesList.scrollHeight;
            });
        }
        
        // Listen for real-time messages via Echo/Pusher
        const userId = parseInt(document.querySelector('meta[name="user-token"]')?.getAttribute('content') || '0');
        const currentChatUserId = {{ isset($user) && $user ? $user->id : 'null' }};
        
        if (window.Echo && userId > 0) {
            window.Echo.private('inbox-' + userId)
                .listen('MessageEvent', (e) => {
                    // Only add message if it's from the current chat user
                    if (currentChatUserId && e.id == currentChatUserId && messagesList) {
                        const messageHtml = `
                            <div class="message-bubble received">
                                <div class="message-content">
                                    <img src="${escapeHtml(e.profile || '/img/undraw_profile.svg')}" alt="" class="message-avatar">
                                    <div>
                                        <div class="message-text">${escapeHtml(e.message || '')}</div>
                                        <div class="message-time">
                                            <i class="fa fa-clock-o mr-1"></i>
                                            ${escapeHtml(e.sent_at || '')}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        messagesList.insertAdjacentHTML('beforeend', messageHtml);
                        // Use setTimeout for mobile browsers to ensure scroll happens
                        setTimeout(function() {
                            messagesList.scrollTop = messagesList.scrollHeight;
                        }, 100);
                    } else if (e.id != currentChatUserId) {
                        // Update conversation list if message is from another user
                        const conversationItem = document.querySelector(`[data-user-id="${e.id}"]`);
                        if (conversationItem) {
                            const preview = conversationItem.querySelector('.conversation-preview');
                            if (preview) {
                                preview.textContent = e.message || '';
                                preview.classList.add('font-weight-bold');
                            }
                        }
                    }
                });
        }
        
        // Mobile sidebar toggle
        const toggleSidebar = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('conversationsSidebar');
        if (toggleSidebar && sidebar) {
            toggleSidebar.addEventListener('click', function() {
                sidebar.classList.toggle('show');
            });
        }
        
        // Auto-focus message input
        const messageInput = document.getElementById('message');
        if (messageInput) {
            messageInput.focus();
        }
        
        // Handle message form submission via AJAX
        const messageForm = document.getElementById('messageForm');
        if (messageForm) {
            messageForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const form = this;
                const messageInput = form.querySelector('#message');
                const messageText = messageInput ? messageInput.value.trim() : '';
                
                if (messageText.length === 0) {
                    if (messageInput) messageInput.focus();
                    return;
                }
                
                // Show loading state
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalBtnText = submitBtn ? submitBtn.innerHTML : '';
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Sending...';
                }
                
                // Store original message for potential retry
                const originalMessage = messageText;
                
                // Send message via AJAX with timeout for mobile browsers
                let fetchOptions = {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new URLSearchParams(new FormData(form))
                };
                
                // Add timeout support if AbortController is available
                let timeoutId = null;
                if (typeof AbortController !== 'undefined') {
                    const controller = new AbortController();
                    timeoutId = setTimeout(() => controller.abort(), 30000); // 30 second timeout
                    fetchOptions.signal = controller.signal;
                }
                
                fetch(form.action, fetchOptions)
                .then(response => {
                    if (timeoutId) clearTimeout(timeoutId);
                    // Check if response is ok
                    if (!response.ok) {
                        // Try to get error message from response
                        return response.text().then(text => {
                            try {
                                const json = JSON.parse(text);
                                throw new Error(json.message || 'Network response was not ok');
                            } catch (e) {
                                throw new Error('Network response was not ok: ' + response.status);
                            }
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    // Check if request was successful
                    if (!data || data.success === false) {
                        const errorMsg = data?.message || 'Failed to send message. Please try again.';
                        console.error('Message send failed:', data);
                        
                        // Show error notification
                        showToastNotification('error', 'Message Failed', errorMsg);
                        
                        // Reset button
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalBtnText;
                        }
                        return;
                    }
                    
                    // Validate response data
                    if (!data.message) {
                        console.error('Invalid response data:', data);
                        alert('Invalid response from server. Please refresh and try again.');
                        
                        // Reset button
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalBtnText;
                        }
                        return;
                    }
                    
                    // Clear input
                    if (messageInput) messageInput.value = '';
                    
                    // Add message to chat with proper structure matching server-rendered messages
                    const messageHtml = `
                        <div class="message-bubble sent">
                            <div class="message-content">
                                <div class="message-text">${escapeHtml(data.message || '')}</div>
                                <div class="message-time">
                                    <i class="fa fa-clock-o mr-1"></i>
                                    ${escapeHtml(data.sent_at || '')}
                                </div>
                            </div>
                        </div>
                    `;
                    
                    if (messagesList) {
                        // Create a temporary element to ensure proper parsing
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = messageHtml.trim();
                        const messageElement = tempDiv.firstElementChild;
                        
                        if (messageElement) {
                            messagesList.appendChild(messageElement);
                            
                            // Force scroll to bottom - use multiple methods for mobile browser compatibility
                            const scrollToBottom = function() {
                                messagesList.scrollTop = messagesList.scrollHeight;
                                // Also try scrollIntoView for mobile
                                if (messageElement.scrollIntoView) {
                                    messageElement.scrollIntoView({ behavior: 'smooth', block: 'end' });
                                }
                            };
                            
                            // Immediate scroll
                            scrollToBottom();
                            
                            // Delayed scrolls for mobile browsers
                            setTimeout(scrollToBottom, 50);
                            setTimeout(scrollToBottom, 200);
                            requestAnimationFrame(scrollToBottom);
                        } else {
                            console.error('Failed to parse message HTML');
                            // Fallback: reload page to show message
                            window.location.reload();
                        }
                    } else {
                        console.error('Messages list element not found');
                        // Fallback: reload page to show message
                        window.location.reload();
                    }
                    
                    // Reset button
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                    }
                    
                    // Focus input - delay for mobile browsers
                    if (messageInput) {
                        setTimeout(function() {
                            try {
                                messageInput.focus();
                            } catch (e) {
                                // Some mobile browsers don't allow programmatic focus
                                console.log('Could not focus input:', e);
                            }
                        }, 100);
                    }
                    
                    // Show success notification
                    showToastNotification('success', 'Message Sent', 'Your message has been delivered successfully!');
                })
                .catch(error => {
                    console.error('Message send error:', error);
                    console.error('Error details:', {
                        message: error.message,
                        stack: error.stack
                    });
                    
                    // Show error notification
                    showToastNotification('error', 'Connection Error', 'Failed to send message. Please check your connection and try again.');
                    
                    // Reset button
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                    }
                    
                    // On mobile, show option to reload page as fallback
                    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                        setTimeout(function() {
                            if (confirm('Message may not have been sent. Reload page to check?')) {
                                window.location.reload();
                            }
                        }, 2000);
                    }
                });
            });
        }
        
        // Video Call Button Handler
        const videoCallButtons = document.querySelectorAll('.btn-video-call');
        videoCallButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const userId = this.getAttribute('data-user-id');
                const userName = this.getAttribute('data-user-name');
                
                if (!userId) {
                    alert('Unable to initiate video call. User ID not found.');
                    return;
                }
                
                // Show loading state
                const originalHTML = this.innerHTML;
                this.innerHTML = '<i class="fa fa-spinner fa-spin mr-2"></i>Connecting...';
                this.disabled = true;
                
                // Make API call to initiate video call (using web route for session auth)
                fetch(`/video-call/${userId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                })
                .then(response => {
                    // Check if response is ok
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw new Error(data.message || 'Server error: ' + response.status);
                        }).catch(() => {
                            throw new Error('Failed to initiate video call. Server returned error: ' + response.status);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success && data.token && data.room_id) {
                        // Redirect to video call page
                        window.location.href = `/video-call?room=${data.room_id}&token=${data.token}`;
                    } else {
                        const errorMsg = data.message || 'Failed to initiate video call. Missing token or room ID.';
                        console.error('Video call initiation failed:', data);
                        showToastNotification('error', 'Video Call Failed', errorMsg);
                        this.innerHTML = originalHTML;
                        this.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Video call error:', error);
                    const errorMsg = error.message || 'An error occurred while initiating the video call. Please check your connection and try again.';
                    showToastNotification('error', 'Connection Error', errorMsg);
                    this.innerHTML = originalHTML;
                    this.disabled = false;
                });
            });
        });
    });
    
    // Toast Notification Function
    function showToastNotification(type, title, message) {
        // Remove any existing toast
        const existingToast = document.querySelector('.toast-notification');
        if (existingToast) {
            existingToast.remove();
        }
        
        // Create toast element
        const toast = document.createElement('div');
        toast.className = `toast-notification ${type}`;
        
        // Set icon based on type
        let icon = '✓';
        if (type === 'success') {
            icon = '<i class="fa fa-check-circle"></i>';
        } else if (type === 'error') {
            icon = '<i class="fa fa-exclamation-circle"></i>';
        } else {
            icon = '<i class="fa fa-info-circle"></i>';
        }
        
        toast.innerHTML = `
            <div class="toast-notification-icon">${icon}</div>
            <div class="toast-notification-content">
                <div class="toast-notification-title">${escapeHtml(title)}</div>
                <div class="toast-notification-message">${escapeHtml(message)}</div>
            </div>
            <button class="toast-notification-close" onclick="this.parentElement.remove()">
                <i class="fa fa-times"></i>
            </button>
        `;
        
        // Add to body
        document.body.appendChild(toast);
        
        // Auto remove after 3 seconds
        setTimeout(function() {
            if (toast.parentElement) {
                toast.style.animation = 'fadeOut 0.3s ease-in forwards';
                setTimeout(function() {
                    if (toast.parentElement) {
                        toast.remove();
                    }
                }, 300);
            }
        }, 3000);
    }
</script>

@endsection
