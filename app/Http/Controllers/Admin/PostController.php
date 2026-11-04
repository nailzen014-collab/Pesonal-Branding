<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * CRUD artikel blog di panel admin.
 */
class PostController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->value();

        $posts = Post::when($search, function ($query) use ($search) {
            $query->where('title', 'like', "%{$search}%");
        })
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.posts.index', compact('posts', 'search'));
    }

    public function create(): View
    {
        return view('admin.posts.form', [
            'post' => new Post(['is_published' => true, 'sort_order' => 0]),
        ]);
    }

    public function store(StorePostRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('thumbnail');

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('post-thumbnails', 'public');
        }

        $post = Post::create($data);

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Artikel "'.$post->title.'" berhasil ditambahkan.');
    }

    public function edit(Post $post): View
    {
        return view('admin.posts.form', [
            'post' => $post,
        ]);
    }

    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $data = $request->safe()->except('thumbnail');

        if ($request->hasFile('thumbnail')) {
            $this->deleteFile($post->thumbnail);
            $data['thumbnail'] = $request->file('thumbnail')->store('post-thumbnails', 'public');
        }

        $post->update($data);

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Artikel "'.$post->title.'" berhasil diperbarui.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $this->deleteFile($post->thumbnail);
        $post->delete();

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Artikel berhasil dihapus.');
    }

    /**
     * Hapus file lama agar folder storage tidak penuh file tidak terpakai.
     */
    private function deleteFile(?string $path): void
    {
        if ($path && ! str_starts_with($path, 'http')) {
            Storage::disk('public')->delete($path);
        }
    }
}
