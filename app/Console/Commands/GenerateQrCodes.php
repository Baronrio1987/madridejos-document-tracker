<?php

namespace App\Console\Commands;

use App\Models\Document;
use App\Services\QrCodeService;
use Illuminate\Console\Command;

class GenerateQrCodes extends Command
{
    protected $signature = 'qr:generate 
                            {--all : Generate QR codes for all documents}
                            {--missing : Generate QR codes only for documents without QR codes}
                            {--tracking= : Generate QR code for specific tracking number}
                            {--size=300 : QR code size}';

    protected $description = 'Generate QR codes for documents';

    protected $qrCodeService;

    public function __construct(QrCodeService $qrCodeService)
    {
        parent::__construct();
        $this->qrCodeService = $qrCodeService;
    }

    public function handle()
    {
        $size = $this->option('size');
        
        if ($trackingNumber = $this->option('tracking')) {
            return $this->generateForTracking($trackingNumber, $size);
        }
        
        if ($this->option('all')) {
            return $this->generateForAll($size);
        }
        
        if ($this->option('missing')) {
            return $this->generateForMissing($size);
        }
        
        $this->error('Please specify --all, --missing, or --tracking option');
        return 1;
    }

    private function generateForTracking($trackingNumber, $size)
    {
        $document = Document::where('tracking_number', $trackingNumber)->first();
        
        if (!$document) {
            $this->error("Document with tracking number {$trackingNumber} not found.");
            return 1;
        }
        
        try {
            $this->info("Generating QR code for {$document->tracking_number}...");
            $result = $this->qrCodeService->generateAndSaveDocumentQr($document, ['size' => $size]);
            $this->info("QR code generated successfully: {$result['url']}");
            return 0;
        } catch (\Exception $e) {
            $this->error("Error generating QR code: " . $e->getMessage());
            return 1;
        }
    }

    private function generateForAll($size)
    {
        $documents = Document::all();
        return $this->processDocuments($documents, $size, 'all documents');
    }

    private function generateForMissing($size)
    {
        $documents = Document::get()->filter(function ($document) {
            return !$document->hasQrCode();
        });
        
        return $this->processDocuments($documents, $size, 'documents without QR codes');
    }

    private function processDocuments($documents, $size, $description)
    {
        if ($documents->isEmpty()) {
            $this->info("No {$description} found.");
            return 0;
        }
        
        $this->info("Generating QR codes for " . $documents->count() . " {$description}...");
        
        $progressBar = $this->output->createProgressBar($documents->count());
        $progressBar->start();
        
        $successful = 0;
        $failed = 0;
        
        foreach ($documents as $document) {
            try {
                $this->qrCodeService->generateAndSaveDocumentQr($document, ['size' => $size]);
                $successful++;
            } catch (\Exception $e) {
                $failed++;
                $this->newLine();
                $this->error("Failed to generate QR code for {$document->tracking_number}: " . $e->getMessage());
            }
            
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine(2);
        
        $this->info("QR code generation completed!");
        $this->info("Successful: {$successful}");
        if ($failed > 0) {
            $this->warn("Failed: {$failed}");
        }
        
        return $failed > 0 ? 1 : 0;
    }
}