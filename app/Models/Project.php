<?php

namespace App\Models;

use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

/**
 * Model proyek portofolio.
 *
 * Catatan: daftar proyek selalu dipanggil dengan `with('technologies')`
 * supaya Laravel tidak melakukan query berulang untuk tiap baris (N+1).
 */
class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'summary',
        'description',
        'category',
        'repo_url',
        'demo_url',
        'thumbnail',
        'source',
        'github_id',
        'is_featured',
        'is_published',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Relasi many-to-many: satu proyek bisa memakai banyak teknologi.
     */
    public function technologies(): BelongsToMany
    {
        return $this->belongsToMany(Technology::class)->withTimestamps();
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Filter + pencarian untuk halaman /portofolio (FR-07).
     */
    public function scopeFilter(Builder $query, ?string $search, ?string $category, ?string $technology): Builder
    {
        return $query
            ->when($search, function (Builder $q, string $search) {
                $q->where(function (Builder $inner) use ($search) {
                    $inner->where('title', 'like', "%{$search}%")
                        ->orWhere('summary', 'like', "%{$search}%");
                });
            })
            ->when($category, fn (Builder $q, string $category) => $q->where('category', $category))
            ->when($technology, function (Builder $q, string $technology) {
                $q->whereHas('technologies', fn (Builder $tech) => $tech->where('name', $technology));
            });
    }

    protected static function booted(): void
    {
        // Slug dibuat otomatis dari judul supaya URL ramah SEO (NFR-04).
        static::saving(function (self $project) {
            if (blank($project->slug)) {
                $project->slug = static::uniqueSlug($project->title, $project->id);
            }
        });
    }

    public static function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'proyek';
        $slug = $base;
        $counter = 2;

        while (static::where('slug', $slug)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists()) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    /**
     * URL thumbnail, atau null bila proyek belum punya gambar.
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
}
