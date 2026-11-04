<?php

namespace App\Models;

use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Model artikel blog.
 *
 * Konsep: sama seperti Project, slug dibuat otomatis dari judul
 * supaya URL ramah SEO (misal: /blog/cara-install-laravel-breeze).
 */
class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'body',
        'category',
        'thumbnail',
        'is_published',
        'published_at',
        'sort_order',
        'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
            'sort_order' => 'integer',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    /**
     * Filter + pencarian untuk halaman /blog.
     */
    public function scopeFilter(Builder $query, ?string $search, ?string $category): Builder
    {
        return $query
            ->when($search, function (Builder $q, string $search) {
                $q->where(function (Builder $inner) use ($search) {
                    $inner->where('title', 'like', "%{$search}%")
                        ->orWhere('excerpt', 'like', "%{$search}%");
                });
            })
            ->when($category, fn (Builder $q, string $category) => $q->where('category', $category));
    }

    protected static function booted(): void
    {
        static::saving(function (self $post) {
            if (blank($post->slug)) {
                $post->slug = static::uniqueSlug($post->title, $post->id);
            }
        });
    }

    public static function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'artikel';
        $slug = $base;
        $counter = 2;

        while (static::where('slug', $slug)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists()) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    /**
     * URL thumbnail, atau null bila artikel belum punya gambar.
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        if (blank($this->thumbnail)) {
            return null;
        }

        return str_starts_with($this->thumbnail, 'http')
            ? $this->thumbnail
            : asset('storage/'.$this->thumbnail);
    }

    /**
     * Excerpt otomatis dari body bila kolom excerpt kosong.
     */
    public function getExcerptAttribute(?string $value): ?string
    {
        if (filled($value)) {
            return $value;
        }

        return $this->body ? Str::limit(strip_tags($this->body_html), 160) : null;
    }

    /**
     * HTML aman dari body Markdown untuk halaman detail artikel.
     */
    public function getBodyHtmlAttribute(): string
    {
        if (blank($this->body)) {
            return '';
        }

        return Str::markdown($this->body, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
    }

    /**
     * Tanggal publikasi untuk ditampilkan; fallback ke created_at.
     */
    public function getDateAttribute()
    {
        return $this->published_at ?? $this->created_at;
    }
}
