<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Muvibes - Music Streaming</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
        body { background-color: #050505; color: #94a3b8; font-family: 'Plus Jakarta Sans', sans-serif; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #1a1a1a; border-radius: 10px; }
        .glass { background: rgba(255, 255, 255, 0.02); backdrop-filter: blur(16px); border: 1px solid rgba(255, 255, 255, 0.05); }
        .glass-sidebar { background: rgba(8, 8, 12, 0.8); backdrop-filter: blur(20px); }
        .hover-card { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .hover-card:hover { transform: translateY(-4px); background: rgba(255, 255, 255, 0.04); border-color: rgba(168, 85, 247, 0.3); }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-screen overflow-hidden flex flex-col justify-between selection:bg-purple-500 selection:text-white" 
      x-data="{ 
          isPlaying: false, hasSelectedSong: false,
          searchQuery: '', songs: @js($newReleases ?? []),
          searchMusic() {
              if (!this.searchQuery.trim()) return;
              fetch(`/api/search?q=${encodeURIComponent(this.searchQuery)}`)
                  .then(r => r.json()).then(data => { this.songs = data; });
          }
      }">

    <!-- AUDIO HTML5 UTAMA -->
    <audio id="main-audio"></audio>

    <div class="flex flex-1 overflow-hidden">
        <!-- SIDEBAR KIRI -->
        <aside class="w-64 glass-sidebar border-r border-white/5 flex flex-col justify-between hidden md:flex shrink-0">
            <div class="flex flex-col h-full overflow-hidden">
                <div class="h-20 flex items-center px-6 gap-3.5 shrink-0">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-purple-600 to-indigo-500 flex items-center justify-center text-white shadow-lg">
                        <i class="fa-solid fa-wave-square text-sm"></i>
                    </div>
                    <span class="text-white font-bold text-base tracking-wider">Muvibes</span>
                </div>
                <div class="px-3 py-2 space-y-1">
                    <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-r-xl text-xs text-purple-400 bg-purple-600/10 font-semibold border-l-2 border-purple-500">
                        <i class="fa-solid fa-house text-sm w-4"></i> Beranda
                    </a>
                </div>
            </div>
        </aside>

        <!-- KONTEN UTAMA -->
        <main class="flex-1 flex flex-col overflow-hidden bg-[#050505]">
            <header class="h-20 border-b border-white/5 flex items-center justify-between px-8 shrink-0 bg-[#050505]/60 backdrop-blur-xl z-10">
                <form @submit.prevent class="relative w-full max-w-md">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-xs"></i>
                    <input type="text" x-model="searchQuery" @input.debounce.400ms="searchMusic()" placeholder="Cari musik (misal: Billie Eilish)..." class="w-full bg-white/[0.03] border border-white/10 rounded-full pl-11 pr-4 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-purple-500/50">
                </form>
            </header>

            <div class="flex-1 overflow-y-auto custom-scrollbar p-8 space-y-8">
                <div class="relative rounded-3xl overflow-hidden bg-gradient-to-r from-purple-950/40 via-indigo-950/20 to-transparent border border-white/5 p-8 flex flex-col justify-between min-h-[200px]">
                    <div class="relative z-10 max-w-lg">
                        <span class="text-[10px] font-bold tracking-widest text-purple-400 uppercase">YT-DLP PYTHON BRIDGE</span>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-white mt-2 leading-tight">Full Durasi Asli,<br>Stabil & Keren.</h1>
                        <p class="text-xs text-slate-400 mt-2 leading-relaxed">Streaming musik interaktif dengan backend ekstraksi lokal.</p>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-5">
                        <h2 class="text-sm font-bold text-white tracking-wide" x-text="searchQuery ? 'Hasil Pencarian: &quot;' + searchQuery + '&quot;' : 'Daftar Musik Tersedia'"></h2>
                        <span class="text-xs text-purple-400 font-medium">Total: <span x-text="songs.length"></span> lagu</span>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <template x-for="song in songs" :key="song.id">
                            <div class="glass p-3 rounded-2xl hover-card group cursor-pointer flex flex-col justify-between" @click="playTrack(song)">
                                <div class="relative overflow-hidden rounded-xl mb-3 aspect-square">
                                    <img :src="song.cover" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                        <div class="w-10 h-10 bg-purple-600 rounded-full shadow-xl hover:scale-110 flex items-center justify-center text-white transition">
                                            <i class="fa-solid fa-play text-xs ml-0.5"></i>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-white truncate" x-text="song.title"></h4>
                                    <p class="text-[10px] text-slate-500 uppercase tracking-wider font-semibold mt-0.5 truncate" x-text="song.artist"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </main>

        <!-- SIDEBAR KANAN -->
        <aside class="w-72 glass-sidebar border-l border-white/5 hidden xl:flex flex-col p-6 overflow-y-auto custom-scrollbar shrink-0 space-y-6">
            <div>
                <h3 class="text-xs font-bold text-white flex items-center gap-2 mb-4">
                    <i class="fa-solid fa-chart-line text-purple-400"></i> Rekomendasi Pilihan
                </h3>
                <div class="space-y-2">
                    @forelse($trendingTracks ?? [] as $index => $track)
                        <div class="flex items-center justify-between p-2 rounded-xl hover:bg-white/5 transition group cursor-pointer" onclick="playTrack(@js($track))">
                            <div class="flex items-center gap-3 overflow-hidden">
                                <img src="{{ $track->cover }}" class="w-10 h-10 rounded-lg object-cover shrink-0">
                                <div class="overflow-hidden">
                                    <h4 class="text-xs font-medium text-slate-200 truncate group-hover:text-purple-400 transition">{{ $track->title }}</h4>
                                    <p class="text-[10px] text-slate-500 truncate">{{ $track->artist }}</p>
                                </div>
                            </div>
                            <div class="w-8 h-8 rounded-full bg-white/5 group-hover:bg-purple-600 text-slate-300 group-hover:text-white flex items-center justify-center transition shrink-0 ml-2">
                                <i class="fa-solid fa-play text-[9px] ml-0.5"></i>
                            </div>
                        </div>
                    @empty
                        <p class="text-[10px] text-slate-600">Tidak ada data.</p>
                    @endforelse
                </div>
            </div>
        </aside>
    </div>

    <!-- AUDIO PLAYER BAR BAWAH -->
    <footer class="h-24 glass border-t border-white/5 px-8 flex items-center justify-between shrink-0 z-20" x-show="hasSelectedSong" x-cloak>
        <div class="flex items-center gap-4 w-1/4">
            <img id="player-cover" src="" class="w-12 h-12 rounded-xl object-cover border border-white/10 shrink-0">
            <div class="overflow-hidden">
                <h4 id="player-title" class="text-xs font-bold text-white truncate"></h4>
                <p id="player-artist" class="text-[10px] text-purple-400 truncate"></p>
            </div>
        </div>

        <div class="flex flex-col items-center max-w-md w-2/4 space-y-2">
            <div class="flex items-center gap-6 text-slate-400">
                <button @click="togglePlay()" class="w-10 h-10 rounded-full bg-white hover:bg-purple-600 text-black hover:text-white flex items-center justify-center transition shadow-lg">
                    <i :class="isPlaying ? 'fa-solid fa-pause text-xs' : 'fa-solid fa-play text-xs'"></i>
                </button>
            </div>
            <div class="w-full flex items-center gap-3 text-[10px] font-mono text-slate-400">
                <span id="current-time">0:00</span>
                <input type="range" id="progress-slider" min="0" max="100" value="0" step="0.1" 
                       oninput="seekAudio(this.value)" 
                       class="flex-1 h-1 bg-white/10 rounded-full appearance-none cursor-pointer accent-purple-500">
                <span id="total-duration">0:00</span>
            </div>
        </div>

        <div class="w-1/4 flex justify-end">
            <input type="range" min="0" max="100" value="70" @input="changeVolume($event)" class="w-24 h-1 bg-white/10 rounded-full accent-purple-500 cursor-pointer">
        </div>
    </footer>

    <!-- SCRIPT EKSTRAKSI YT-DLP & AUDIO HTML5 -->
    <script>
        const audio = document.getElementById('main-audio');
        let isSeeking = false;

        async function playTrack(song) {
            let alpineData = Alpine.$data(document.body);
            alpineData.hasSelectedSong = true;

            document.getElementById('player-title').innerText = song.title;
            document.getElementById('player-artist').innerText = "Memuat Audio...";
            document.getElementById('player-cover').src = song.cover;

            try {
                let res = await fetch(`/get-direct-stream?title=${encodeURIComponent(song.title)}&artist=${encodeURIComponent(song.artist)}`);
                let data = await res.json();

                if (data.status === 'success' && data.url) {
                    audio.src = data.url;
                    audio.load();
                    audio.play().then(() => {
                        alpineData.isPlaying = true;
                        document.getElementById('player-artist').innerText = song.artist + " • Full Track";
                    }).catch(e => {
                        alpineData.isPlaying = false;
                        document.getElementById('player-artist').innerText = song.artist + " • Klik Tombol Play";
                    });
                } else {
                    document.getElementById('player-artist').innerText = song.artist + " • Gagal Memuat Audio";
                }
            } catch (err) {
                document.getElementById('player-artist').innerText = "Gangguan Jaringan Server";
            }
        }

        audio.addEventListener('timeupdate', () => {
            if (!isNaN(audio.duration) && !isSeeking) {
                let currentTime = audio.currentTime;
                let duration = audio.duration;

                document.getElementById('current-time').innerText = formatTime(currentTime);
                document.getElementById('total-duration').innerText = formatTime(duration);

                let percent = (currentTime / duration) * 100;
                document.getElementById('progress-slider').value = percent;
            }
        });

        audio.addEventListener('ended', () => {
            let alpineData = Alpine.$data(document.body);
            alpineData.isPlaying = false;
        });

        function seekAudio(percent) {
            if (!isNaN(audio.duration)) {
                isSeeking = true;
                let seekTime = (percent / 100) * audio.duration;
                audio.currentTime = seekTime;
                setTimeout(() => { isSeeking = false; }, 200);
            }
        }

        function togglePlay() {
            let alpineData = Alpine.$data(document.body);
            if (audio.paused) {
                audio.play();
                alpineData.isPlaying = true;
            } else {
                audio.pause();
                alpineData.isPlaying = false;
            }
        }

        function changeVolume(event) {
            audio.volume = event.target.value / 100;
        }

        function formatTime(seconds) {
            let minutes = Math.floor(seconds / 60);
            let remainingSeconds = Math.floor(seconds % 60);
            return minutes + ':' + (remainingSeconds < 10 ? '0' : '') + remainingSeconds;
        }
    </script>
</body>
</html>