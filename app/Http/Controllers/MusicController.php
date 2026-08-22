<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Process\Process;

class MusicController extends Controller
{
    public function index()
    {
        $songs = $this->searchiTunes('Billie Eilish');
        $newReleases = collect($songs);
        $trendingTracks = $newReleases->take(4);
        return view('home', compact('newReleases', 'trendingTracks'));
    }

    public function searchAjax(Request $request)
    {
        return response()->json($this->searchiTunes($request->input('q') ?: 'Billie Eilish'));
    }

    private function searchiTunes($query)
    {
        try {
            $response = Http::get("https://itunes.apple.com/search", ['term' => $query, 'media' => 'music', 'limit' => 12]);
            return collect($response->json()['results'] ?? [] )->map(function ($item) {
                return (object) [
                    'id' => $item['trackId'] ?? rand(1000, 9999),
                    'title' => $item['trackName'] ?? 'Unknown',
                    'artist' => $item['artistName'] ?? 'Unknown',
                    'cover' => str_replace('100x100bb.jpg', '400x400bb.jpg', $item['artworkUrl100'] ?? ''),
                ];
            });
        } catch (\Exception $e) { return []; }
    }

    public function getDirectStream(Request $request)
    {
        $title = $request->input('title');
        $artist = $request->input('artist');
        $searchQuery = "ytsearch1:{$title} {$artist} official audio";

        $localWindowsPath = 'D:\\laragon\\bin\\python\\python-3.10\\python.exe';

        try {
            // Jika di Windows pakai path lokal dengan -m yt_dlp, jika di Linux (Railway) panggil binary global 'yt-dlp' langsung
            if (file_exists($localWindowsPath)) {
                $process = new Process([$localWindowsPath, '-m', 'yt_dlp', '-g', '-f', 'bestaudio', $searchQuery]);
            } else {
                $process = new Process(['yt-dlp', '-g', '-f', 'bestaudio', $searchQuery]);
            }
            
            $process->setTimeout(30);
            $process->run();

            if ($process->isSuccessful()) {
                $output = trim($process->getOutput());
                $lines = explode("\n", $output);
                $audioUrl = trim($lines[0] ?? '');

                if (!empty($audioUrl)) {
                    return response()->json([
                        'status' => 'success',
                        'url' => $audioUrl
                    ]);
                }
            } else {
                // Catat error asli dari yt-dlp ke log Railway jika proses gagal
                \Log::error("YT-DLP Process Failed: " . $process->getErrorOutput());
            }
        } catch (\Exception $e) {
            // Catat exception jika ada error sistem
            \Log::error("YT-DLP Exception: " . $e->getMessage());
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Gagal mendapatkan URL audio'
        ], 500);
    }
}
