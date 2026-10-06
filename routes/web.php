<?php

use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExperienceController;
use App\Http\Controllers\Admin\GithubSyncController;
use App\Http\Controllers\Admin\MessageController as AdminMessageController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SkillController as AdminSkillController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Route Publik
|--------------------------------------------------------------------------
| Semua halaman yang dilihat pengunjung website.
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/tentang', [PageController::class, 'tentang'])->name('tentang');
Route::get('/skills', [PageController::class, 'skills'])->name('skills');
Route::get('/portofolio', [ProjectController::class, 'index'])->name('portofolio');
Route::get('/portofolio/{slug}', [ProjectController::class, 'show'])->name('portofolio.show');
Route::get('/sertifikat', [PageController::class, 'sertifikat'])->name('sertifikat');

// Proteksi spam: maksimal 5 pesan per menit dari satu alamat IP.
Route::post('/kontak', [ContactController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('kontak.store');
Route::get('/kontak', [ContactController::class, 'index'])->name('kontak');

// SEO: sitemap.xml & robots.txt (NFR-04).
Route::get('/sitemap.xml', [SitemapController::class, '__invoke'])->name('sitemap');
Route::get('/robots.txt', [RobotsController::class, '__invoke'])->name('robots');

/*
|--------------------------------------------------------------------------
| Route Admin
|--------------------------------------------------------------------------
| Middleware "auth" memastikan hanya pengguna login yang bisa mengakses.
| Registrasi publik dinonaktifkan; akun admin dibuat lewat seeder.
*/
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('projects', AdminProjectController::class)->except(['show']);
    Route::post('projects/{project}/featured', [AdminProjectController::class, 'toggleFeatured'])
        ->name('projects.toggle-featured');
    Route::get('skills', [AdminSkillController::class, 'index'])->name('skills.index');
    Route::get('skills/create', [AdminSkillController::class, 'create'])->name('skills.create');
    Route::post('skills', [AdminSkillController::class, 'store'])->name('skills.store');
    Route::get('skills/{skill}/edit', [AdminSkillController::class, 'edit'])->name('skills.edit');
    Route::put('skills/{skill}', [AdminSkillController::class, 'update'])->name('skills.update');
    Route::delete('skills/{skill}', [AdminSkillController::class, 'destroy'])->name('skills.destroy');

    Route::get('experiences', [ExperienceController::class, 'index'])->name('experiences.index');
    Route::get('experiences/create', [ExperienceController::class, 'create'])->name('experiences.create');
    Route::post('experiences', [ExperienceController::class, 'store'])->name('experiences.store');
    Route::get('experiences/{experience}/edit', [ExperienceController::class, 'edit'])->name('experiences.edit');
    Route::put('experiences/{experience}', [ExperienceController::class, 'update'])->name('experiences.update');
    Route::delete('experiences/{experience}', [ExperienceController::class, 'destroy'])->name('experiences.destroy');

    Route::get('certificates', [CertificateController::class, 'index'])->name('certificates.index');
    Route::get('certificates/create', [CertificateController::class, 'create'])->name('certificates.create');
    Route::post('certificates', [CertificateController::class, 'store'])->name('certificates.store');
    Route::get('certificates/{certificate}/edit', [CertificateController::class, 'edit'])->name('certificates.edit');
    Route::put('certificates/{certificate}', [CertificateController::class, 'update'])->name('certificates.update');
    Route::delete('certificates/{certificate}', [CertificateController::class, 'destroy'])->name('certificates.destroy');

    Route::get('messages', [AdminMessageController::class, 'index'])->name('messages.index');
    Route::get('messages/{message}', [AdminMessageController::class, 'show'])->name('messages.show');
    Route::put('messages/{message}/read', [AdminMessageController::class, 'markAsRead'])->name('messages.read');
    Route::delete('messages/{message}', [AdminMessageController::class, 'destroy'])->name('messages.destroy');

    Route::get('github', [GithubSyncController::class, 'index'])->name('github.index');
    Route::post('github', [GithubSyncController::class, 'store'])->name('github.store');
    Route::post('github/refresh', [GithubSyncController::class, 'refresh'])->name('github.refresh');
    Route::post('github/sync', [GithubSyncController::class, 'sync'])->name('github.sync');

    Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
});

require __DIR__.'/auth.php';
