<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Sertifikat / pencapaian (FR-11).
 */
class Certificate extends Model
{
    protected $fillable = [
        'title',
        'issuer',
        'issued_at',
        'image',
        'url',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'date',
            'sort_order' => 'integer',
        ];
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByDesc('issued_at')->orderBy('sort_order');
    }

    public function getImageUrlAttribute(): ?string
    {
        if (blank($this->image)) {
            return null;
        }

        return str_starts_with($this->image, 'http')
            ? $this->image
            : asset('storage/'.$this->image);
    }
}
