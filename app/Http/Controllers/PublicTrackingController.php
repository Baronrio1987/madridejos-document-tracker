<?php 

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

class PublicTrackingController extends Controller
{
    public function index()
    {
        return view('public.track');
    }
    
    public function search(Request $request)
    {
        $request->validate([
            'tracking_number' => 'required|string|min:5|max:50'
        ]);
        
        $trackingNumber = strtoupper(trim($request->tracking_number));
        
        $document = Document::where('tracking_number', $trackingNumber)
                          ->with([
                              'documentType', 
                              'originDepartment', 
                              'currentDepartment',
                              'routes' => function($query) {
                                  $query->orderBy('routed_at', 'asc');
                              },
                              'routes.fromDepartment', 
                              'routes.toDepartment'
                          ])
                          ->first();
        
        if (!$document) {
            return back()->with('error', 'Document with tracking number "' . $trackingNumber . '" was not found. Please check the tracking number and try again.');
        }
        
        return view('public.track-result', compact('document'));
    }
    
    public function show($trackingNumber)
    {
        $trackingNumber = strtoupper(trim($trackingNumber));
        
        $document = Document::where('tracking_number', $trackingNumber)
                          ->with([
                              'documentType', 
                              'originDepartment', 
                              'currentDepartment',
                              'routes' => function($query) {
                                  $query->orderBy('routed_at', 'asc');
                              },
                              'routes.fromDepartment', 
                              'routes.toDepartment'
                          ])
                          ->first();
        
        if (!$document) {
            abort(404, 'Document not found');
        }
        
        return view('public.track-result', compact('document'));
    }
}