<?php

namespace App\Models;

use Database\Factories\SkillFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Skill / teknologi yang dikuasai, dikelompokkan per kategori
 * (Frontend, Backend, Database, Tools).
 */
class Skill extends Model
{
    /** @use HasFactory<SkillFactory> */
    use HasFactory;

    /** Kategori yang dipakai pada halaman publik & form admin. */
    public const CATEGORIES = [
        'frontend' => 'Frontend',
        'backend' => 'Backend',
        'database' => 'Database',
        'tools' => 'Tools',
    ];

    protected $fillable = [
        'name',
        'category',
        'level',
        'icon',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'level' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Label kategori yang rapi untuk ditampilkan (mis. "frontend" -> "Frontend").
     */
    protected function categoryLabel(): Attribute
    {
        return Attribute::get(fn () => self::CATEGORIES[$this->category] ?? ucfirst((string) $this->category));
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Kelompokkan skill per kategori untuk ditampilkan dalam satu halaman.
     */
    public static function groupedByCategory(): array
    {
        return self::ordered()
            ->get()
            ->groupBy(fn (self $skill) => $skill->category ?: 'lainnya')
            ->all();
    }
}
