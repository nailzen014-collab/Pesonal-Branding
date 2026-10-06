<?php

namespace App\Models;

use Database\Factories\TechnologyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Teknologi (Laravel, PHP, Tailwind, ...) yang dipakai pada proyek.
 */
class Technology extends Model
{
    /** @use HasFactory<TechnologyFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'icon',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class)->withTimestamps();
    }

    /**
     * Nama kategori/teknologi yang sudah dipakai, untuk mengisi filter.
     */
    public function scopeUsedInProjects($query)
    {
        return $query->whereHas('projects');
    }
}
