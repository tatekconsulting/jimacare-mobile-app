<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ComplianceController extends Controller
{
    /**
     * Display compliance dashboard
     */
    public function index()
    {
        $user = auth()->user();
        
        // Get all documents with compliance status
        $documents = $user->documents()
            ->orderBy('expiration', 'asc')
            ->get()
            ->map(function ($doc) {
                $doc->updateComplianceStatus();
                $doc->save();
                return $doc;
            });

        // Count by status
        $stats = [
            'total' => $documents->count(),
            'valid' => $documents->where('compliance_status', 'valid')->count(),
            'expiring' => $documents->where('compliance_status', 'expiring')->count(),
            'expired' => $documents->where('compliance_status', 'expired')->count(),
        ];

        // Get expiring documents (within 30 days)
        $expiringSoon = $documents->filter(function ($doc) {
            return $doc->isExpiringSoon();
        });

        // Get expired documents
        $expired = $documents->filter(function ($doc) {
            return $doc->isExpired();
        });

        return view('app.pages.compliance.index', compact('documents', 'stats', 'expiringSoon', 'expired'));
    }

    /**
     * Get compliance alerts (for AJAX)
     */
    public function alerts()
    {
        $user = auth()->user();
        
        $alerts = [];
        
        // Check for expiring documents (within 30 days)
        $expiringDocuments = $user->documents()
            ->whereNotNull('expiration')
            ->where('expiration', '>', now())
            ->where('expiration', '<=', now()->addDays(30))
            ->get();

        foreach ($expiringDocuments as $doc) {
            $daysUntilExpiry = now()->diffInDays($doc->expiration);
            $alerts[] = [
                'type' => 'expiring',
                'message' => "Your document '{$doc->name}' expires in {$daysUntilExpiry} days",
                'document_id' => $doc->id,
                'expiration_date' => $doc->expiration->format('d/m/Y'),
                'days_until' => $daysUntilExpiry
            ];
        }

        // Check for expired documents
        $expiredDocuments = $user->documents()
            ->whereNotNull('expiration')
            ->where('expiration', '<', now())
            ->get();

        foreach ($expiredDocuments as $doc) {
            $daysExpired = now()->diffInDays($doc->expiration);
            $alerts[] = [
                'type' => 'expired',
                'message' => "Your document '{$doc->name}' expired {$daysExpired} days ago",
                'document_id' => $doc->id,
                'expiration_date' => $doc->expiration->format('d/m/Y'),
                'days_expired' => $daysExpired
            ];
        }

        return response()->json([
            'success' => true,
            'alerts' => $alerts,
            'count' => count($alerts)
        ]);
    }

    /**
     * Update document expiry date
     */
    public function updateExpiry(Request $request, Document $document)
    {
        // Verify user owns the document
        if ($document->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $request->validate([
            'expiry_date' => 'required|date|after:today'
        ]);

        $document->expiration = Carbon::parse($request->expiry_date);
        $document->updateComplianceStatus();
        $document->save();

        return response()->json([
            'success' => true,
            'message' => 'Expiry date updated successfully',
            'compliance_status' => $document->compliance_status
        ]);
    }
}

