<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

/**
 * Halaman publik blog: daftar artikel dan detail artikel.
 */
class PostController extends Controller
{
    /** Jumlah artikel per halaman. */
    private const PER_PAGE = 9;

    /**
     * Daftar artikel + filter kategori + pencarian judul.
     */
    public function index(Request $request)
    {
        $search = $request->string('q')->trim()->value();
        $category = $request->string('category')->trim()->value();

        $posts = Post::published()
            ->filter($search ?: null, $category ?: null)
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        $categories = Post::published()
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('pages.blog.index', compact('posts', 'search', 'category', 'categories'));
    }

    /**
     * Detail artikel. Route memakai slug supaya ramah SEO (NFR-04).
     */
    public function show(string $slug)
    {
        $post = Post::published()
            ->where('slug', $slug)
            ->firstOrFail();

        $related = Post::published()
            ->whereKeyNot($post->id)
            ->when($post->category, fn ($query) => $query->where('category', $post->category))
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('pages.blog.show', compact('post', 'related'));
    }
}
