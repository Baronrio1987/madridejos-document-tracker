<?php

namespace App\Services;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\Label\Font\NotoSans;
use Illuminate\Support\Facades\Storage;

class QrCodeService
{
    public function generateDocumentQr($document, $options = [])
    {
        // Create the tracking URL - force correct base URL
        $baseUrl = config('app.url');
        $trackingUrl = $baseUrl . '/track/' . $document->tracking_number;
        
        // Default options
        $defaultOptions = [
            'size' => 300,
            'margin' => 10,
            'label' => $document->tracking_number,
            'logo' => null,
        ];
        
        $options = array_merge($defaultOptions, $options);
        
        try {
            // Build QR code using Builder (v4.x style)
            $builder = Builder::create()
                ->writer(new PngWriter())
                ->writerOptions([])
                ->data($trackingUrl)
                ->encoding(new Encoding('UTF-8'))
                ->errorCorrectionLevel(new ErrorCorrectionLevelLow())
                ->size($options['size'])
                ->margin($options['margin'])
                ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
                ->foregroundColor(new Color(0, 0, 0))
                ->backgroundColor(new Color(255, 255, 255));

            // Add label if provided
            if (!empty($options['label'])) {
                $builder->labelText($options['label'])
                       ->labelFont(new NotoSans(16))
                       ->labelAlignment(new LabelAlignmentCenter());
            }

            // Add logo if provided
            if (!empty($options['logo']) && file_exists($options['logo'])) {
                $builder->logoPath($options['logo'])
                       ->logoResizeToWidth(50);
            }

            return $builder->build();
        } catch (\Exception $e) {
            throw new \Exception('QR Code generation failed: ' . $e->getMessage());
        }
    }

    public function saveQrCode($qrCodeResult, $filename)
    {
        $path = 'qr-codes/' . $filename;
        
        // Ensure the directory exists
        $directory = dirname(storage_path('app/public/' . $path));
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        
        Storage::disk('public')->put($path, $qrCodeResult->getString());
        return $path;
    }

    public function generateAndSaveDocumentQr($document, $options = [])
    {
        $qrCodeResult = $this->generateDocumentQr($document, $options);
        $filename = 'document_' . $document->tracking_number . '.png';
        $path = $this->saveQrCode($qrCodeResult, $filename);
        
        return [
            'qr_code' => $qrCodeResult,
            'path' => $path,
            'url' => Storage::disk('public')->url($path)
        ];
    }

    public function generateBulkQrCodes($documents, $options = [])
    {
        $results = [];
        
        foreach ($documents as $document) {
            try {
                $result = $this->generateAndSaveDocumentQr($document, $options);
                $results[] = [
                    'document' => $document,
                    'success' => true,
                    'qr_data' => $result
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'document' => $document,
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        }
        
        return $results;
    }
}