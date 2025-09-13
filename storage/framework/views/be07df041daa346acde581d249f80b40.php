<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Quét QR Code - <?php echo e($group->name ?? 'Group'); ?></title>
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
                        Group: <span class="font-semibold text-blue-600"><?php echo e($group->name ?? 'Chưa có group'); ?></span>
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                    <a href="<?php echo e(route('qr.statistics', $group->id ?? 1)); ?>" 
                       class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center justify-center text-sm">
                        <i class="fas fa-chart-bar mr-2"></i>
                        Thống kê
                    </a>
                    <a href="<?php echo e(route('group.index')); ?>" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center justify-center text-sm">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Quay lại
                    </a>
                </div>
            </div>
        </div>

        <!-- Scanner Section -->
        <div class="bg-white rounded-lg shadow-md p-4 sm:p-6">
            <h2 class="text-lg sm:text-xl font-semibold text-gray-800 mb-4">
                <i class="fas fa-camera mr-2 text-purple-600"></i>
                Camera Scanner
            </h2>
            
            <div class="scanner-container">
                <div id="qr-reader" class="w-full"></div>
            </div>
            
            <div class="mt-4 flex justify-center gap-3">
                <button id="startScanner" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg flex items-center justify-center text-base">
                    <i class="fas fa-play mr-2"></i>
                    Bắt đầu quét
                </button>
                <button id="stopScanner" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg flex items-center justify-center text-base hidden">
                    <i class="fas fa-stop mr-2"></i>
                    Dừng quét
                </button>
            </div>
            
            <div class="mt-4 text-center">
                <p class="text-sm text-gray-600" id="scanner-status">
                    <i class="fas fa-info-circle mr-1"></i>
                    Hướng camera vào mã QR để quét
                </p>
                <div id="processing-indicator" class="hidden mt-2">
                    <div class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-yellow-100 text-yellow-800">
                        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-yellow-600 mr-2"></div>
                        Đang xử lý QR code...
                    </div>
                </div>
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
                        Xử lý
                    </button>
                </div>
            </div>
        </div>

        <!-- QR Code Info Section -->
        <div id="qrInfo" class="bg-white rounded-lg shadow-md p-4 sm:p-6 mt-4 sm:mt-6 hidden">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">
                <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                Thông tin QR Code
            </h3>
            <div id="qrInfoContent" class="text-sm text-gray-600">
                <!-- QR info will be displayed here -->
            </div>
        </div>

        <!-- Scan Result Section -->
        <div id="scanResult" class="bg-white rounded-lg shadow-md p-4 sm:p-6 mt-4 sm:mt-6 hidden">
            <div class="flex justify-between items-center mb-3">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-check-circle mr-2 text-green-600"></i>
                    Kết quả quét QR
                </h3>
                <button id="clearResultBtn" class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded text-sm">
                    <i class="fas fa-times mr-1"></i>Xóa
                </button>
            </div>
            <div id="scanResultContent" class="text-sm text-gray-600">
                <!-- Scan result will be displayed here -->
            </div>
        </div>

        <!-- Message Display -->
        <div id="messageContainer" class="fixed top-4 right-4 z-50 max-w-sm">
            <!-- Messages will be displayed here -->
        </div>
    </div>

    <script>
        let html5QrcodeScanner = null;
        let isScanning = false;
        let isProcessing = false; // Flag to prevent multiple simultaneous scans

        document.addEventListener('DOMContentLoaded', function() {
            console.log('QR Scanner page loaded');
            console.log('Html5Qrcode available:', typeof Html5Qrcode !== 'undefined');
            if (typeof Html5Qrcode !== 'undefined') {
                console.log('Html5Qrcode version:', Html5Qrcode.version || 'unknown');
            }
            
            document.getElementById('startScanner').addEventListener('click', startScanner);
            document.getElementById('stopScanner').addEventListener('click', stopScanner);
            document.getElementById('manualScanBtn').addEventListener('click', processManualInput);
            document.getElementById('clearResultBtn').addEventListener('click', clearResults);
            
            // Enter key for manual input
            document.getElementById('manualInput').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    processManualInput();
                }
            });
        });

        async function startScanner() {
            if (isScanning) {
                showMessage('Scanner đang chạy rồi!', 'info');
                return;
            }

            try {
                console.log('Starting QR scanner...');
                showMessage('Đang khởi tạo camera...', 'info');

                // Check if Html5Qrcode is available
                if (typeof Html5Qrcode === 'undefined') {
                    throw new Error('Html5Qrcode library not loaded');
                }

                // Clear previous scanner if exists
                if (html5QrcodeScanner) {
                    await html5QrcodeScanner.clear();
                }

                // Create new scanner
                html5QrcodeScanner = new Html5Qrcode("qr-reader");
                
                // Get available cameras
                const cameras = await Html5Qrcode.getCameras();
                console.log('Available cameras:', cameras);

                if (cameras.length === 0) {
                    throw new Error('Không tìm thấy camera nào');
                }

                // Use back camera if available, otherwise use first camera
                const cameraId = cameras.find(camera => camera.label.toLowerCase().includes('back'))?.id || cameras[0].id;
                console.log('Using camera:', cameraId);

                // Start scanning
                await html5QrcodeScanner.start(
                    cameraId,
                    {
                        fps: 10,
                        qrbox: { width: 250, height: 250 },
                        aspectRatio: 1.0
                    },
                    onScanSuccess,
                    onScanFailure
                );

                isScanning = true;
                document.getElementById('startScanner').classList.add('hidden');
                document.getElementById('stopScanner').classList.remove('hidden');
                
                showMessage('Camera đã sẵn sàng! Hướng camera vào mã QR.', 'success');

            } catch (error) {
                console.error('Scanner error:', error);
                showMessage('Lỗi khởi tạo camera: ' + error.message, 'error');
                isScanning = false;
            }
        }

        async function stopScanner() {
            if (html5QrcodeScanner && isScanning) {
                try {
                    await html5QrcodeScanner.stop();
                    isScanning = false;
                    document.getElementById('startScanner').classList.remove('hidden');
                    document.getElementById('stopScanner').classList.add('hidden');
                    showMessage('Đã dừng camera', 'info');
                } catch (error) {
                    console.error('Error stopping scanner:', error);
                    showMessage('Lỗi khi dừng camera', 'error');
                }
            }
        }

        async function onScanSuccess(decodedText, decodedResult) {
            console.log('QR Code scanned:', decodedText);
            console.log('Decoded result:', decodedResult);
            
            // Prevent multiple simultaneous scans
            if (isProcessing) {
                console.log('Already processing a scan, ignoring this one');
                return;
            }
            
            // Don't stop scanning - keep camera running for continuous scanning
            // Process the scanned QR code
            await handleScan(decodedText);
        }

        function onScanFailure(error) {
            // This is called for every scan attempt, so we don't log every failure
            // console.log('Scan failed:', error);
        }

        async function handleScan(qrData) {
            try {
                // Set processing flag to prevent multiple scans
                isProcessing = true;
                
                // Show processing indicator
                document.getElementById('processing-indicator').classList.remove('hidden');
                document.getElementById('scanner-status').innerHTML = '<i class="fas fa-clock mr-1"></i>Đang xử lý QR code...';
                
                console.log('Processing QR data:', qrData);
                showMessage('Đang xử lý QR code...', 'info');

                // Display QR info
                displayQRInfo(qrData);

                // Try to parse as JSON first
                let parsedData = null;
                try {
                    parsedData = JSON.parse(qrData);
                    console.log('Parsed QR data:', parsedData);
                } catch (e) {
                    console.log('QR data is not JSON, treating as plain text');
                }

                // Prepare data for sending
                const requestData = {
                    qr_data: qrData,
                    parsed_data: parsedData,
                    timestamp: new Date().toISOString()
                };

                console.log('Sending request data:', requestData);

                // Use XMLHttpRequest for better mobile compatibility
                const xhr = new XMLHttpRequest();
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                // Determine the correct URL based on current protocol
                let scanUrl = '<?php echo e(route("qr.scan")); ?>';
                if (window.location.protocol === 'https:' && scanUrl.startsWith('http:')) {
                    scanUrl = scanUrl.replace('http:', 'https:');
                }
                
                xhr.open('POST', scanUrl, true);
                xhr.setRequestHeader('Content-Type', 'application/json');
                xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                xhr.setRequestHeader('Accept', 'application/json');
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                
                // Add credentials for CORS
                xhr.withCredentials = true;
                
                // Set timeout
                xhr.timeout = 30000; // 30 seconds
                
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4) {
                        console.log('Response status:', xhr.status);
                        console.log('Response text:', xhr.responseText);
                        
                        if (xhr.status >= 200 && xhr.status < 300) {
                            try {
                                const result = JSON.parse(xhr.responseText);
                                console.log('Scan result:', result);
                                
                                if (result.success) {
                                    showMessage(result.message, 'success');
                                    
                                    // Display scan result with detailed information
                                    displayScanResult(result.data);
                                    
                                    // Add success animation
                                    document.getElementById('scanResult').classList.add('success-animation');
                                    setTimeout(() => {
                                        document.getElementById('scanResult').classList.remove('success-animation');
                                    }, 600);
                                } else {
                                    showMessage(result.message, 'error');
                                }
                                
                                // Add delay before allowing next scan
                                setTimeout(() => {
                                    isProcessing = false;
                                    document.getElementById('processing-indicator').classList.add('hidden');
                                    document.getElementById('scanner-status').innerHTML = '<i class="fas fa-info-circle mr-1"></i>Hướng camera vào mã QR để quét';
                                }, 1500);
                            } catch (e) {
                                console.error('Parse error:', e);
                                showMessage('Lỗi xử lý phản hồi từ server', 'error');
                            }
                        } else {
                            console.error('HTTP error:', xhr.status, xhr.responseText);
                            
                            // Try to parse error response for custom message
                            try {
                                const errorResult = JSON.parse(xhr.responseText);
                                if (errorResult.message) {
                                    showMessage(errorResult.message, 'error');
                                } else {
                                    showMessage('Lỗi HTTP ' + xhr.status, 'error');
                                }
                            } catch (e) {
                                showMessage('Lỗi HTTP ' + xhr.status, 'error');
                            }
                            
                            // Reset processing flag on error
                            setTimeout(() => {
                                isProcessing = false;
                                document.getElementById('processing-indicator').classList.add('hidden');
                                document.getElementById('scanner-status').innerHTML = '<i class="fas fa-info-circle mr-1"></i>Hướng camera vào mã QR để quét';
                            }, 1500);
                        }
                    }
                };
                
                xhr.onerror = function() {
                    console.error('Network error:', xhr.status, xhr.statusText);
                    if (xhr.status === 0) {
                        showMessage('Lỗi kết nối: Có thể do mixed content (HTTP/HTTPS). Vui lòng kiểm tra URL.', 'error');
                    } else {
                        showMessage('Lỗi kết nối mạng: ' + xhr.statusText, 'error');
                    }
                    
                    // Reset processing flag on network error
                    setTimeout(() => {
                        isProcessing = false;
                        document.getElementById('processing-indicator').classList.add('hidden');
                        document.getElementById('scanner-status').innerHTML = '<i class="fas fa-info-circle mr-1"></i>Hướng camera vào mã QR để quét';
                    }, 1500);
                };
                
                xhr.ontimeout = function() {
                    console.error('Request timeout');
                    showMessage('Request timeout - vui lòng thử lại', 'error');
                    
                    // Reset processing flag on timeout
                    setTimeout(() => {
                        isProcessing = false;
                        document.getElementById('processing-indicator').classList.add('hidden');
                        document.getElementById('scanner-status').innerHTML = '<i class="fas fa-info-circle mr-1"></i>Hướng camera vào mã QR để quét';
                    }, 1500);
                };
                
                xhr.send(JSON.stringify(requestData));

            } catch (error) {
                console.error('Handle scan error:', error);
                showMessage('Lỗi xử lý QR code: ' + error.message, 'error');
                
                // Reset processing flag on catch error
                setTimeout(() => {
                    isProcessing = false;
                    document.getElementById('processing-indicator').classList.add('hidden');
                    document.getElementById('scanner-status').innerHTML = '<i class="fas fa-info-circle mr-1"></i>Hướng camera vào mã QR để quét';
                    showMessage('Sẵn sàng quét QR code tiếp theo', 'info');
                }, 1500);
            }
        }

        async function processManualInput() {
            const input = document.getElementById('manualInput').value.trim();
            if (!input) {
                showMessage('Vui lòng nhập nội dung QR code', 'error');
                return;
            }

            if (isProcessing) {
                showMessage('Đang xử lý QR code khác, vui lòng chờ...', 'warning');
                return;
            }

            await handleScan(input);
            document.getElementById('manualInput').value = '';
        }

        function displayQRInfo(qrData) {
            const qrInfoDiv = document.getElementById('qrInfo');
            const qrInfoContent = document.getElementById('qrInfoContent');
            
            let infoHtml = '<div class="space-y-3">';
            
            // Try to parse as JSON first (for backward compatibility)
            try {
                const parsed = JSON.parse(qrData);
                if (parsed.mssv) {
                    infoHtml += '<div class="bg-blue-50 border border-blue-200 rounded-lg p-3">';
                    infoHtml += '<div class="flex items-center mb-2">';
                    infoHtml += '<i class="fas fa-id-card text-blue-600 mr-2"></i>';
                    infoHtml += '<span class="font-semibold text-blue-800">Mã số sinh viên</span>';
                    infoHtml += '</div>';
                    infoHtml += '<p class="text-lg font-bold text-blue-900">' + parsed.mssv + '</p>';
                    if (parsed.type) {
                        infoHtml += '<p class="text-sm text-blue-700 mt-1">Loại: ' + parsed.type + '</p>';
                    }
                    infoHtml += '</div>';
                } else {
                    infoHtml += '<div class="bg-gray-50 border border-gray-200 rounded-lg p-3">';
                    infoHtml += '<p class="text-sm text-gray-600">Dữ liệu JSON không hợp lệ</p>';
                    infoHtml += '</div>';
                }
            } catch (e) {
                // Not JSON, treat as plain MSSV
                infoHtml += '<div class="bg-green-50 border border-green-200 rounded-lg p-3">';
                infoHtml += '<div class="flex items-center mb-2">';
                infoHtml += '<i class="fas fa-id-card text-green-600 mr-2"></i>';
                infoHtml += '<span class="font-semibold text-green-800">Mã số sinh viên</span>';
                infoHtml += '</div>';
                infoHtml += '<p class="text-lg font-bold text-green-900">' + qrData + '</p>';
                infoHtml += '<p class="text-sm text-green-700 mt-1">Định dạng: Văn bản đơn giản</p>';
                infoHtml += '</div>';
            }
            
            infoHtml += '</div>';
            
            qrInfoContent.innerHTML = infoHtml;
            qrInfoDiv.classList.remove('hidden');
        }

        function displayScanResult(data) {
            const scanResultDiv = document.getElementById('scanResult');
            const scanResultContent = document.getElementById('scanResultContent');
            
            let resultHtml = '<div class="space-y-3">';
            
            // Student information
            if (data.student) {
                resultHtml += '<div class="bg-blue-50 p-3 rounded-lg">';
                resultHtml += '<h4 class="font-semibold text-blue-800 mb-2"><i class="fas fa-user mr-2"></i>Thông tin sinh viên</h4>';
                resultHtml += '<div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">';
                resultHtml += '<p><strong>MSSV:</strong> ' + data.student.mssv + '</p>';
                resultHtml += '<p><strong>Họ tên:</strong> ' + data.student.name + '</p>';
                resultHtml += '</div>';
                resultHtml += '</div>';
            }
            
            // Scan statistics
            if (data.scan_count !== undefined) {
                resultHtml += '<div class="bg-yellow-50 p-3 rounded-lg">';
                resultHtml += '<h4 class="font-semibold text-yellow-800 mb-2"><i class="fas fa-chart-bar mr-2"></i>Thống kê quét</h4>';
                resultHtml += '<div class="flex items-center justify-between">';
                resultHtml += '<span class="text-sm"><strong>Số lần quét:</strong></span>';
                resultHtml += '<span class="bg-yellow-200 text-yellow-800 px-3 py-1 rounded-full text-sm font-bold">' + data.scan_count + '</span>';
                resultHtml += '</div>';
                if (data.last_scanned_at) {
                    resultHtml += '<p class="text-sm mt-2"><strong>Lần cuối quét:</strong> ' + data.last_scanned_at + '</p>';
                }
                resultHtml += '</div>';
            }
            
            resultHtml += '</div>';
            
            scanResultContent.innerHTML = resultHtml;
            scanResultDiv.classList.remove('hidden');
            
            // Scroll to result
            scanResultDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        function showMessage(message, type = 'info') {
            const container = document.getElementById('messageContainer');
            const messageId = 'msg-' + Date.now();
            
            const colors = {
                success: 'bg-green-500',
                error: 'bg-red-500',
                warning: 'bg-yellow-500',
                info: 'bg-blue-500'
            };
            
            const icons = {
                success: 'fas fa-check-circle',
                error: 'fas fa-exclamation-circle',
                warning: 'fas fa-exclamation-triangle',
                info: 'fas fa-info-circle'
            };
            
            const messageDiv = document.createElement('div');
            messageDiv.id = messageId;
            messageDiv.className = `${colors[type]} text-white p-3 rounded-lg shadow-lg mb-2 flex items-center justify-between`;
            messageDiv.innerHTML = `
                <div class="flex items-center">
                    <i class="${icons[type]} mr-2"></i>
                    <span>${message}</span>
                </div>
                <button onclick="removeMessage('${messageId}')" class="ml-3 text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            `;
            
            container.appendChild(messageDiv);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                removeMessage(messageId);
            }, 5000);
        }

        function removeMessage(messageId) {
            const message = document.getElementById(messageId);
            if (message) {
                message.remove();
            }
        }

        function clearResults() {
            // Hide result sections
            document.getElementById('qrInfo').classList.add('hidden');
            document.getElementById('scanResult').classList.add('hidden');
            
            // Clear content
            document.getElementById('qrInfoContent').innerHTML = '';
            document.getElementById('scanResultContent').innerHTML = '';
            
            // Clear manual input
            document.getElementById('manualInput').value = '';
            
            showMessage('Đã xóa kết quả. Sẵn sàng quét QR code mới!', 'info');
        }
    </script>
</body>
</html><?php /**PATH C:\Workspace\Laravel\VTTU\QRScan\resources\views/qr/scanner.blade.php ENDPATH**/ ?>