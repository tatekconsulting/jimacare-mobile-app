@extends('app.template.layout')

@section('content')
<div class="container-fluid p-0" style="height: 100vh; background: #000;">
    <div class="row no-gutters h-100">
        <!-- Video Call Interface -->
        <div class="col-12 h-100 position-relative">
            <!-- Remote Video (Other Participant) -->
            <div id="remote-video-container" class="w-100 h-100 position-relative" style="background: #1a1a1a;">
                <video id="remote-video" autoplay playsinline class="w-100 h-100" style="object-fit: cover;"></video>
                <div class="position-absolute top-0 left-0 p-3 text-white">
                    <h5 class="mb-0">
                        <img src="{{ asset($otherUser->profile ?? 'img/undraw_profile.svg') }}" 
                             alt="{{ $otherUser->name }}" 
                             class="rounded-circle mr-2" 
                             style="width: 40px; height: 40px; object-fit: cover;">
                        {{ $otherUser->name }}
                    </h5>
                </div>
            </div>

            <!-- Local Video (Your Video) -->
            <div id="local-video-container" class="position-absolute" 
                 style="bottom: 100px; right: 20px; width: 200px; height: 150px; border-radius: 10px; overflow: hidden; border: 2px solid #fff; background: #2a2a2a;">
                <video id="local-video" autoplay playsinline muted class="w-100 h-100" style="object-fit: cover;"></video>
            </div>

            <!-- Controls -->
            <div class="position-absolute bottom-0 left-0 right-0 p-4 text-center" style="background: rgba(0,0,0,0.7);">
                <div class="d-flex justify-content-center align-items-center gap-3">
                    <!-- Mute/Unmute -->
                    <button id="toggle-mute" class="btn btn-lg rounded-circle" 
                            style="width: 60px; height: 60px; background: rgba(255,255,255,0.2); border: none; color: #fff;">
                        <i class="fa fa-microphone"></i>
                    </button>

                    <!-- End Call -->
                    <button id="end-call" class="btn btn-lg rounded-circle" 
                            style="width: 70px; height: 70px; background: #dc3545; border: none; color: #fff;">
                        <i class="fa fa-phone"></i>
                    </button>

                    <!-- Toggle Video -->
                    <button id="toggle-video" class="btn btn-lg rounded-circle" 
                            style="width: 60px; height: 60px; background: rgba(255,255,255,0.2); border: none; color: #fff;">
                        <i class="fa fa-video-camera"></i>
                    </button>
                </div>
            </div>

            <!-- Loading/Status -->
            <div id="call-status" class="position-absolute text-white text-center" 
                 style="top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 1000;">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="sr-only">Connecting...</span>
                </div>
                <p class="mb-0">Connecting to video call...</p>
            </div>
        </div>
    </div>
</div>

