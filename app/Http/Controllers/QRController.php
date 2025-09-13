<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Endroid\QrCode\QrCode as EndroidQrCode;
use Endroid\QrCode\Writer\PngWriter;
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
                
                // Format mới: MSSV, HOLOT, Ten, Gioi, NgaySinh
                $mssv = trim($row[0] ?? '');
                $holot = trim($row[1] ?? '');
                $ten = trim($row[2] ?? '');
                $gioi = trim($row[3] ?? '');
                $ngaySinh = trim($row[4] ?? '');
                
                if (empty($mssv)) {
                    continue;
                }

                // Kiểm tra xem MSSV đã tồn tại chưa
                $existingStudent = Student::where('mssv', $mssv)->first();
                
                if ($existingStudent) {
                    $errors[] = "MSSV {$mssv} đã tồn tại";
                    continue;
                }

                // Xử lý ngày sinh
                $ngaySinhFormatted = null;
                if (!empty($ngaySinh)) {
                    try {
                        // Hỗ trợ format dd/mm/yyyy
                        if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $ngaySinh, $matches)) {
                            $ngaySinhFormatted = $matches[3] . '-' . str_pad($matches[2], 2, '0', STR_PAD_LEFT) . '-' . str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                        }
                    } catch (\Exception $e) {
                        $errors[] = "MSSV {$mssv}: Ngày sinh không hợp lệ ({$ngaySinh})";
                        continue;
                    }
                }

                // Tạo QR code
                $qrCodePath = $this->generateQRCode($mssv);
                
                // Lưu thông tin sinh viên
                Student::create([
                    'mssv' => $mssv,
                    'holot' => $holot ?: null,
                    'ten' => $ten ?: null,
                    'gioi' => $gioi ?: null,
                    'ngay_sinh' => $ngaySinhFormatted,
                    'name' => trim($holot . ' ' . $ten), // Tạo full name từ holot + ten
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
            'holot' => 'nullable|string|max:255',
            'ten' => 'nullable|string|max:255',
            'gioi' => 'nullable|string|max:10',
            'ngay_sinh' => 'nullable|date',
            'name' => 'nullable|string|max:255',
            'class' => 'nullable|string|max:100'
        ], [
            'mssv.required' => 'MSSV là bắt buộc',
            'mssv.unique' => 'MSSV đã tồn tại',
            'mssv.max' => 'MSSV không được quá 20 ký tự',
            'holot.max' => 'Họ lót không được quá 255 ký tự',
            'ten.max' => 'Tên không được quá 255 ký tự',
            'gioi.max' => 'Giới tính không được quá 10 ký tự',
            'ngay_sinh.date' => 'Ngày sinh không hợp lệ',
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
            $holot = trim($request->holot);
            $ten = trim($request->ten);
            $gioi = trim($request->gioi);
            $ngaySinh = $request->ngay_sinh;
            $name = trim($request->name);
            $class = trim($request->class);

            // Tạo QR code
            $qrCodePath = $this->generateQRCode($mssv);
            
            // Lưu thông tin sinh viên
            Student::create([
                'mssv' => $mssv,
                'holot' => $holot ?: null,
                'ten' => $ten ?: null,
                'gioi' => $gioi ?: null,
                'ngay_sinh' => $ngaySinh,
                'name' => $name ?: trim($holot . ' ' . $ten),
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

        // Sử dụng Endroid QR Code thay vì SimpleSoftwareIO
        $qrCode = new EndroidQrCode($qrData);
        $qrCode->setSize(300);
        $qrCode->setMargin(10);

        // Tạo writer để tạo PNG
        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        $qrCodePath = $qrDir . '/' . $mssv . '.png';
        
        // Lưu QR code vào storage
        Storage::put($qrCodePath, $result->getString());

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