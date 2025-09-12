<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quét QR Code - {{ $group->name ?? 'Group' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/instascan/1.0.0/instascan.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .scanner-container {
            position: relative;
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
        }
        
        .scanner-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border: 2px solid #3b82f6;
            border-radius: 8px;
            pointer-events: none;
        }
        
        .scanner-corner {
            position: absolute;
            width: 30px;
            height: 30px;
            border: 3px solid #3b82f6;
        }
        
        .scanner-corner.top-left {
            top: -3px;
            left: -3px;
            border-right: none;
            border-bottom: none;
        }
        
        .scanner-corner.top-right {
            top: -3px;
            right: -3px;
            border-left: none;
            border-bottom: none;
        }
        
        .scanner-corner.bottom-left {
            bottom: -3px;
            left: -3px;
            border-right: none;
            border-top: none;
        }
        
        .scanner-corner.bottom-right {
            bottom: -3px;
            right: -3px;
            border-left: none;
            border-top: none;
        }
        
        .scan-line {
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, #3b82f6, transparent);
            animation: scan 2s linear infinite;
        }
        
        @keyframes scan {
            0% { transform: translateY(-100px); }
            100% { transform: translateY(100px); }
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
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">
                        <i class="fas fa-qrcode mr-2 text-blue-600"></i>
                        Quét QR Code
                    </h1>
                    <p class="text-gray-600 mt-1">
                        Group: <span class="font-semibold text-blue-600">{{ $group->name ?? 'Chưa có group' }}</span>
                    </p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('qr.statistics', $group->id ?? 1) }}" 
                       class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
                        <i class="fas fa-chart-bar mr-2"></i>
                        Thống kê
                    </a>
                    <a href="{{ route('group.index') }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Quay lại
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Scanner Section -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    <i class="fas fa-camera mr-2 text-blue-600"></i>
                    Camera Scanner
                </h2>
                
                <div class="scanner-container">
                    <video id="preview" class="w-full rounded-lg" style="display: none;"></video>
                    <div id="scanner-placeholder" class="w-full h-64 bg-gray-200 rounded-lg flex items-center justify-center">
                        <div class="text-center">
                            <i class="fas fa-camera text-4xl text-gray-400 mb-2"></i>
                            <p class="text-gray-500">Đang khởi tạo camera...</p>
                        </div>
                    </div>
                    <div class="scanner-overlay" style="display: none;">
                        <div class="scanner-corner top-left"></div>
                        <div class="scanner-corner top-right"></div>
                        <div class="scanner-corner bottom-left"></div>
                        <div class="scanner-corner bottom-right"></div>
                        <div class="scan-line"></div>
                    </div>
                </div>
                
                <div class="mt-4 flex justify-center space-x-3">
                    <button id="startScanner" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg flex items-center">
                        <i class="fas fa-play mr-2"></i>
                        Bắt đầu quét
                    </button>
                    <button id="stopScanner" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg flex items-center" style="display: none;">
                        <i class="fas fa-stop mr-2"></i>
                        Dừng quét
                    </button>
                </div>
                
                <div class="mt-4 text-center">
                    <p class="text-sm text-gray-600">
                        <i class="fas fa-info-circle mr-1"></i>
                        Hướng camera vào mã QR để quét
                    </p>
                </div>
            </div>

            <!-- Results Section -->
            <div class="bg-white rounded-lg shadow-md p-6">
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
        let scanner = null;
        let isScanning = false;
        let scanCount = 0;
        let uniqueStudents = new Set();

        // Initialize scanner
        document.getElementById('startScanner').addEventListener('click', startScanner);
        document.getElementById('stopScanner').addEventListener('click', stopScanner);

        async function startScanner() {
            try {
                const cameras = await Instascan.Camera.getCameras();
                
                if (cameras.length === 0) {
                    alert('Không tìm thấy camera trên thiết bị!');
                    return;
                }

                // Use back camera if available, otherwise use first camera
                const camera = cameras.find(c => c.name.toLowerCase().includes('back')) || cameras[0];
                
                scanner = new Instascan.Scanner({
                    video: document.getElementById('preview'),
                    scanPeriod: 5,
                    mirror: false
                });

                scanner.addListener('scan', function (content) {
                    handleScan(content);
                });

                await scanner.start(camera);
                
                // Show scanner UI
                document.getElementById('preview').style.display = 'block';
                document.getElementById('scanner-placeholder').style.display = 'none';
                document.querySelector('.scanner-overlay').style.display = 'block';
                document.getElementById('startScanner').style.display = 'none';
                document.getElementById('stopScanner').style.display = 'inline-flex';
                
                isScanning = true;
                
            } catch (error) {
                console.error('Scanner error:', error);
                alert('Lỗi khởi tạo camera: ' + error.message);
            }
        }

        function stopScanner() {
            if (scanner) {
                scanner.stop();
                scanner = null;
            }
            
            // Hide scanner UI
            document.getElementById('preview').style.display = 'none';
            document.getElementById('scanner-placeholder').style.display = 'block';
            document.querySelector('.scanner-overlay').style.display = 'none';
            document.getElementById('startScanner').style.display = 'inline-flex';
            document.getElementById('stopScanner').style.display = 'none';
            
            isScanning = false;
        }

        async function handleScan(content) {
            if (!isScanning) return;
            
            // Prevent multiple scans of the same content in quick succession
            if (window.lastScanContent === content && Date.now() - window.lastScanTime < 2000) {
                return;
            }
            window.lastScanContent = content;
            window.lastScanTime = Date.now();

            try {
                const response = await fetch('{{ route("qr.scan") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        qr_data: content,
                        group_id: {{ $group->id ?? 1 }}
                    })
                });

                const result = await response.json();
                
                if (result.success) {
                    addScanResult(result.data);
                    showSuccessModal(result.data);
                    scanCount++;
                    uniqueStudents.add(result.data.student.id);
                    updateStatistics();
                } else {
                    showError(result.message);
                }
                
            } catch (error) {
                console.error('Scan processing error:', error);
                showError('Lỗi xử lý QR code: ' + error.message);
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

        function showError(message) {
            const resultsContainer = document.getElementById('scan-results');
            
            const errorElement = document.createElement('div');
            errorElement.className = 'bg-red-50 border border-red-200 rounded-lg p-3';
            errorElement.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                    <span class="text-red-800">${message}</span>
                </div>
            `;
            
            resultsContainer.insertBefore(errorElement, resultsContainer.firstChild);
            
            // Remove after 5 seconds
            setTimeout(() => {
                if (errorElement.parentNode) {
                    errorElement.parentNode.removeChild(errorElement);
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
            if (scanner) {
                scanner.stop();
            }
        });
    </script>
</body>
</html>
