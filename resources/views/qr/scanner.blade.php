<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quét QR Code - {{ $group->name ?? 'Group' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Load HTML5 QR Code from CDN -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <style>
        .scanner-container {
            position: relative;
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
        }
        
        .success-animation {
            animation: successPulse 0.6s ease-in-out;
        }
        
        @keyframes successPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-3 sm:px-4 py-4 sm:py-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 mb-4 sm:mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-800">
                        <i class="fas fa-qrcode mr-2 text-blue-600"></i>
                        Quét QR Code
                    </h1>
                    <p class="text-gray-600 mt-1 text-sm sm:text-base">
                        Group: <span class="font-semibold text-blue-600">{{ $group->name ?? 'Chưa có group' }}</span>
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                    <a href="{{ route('qr.statistics', $group->id ?? 1) }}" 
                       class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center justify-center text-sm">
                        <i class="fas fa-chart-bar mr-2"></i>
                        Thống kê
                    </a>
                    <a href="{{ route('group.index') }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center justify-center text-sm">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Quay lại
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 lg:gap-6">
            <!-- Camera Scanner Section -->
            <div class="bg-white rounded-lg shadow-md p-4 sm:p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    <i class="fas fa-camera mr-2 text-blue-600"></i>
                    Camera Scanner
                </h2>
                
                <div class="scanner-container">
                    <div id="qr-reader" class="w-full"></div>
                </div>
                
                <div class="mt-4 flex flex-col sm:flex-row gap-2 sm:gap-3 justify-center">
                    <button id="startScanner" class="bg-blue-600 hover:bg-blue-700 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg flex items-center justify-center text-sm sm:text-base w-full sm:w-auto">
                        <i class="fas fa-play mr-2"></i>
                        Bắt đầu quét
                    </button>
                    <button id="testConnection" class="bg-green-600 hover:bg-green-700 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg flex items-center justify-center text-sm sm:text-base w-full sm:w-auto">
                        <i class="fas fa-wifi mr-2"></i>
                        Test kết nối
                    </button>
                    <button id="testSimple" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg flex items-center justify-center text-sm sm:text-base w-full sm:w-auto">
                        <i class="fas fa-bolt mr-2"></i>
                        Test đơn giản
                    </button>
                    <button id="debugCsrf" class="bg-purple-600 hover:bg-purple-700 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg flex items-center justify-center text-sm sm:text-base w-full sm:w-auto">
                        <i class="fas fa-bug mr-2"></i>
                        Debug CSRF
                    </button>
                    <button id="refreshCsrf" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg flex items-center justify-center text-sm sm:text-base w-full sm:w-auto">
                        <i class="fas fa-sync mr-2"></i>
                        Refresh CSRF
                    </button>
                    <button id="testXhr" class="bg-red-600 hover:bg-red-700 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg flex items-center justify-center text-sm sm:text-base w-full sm:w-auto">
                        <i class="fas fa-code mr-2"></i>
                        Test XHR
                    </button>
                    <button id="testBasic" class="bg-orange-600 hover:bg-orange-700 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg flex items-center justify-center text-sm sm:text-base w-full sm:w-auto">
                        <i class="fas fa-circle mr-2"></i>
                        Test Basic
                    </button>
                    <button id="testGet" class="bg-teal-600 hover:bg-teal-700 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg flex items-center justify-center text-sm sm:text-base w-full sm:w-auto">
                        <i class="fas fa-download mr-2"></i>
                        Test GET
                    </button>
                    <button id="testUrl" class="bg-pink-600 hover:bg-pink-700 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg flex items-center justify-center text-sm sm:text-base w-full sm:w-auto">
                        <i class="fas fa-link mr-2"></i>
                        Test URL
                    </button>
                </div>
                
                <div class="mt-4 text-center">
                    <p class="text-sm text-gray-600">
                        <i class="fas fa-info-circle mr-1"></i>
                        Hướng camera vào mã QR để quét
                    </p>
                </div>
                
                <!-- Manual Input -->
                <div class="mt-4 sm:mt-6 pt-4 sm:pt-6 border-t">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-3">
                        <i class="fas fa-keyboard mr-2 text-purple-600"></i>
                        Hoặc nhập thủ công
                    </h3>
                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                        <input type="text" id="manualInput" 
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm sm:text-base"
                               placeholder="Nhập MSSV hoặc nội dung QR code...">
                        <button id="manualScanBtn" class="bg-purple-600 hover:bg-purple-700 text-white px-4 sm:px-6 py-2 rounded-lg flex items-center justify-center text-sm sm:text-base">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Gửi
                        </button>
                    </div>
                </div>
            </div>

            <!-- QR Code Info Section -->
            <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 mb-4 sm:mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                    Thông tin QR Code
                </h2>
                
                <div id="qr-info" class="space-y-3">
                    <div class="text-center text-gray-500 py-4">
                        <i class="fas fa-qrcode text-2xl mb-2"></i>
                        <p>Chưa có QR code nào được quét</p>
                    </div>
                </div>
            </div>

            <!-- Results Section -->
            <div class="bg-white rounded-lg shadow-md p-4 sm:p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    <i class="fas fa-list mr-2 text-green-600"></i>
                    Kết quả quét
                </h2>
                
                <div id="scan-results" class="space-y-3 max-h-96 overflow-y-auto">
                    <div class="text-center text-gray-500 py-8">
                        <i class="fas fa-qrcode text-3xl mb-2"></i>
                        <p>Chưa có kết quả quét nào</p>
                    </div>
                </div>
                
                <div class="mt-4 pt-4 border-t">
                    <div class="flex justify-between items-center text-sm text-gray-600">
                        <span>Tổng số lần quét:</span>
                        <span id="total-scans" class="font-semibold text-blue-600">0</span>
                    </div>
                    <div class="flex justify-between items-center text-sm text-gray-600 mt-1">
                        <span>Sinh viên đã quét:</span>
                        <span id="unique-students" class="font-semibold text-green-600">0</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Modal -->
        <div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
            <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4 success-animation">
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-check text-2xl text-green-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Quét thành công!</h3>
                    <div id="success-details" class="text-gray-600 mb-4">
                        <!-- Success details will be inserted here -->
                    </div>
                    <button onclick="closeSuccessModal()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg">
                        Đóng
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let html5QrcodeScanner = null;
        let isScanning = false;
        let scanCount = 0;
        let uniqueStudents = new Set();

        // Initialize scanner
        document.addEventListener('DOMContentLoaded', function() {
            // Check if Html5Qrcode is loaded
            console.log('Html5Qrcode available:', typeof Html5Qrcode !== 'undefined');
            if (typeof Html5Qrcode !== 'undefined') {
                console.log('Html5Qrcode version:', Html5Qrcode.version || 'unknown');
            }
            
            document.getElementById('startScanner').addEventListener('click', startScanner);
            document.getElementById('testConnection').addEventListener('click', testConnection);
            document.getElementById('testSimple').addEventListener('click', testSimple);
            document.getElementById('debugCsrf').addEventListener('click', debugCsrf);
            document.getElementById('refreshCsrf').addEventListener('click', refreshCsrf);
            document.getElementById('testXhr').addEventListener('click', testXhr);
            document.getElementById('testBasic').addEventListener('click', testBasic);
            document.getElementById('testGet').addEventListener('click', testGet);
            document.getElementById('testUrl').addEventListener('click', testUrl);
            document.getElementById('manualScanBtn').addEventListener('click', processManualInput);
            
            // Enter key for manual input
            document.getElementById('manualInput').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    processManualInput();
                }
            });
        });

        async function testUrl() {
            try {
                console.log('Testing URL access...');
                showMessage('Đang test URL access...', 'info');
                
                // Test trực tiếp URL
                const testUrl = window.location.origin + '/test-get';
                console.log('Testing URL:', testUrl);
                
                // Thử mở URL trong tab mới
                window.open(testUrl, '_blank');
                
                showMessage('Đã mở URL test trong tab mới. Kiểm tra tab đó.', 'info');
                
            } catch (error) {
                console.error('URL test failed:', error);
                showMessage('URL test lỗi: ' + error.message, 'error');
            }
        }

        async function testGet() {
            try {
                console.log('Testing GET request...');
                showMessage('Đang test GET request...', 'info');
                
                const response = await fetch('/test-get', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                
                console.log('GET response status:', response.status);
                
                if (response.ok) {
                    const result = await response.json();
                    console.log('GET test successful:', result);
                    showMessage('GET test OK!', 'success');
                } else {
                    throw new Error(`HTTP ${response.status}`);
                }
                
            } catch (error) {
                console.error('GET test failed:', error);
                showMessage('GET test lỗi: ' + error.message, 'error');
            }
        }

        async function testBasic() {
            try {
                console.log('Testing basic POST...');
                showMessage('Đang test basic POST...', 'info');
                
                const response = await fetch('/test-basic', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ test: 'basic' })
                });
                
                console.log('Basic POST response status:', response.status);
                
                if (response.ok) {
                    const result = await response.json();
                    console.log('Basic POST test successful:', result);
                    showMessage('Basic POST test OK!', 'success');
                } else {
                    throw new Error(`HTTP ${response.status}`);
                }
                
            } catch (error) {
                console.error('Basic POST test failed:', error);
                showMessage('Basic POST test lỗi: ' + error.message, 'error');
            }
        }

        async function testXhr() {
            try {
                console.log('Testing XMLHttpRequest...');
                showMessage('Đang test XMLHttpRequest...', 'info');
                
                const csrfToken = '{{ csrf_token() }}';
                const testUrl = new URL('/test-xhr', window.location.origin).href;
                
                console.log('XHR Test URL:', testUrl);
                console.log('XHR CSRF Token:', csrfToken);
                
                const xhr = new XMLHttpRequest();
                
                xhr.open('POST', testUrl, true);
                xhr.setRequestHeader('Content-Type', 'application/json');
                xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                xhr.setRequestHeader('Accept', 'application/json');
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4) {
                        console.log('XHR Test Status:', xhr.status);
                        console.log('XHR Test Response:', xhr.responseText);
                        
                        if (xhr.status >= 200 && xhr.status < 300) {
                            try {
                                const result = JSON.parse(xhr.responseText);
                                console.log('XHR Test Success:', result);
                                showMessage('XMLHttpRequest test OK!', 'success');
                            } catch (e) {
                                console.error('XHR Test Parse error:', e);
                                showMessage('XHR Test parse lỗi: ' + e.message, 'error');
                            }
                        } else {
                            console.error('XHR Test Error:', xhr.status, xhr.responseText);
                            showMessage('XHR Test lỗi HTTP ' + xhr.status, 'error');
                        }
                    }
                };
                
                xhr.onerror = function() {
                    console.error('XHR Test Network error');
                    showMessage('XHR Test lỗi mạng', 'error');
                };
                
                xhr.send(JSON.stringify({ test: 'xhr connection' }));
                
            } catch (error) {
                console.error('XHR Test failed:', error);
                showMessage('XHR Test lỗi: ' + error.message, 'error');
            }
        }

        async function refreshCsrf() {
            try {
                console.log('Refreshing CSRF token...');
                showMessage('Đang refresh CSRF token...', 'info');
                
                const response = await fetch('/refresh-csrf', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    },
                    credentials: 'include'
                });
                
                if (response.ok) {
                    const result = await response.json();
                    console.log('New CSRF token:', result.csrf_token);
                    
                    // Cập nhật meta tag
                    const metaToken = document.querySelector('meta[name="csrf-token"]');
                    if (metaToken) {
                        metaToken.setAttribute('content', result.csrf_token);
                    }
                    
                    showMessage('CSRF token đã được refresh!', 'success');
                } else {
                    throw new Error(`HTTP ${response.status}`);
                }
                
            } catch (error) {
                console.error('CSRF refresh failed:', error);
                showMessage('Refresh CSRF lỗi: ' + error.message, 'error');
            }
        }

        async function debugCsrf() {
            try {
                console.log('Debugging CSRF...');
                showMessage('Đang debug CSRF...', 'info');
                
                const response = await fetch('/debug-csrf', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                
                if (response.ok) {
                    const result = await response.json();
                    console.log('CSRF Debug info:', result);
                    showMessage('CSRF Debug OK! Xem console.', 'success');
                } else {
                    throw new Error(`HTTP ${response.status}`);
                }
                
            } catch (error) {
                console.error('CSRF Debug failed:', error);
                showMessage('CSRF Debug lỗi: ' + error.message, 'error');
            }
        }

        async function testSimple() {
            try {
                console.log('Testing simple connection...');
                showMessage('Đang test đơn giản...', 'info');
                
                const response = await fetch('/test-simple', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ test: 'simple' })
                });
                
                console.log('Simple test response status:', response.status);
                
                if (response.ok) {
                    const result = await response.json();
                    console.log('Simple test successful:', result);
                    showMessage('Test đơn giản OK!', 'success');
                } else {
                    throw new Error(`HTTP ${response.status}`);
                }
                
            } catch (error) {
                console.error('Simple test failed:', error);
                showMessage('Test đơn giản lỗi: ' + error.message, 'error');
            }
        }

        async function testConnection() {
            try {
                console.log('Testing connection...');
                showMessage('Đang test kết nối...', 'info');
                
                // Test với cùng headers như QR scan
                const csrfToken = '{{ csrf_token() }}';
                const testUrl = new URL('/test-mobile', window.location.origin).href;
                
                console.log('Test URL:', testUrl);
                console.log('CSRF Token:', csrfToken);
                
                const response = await fetch(testUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Cache-Control': 'no-cache',
                        'Pragma': 'no-cache'
                    },
                    credentials: 'include',
                    mode: 'cors',
                    body: JSON.stringify({ 
                        test: 'mobile connection',
                        group_id: {{ $group->id ?? 1 }}
                    })
                });
                
                console.log('Test response status:', response.status);
                console.log('Test response headers:', response.headers);
                
                if (response.ok) {
                    const result = await response.json();
                    console.log('Connection test successful:', result);
                    showMessage('Kết nối OK! Có thể quét QR code.', 'success');
                } else {
                    const errorText = await response.text();
                    console.error('Test response error:', errorText);
                    throw new Error(`HTTP ${response.status}: ${errorText}`);
                }
                
            } catch (error) {
                console.error('Connection test failed:', error);
                showMessage('Lỗi kết nối: ' + error.message, 'error');
            }
        }

        async function startScanner() {
            try {
                // Kiểm tra thư viện Html5Qrcode
                if (typeof Html5Qrcode === 'undefined') {
                    showMessage('Thư viện quét QR chưa được tải. Vui lòng làm mới trang và thử lại.', 'error');
                    return;
                }

                // Tạo HTML5 QR Code Scanner với giao diện mặc định
                html5QrcodeScanner = new Html5QrcodeScanner(
                    "qr-reader",
                    { 
                        fps: 10,
                        qrbox: { width: 250, height: 250 },
                        aspectRatio: 1.0
                    },
                    false // verbose = false
                );

                // Bắt đầu quét
                html5QrcodeScanner.render(onScanSuccess, onScanFailure);
                
                // Hide start button
                document.getElementById('startScanner').style.display = 'none';
                
                isScanning = true;
                
                // Show success message
                showMessage('Camera đã được khởi tạo thành công!', 'success');
                
            } catch (error) {
                console.error('Scanner error:', error);
                showMessage('Lỗi khởi tạo camera: ' + error.message, 'error');
            }
        }



        async function processManualInput() {
            const input = document.getElementById('manualInput').value.trim();
            
            if (!input) {
                showMessage('Vui lòng nhập nội dung QR code!', 'error');
                return;
            }

            try {
                console.log('Processing manual input:', input);
                
                const response = await fetch('{{ route("qr.scan") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        qr_data: input,
                        group_id: {{ $group->id ?? 1 }}
                    })
                });

                console.log('Manual input response status:', response.status);

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();
                console.log('Manual input response data:', result);
                
                if (result.success) {
                    addScanResult(result.data);
                    showSuccessModal(result.data);
                    scanCount++;
                    uniqueStudents.add(result.data.student.id);
                    updateStatistics();
                    document.getElementById('manualInput').value = '';
                } else {
                    showMessage(result.message, 'error');
                }
                
            } catch (error) {
                console.error('Manual scan error:', error);
                console.error('Manual scan error details:', {
                    message: error.message,
                    stack: error.stack
                });
                showMessage('Lỗi khi xử lý dữ liệu: ' + error.message, 'error');
            }
        }

        function onScanSuccess(decodedText, decodedResult) {
            if (!isScanning) return;
            
            // Prevent multiple scans of the same content in quick succession
            if (window.lastScanContent === decodedText && Date.now() - window.lastScanTime < 2000) {
                return;
            }
            window.lastScanContent = decodedText;
            window.lastScanTime = Date.now();

            console.log('QR Code detected:', decodedText);
            console.log('Decoded result:', decodedResult);
            
            // Hiển thị thông tin QR code
            displayQRInfo(decodedText, decodedResult);
            
            // Hiển thị kết quả quét
            showMessage(`QR Code quét được: ${decodedText}`, 'info');
            
            // Tự động xử lý QR code
            handleScan(decodedText);
        }

        function displayQRInfo(decodedText, decodedResult) {
            const qrInfoContainer = document.getElementById('qr-info');
            
            // Remove placeholder if exists
            const placeholder = qrInfoContainer.querySelector('.text-center');
            if (placeholder) {
                placeholder.remove();
            }
            
            const infoElement = document.createElement('div');
            infoElement.className = 'bg-blue-50 border border-blue-200 rounded-lg p-4';
            infoElement.innerHTML = `
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="font-semibold text-gray-800">Nội dung QR:</span>
                        <span class="text-blue-600 font-mono text-sm">${decodedText}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="font-semibold text-gray-800">Độ dài:</span>
                        <span class="text-gray-600">${decodedText.length} ký tự</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="font-semibold text-gray-800">Thời gian:</span>
                        <span class="text-gray-600">${new Date().toLocaleTimeString()}</span>
                    </div>
                    <div class="mt-2">
                        <div class="flex items-center text-green-600 text-sm">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span>Đã tự động xử lý và lưu vào database</span>
                        </div>
                    </div>
                </div>
            `;
            
            qrInfoContainer.innerHTML = '';
            qrInfoContainer.appendChild(infoElement);
        }


        function onScanFailure(error) {
            // Không cần xử lý lỗi scan, chỉ log để debug
            // console.log('Scan failed:', error);
        }


        async function handleScan(content) {
            try {
                console.log('Processing QR data:', content);
                console.log('Sending to URL:', '{{ route("qr.scan") }}');
                console.log('Current domain:', window.location.origin);
                console.log('User agent:', navigator.userAgent);
                console.log('Is secure:', window.location.protocol === 'https:');
                
                // Lấy CSRF token từ meta tag hoặc cookie
                let csrfToken = '{{ csrf_token() }}';
                const metaToken = document.querySelector('meta[name="csrf-token"]');
                if (metaToken) {
                    csrfToken = metaToken.getAttribute('content');
                }
                
                const requestData = {
                    qr_data: content,
                    group_id: {{ $group->id ?? 1 }}
                };
                console.log('Request data:', requestData);
                console.log('Using CSRF token:', csrfToken);
                
                // Tạo URL tuyệt đối cho mobile
                const scanUrl = new URL('{{ route("qr.scan") }}', window.location.origin).href;
                console.log('Full scan URL:', scanUrl);
                
                // Thử với XMLHttpRequest thay vì fetch
                console.log('Trying XMLHttpRequest...');
                const xhr = new XMLHttpRequest();
                
                return new Promise((resolve, reject) => {
                    xhr.open('POST', scanUrl, true);
                    xhr.setRequestHeader('Content-Type', 'application/json');
                    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                    xhr.setRequestHeader('Accept', 'application/json');
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4) {
                            console.log('XHR Status:', xhr.status);
                            console.log('XHR Response:', xhr.responseText);
                            
                            if (xhr.status >= 200 && xhr.status < 300) {
                                try {
                                    const result = JSON.parse(xhr.responseText);
                                    console.log('XHR Success:', result);
                                    
                                    if (result.success) {
                                        addScanResult(result.data);
                                        showSuccessModal(result.data);
                                        scanCount++;
                                        uniqueStudents.add(result.data.student.id);
                                        updateStatistics();
                                    } else {
                                        showMessage(result.message, 'error');
                                    }
                                    resolve(result);
                                } catch (e) {
                                    console.error('XHR Parse error:', e);
                                    showMessage('Lỗi parse response: ' + e.message, 'error');
                                    reject(e);
                                }
                            } else {
                                console.error('XHR Error:', xhr.status, xhr.responseText);
                                showMessage('Lỗi HTTP ' + xhr.status + ': ' + xhr.responseText, 'error');
                                reject(new Error(`HTTP ${xhr.status}`));
                            }
                        }
                    };
                    
                    xhr.onerror = function() {
                        console.error('XHR Network error');
                        showMessage('Lỗi kết nối mạng', 'error');
                        reject(new Error('Network error'));
                    };
                    
                    xhr.send(JSON.stringify(requestData));
                });
                
            } catch (error) {
                console.error('Scan processing error:', error);
                console.error('Error details:', {
                    message: error.message,
                    stack: error.stack
                });
                
                showMessage('Lỗi kết nối: ' + error.message + '. Vui lòng thử lại.', 'error');
            }
        }

        function addScanResult(data) {
            const resultsContainer = document.getElementById('scan-results');
            
            // Remove placeholder if exists
            const placeholder = resultsContainer.querySelector('.text-center');
            if (placeholder) {
                placeholder.remove();
            }
            
            const resultElement = document.createElement('div');
            resultElement.className = 'bg-green-50 border border-green-200 rounded-lg p-3';
            resultElement.innerHTML = `
                <div class="flex items-center justify-between">
                    <div>
                        <div class="font-semibold text-gray-800">${data.student.name || 'N/A'}</div>
                        <div class="text-sm text-gray-600">MSSV: ${data.student.mssv}</div>
                        <div class="text-sm text-gray-600">Lớp: ${data.student.class || 'N/A'}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-gray-600">Lần quét: <span class="font-semibold text-blue-600">${data.scan_count}</span></div>
                        <div class="text-xs text-gray-500">${data.last_scanned_at}</div>
                    </div>
                </div>
            `;
            
            resultsContainer.insertBefore(resultElement, resultsContainer.firstChild);
            
            // Keep only last 10 results
            const results = resultsContainer.children;
            if (results.length > 10) {
                resultsContainer.removeChild(results[results.length - 1]);
            }
        }

        function showSuccessModal(data) {
            const modal = document.getElementById('successModal');
            const details = document.getElementById('success-details');
            
            details.innerHTML = `
                <div class="text-left">
                    <p><strong>Tên:</strong> ${data.student.name || 'N/A'}</p>
                    <p><strong>MSSV:</strong> ${data.student.mssv}</p>
                    <p><strong>Lớp:</strong> ${data.student.class || 'N/A'}</p>
                    <p><strong>Group:</strong> ${data.group.name}</p>
                    <p><strong>Số lần quét:</strong> ${data.scan_count}</p>
                </div>
            `;
            
            modal.style.display = 'flex';
            
            // Auto close after 3 seconds
            setTimeout(() => {
                closeSuccessModal();
            }, 3000);
        }

        function closeSuccessModal() {
            document.getElementById('successModal').style.display = 'none';
        }

        function showMessage(message, type = 'info') {
            const resultsContainer = document.getElementById('scan-results');
            
            const messageElement = document.createElement('div');
            let bgColor, textColor, icon;
            
            switch(type) {
                case 'success':
                    bgColor = 'bg-green-50 border-green-200';
                    textColor = 'text-green-800';
                    icon = 'fas fa-check-circle text-green-600';
                    break;
                case 'error':
                    bgColor = 'bg-red-50 border-red-200';
                    textColor = 'text-red-800';
                    icon = 'fas fa-exclamation-triangle text-red-600';
                    break;
                default:
                    bgColor = 'bg-blue-50 border-blue-200';
                    textColor = 'text-blue-800';
                    icon = 'fas fa-info-circle text-blue-600';
            }
            
            messageElement.className = `${bgColor} border rounded-lg p-3`;
            messageElement.innerHTML = `
                <div class="flex items-center">
                    <i class="${icon} mr-2"></i>
                    <span class="${textColor}">${message}</span>
                </div>
            `;
            
            resultsContainer.insertBefore(messageElement, resultsContainer.firstChild);
            
            // Remove after 5 seconds
            setTimeout(() => {
                if (messageElement.parentNode) {
                    messageElement.parentNode.removeChild(messageElement);
                }
            }, 5000);
        }

        function updateStatistics() {
            document.getElementById('total-scans').textContent = scanCount;
            document.getElementById('unique-students').textContent = uniqueStudents.size;
        }

        // Close modal when clicking outside
        document.getElementById('successModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeSuccessModal();
            }
        });

        // Cleanup on page unload
        window.addEventListener('beforeunload', function() {
            if (html5QrcodeScanner && isScanning) {
                html5QrcodeScanner.stop();
            }
        });
    </script>
</body>
</html>