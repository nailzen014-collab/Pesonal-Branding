<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCertificateRequest;
use App\Http\Requests\UpdateCertificateRequest;
use App\Models\Certificate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * CRUD sertifikat / pencapaian (FR-18).
 */
class CertificateController extends Controller
{
    public function index(): View
    {
        $certificates = Certificate::ordered()->paginate(12);

        return view('admin.certificates.index', compact('certificates'));
    }

    public function create(): View
    {
        return view('admin.certificates.form', ['certificate' => new Certificate]);
    }

    public function store(StoreCertificateRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('image');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('certificates', 'public');
        }

        $certificate = Certificate::create($data);

        return redirect()
            ->route('admin.certificates.index')
            ->with('success', 'Sertifikat "'.$certificate->title.'" berhasil ditambahkan.');
    }

    public function edit(Certificate $certificate): View
    {
        return view('admin.certificates.form', compact('certificate'));
    }

    public function update(UpdateCertificateRequest $request, Certificate $certificate): RedirectResponse
    {
        $data = $request->safe()->except('image');

        if ($request->hasFile('image')) {
            if ($certificate->image && ! str_starts_with($certificate->image, 'http')) {
                Storage::disk('public')->delete($certificate->image);
            }

            $data['image'] = $request->file('image')->store('certificates', 'public');
        }

        $certificate->update($data);

        return redirect()
            ->route('admin.certificates.index')
            ->with('success', 'Sertifikat berhasil diperbarui.');
    }

    public function destroy(Certificate $certificate): RedirectResponse
    {
        if ($certificate->image && ! str_starts_with($certificate->image, 'http')) {
            Storage::disk('public')->delete($certificate->image);
        }

        $certificate->delete();

        return redirect()
            ->route('admin.certificates.index')
            ->with('success', 'Sertifikat berhasil dihapus.');
    }
}
