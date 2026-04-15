<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class NotifikasiController extends Controller
{
    public function index()
    {
        // Eager loading untuk menghindari N+1 query
        $notifikasi = Notifikasi::where('user_id', Auth::id())
            ->with('user') // Tambahkan ini jika perlu data user
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        // Gunakan cache untuk unread count (5 detik)
        $unreadCount = $this->getCachedUnreadCount();
        
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
        
        // Clear cache setelah update
        $this->clearUnreadCountCache();
        
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
        
        // Clear cache setelah update
        $this->clearUnreadCountCache();
        
        return redirect()->route('notifikasi.index')
            ->with('success', 'Semua notifikasi telah ditandai sebagai dibaca');
    }
    
    public function getUnreadCount()
    {
        $count = $this->getCachedUnreadCount();
        
        return response()->json(['count' => $count]);
    }
    
    // Cache helper methods
    private function getCachedUnreadCount()
    {
        $userId = Auth::id();
        $cacheKey = "unread_count_{$userId}";
        
        return Cache::remember($cacheKey, now()->addSeconds(5), function () use ($userId) {
            return Notifikasi::where('user_id', $userId)
                ->where('status', false)
                ->count();
        });
    }
    
    private function clearUnreadCountCache()
    {
        $userId = Auth::id();
        $cacheKey = "unread_count_{$userId}";
        Cache::forget($cacheKey);
    }
}