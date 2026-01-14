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
                <button type="button" id="toggleMic" class="w-14 h-14 bg-gray-700 hover:bg-gray-600 rounded-full flex items-center justify-center transition cursor-pointer" style="pointer-events: auto;">
                    <svg id="micOnIcon" class="w-6 h-6 text-white pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                    </svg>
                    <svg id="micOffIcon" class="w-6 h-6 text-white hidden pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" clip-rule="evenodd"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"/>
                    </svg>
                </button>

                <!-- Video Toggle -->
                <button type="button" id="toggleVideo" class="w-14 h-14 bg-gray-700 hover:bg-gray-600 rounded-full flex items-center justify-center transition cursor-pointer" style="pointer-events: auto;">
                    <svg id="videoOnIcon" class="w-6 h-6 text-white pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    <svg id="videoOffIcon" class="w-6 h-6 text-white hidden pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                </button>

                <!-- End Call -->
                <button type="button" id="endCall" class="w-14 h-14 bg-red-600 hover:bg-red-700 rounded-full flex items-center justify-center transition cursor-pointer" style="pointer-events: auto;">
                    <svg class="w-6 h-6 text-white pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M5 3a2 2 0 00-2 2v1c0 8.284 6.716 15 15 15h1a2 2 0 002-2v-3.28a1 1 0 00-.684-.948l-4.493-1.498a1 1 0 00-1.21.502l-1.13 2.257a11.042 11.042 0 01-5.516-5.517l2.257-1.128a1 1 0 00.502-1.21L9.228 3.683A1 1 0 008.279 3H5z"/>
                    </svg>
                </button>

                <!-- Share Screen -->
                <button type="button" id="shareScreen" class="w-14 h-14 bg-gray-700 hover:bg-gray-600 rounded-full flex items-center justify-center transition cursor-pointer" style="pointer-events: auto;">
                    <svg class="w-6 h-6 text-white pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
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
                    if (peerConnection.connectionState === 'connected') {
                        updateStatus('connected');
                    } else if (peerConnection.connectionState === 'disconnected') {
                        updateStatus('disconnected');
                    }
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

        // Setup control buttons
        function setupControls() {
            console.log('Setting up control buttons...');

            const toggleMic = document.getElementById('toggleMic');
            const toggleVideo = document.getElementById('toggleVideo');
            const endCallBtn = document.getElementById('endCall');
            const shareScreenBtn = document.getElementById('shareScreen');

            if (!toggleMic || !toggleVideo || !endCallBtn || !shareScreenBtn) {
                console.error('One or more control buttons not found!');
                return;
            }

            console.log('All control buttons found:', {
                toggleMic: !!toggleMic,
                toggleVideo: !!toggleVideo,
                endCallBtn: !!endCallBtn,
                shareScreenBtn: !!shareScreenBtn
            });

            // Microphone Toggle
            toggleMic.addEventListener('click', (e) => {
                e.preventDefault();
                console.log('=== MIC BUTTON CLICKED ===');
                console.log('LocalStream exists:', !!localStream);

                if (!localStream) {
                    console.error('No local stream available');
                    alert('Belum terhubung ke kamera/mikrofon. Silakan tunggu sebentar.');
                    return;
                }

                const audioTrack = localStream.getAudioTracks()[0];
                console.log('Audio track:', audioTrack);

                if (!audioTrack) {
                    console.error('No audio track found');
                    alert('Audio track tidak ditemukan');
                    return;
                }

                console.log('Current audio enabled:', audioTrack.enabled);
                audioTrack.enabled = !audioTrack.enabled;
                console.log('New audio enabled:', audioTrack.enabled);

                const micOnIcon = document.getElementById('micOnIcon');
                const micOffIcon = document.getElementById('micOffIcon');

                if (audioTrack.enabled) {
                    micOnIcon.classList.remove('hidden');
                    micOffIcon.classList.add('hidden');
                    toggleMic.classList.remove('bg-red-600');
                    toggleMic.classList.add('bg-gray-700');
                    console.log('Mic UI set to ON');
                } else {
                    micOnIcon.classList.add('hidden');
                    micOffIcon.classList.remove('hidden');
                    toggleMic.classList.remove('bg-gray-700');
                    toggleMic.classList.add('bg-red-600');
                    console.log('Mic UI set to OFF');
                }

                console.log('Microphone is now:', audioTrack.enabled ? 'ON' : 'OFF');
            }, false);

            // Video Toggle
            toggleVideo.addEventListener('click', (e) => {
                e.preventDefault();
                console.log('=== VIDEO BUTTON CLICKED ===');
                console.log('LocalStream exists:', !!localStream);

                if (!localStream) {
                    console.error('No local stream available');
                    alert('Belum terhubung ke kamera/mikrofon. Silakan tunggu sebentar.');
                    return;
                }

                const videoTrack = localStream.getVideoTracks()[0];
                console.log('Video track:', videoTrack);

                if (!videoTrack) {
                    console.error('No video track found');
                    alert('Video track tidak ditemukan');
                    return;
                }

                console.log('Current video enabled:', videoTrack.enabled);
                videoTrack.enabled = !videoTrack.enabled;
                console.log('New video enabled:', videoTrack.enabled);

                const videoOnIcon = document.getElementById('videoOnIcon');
                const videoOffIcon = document.getElementById('videoOffIcon');

                if (videoTrack.enabled) {
                    videoOnIcon.classList.remove('hidden');
                    videoOffIcon.classList.add('hidden');
                    toggleVideo.classList.remove('bg-red-600');
                    toggleVideo.classList.add('bg-gray-700');
                    localVideo.style.opacity = '1';
                    console.log('Video UI set to ON');
                } else {
                    videoOnIcon.classList.add('hidden');
                    videoOffIcon.classList.remove('hidden');
                    toggleVideo.classList.remove('bg-gray-700');
                    toggleVideo.classList.add('bg-red-600');
                    localVideo.style.opacity = '0.3';
                    console.log('Video UI set to OFF');
                }

                console.log('Video is now:', videoTrack.enabled ? 'ON' : 'OFF');
            }, false);

            // End Call
            endCallBtn.addEventListener('click', async (e) => {
                e.preventDefault();
                console.log('End call button clicked');
                if (confirm('{{ __("messages.confirm_end_call") }}')) {
                    console.log('User confirmed end call');
                    await endCall();
                } else {
                    console.log('User cancelled end call');
                }
            }, false);

            // Share Screen (Toggle)
            shareScreenBtn.addEventListener('click', (e) => {
                e.preventDefault();
                console.log('=== SHARE SCREEN BUTTON CLICKED ===');
                console.log('Is currently sharing:', isScreenSharing);

                // If already sharing, stop it
                if (isScreenSharing) {
                    console.log('Stopping screen share...');
                    stopScreenSharing();
                    return;
                }

                // Start screen sharing
                console.log('Starting screen share...');

                if (!peerConnection) {
                    console.error('No peer connection available');
                    alert('Belum terhubung dengan peserta lain');
                    return;
                }

                if (!localStream) {
                    console.error('No local stream available');
                    alert('Belum terhubung ke kamera/mikrofon');
                    return;
                }

                // Call getDisplayMedia synchronously for Safari compatibility
                navigator.mediaDevices.getDisplayMedia({
                    video: {
                        cursor: 'always'
                    },
                    audio: false
                })
                    .then(stream => {
                        console.log('Screen sharing stream obtained:', stream);
                        screenStream = stream;
                        const screenTrack = screenStream.getVideoTracks()[0];

                        if (!screenTrack) {
                            console.error('No screen track found');
                            alert('Tidak dapat mendapatkan screen track');
                            return;
                        }

                        const videoSender = peerConnection.getSenders().find(s => s.track && s.track.kind === 'video');
                        if (!videoSender) {
                            console.error('No video sender found in peer connection');
                            alert('Tidak dapat menemukan video sender');
                            screenTrack.stop();
                            return;
                        }

                        // Replace camera track with screen track
                        videoSender.replaceTrack(screenTrack)
                            .then(() => {
                                console.log('Screen sharing started successfully');
                                isScreenSharing = true;

                                // Update local video to show screen share
                                localVideo.srcObject = screenStream;

                                // Change button appearance
                                shareScreenBtn.classList.remove('bg-gray-700');
                                shareScreenBtn.classList.add('bg-green-600');
                                shareScreenBtn.title = 'Klik untuk berhenti berbagi layar';

                                // When user stops sharing (clicks stop sharing in browser)
                                screenTrack.onended = () => {
                                    console.log('Screen sharing stopped by user (browser button)');
                                    stopScreenSharing();
                                };
                            })
                            .catch(error => {
                                console.error('Error replacing track:', error);
                                alert('Gagal mengganti track: ' + error.message);
                                screenTrack.stop();
                                screenStream = null;
                            });
                    })
                    .catch(error => {
                        console.error('Error sharing screen:', error);
                        if (error.name === 'NotAllowedError') {
                            alert('Izin untuk membagikan layar ditolak');
                        } else if (error.name === 'NotFoundError') {
                            alert('Tidak ada layar yang dapat dibagikan');
                        } else {
                            alert('Tidak dapat membagikan layar: ' + error.message);
                        }
                    });
            }, false);

            console.log('Control buttons setup complete');
        }

        function stopScreenSharing() {
            console.log('=== Stopping screen sharing ===');

            if (!isScreenSharing) {
                console.log('Not currently sharing, nothing to stop');
                return;
            }

            if (!localStream) {
                console.error('No local stream to restore');
                isScreenSharing = false;
                return;
            }

            const cameraTrack = localStream.getVideoTracks()[0];
            if (!cameraTrack) {
                console.error('No camera track to restore');
                isScreenSharing = false;
                return;
            }

            const videoSender = peerConnection.getSenders().find(s => s.track && s.track.kind === 'video');
            if (!videoSender) {
                console.error('No video sender found');
                isScreenSharing = false;
                return;
            }

            // Stop screen stream tracks
            if (screenStream) {
                screenStream.getTracks().forEach(track => {
                    console.log('Stopping screen track:', track.kind);
                    track.stop();
                });
                screenStream = null;
            }

            // Replace screen track with camera track
            videoSender.replaceTrack(cameraTrack)
                .then(() => {
                    console.log('Restored camera feed');
                    isScreenSharing = false;

                    // Restore local video to camera
                    localVideo.srcObject = localStream;

                    // Reset button appearance
                    const shareScreenBtn = document.getElementById('shareScreen');
                    shareScreenBtn.classList.remove('bg-green-600');
                    shareScreenBtn.classList.add('bg-gray-700');
                    shareScreenBtn.title = 'Bagikan layar';

                    console.log('Screen sharing stopped successfully');
                })
                .catch(error => {
                    console.error('Error restoring camera:', error);
                    isScreenSharing = false;
                });
        }

        async function endCall() {
            console.log('=== Starting end call process ===');
            try {
                // Stop all tracks
                console.log('Stopping local stream...');
                if (localStream) {
                    localStream.getTracks().forEach(track => {
                        console.log('Stopping track:', track.kind, track.label);
                        track.stop();
                    });
                }

                console.log('Stopping remote stream...');
                if (remoteStream) {
                    remoteStream.getTracks().forEach(track => {
                        console.log('Stopping remote track:', track.kind);
                        track.stop();
                    });
                }

                // Close peer connection
                console.log('Closing peer connection...');
                if (peerConnection) {
                    peerConnection.close();
                    console.log('Peer connection closed');
                }

                // Stop timer
                console.log('Stopping timer...');
                if (timerInterval) {
                    clearInterval(timerInterval);
                    console.log('Timer stopped');
                }

                // Notify server
                console.log('Notifying server about call end...');
                const response = await fetch(`/video-chat/appointments/${appointmentId}/end`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                console.log('Server response:', response.status, await response.text());

                // Redirect
                console.log('Redirecting to appointments page...');
                window.location.href = '/appointments/{{ $appointment->id }}';

            } catch (error) {
                console.error('Error ending call:', error);
                alert('Error mengakhiri panggilan: ' + error.message);
                // Still try to redirect even if there's an error
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

                timerDisplay.textContent =
                    `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
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

        // Initialize on load
        window.addEventListener('load', () => {
            console.log('Page loaded, initializing...');
            setupControls();  // Setup control buttons first
            init();  // Then initialize video chat
        });

        // Also setup on DOMContentLoaded as backup
        document.addEventListener('DOMContentLoaded', () => {
            console.log('DOM ready');
            if (!window.controlsSetup) {
                setupControls();
                window.controlsSetup = true;
            }
        });

        // Clean up on page unload
        window.addEventListener('beforeunload', () => {
            console.log('Page unloading, cleaning up...');
            if (localStream) {
                localStream.getTracks().forEach(track => track.stop());
            }
            if (peerConnection) {
                peerConnection.close();
            }
        });
    </script>
    @endpush
</x-app-layout>
