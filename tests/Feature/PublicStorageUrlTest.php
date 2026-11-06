<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicStorageUrlTest extends TestCase
{
    public function test_it_keeps_static_and_external_paths(): void
    {
        $this->assertSame(asset('images/profile.jpg'), public_storage_url('images/profile.jpg'));
        $this->assertSame('https://example.com/photo.jpg', public_storage_url('https://example.com/photo.jpg'));
        $this->assertNull(public_storage_url(null));
        $this->assertSame('fallback.jpg', public_storage_url(null, 'fallback.jpg'));
    }

    public function test_it_builds_urls_from_the_public_disk(): void
    {
        Storage::forgetDisk('public');
        config(['filesystems.disks.public' => [
            'driver' => 's3',
            'key' => 'key',
            'secret' => 'secret',
            'region' => 'ap-southeast-1',
            'bucket' => 'public',
            'url' => 'https://cdn.example.com/public',
            'endpoint' => 'https://example.supabase.co/storage/v1/s3',
            'use_path_style_endpoint' => true,
        ]]);

        $this->assertSame(
            'https://cdn.example.com/public/certificates/certificate.png',
            public_storage_url('certificates/certificate.png'),
        );
    }

    protected function tearDown(): void
    {
        Storage::forgetDisk('public');

        parent::tearDown();
    }
}
