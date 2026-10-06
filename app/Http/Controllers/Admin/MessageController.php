<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Kotak masuk pesan dari form kontak (FR-19).
 */
class MessageController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->string('filter')->toString();

        $messages = Message::query()
            ->when($filter === 'unread', fn ($query) => $query->unread())
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $unreadCount = Message::unread()->count();

        return view('admin.messages.index', compact('messages', 'filter', 'unreadCount'));
    }

    public function show(Message $message): View
    {
        // Pesan langsung ditandai dibaca saat dibuka.
        $message->markAsRead();

        return view('admin.messages.show', compact('message'));
    }

    public function markAsRead(Message $message): RedirectResponse
    {
        $message->markAsRead();

        return back()->with('success', 'Pesan ditandai sudah dibaca.');
    }

    public function destroy(Message $message): RedirectResponse
    {
        $message->delete();

        return redirect()
            ->route('admin.messages.index')
            ->with('success', 'Pesan berhasil dihapus.');
    }
}
