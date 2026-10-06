<?php

namespace App\Models;

use Database\Factories\ExperienceFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Timeline pendidikan / pengalaman / organisasi (FR-10).
 */
class Experience extends Model
{
    /** @use HasFactory<ExperienceFactory> */
    use HasFactory;

    public const TYPES = [
        'education' => 'Pendidikan',
        'experience' => 'Pengalaman',
        'organization' => 'Organisasi',
    ];

    protected $fillable = [
        'type',
        'title',
        'organization',
        'start_date',
        'end_date',
        'description',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'sort_order' => 'integer',
        ];
    }

    protected function typeLabel(): Attribute
    {
        return Attribute::get(
            fn () => self::TYPES[$this->type] ?? ucfirst((string) $this->type)
        );
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByDesc('start_date')->orderBy('sort_order');
    }

    /**
     * Rentang waktu siap tampil, mis. "2024 - Sekarang".
     */
    public function getPeriodAttribute(): string
    {
        $start = $this->start_date?->format('M Y') ?? '-';

        return $start.' - '.($this->end_date?->format('M Y') ?? 'Sekarang');
    }
}
