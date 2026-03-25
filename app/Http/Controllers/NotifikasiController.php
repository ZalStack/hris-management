<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    public function index()
    {
        $notifikasi = Notifikasi::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        $unreadCount = Notifikasi::where('user_id', Auth::id())
            ->where('status', false)
            ->count();
        
        return view('notifikasi.index', compact('notifikasi', 'unreadCount'));
    }
    
    public function markAsRead($id)
    {
        $notifikasi = Notifikasi::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();
        
        $notifikasi->update([
            'status' => true,
            'tanggal_dibaca' => now(),
        ]);
        
        return response()->json(['success' => true]);
    }
    
    public function markAllAsRead()
    {
        Notifikasi::where('user_id', Auth::id())
            ->where('status', false)
            ->update([
                'status' => true,
                'tanggal_dibaca' => now(),
            ]);
        
        return redirect()->route('notifikasi.index')
            ->with('success', 'Semua notifikasi telah ditandai sebagai dibaca');
    }
    
    public function getUnreadCount()
    {
        $count = Notifikasi::where('user_id', Auth::id())
            ->where('status', false)
            ->count();
        
        return response()->json(['count' => $count]);
    }
}