<x-app-layout>
    <div class="min-h-screen bg-gray-900">
        <!-- Top Bar -->
        <div class="bg-gray-800 border-b border-gray-700 px-6 py-4">
            <div class="max-w-7xl mx-auto flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 bg-purple-600 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-white font-semibold text-lg">{{ __('messages.video_consultation') }}</h1>
                        <p class="text-gray-400 text-sm">
                            {{ $isProvider ? __('messages.client') : __('messages.professional') }}:
                            {{ $isProvider ? $appointment->user->name : $appointment->professional->user->name }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center space-x-2">
                    <span class="text-white font-mono" id="timer">00:00:00</span>
                    <span class="px-3 py-1 bg-green-600 text-white text-sm rounded-full" id="status-badge">
                        {{ __('messages.connecting') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Video Container -->
        <div class="relative h-[calc(100vh-80px)]">
            <!-- Remote Video (Main) -->
            <div class="absolute inset-0 bg-gray-900 flex items-center justify-center">
                <video id="remoteVideo" autoplay playsinline class="w-full h-full object-contain"></video>
                <div id="waitingMessage" class="absolute inset-0 flex flex-col items-center justify-center bg-gray-900">
                    <div class="w-24 h-24 bg-gray-800 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <p class="text-white text-lg mb-2">{{ __('messages.waiting_for_participant') }}</p>
                    <p class="text-gray-400 text-sm">{{ __('messages.other_participant_will_join') }}</p>
                </div>
            </div>

            <!-- Local Video (Picture-in-Picture) -->
            <div class="absolute bottom-6 right-6 w-64 h-48 bg-gray-800 rounded-lg overflow-hidden shadow-2xl border-2 border-gray-700">
                <video id="localVideo" autoplay muted playsinline class="w-full h-full object-cover"></video>
                <div class="absolute bottom-2 left-2 px-2 py-1 bg-black bg-opacity-60 text-white text-xs rounded">
                    {{ __('messages.you') }}
                </div>
            </div>

            <!-- Controls -->
            <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2 flex items-center space-x-4">
                <!-- Microphone Toggle -->
                <button type="button" id="toggleMic" onclick="return window.toggleMic && window.toggleMic()" class="w-14 h-14 bg-gray-700 hover:bg-gray-600 rounded-full flex items-center justify-center transition cursor-pointer">
                    <svg id="micOnIcon" class="w-6 h-6 text-white pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                    </svg>
                    <svg id="micOffIcon" class="w-6 h-6 text-white hidden pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" clip-rule="evenodd"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"/>
                    </svg>
                </button>

                <!-- Video Toggle -->
                <button type="button" id="toggleVideo" onclick="return window.toggleVideo && window.toggleVideo()" class="w-14 h-14 bg-gray-700 hover:bg-gray-600 rounded-full flex items-center justify-center transition cursor-pointer">
                    <svg id="videoOnIcon" class="w-6 h-6 text-white pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    <svg id="videoOffIcon" class="w-6 h-6 text-white hidden pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                </button>

                <!-- End Call -->
                <button type="button" id="endCall" onclick="return window.handleEnd && window.handleEnd()" class="w-14 h-14 bg-red-600 hover:bg-red-700 rounded-full flex items-center justify-center transition cursor-pointer">
                    <svg class="w-6 h-6 text-white pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M5 3a2 2 0 00-2 2v1c0 8.284 6.716 15 15 15h1a2 2 0 002-2v-3.28a1 1 0 00-.684-.948l-4.493-1.498a1 1 0 00-1.21.502l-1.13 2.257a11.042 11.042 0 01-5.516-5.517l2.257-1.128a1 1 0 00.502-1.21L9.228 3.683A1 1 0 008.279 3H5z"/>
                    </svg>
                </button>

                <!-- Share Screen -->
                <button type="button" id="shareScreen" onclick="return window.toggleScreen && window.toggleScreen()" class="w-14 h-14 bg-gray-700 hover:bg-gray-600 rounded-full flex items-center justify-center transition cursor-pointer">
                    <svg class="w-6 h-6 text-white pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        console.log('Video chat script loading...');

        const appointmentId = {{ $appointment->id }};
        const roomId = '{{ $room->room_id }}';
        const userName = '{{ $userName }}';
        const isProvider = {{ $isProvider ? 'true' : 'false' }};

        // WebRTC Configuration
        const configuration = {
            iceServers: [
                { urls: 'stun:stun.l.google.com:19302' },
                { urls: 'stun:stun1.l.google.com:19302' },
            ]
        };

        let localStream;
        let remoteStream;
        let peerConnection;
        let startTime;
        let timerInterval;
        let isScreenSharing = false;
        let screenStream = null;

        const localVideo = document.getElementById('localVideo');
        const remoteVideo = document.getElementById('remoteVideo');
        const waitingMessage = document.getElementById('waitingMessage');
        const statusBadge = document.getElementById('status-badge');
        const timerDisplay = document.getElementById('timer');

        // Initialize
        async function init() {
            try {
                console.log('Requesting camera and microphone access...');

                // Get user media with better constraints
                localStream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        width: { ideal: 1280 },
                        height: { ideal: 720 }
                    },
                    audio: {
                        echoCancellation: true,
                        noiseSuppression: true
                    }
                });

                console.log('Got local stream:', localStream);
                console.log('Video tracks:', localStream.getVideoTracks());
                console.log('Audio tracks:', localStream.getAudioTracks());

                // Set video source
                localVideo.srcObject = localStream;

                // Force play the video (some browsers need this)
                try {
                    await localVideo.play();
                    console.log('Local video playing successfully!');
                } catch (playError) {
                    console.warn('Auto-play prevented, but video should still work:', playError);
                }

                // Show success message
                console.log('Camera and microphone access granted!');

                // Check if video is actually playing
                setTimeout(() => {
                    if (localVideo.paused) {
                        console.warn('Local video is paused, trying to play again...');
                        localVideo.play().catch(e => console.error('Play failed:', e));
                    }
                    console.log('Local video state:', {
                        paused: localVideo.paused,
                        readyState: localVideo.readyState,
                        videoWidth: localVideo.videoWidth,
                        videoHeight: localVideo.videoHeight
                    });
                }, 1000);

                // Create peer connection
                peerConnection = new RTCPeerConnection(configuration);

                // Add local tracks to peer connection
                localStream.getTracks().forEach(track => {
                    console.log('Adding track:', track.kind, track.label);
                    peerConnection.addTrack(track, localStream);
                });

                // Handle incoming tracks
                peerConnection.ontrack = (event) => {
                    console.log('Received remote track:', event.track.kind);
                    if (!remoteStream) {
                        remoteStream = new MediaStream();
                        remoteVideo.srcObject = remoteStream;
                    }
                    remoteStream.addTrack(event.track);
                    waitingMessage.style.display = 'none';
                    startTimer();
                    updateStatus('connected');
                };

                // Handle ICE candidates
                peerConnection.onicecandidate = (event) => {
                    if (event.candidate) {
                        console.log('ICE candidate:', event.candidate);
                        sendSignal({
                            type: 'candidate',
                            candidate: event.candidate
                        });
                    }
                };

                // Handle connection state changes
                peerConnection.onconnectionstatechange = () => {
                    console.log('Connection state:', peerConnection.connectionState);
                    updateStatus(peerConnection.connectionState);
                };

                // Start the call
                await startCall();

                // Poll for signals every 2 seconds
                setInterval(pollSignals, 2000);

            } catch (error) {
                console.error('Error initializing:', error);
                let errorMessage = '{{ __("messages.error_accessing_camera") }}';

                if (error.name === 'NotAllowedError') {
                    errorMessage = 'Izin akses kamera dan mikrofon ditolak. Silakan klik ikon gembok di address bar dan izinkan akses kamera/mikrofon.';
                } else if (error.name === 'NotFoundError') {
                    errorMessage = 'Kamera atau mikrofon tidak ditemukan. Pastikan perangkat terhubung dengan benar.';
                } else if (error.name === 'NotReadableError') {
                    errorMessage = 'Kamera atau mikrofon sedang digunakan oleh aplikasi lain. Tutup aplikasi lain yang menggunakan kamera/mikrofon.';
                }

                alert(errorMessage + '\n\nError: ' + error.message);

                // Show error in the waiting message area
                waitingMessage.innerHTML = `
                    <div class="text-center p-6">
                        <div class="w-24 h-24 bg-red-900 rounded-full flex items-center justify-center mb-4 mx-auto">
                            <svg class="w-12 h-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-white text-lg mb-2">Tidak dapat mengakses kamera/mikrofon</p>
                        <p class="text-gray-400 text-sm mb-4">${errorMessage}</p>
                        <button onclick="location.reload()" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg">
                            Coba Lagi
                        </button>
                    </div>
                `;
            }
        }

        async function startCall() {
            try {
                const response = await fetch(`/video-chat/appointments/${appointmentId}/start`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await response.json();
                console.log('Call started:', data);

                // Create and send offer
                const offer = await peerConnection.createOffer();
                await peerConnection.setLocalDescription(offer);

                sendSignal({
                    type: 'offer',
                    offer: offer
                });

            } catch (error) {
                console.error('Error starting call:', error);
            }
        }

        async function sendSignal(data) {
            try {
                await fetch(`/video-chat/appointments/${appointmentId}/signal`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        room_id: roomId,
                        signal: data
                    })
                });
            } catch (error) {
                console.error('Error sending signal:', error);
            }
        }

        async function pollSignals() {
            // In production, you would use WebSockets or a proper signaling server
            // For now, this is a placeholder for the signaling mechanism
            console.log('Polling for signals...');
        }

        async function handleSignal(signal) {
            try {
                if (signal.type === 'offer' && !isProvider) {
                    await peerConnection.setRemoteDescription(new RTCSessionDescription(signal.offer));
                    const answer = await peerConnection.createAnswer();
                    await peerConnection.setLocalDescription(answer);
                    sendSignal({
                        type: 'answer',
                        answer: answer
                    });
                } else if (signal.type === 'answer' && isProvider) {
                    await peerConnection.setRemoteDescription(new RTCSessionDescription(signal.answer));
                } else if (signal.type === 'candidate') {
                    await peerConnection.addIceCandidate(new RTCIceCandidate(signal.candidate));
                }
            } catch (error) {
                console.error('Error handling signal:', error);
            }
        }

        // Global toggle functions (Zoom-style)
        window.toggleMic = function() {
            console.log('=== MIC BUTTON CLICKED ===');

            if (!localStream) {
                console.error('No local stream');
                alert('Belum terhubung ke kamera/mikrofon');
                return false;
            }

            const audioTrack = localStream.getAudioTracks()[0];
            if (!audioTrack) {
                console.error('No audio track');
                return false;
            }

            audioTrack.enabled = !audioTrack.enabled;
            console.log('Audio enabled:', audioTrack.enabled);

            const btn = document.getElementById('toggleMic');
            const onIcon = document.getElementById('micOnIcon');
            const offIcon = document.getElementById('micOffIcon');

            if (audioTrack.enabled) {
                onIcon.classList.remove('hidden');
                offIcon.classList.add('hidden');
                btn.classList.remove('bg-red-600');
                btn.classList.add('bg-gray-700');
            } else {
                onIcon.classList.add('hidden');
                offIcon.classList.remove('hidden');
                btn.classList.remove('bg-gray-700');
                btn.classList.add('bg-red-600');
            }

            return false;
        };

        window.toggleVideo = function() {
            console.log('=== VIDEO BUTTON CLICKED ===');

            if (!localStream) {
                console.error('No local stream');
                alert('Belum terhubung ke kamera/mikrofon');
                return false;
            }

            const videoTrack = localStream.getVideoTracks()[0];
            if (!videoTrack) {
                console.error('No video track');
                return false;
            }

            videoTrack.enabled = !videoTrack.enabled;
            console.log('Video enabled:', videoTrack.enabled);

            const btn = document.getElementById('toggleVideo');
            const onIcon = document.getElementById('videoOnIcon');
            const offIcon = document.getElementById('videoOffIcon');

            if (videoTrack.enabled) {
                onIcon.classList.remove('hidden');
                offIcon.classList.add('hidden');
                btn.classList.remove('bg-red-600');
                btn.classList.add('bg-gray-700');
                localVideo.style.opacity = '1';
            } else {
                onIcon.classList.add('hidden');
                offIcon.classList.remove('hidden');
                btn.classList.remove('bg-gray-700');
                btn.classList.add('bg-red-600');
                localVideo.style.opacity = '0.3';
            }

            return false;
        };

        window.handleEnd = async function() {
            console.log('End call clicked');
            if (confirm('{{ __("messages.confirm_end_call") }}')) {
                await endCall();
            }
            return false;
        };

        window.toggleScreen = function() {
            console.log('=== SHARE SCREEN CLICKED ===');

            if (isScreenSharing) {
                stopScreenSharing();
                return false;
            }

            if (!peerConnection || !localStream) {
                alert('Belum terhubung');
                return false;
            }

            navigator.mediaDevices.getDisplayMedia({
                video: { cursor: 'always' },
                audio: false
            })
            .then(stream => {
                screenStream = stream;
                const screenTrack = screenStream.getVideoTracks()[0];
                const videoSender = peerConnection.getSenders().find(s => s.track && s.track.kind === 'video');

                if (!videoSender) {
                    alert('Tidak dapat menemukan video sender');
                    screenTrack.stop();
                    return;
                }

                videoSender.replaceTrack(screenTrack)
                    .then(() => {
                        isScreenSharing = true;
                        localVideo.srcObject = screenStream;

                        const btn = document.getElementById('shareScreen');
                        btn.classList.remove('bg-gray-700');
                        btn.classList.add('bg-green-600');

                        screenTrack.onended = () => stopScreenSharing();
                    });
            })
            .catch(error => {
                console.error('Screen share error:', error);
                if (error.name === 'NotAllowedError') {
                    alert('Izin berbagi layar ditolak');
                }
            });

            return false;
        };

        function stopScreenSharing() {
            if (!isScreenSharing) return;

            if (screenStream) {
                screenStream.getTracks().forEach(track => track.stop());
                screenStream = null;
            }

            const cameraTrack = localStream.getVideoTracks()[0];
            const videoSender = peerConnection.getSenders().find(s => s.track && s.track.kind === 'video');

            if (videoSender && cameraTrack) {
                videoSender.replaceTrack(cameraTrack)
                    .then(() => {
                        isScreenSharing = false;
                        localVideo.srcObject = localStream;

                        const btn = document.getElementById('shareScreen');
                        btn.classList.remove('bg-green-600');
                        btn.classList.add('bg-gray-700');
                    });
            }
        }

        async function endCall() {
            console.log('=== Ending call ===');
            try {
                if (localStream) {
                    localStream.getTracks().forEach(track => track.stop());
                }
                if (remoteStream) {
                    remoteStream.getTracks().forEach(track => track.stop());
                }
                if (peerConnection) {
                    peerConnection.close();
                }
                if (timerInterval) {
                    clearInterval(timerInterval);
                }

                await fetch(`/video-chat/appointments/${appointmentId}/end`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                window.location.href = '/appointments/{{ $appointment->id }}';
            } catch (error) {
                console.error('Error ending call:', error);
                window.location.href = '/appointments';
            }
        }

        function startTimer() {
            startTime = Date.now();
            timerInterval = setInterval(() => {
                const elapsed = Date.now() - startTime;
                const hours = Math.floor(elapsed / 3600000);
                const minutes = Math.floor((elapsed % 3600000) / 60000);
                const seconds = Math.floor((elapsed % 60000) / 1000);
                timerDisplay.textContent = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            }, 1000);
        }

        function updateStatus(status) {
            const statusTexts = {
                'connecting': '{{ __("messages.connecting") }}',
                'connected': '{{ __("messages.connected") }}',
                'disconnected': '{{ __("messages.disconnected") }}'
            };

            const statusColors = {
                'connecting': 'bg-yellow-600',
                'connected': 'bg-green-600',
                'disconnected': 'bg-red-600'
            };

            statusBadge.textContent = statusTexts[status];
            statusBadge.className = `px-3 py-1 text-white text-sm rounded-full ${statusColors[status]}`;
        }

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', () => {
            console.log('DOM ready, initializing...');
            init();
        });

        // Clean up on page unload
        window.addEventListener('beforeunload', () => {
            if (localStream) {
                localStream.getTracks().forEach(track => track.stop());
            }
            if (peerConnection) {
                peerConnection.close();
            }
        });

        console.log('Video chat script loaded successfully');
    </script>
    @endpush
</x-app-layout>
