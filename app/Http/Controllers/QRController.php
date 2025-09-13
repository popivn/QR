<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use PhpOffice\PhpSpreadsheet\IOFactory;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class QRController extends Controller
{
    public function index()
    {
        return view('qr.index');
    }

    public function uploadExcel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'excel_file' => 'required|file|mimes:xlsx,xls|max:10240'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $file = $request->file('excel_file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            $processedCount = 0;
            $errors = [];

            // Bỏ qua header row (row đầu tiên)
            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                
                // Giả sử cột đầu tiên là MSSV
                $mssv = trim($row[0] ?? '');
                
                if (empty($mssv)) {
                    continue;
                }

                // Kiểm tra xem MSSV đã tồn tại chưa
                $existingStudent = Student::where('mssv', $mssv)->first();
                
                if ($existingStudent) {
                    $errors[] = "MSSV {$mssv} đã tồn tại";
                    continue;
                }

                // Tạo QR code
                $qrCodePath = $this->generateQRCode($mssv);
                
                // Lưu thông tin sinh viên
                Student::create([
                    'mssv' => $mssv,
                    'name' => $row[1] ?? null, // Cột thứ 2 là tên
                    'class' => $row[2] ?? null, // Cột thứ 3 là lớp
                    'qr_code_path' => $qrCodePath
                ]);

                $processedCount++;
            }

            $message = "Đã xử lý thành công {$processedCount} sinh viên";
            if (!empty($errors)) {
                $message .= ". Có " . count($errors) . " lỗi: " . implode(', ', $errors);
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Lỗi khi xử lý file Excel: ' . $e->getMessage());
        }
    }

    public function createManual(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mssv' => 'required|string|max:20|unique:students,mssv',
            'name' => 'nullable|string|max:255',
            'class' => 'nullable|string|max:100'
        ], [
            'mssv.required' => 'MSSV là bắt buộc',
            'mssv.unique' => 'MSSV đã tồn tại',
            'mssv.max' => 'MSSV không được quá 20 ký tự',
            'name.max' => 'Tên không được quá 255 ký tự',
            'class.max' => 'Lớp không được quá 100 ký tự'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $mssv = trim($request->mssv);
            $name = trim($request->name);
            $class = trim($request->class);

            // Tạo QR code
            $qrCodePath = $this->generateQRCode($mssv);
            
            // Lưu thông tin sinh viên
            Student::create([
                'mssv' => $mssv,
                'name' => $name ?: null,
                'class' => $class ?: null,
                'qr_code_path' => $qrCodePath
            ]);

            return redirect()->back()->with('success', "Đã tạo thành công QR code cho sinh viên {$mssv}");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Lỗi khi tạo QR code: ' . $e->getMessage())
                ->withInput();
        }
    }

    private function generateQRCode($mssv)
    {
        // Tạo thư mục qr-codes nếu chưa có
        $qrDir = 'qr-codes';
        if (!Storage::exists($qrDir)) {
            Storage::makeDirectory($qrDir);
        }

        // Tạo QR code với format JSON để scanner có thể parse dễ dàng
        $qrData = json_encode([
            'mssv' => $mssv,
            'type' => 'student',
            'timestamp' => now()->toISOString()
        ]);

        $qrCodePath = $qrDir . '/' . $mssv . '.png';
        $qrCode = QrCode::format('png')
            ->size(300)
            ->margin(2)
            ->generate($qrData);

        // Lưu QR code vào storage
        Storage::put($qrCodePath, $qrCode);

        return $qrCodePath;
    }

    public function listStudents()
    {
        $students = Student::orderBy('created_at', 'desc')->paginate(20);
        
        // Tính toán thống kê chính xác từ toàn bộ database
        $totalStudents = Student::count();
        $studentsWithQR = Student::whereNotNull('qr_code_path')->count();
        $studentsWithoutQR = Student::whereNull('qr_code_path')->count();
        
        return view('qr.list', compact('students', 'totalStudents', 'studentsWithQR', 'studentsWithoutQR'));
    }

    public function downloadQR($id)
    {
        $student = Student::findOrFail($id);
        
        if (!$student->qr_code_path || !Storage::exists($student->qr_code_path)) {
            return redirect()->back()->with('error', 'File QR code không tồn tại');
        }

        // Tải file với tên có extension .png và set đúng MIME type
        $fileName = $student->mssv . '.png';
        $filePath = Storage::path($student->qr_code_path);
        
        return response()->download($filePath, $fileName, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
        ]);
    }

    public function downloadAllQR()
    {
        $students = Student::whereNotNull('qr_code_path')->get();
        
        if ($students->isEmpty()) {
            return redirect()->back()->with('error', 'Không có QR code nào để tải xuống');
        }

        $zip = new \ZipArchive();
        $zipFileName = 'qr_codes_' . date('Y-m-d_H-i-s') . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);
        
        // Tạo thư mục temp nếu chưa có
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        if ($zip->open($zipPath, \ZipArchive::CREATE) !== TRUE) {
            return redirect()->back()->with('error', 'Không thể tạo file ZIP');
        }

        foreach ($students as $student) {
            if (Storage::exists($student->qr_code_path)) {
                $fileContent = Storage::get($student->qr_code_path);
                $fileName = $student->mssv . '.png';
                $zip->addFromString($fileName, $fileContent);
            }
        }

        $zip->close();

        return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
    }
}