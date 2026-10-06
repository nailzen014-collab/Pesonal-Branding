<?php

namespace App\Providers;

use App\Models\Message;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Daftar menu utama ditulis satu kali lalu dipakai navbar & footer
        // supaya tidak ada duplikasi sumber kebenaran.
        View::share('navItems', [
            ['label' => 'Beranda', 'route' => 'home'],
            ['label' => 'Tentang', 'route' => 'tentang'],
            ['label' => 'Skills', 'route' => 'skills'],
            ['label' => 'Portofolio', 'route' => 'portofolio'],
            ['label' => 'Sertifikat', 'route' => 'sertifikat'],
            ['label' => 'Kontak', 'route' => 'kontak'],
        ]);

        // Lencana jumlah pesan belum dibaca pada sidebar admin.
        View::composer('layouts.admin', function ($view) {
            $view->with('unreadCount', Message::unread()->count());
        });
    }
}