<!-- Twilio Video SDK -->
<script src="https://sdk.twilio.com/js/video/releases/2.20.1/twilio-video.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roomId = @json($roomId);
    const token = @json($token);
    const callId = {{ $callId }};
    
    // Debug logging
    console.log('Video Call Debug Info:');
    console.log('Room ID:', roomId);
    console.log('Token length:', token ? token.length : 0);
    console.log('Token preview:', token ? token.substring(0, 50) + '...' : 'null');
    console.log('Call ID:', callId);
    
    let room = null;
    let localTrack = null;
    let remoteTrack = null;
    let isMuted = false;
    let isVideoEnabled = true;

    const localVideo = document.getElementById('local-video');
    const remoteVideo = document.getElementById('remote-video');
    const callStatus = document.getElementById('call-status');
    const toggleMuteBtn = document.getElementById('toggle-mute');
    const toggleVideoBtn = document.getElementById('toggle-video');
    const endCallBtn = document.getElementById('end-call');

    // Check if Twilio Video SDK is loaded
    if (typeof Twilio === 'undefined' || !Twilio.Video) {
        console.error('Twilio Video SDK not loaded');
        callStatus.innerHTML = '<p class="text-danger">Video SDK failed to load. Please refresh the page.</p>';
        setTimeout(function() {
            window.location.href = '/inbox';
        }, 3000);
        return;
    }
    
    console.log('Twilio Video SDK version:', Twilio.Video.version);

    // Connect to Twilio Video Room
    if (token && roomId) {
        // Validate token format (should be a JWT)
        if (!token || typeof token !== 'string' || token.length < 50) {
            const errorMsg = !token ? 'Token is missing' : 
                           token.length < 50 ? 'Token is too short (invalid format)' : 
                           'Invalid token format';
            callStatus.innerHTML = '<p class="text-danger">' + errorMsg + '. Please try again.</p>';
            console.error('Invalid token:', {
                exists: !!token,
                type: typeof token,
                length: token ? token.length : 0,
                preview: token ? token.substring(0, 20) + '...' : 'null'
            });
            
            // Try to regenerate token by reloading without token parameter
            setTimeout(function() {
                window.location.href = '/video-call?room=' + encodeURIComponent(roomId);
            }, 3000);
            return;
        }
        
        // Validate token is a JWT (starts with eyJ)
        if (!token.startsWith('eyJ')) {
            callStatus.innerHTML = '<p class="text-danger">Invalid token format (not a JWT). Please check Twilio configuration.</p>';
            console.error('Token is not a JWT:', token.substring(0, 20));
            setTimeout(function() {
                window.location.href = '/inbox';
            }, 5000);
            return;
        }
        
        console.log('Token validation passed. Requesting media permissions...');
        
        // Request camera and microphone permissions first
        navigator.mediaDevices.getUserMedia({ video: true, audio: true })
            .then(function(stream) {
                // Permissions granted, stop the stream and connect
                stream.getTracks().forEach(track => track.stop());
                
                console.log('Media permissions granted. Connecting to Twilio room...');
                console.log('Room:', roomId);
                console.log('Token length:', token.length);
                
                // Connect to Twilio room with better error handling
                // Add connection options for better WebSocket reliability
                const connectOptions = {
                    name: roomId,
                    audio: true,
                    video: { width: 1280, height: 720 },
                    logLevel: 'warn', // Reduce console noise
                    preferredVideoCodecs: [{ codec: 'VP8', simulcast: true }],
                    // Add connection timeout and retry options
                    iceServers: [], // Use Twilio's default ICE servers
                    // Enable automatic reconnection
                    automaticSubscription: true
                };
                
                console.log('Connection options:', connectOptions);
                console.log('Attempting Twilio.Video.connect...');
                
                return Twilio.Video.connect(token, connectOptions);
            })
            .then(function(connectedRoom) {
                room = connectedRoom;
                callStatus.style.display = 'none';

                // Attach local tracks
                room.localParticipant.tracks.forEach(function(track) {
                    if (track.kind === 'video') {
                        localTrack = track;
                        track.attach(localVideo);
                    } else if (track.kind === 'audio') {
                        // Audio is handled automatically
                    }
                });

                // Handle remote participants
                room.participants.forEach(function(participant) {
                    participant.tracks.forEach(function(track) {
                        if (track.kind === 'video') {
                            remoteTrack = track;
                            track.attach(remoteVideo);
                        }
                    });
                });

                // Listen for new participants
                room.on('participantConnected', function(participant) {
                    console.log('Participant connected:', participant.identity);
                    participant.tracks.forEach(function(track) {
                        if (track.kind === 'video') {
                            remoteTrack = track;
                            track.attach(remoteVideo);
                        }
                    });
                });

                // Handle track subscriptions
                room.on('trackSubscribed', function(track, publication, participant) {
                    console.log('Track subscribed:', track.kind, participant.identity);
                    if (track.kind === 'video') {
                        remoteTrack = track;
                        track.attach(remoteVideo);
                    }
                });

                // Handle track unsubscribed
                room.on('trackUnsubscribed', function(track) {
                    console.log('Track unsubscribed:', track.kind);
                    if (track.kind === 'video') {
                        track.detach();
                    }
                });

                // Handle disconnection
                room.on('disconnected', function(room, error) {
                    console.log('Room disconnected:', error);
                    endCall();
                });

                // Handle connection errors
                room.on('reconnecting', function(error) {
                    console.log('Reconnecting to room:', error);
                    callStatus.style.display = 'block';
                    callStatus.innerHTML = '<p class="text-warning">Reconnecting...</p>';
                });

                room.on('reconnected', function() {
                    console.log('Reconnected to room');
                    callStatus.style.display = 'none';
                });
            })
            .catch(function(error) {
                console.error('=== VIDEO CALL CONNECTION ERROR ===');
                console.error('Error object:', error);
                console.error('Error name:', error.name);
                console.error('Error message:', error.message);
                console.error('Error code:', error.code);
                console.error('Error stack:', error.stack);
                
                // Check for specific Twilio error codes
                if (error.code) {
                    console.error('Twilio error code:', error.code);
                    // Common Twilio error codes:
                    // 20101: Invalid token
                    // 20104: Token expired
                    // 20105: Network error
                    // 20108: Signaling connection error
                }
                
                let errorMessage = 'Failed to connect to video call. ';
                let showRetry = false;
                let showDiagnostic = false;
                
                if (error.name === 'NotAllowedError' || error.name === 'PermissionDeniedError') {
                    errorMessage += 'Please allow camera and microphone access in your browser settings.';
                } else if (error.name === 'NotFoundError' || error.name === 'DevicesNotFoundError') {
                    errorMessage += 'Camera or microphone not found. Please connect a device.';
                } else if (error.code === 20101) {
                    errorMessage += 'Invalid token. This usually means Twilio API credentials are missing or incorrect.';
                    showDiagnostic = true;
                } else if (error.code === 20104) {
                    errorMessage += 'Token expired. Please refresh and try again.';
                    showRetry = true;
                } else if (error.code === 53000) {
                    // Twilio error 53000: Signaling connection failed
                    errorMessage += 'Signaling connection failed (Error 53000). This usually means:<br>' +
                                   '• WebSocket connection to Twilio servers failed<br>' +
                                   '• Network/firewall blocking WebSocket connections<br>' +
                                   '• Browser blocking WebSocket connections<br>' +
                                   '• Token identity format issue<br>' +
                                   '<br><strong>Try these fixes:</strong><br>' +
                                   '1. Check your internet connection<br>' +
                                   '2. Try a different network (mobile hotspot)<br>' +
                                   '3. Check browser console for WebSocket errors<br>' +
                                   '4. Verify Twilio credentials are correct<br>' +
                                   '5. Try refreshing the page';
                    showRetry = true;
                    showDiagnostic = true;
                } else if (error.code === 20105 || error.code === 20108) {
                    errorMessage += 'Network or signaling connection error. This may be due to:<br>' +
                                   '• Firewall blocking WebSocket connections<br>' +
                                   '• Network connectivity issues<br>' +
                                   '• Twilio service unavailable<br>' +
                                   'Please check your internet connection and try again.';
                    showRetry = true;
                } else if (error.message && (error.message.includes('token') || error.message.includes('Token') || error.message.includes('authentication'))) {
                    errorMessage += 'Token authentication failed. Please check Twilio configuration.';
                    showDiagnostic = true;
                } else if (error.message && (error.message.includes('signaling') || error.message.includes('Signaling') || error.message.includes('WebSocket'))) {
                    errorMessage += 'Signaling connection error. This may be due to:<br>' +
                                   '• Network/firewall blocking WebSocket connections<br>' +
                                   '• Browser blocking WebSocket connections<br>' +
                                   '• Internet connectivity issues<br>' +
                                   'Please check your connection and try again.';
                    showRetry = true;
                } else if (error.message) {
                    errorMessage += error.message;
                    showRetry = true;
                } else {
                    errorMessage += 'Please check your connection and try again.';
                    showRetry = true;
                }
                
                let errorHtml = '<div class="text-danger mb-3">' + errorMessage + '</div>';
                errorHtml += '<div class="mt-2"><small class="text-muted">Error Code: ' + (error.code || 'N/A') + '</small></div>';
                
                if (showRetry) {
                    errorHtml += '<button id="retry-connection" class="btn btn-primary mt-3">Retry Connection</button>';
                }
                
                if (showDiagnostic) {
                    errorHtml += '<a href="/video-call/diagnose" target="_blank" class="btn btn-info mt-3 ml-2">Check Configuration</a>';
                }
                
                callStatus.innerHTML = errorHtml;
                
                // Add retry button handler
                if (showRetry) {
                    const retryBtn = document.getElementById('retry-connection');
                    if (retryBtn) {
                        retryBtn.addEventListener('click', function() {
                            console.log('Retrying connection...');
                            location.reload();
                        });
                    }
                }
                
                setTimeout(function() {
                    if (!showRetry && !showDiagnostic) {
                        window.location.href = '/inbox';
                    }
                }, 10000);
            });
    } else {
        let errorMsg = 'Missing ';
        if (!token) errorMsg += 'token';
        if (!roomId) errorMsg += (!token ? ' and ' : '') + 'room ID';
        errorMsg += '. Please try again.';
        
        callStatus.innerHTML = '<p class="text-danger">' + errorMsg + '</p>';
        console.error('Missing video call parameters:', { token: !!token, roomId: !!roomId });
        setTimeout(function() {
            window.location.href = '/inbox';
        }, 3000);
    }

    // Toggle Mute
    toggleMuteBtn.addEventListener('click', function() {
        if (room && room.localParticipant) {
            room.localParticipant.audioTracks.forEach(function(track) {
                if (isMuted) {
                    track.track.enable();
                } else {
                    track.track.disable();
                }
            });
            isMuted = !isMuted;
            this.innerHTML = isMuted ? '<i class="fa fa-microphone-slash"></i>' : '<i class="fa fa-microphone"></i>';
            this.style.background = isMuted ? 'rgba(220, 53, 69, 0.8)' : 'rgba(255,255,255,0.2)';
        }
    });

    // Toggle Video
    toggleVideoBtn.addEventListener('click', function() {
        if (room && room.localParticipant) {
            room.localParticipant.videoTracks.forEach(function(track) {
                if (isVideoEnabled) {
                    track.track.disable();
                } else {
                    track.track.enable();
                }
            });
            isVideoEnabled = !isVideoEnabled;
            this.innerHTML = isVideoEnabled ? '<i class="fa fa-video-camera"></i>' : '<i class="fa fa-video-slash"></i>';
            this.style.background = isVideoEnabled ? 'rgba(255,255,255,0.2)' : 'rgba(220, 53, 69, 0.8)';
        }
    });

    // End Call
    function endCall() {
        if (room) {
            room.disconnect();
        }

        // Call API to end the call
        fetch(`/video-call/end/${roomId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        }).then(function() {
            window.location.href = '/inbox';
        }).catch(function(error) {
            console.error('Error ending call:', error);
            window.location.href = '/inbox';
        });
    }

    endCallBtn.addEventListener('click', function() {
        endCall();
    });

    // Handle page unload
    window.addEventListener('beforeunload', function() {
        if (room) {
            room.disconnect();
        }
    });
});
</script>

<style>
body {
    overflow: hidden;
}

#local-video-container {
    z-index: 100;
}

#remote-video-container {
    z-index: 1;
}

.btn:hover {
    opacity: 0.8;
    transform: scale(1.05);
    transition: all 0.2s;
}

.btn:active {
    transform: scale(0.95);
}
</style>
@endsection

