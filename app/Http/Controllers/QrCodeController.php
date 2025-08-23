<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class QrCodeController extends Controller
{
    protected $qrCodeService;

    public function __construct(QrCodeService $qrCodeService)
    {
        $this->middleware('auth');
        $this->qrCodeService = $qrCodeService;
    }

    public function generate(Document $document, Request $request)
    {
        $this->authorize('view', $document);

        $options = [
            'size' => $request->get('size', 300),
            'label' => $request->get('label', $document->tracking_number),
            'logo' => $this->getLogoPath(),
        ];

        try {
            $result = $this->qrCodeService->generateAndSaveDocumentQr($document, $options);
            
            return response()->json([
                'success' => true,
                'qr_url' => $result['url'],
                'tracking_url' => route('public.track.show', $document->tracking_number),
                'message' => 'QR Code generated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating QR Code: ' . $e->getMessage()
            ], 500);
        }
    }

    public function download(Document $document, Request $request)
    {
        $this->authorize('view', $document);

        $options = [
            'size' => $request->get('size', 300),
            'label' => $request->get('label', $document->tracking_number),
            'logo' => $this->getLogoPath(),
        ];

        try {
            $qrCode = $this->qrCodeService->generateDocumentQr($document, $options);
            $filename = 'QR_' . $document->tracking_number . '.png';

            return Response::make($qrCode->getString(), 200, [
                'Content-Type' => 'image/png',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Error generating QR Code: ' . $e->getMessage());
        }
    }

    public function bulkGenerate(Request $request)
    {
        $this->authorize('viewAny', Document::class);

        $request->validate([
            'document_ids' => 'required|array',
            'document_ids.*' => 'exists:documents,id',
            'size' => 'nullable|integer|min:100|max:500',
        ]);

        $documents = Document::whereIn('id', $request->document_ids)->get();
        
        $options = [
            'size' => $request->get('size', 300),
            'logo' => $this->getLogoPath(),
        ];

        try {
            $results = $this->qrCodeService->generateBulkQrCodes($documents, $options);
            
            // Create ZIP file for download
            $zipFileName = 'qr_codes_' . date('Y-m-d_H-i-s') . '.zip';
            $zipPath = storage_path('app/temp/' . $zipFileName);
            
            // Ensure temp directory exists
            if (!file_exists(dirname($zipPath))) {
                mkdir(dirname($zipPath), 0755, true);
            }

            $zip = new ZipArchive();
            
            if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
                foreach ($results as $result) {
                    if ($result['success']) {
                        $document = $result['document'];
                        $qrPath = storage_path('app/public/' . $result['qr_data']['path']);
                        $zipEntryName = 'QR_' . $document->tracking_number . '.png';
                        $zip->addFile($qrPath, $zipEntryName);
                    }
                }
                $zip->close();

                return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
            } else {
                throw new \Exception('Could not create ZIP file');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error generating QR Codes: ' . $e->getMessage());
        }
    }

    public function showGenerator(Document $document = null)
    {
        if ($document) {
            $this->authorize('view', $document);
        }

        $documents = null;
        if (!$document) {
            // Show all documents if no specific document
            $documents = Document::with(['documentType', 'currentDepartment'])
                               ->orderBy('created_at', 'desc')
                               ->paginate(20);
        }

        return view('qr-codes.generator', compact('document', 'documents'));
    }

    private function getLogoPath()
    {
        $logoSetting = setting('appearance.logo');
        if ($logoSetting && Storage::disk('public')->exists($logoSetting)) {
            return storage_path('app/public/' . $logoSetting);
        }
        return null;
    }
}