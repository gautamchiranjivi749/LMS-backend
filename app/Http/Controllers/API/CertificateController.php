<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\CertificateResource;
use App\Models\Certificate;
use App\Traits\ApiResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class CertificateController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $certificates = Certificate::with([
            'course',
            'quizAttempt',
            'user'
        ])
        ->where('user_id', Auth::id())
        ->latest()
        ->paginate(10);

        return $this->success(
            'Certificates retrieved successfully.',
            CertificateResource::collection($certificates)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
     public function show(Certificate $certificate)
    {
        if ($certificate->user_id != Auth::id()) {
            return $this->error(
                'Unauthorized.',
                [],
                403
            );
        }

        return $this->success(
            'Certificate details.',
            new CertificateResource(
                $certificate->load([
                    'course',
                    'quizAttempt',
                    'user'
                ])
            )
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function download(Certificate $certificate)
{
    if ($certificate->user_id != Auth::id()) {

        return $this->error(
            'Unauthorized.',
            [],
            403
        );

    }

    $certificate->load([
        'user',
        'course',
    ]);

  $pdf = Pdf::loadView('certificates.certificate', compact('certificate'))
    ->setPaper('a4', 'landscape');

return $pdf->download(
    'certificate-'.$certificate->certificate_no.'.pdf'
);
}
}
