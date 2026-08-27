<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Process\Process;

class MusicController extends Controller
{
    public function index()
    {
        $songs = $this->searchiTunes('Indonesian Pop');
        $newReleases = $songs;
        $trendingTracks = collect($songs)->take(4)->all();
        return view('home', compact('newReleases', 'trendingTracks'));
    }

    public function searchAjax(Request $request)
    {
        $query = $request->input('q');
        if (empty($query)) {
            return response()->json([]);
        }

        $cleanQuery = $this->extractSmartKeyword($query);
        $results = $this->searchiTunes($cleanQuery);
        
        if (empty($results)) {
            $results = $this->searchiTunes($query);
        }

        return response()->json($results);
    }

    private function extractSmartKeyword($input)
    {
        $clean = preg_replace('/(official|audio|video|lyrics|ft|feat|\(|\[).*$/i', '', $input);
        $words = explode(' ', trim($clean));
        if (count($words) > 1) {
            return $words[0] . ' ' . ($words[1] ?? '');
        }
        return trim($clean);
    }

    private function searchiTunes($query)
    {
        try {
            $response = Http::get("https://itunes.apple.com/search", [
                'term' => $query, 
                'media' => 'music', 
                'limit' => 20
            ]);
            
            return collect($response->json()['results'] ?? [] )->map(function ($item) {
                $imgUrl = $item['artworkUrl100'] ?? '';
                if (!empty($imgUrl)) {
                    $imgUrl = str_replace('100x100bb.jpg', '400x400bb.jpg', $imgUrl);
                } else {
                    $imgUrl = 'https://images.unsplash.com/photo-1614613535308-eb5fbd3d2c17?w=500';
                }

                return (object) [
                    'id' => $item['trackId'] ?? rand(1000, 9999),
                    'title' => $item['trackName'] ?? 'Unknown',
                    'artist' => $item['artistName'] ?? 'Unknown',
                    'cover' => $imgUrl,
                ];
            })->filter(function ($song) {
                // FILTER MUTLAK DI BACKEND: Mencegah lagu anak-anak masuk ke sistem
                $title = strtolower($song->title);
                $artist = strtolower($song->artist);
                
                $isKidsSong = str_contains($title, 'monkey') || 
                            str_contains($title, 'nursery') || 
                            str_contains($title, 'rhymes') || 
                            str_contains($artist, 'kids');
                            
                return !$isKidsSong;
            })->values()->all();

        } catch (\Exception $e) { 
            return []; 
        }
    }

    public function getDirectStream(Request $request)
    {
        $title = $request->input('title');
        $artist = $request->input('artist');
        $searchQuery = "ytsearch1:{$title} {$artist} official audio";

        $localWindowsPath = 'D:\\laragon\\bin\\python\\python-3.10\\python.exe';
        $cookiePath = base_path('cookies.txt');

        try {
            // Jika di Windows (Laragon) lokal, jalankan standar. Jika di Railway, gunakan ejs:npm dan cookies.txt.
            if (file_exists($localWindowsPath)) {
                $process = new Process([$localWindowsPath, '-m', 'yt_dlp', '-g', '-f', 'bestaudio', $searchQuery]);
            } else {
                $processArgs = [
                    'yt-dlp', 
                    '--remote-components', 'ejs:npm',
                    '--extractor-args', 'youtube:player-client=web',
                ];

                if (file_exists($cookiePath)) {
                    $processArgs[] = '--cookies';
                    $processArgs[] = $cookiePath;
                }

                $processArgs[] = '-g';
                $processArgs[] = '-f';
                $processArgs[] = 'bestaudio';
                $processArgs[] = $searchQuery;

                $process = new Process($processArgs);
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
                // Tangkap error asli dari proses yt-dlp
                $errorOutput = $process->getErrorOutput() ?: $process->getOutput();
                return response()->json([
                    'status' => 'error',
                    'message' => 'YT-DLP Error: ' . trim($errorOutput)
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Exception Error: ' . $e->getMessage()
            ], 500);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Gagal mendapatkan URL audio (Unknown)'
        ], 500);
    }
}
