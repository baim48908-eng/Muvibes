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
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #262626; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #404040; }
        .glass { background: rgba(255, 255, 255, 0.02); backdrop-filter: blur(16px); border: 1px solid rgba(255, 255, 255, 0.05); }
        .glass-sidebar { background: rgba(8, 8, 12, 0.95); backdrop-filter: blur(20px); }
        .glass-modal { background: rgba(13, 13, 18, 0.96); backdrop-filter: blur(30px); }
        .hover-card { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .hover-card:hover { transform: translateY(-4px); background: rgba(255, 255, 255, 0.04); border-color: rgba(168, 85, 247, 0.3); }
        .muvibes-chip { background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.05); transition: all 0.2s ease; }
        .muvibes-chip:hover { background: rgba(168, 85, 247, 0.15); border-color: rgba(168, 85, 247, 0.3); color: #fff; }
        
        .slide-up-enter { transform: translateY(100%); opacity: 0; }
        .slide-up-enter-active { transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1); transform: translateY(0); opacity: 1; }
        .slide-up-leave-active { transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1); transform: translateY(100%); opacity: 0; }
        
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-screen overflow-hidden flex flex-col justify-between selection:bg-purple-500 selection:text-white" 
      x-data="musicApp()">

    <!-- AUDIO HTML5 UTAMA -->
    <audio id="main-audio" x-on:ended="playNextInQueue()"></audio>

    <div class="flex flex-1 overflow-hidden relative">
        <!-- SIDEBAR KIRI -->
        <aside class="w-64 glass-sidebar border-r border-white/5 flex flex-col justify-between hidden md:flex shrink-0 select-none z-10">
            <div class="flex flex-col h-full overflow-hidden">
                <div class="h-20 flex items-center px-6 gap-3.5 shrink-0">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-purple-600 to-indigo-500 flex items-center justify-center text-white shadow-lg shadow-purple-600/30">
                        <i class="fa-solid fa-wave-square text-sm"></i>
                    </div>
                    <span class="text-white font-bold text-base tracking-wider">Muvibes</span>
                </div>

                <div class="px-3 py-2 space-y-1 text-xs font-medium">
                    <a href="#" class="flex items-center gap-4 px-3.5 py-3 rounded-xl text-purple-400 bg-purple-600/10 font-semibold border-l-2 border-purple-500">
                        <i class="fa-solid fa-house text-sm w-4"></i> Beranda
                    </a>
                    <a href="#" class="flex items-center gap-4 px-3.5 py-3 rounded-xl text-slate-400 hover:text-slate-200 hover:bg-white/5 transition">
                        <i class="fa-solid fa-compass text-sm w-4"></i> Eksplorasi
                    </a>
                    <a href="#" class="flex items-center gap-4 px-3.5 py-3 rounded-xl text-slate-400 hover:text-slate-200 hover:bg-white/5 transition">
                        <i class="fa-solid fa-layer-group text-sm w-4"></i> Koleksi
                    </a>
                </div>

                <hr class="border-white/5 my-3 mx-4">

                <div class="px-3 overflow-y-auto custom-scrollbar flex-1 space-y-1 text-xs">
                    <button class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-slate-400 hover:text-slate-200 hover:bg-white/5 transition text-left">
                        <i class="fa-solid fa-plus text-xs w-4"></i> Playlist baru
                    </button>
                    <div class="pt-3 pb-1.5 px-3.5 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Musik yang Disukai</div>
                    <a href="#" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-slate-300 hover:bg-white/5 transition">
                        <div class="truncate">
                            <p class="text-xs text-slate-200 font-medium truncate">Playlist otomatis</p>
                            <p class="text-[10px] text-purple-400 font-mono">12 lagu</p>
                        </div>
                        <i class="fa-solid fa-volume-high text-xs text-purple-400"></i>
                    </a>
                </div>
            </div>
        </aside>

        <!-- KONTEN UTAMA -->
        <main class="flex-1 flex flex-col overflow-hidden bg-[#050505]">
            <header class="h-20 border-b border-white/5 flex items-center justify-between px-8 shrink-0 bg-[#050505]/80 backdrop-blur-xl z-10">
                <form x-on:submit.prevent class="relative w-full max-w-xl">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-xs"></i>
                    <input type="text" x-model="searchQuery" x-on:input.debounce.400ms="searchMusic()" placeholder="Telusuri lagu, album, artis, podcast..." class="w-full bg-white/[0.03] border border-white/10 rounded-full pl-11 pr-4 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-purple-500/50 transition">
                </form>
                <div class="flex items-center gap-4">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-purple-600 to-indigo-500 flex items-center justify-center text-white text-xs font-bold shadow-md">R</div>
                </div>
            </header>

            <div class="flex-1 overflow-y-auto custom-scrollbar p-8 space-y-8">
                <!-- Filter Kategori Chips -->
                <div class="flex items-center gap-2 overflow-x-auto pb-2 custom-scrollbar">
                    <template x-for="cat in ['Senang', 'Sedih', 'Bersantai', 'Perjalanan', 'Tidur', 'Romansa', 'Mengisi energi', 'Olahraga', 'Fokus']">
                        <span x-on:click="searchQuery = cat; searchMusic()" class="muvibes-chip px-4 py-1.5 rounded-full text-xs text-slate-300 font-medium cursor-pointer shrink-0" x-text="cat"></span>
                    </template>
                </div>

                <!-- Seksi: Dengarkan Lagi -->
                <div x-show="!searchQuery.trim()">
                    <div class="text-[10px] font-bold tracking-widest text-purple-400 uppercase mb-1">MUVIBES RECOMMENDED</div>
                    <h2 class="text-2xl font-extrabold text-white mb-6">Dengarkan lagi</h2>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($trendingTracks ?? [] as $track)
                            <div class="glass p-3 rounded-2xl hover-card group cursor-pointer flex items-center justify-between" 
                                 onclick="playTrack(@js($track))">
                                <div class="flex items-center gap-3.5 overflow-hidden">
                                    <img src="{{ $track->cover }}" onerror="this.src='https://images.unsplash.com/photo-1614613535308-eb5fbd3d2c17?w=500'" class="w-14 h-14 rounded-xl object-cover shrink-0 bg-white/5">
                                    <div class="overflow-hidden">
                                        <h4 class="text-xs font-bold text-white truncate group-hover:text-purple-400 transition">{{ $track->title }}</h4>
                                        <p class="text-[10px] text-slate-400 mt-0.5 truncate">{{ $track->artist }} • 12 jt ditonton</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <!-- Tombol Tambah ke Antrean (Plus) -->
                                    <button x-on:click.stop="addToQueue(@js($track))" class="w-8 h-8 rounded-lg bg-white/5 hover:bg-purple-600/30 text-slate-400 hover:text-purple-300 flex items-center justify-center transition shrink-0" title="Tambah ke Antrean">
                                        <i class="fa-solid fa-plus text-xs"></i>
                                    </button>
                                    <div class="w-9 h-9 rounded-full bg-purple-600/20 group-hover:bg-purple-600 text-purple-300 group-hover:text-white flex items-center justify-center transition shrink-0 shadow-md">
                                        <i class="fa-solid fa-play text-xs ml-0.5"></i>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Seksi: Pilihan Cepat / Hasil Pencarian -->
                <div>
                    <div class="flex items-center justify-between mb-5">
                        <h2 class="text-sm font-bold text-white tracking-wide" x-text="searchQuery ? 'Hasil Pencarian: &quot;' + searchQuery + '&quot;' : 'Pilihan cepat'"></h2>
                        <span class="text-xs text-purple-400 font-medium"><span x-text="songs.length"></span> lagu tersedia</span>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <template x-for="song in songs" :key="song.id">
                            <div class="glass p-3 rounded-2xl hover-card group cursor-pointer flex items-center justify-between" x-on:click="playTrack(song)">
                                <div class="flex items-center gap-3.5 overflow-hidden">
                                    <img :src="song.cover" onerror="this.src='https://images.unsplash.com/photo-1614613535308-eb5fbd3d2c17?w=500'" class="w-14 h-14 rounded-xl object-cover shrink-0 bg-white/5">
                                    <div class="overflow-hidden">
                                        <h4 class="text-xs font-bold text-white truncate group-hover:text-purple-400 transition" x-text="song.title"></h4>
                                        <p class="text-[10px] text-slate-400 mt-0.5 truncate" x-text="song.artist + ' • 5.2 jt pemutaran'"></p>
                                    </div>
                                </div>
                                <button x-on:click.stop="addToQueue(song)" class="w-8 h-8 rounded-lg bg-white/5 hover:bg-purple-600/30 text-slate-400 hover:text-purple-300 flex items-center justify-center transition shrink-0" title="Tambah ke Antrean">
                                    <i class="fa-solid fa-plus text-xs"></i>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </main>

        <!-- FULLSCREEN EXPANDED PLAYER MODAL -->
        <div x-show="showExpandedPlayer" 
             x-transition:enter="slide-up-enter-active"
             x-transition:leave="slide-up-leave-active"
             class="absolute inset-0 z-50 glass-modal flex flex-col justify-between p-4 sm:p-8 overflow-y-auto custom-scrollbar" 
             x-cloak>
            
            <div class="flex items-center justify-between max-w-7xl mx-auto w-full shrink-0 py-2">
                <div class="flex items-center gap-3">
                    <span class="w-2.5 h-2.5 rounded-full bg-purple-500 animate-pulse"></span>
                    <span class="text-[10px] sm:text-xs font-bold uppercase tracking-widest text-slate-300">Daftar Putar (Murni Sesuai Pilihanmu)</span>
                </div>
                <button x-on:click="showExpandedPlayer = false" class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-white/5 hover:bg-white/10 flex items-center justify-center text-slate-300 hover:text-white transition">
                    <i class="fa-solid fa-chevron-down text-sm"></i>
                </button>
            </div>

            <div class="flex-1 grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-12 max-w-7xl mx-auto w-full items-center my-auto py-4">
                <div class="lg:col-span-6 flex flex-col items-center justify-center">
                    <div class="relative group">
                        <div class="absolute -inset-4 bg-gradient-to-r from-purple-600 to-indigo-600 rounded-3xl blur-2xl opacity-30"></div>
                        <img :src="currentSong?.cover" class="relative w-64 h-64 sm:w-80 sm:h-80 md:w-96 md:h-96 rounded-2xl object-cover shadow-2xl border border-white/10 bg-white/5">
                    </div>
                    <div class="mt-6 text-center max-w-sm px-4">
                        <h2 class="text-lg sm:text-2xl font-extrabold text-white truncate" x-text="currentSong?.title"></h2>
                        <p class="text-xs sm:text-sm text-purple-400 mt-1 font-medium truncate" x-text="currentSong?.artist"></p>
                    </div>
                </div>

                <div class="lg:col-span-6 flex flex-col h-[350px] sm:h-[450px] bg-white/[0.02] border border-white/5 rounded-3xl p-4 sm:p-6 overflow-hidden">
                    <div class="flex items-center gap-6 sm:gap-8 border-b border-white/10 pb-3 text-xs font-bold shrink-0">
                        <button class="text-white border-b-2 border-purple-500 pb-1">BERIKUTNYA</button>
                        <button class="text-slate-500 hover:text-slate-300 transition pb-1">LIRIK</button>
                        <button class="text-slate-500 hover:text-slate-300 transition pb-1">TERKAIT</button>
                    </div>

                    <div class="flex items-center justify-between py-3 shrink-0">
                        <div>
                            <p class="text-xs font-bold text-white">Putar otomatis</p>
                            <p class="text-[10px] text-slate-500">Antrean steril tanpa lagu acak</p>
                        </div>
                        <span class="w-8 h-4 rounded-full bg-purple-600 relative inline-block">
                            <span class="w-3 h-3 bg-white rounded-full absolute right-0.5 top-0.5"></span>
                        </span>
                    </div>

                    <div class="flex-1 overflow-y-auto custom-scrollbar space-y-2 pr-1">
                        <template x-for="(song, index) in activeQueueList" :key="song.id + '-' + index">
                            <div class="flex items-center justify-between p-2.5 rounded-xl transition cursor-pointer group"
                                 :class="index === 0 ? 'bg-purple-600/20 border border-purple-500/40 shadow-lg shadow-purple-500/10' : 'hover:bg-white/5 border border-transparent'"
                                 x-on:click="playTrackByIndex(index)">
                                <div class="flex items-center gap-3.5 overflow-hidden">
                                    <div class="w-6 flex items-center justify-center shrink-0">
                                        <template x-if="index === 0">
                                            <div class="flex items-end gap-0.5 h-3.5">
                                                <span class="w-1 bg-purple-400 animate-pulse h-full rounded-full"></span>
                                                <span class="w-1 bg-purple-400 animate-pulse h-2/3 rounded-full" style="animation-delay: 0.15s"></span>
                                                <span class="w-1 bg-purple-400 animate-pulse h-4/5 rounded-full" style="animation-delay: 0.3s"></span>
                                            </div>
                                        </template>
                                        <template x-if="index !== 0">
                                            <span class="text-[10px] font-mono text-slate-500" x-text="index + 1"></span>
                                        </template>
                                    </div>

                                    <img :src="song.cover" class="w-9 h-9 sm:w-10 sm:h-10 rounded-lg object-cover shrink-0 bg-white/5">
                                    
                                    <div class="overflow-hidden">
                                        <h4 class="text-xs font-bold truncate" :class="index === 0 ? 'text-purple-300 font-extrabold' : 'text-white group-hover:text-purple-300'" x-text="song.title"></h4>
                                        <p class="text-[10px] truncate" :class="index === 0 ? 'text-purple-400/80' : 'text-slate-400'" x-text="song.artist"></p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <template x-if="index === 0">
                                        <span class="text-[9px] font-bold uppercase tracking-wider text-purple-300 bg-purple-500/20 px-2 py-0.5 rounded-full border border-purple-500/30">Memutar</span>
                                    </template>
                                    <span class="text-[10px] font-mono text-slate-500" x-text="getSongDuration(song)"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- AUDIO PLAYER BAR -->
    <footer class="h-24 glass border-t border-white/5 px-8 flex items-center justify-between shrink-0 z-20" x-show="hasSelectedSong" x-cloak>
        <div class="flex items-center gap-4 w-1/4 cursor-pointer group" x-on:click="showExpandedPlayer = true" title="Perbesar Pemutar Musik">
            <img id="player-cover" src="" class="w-12 h-12 rounded-xl object-cover border border-white/10 shrink-0 group-hover:opacity-80 transition">
            <div class="overflow-hidden">
                <h4 id="player-title" class="text-xs font-bold text-white truncate group-hover:text-purple-400 transition"></h4>
                <p id="player-artist" class="text-[10px] text-purple-400 truncate"></p>
            </div>
            <i class="fa-solid fa-chevron-up text-xs text-slate-500 ml-2 group-hover:text-white transition"></i>
        </div>

        <div class="flex flex-col items-center max-w-md w-2/4 space-y-2">
            <div class="flex items-center gap-6 text-slate-400 text-xs">
                <button class="text-purple-400 hover:text-white transition" title="Antrean Stabil Aktif"><i class="fa-solid fa-shuffle"></i></button>
                <button x-on:click="playPreviousInQueue()" class="hover:text-white transition" title="Previous Song"><i class="fa-solid fa-backward-step"></i></button>
                <button x-on:click="togglePlay()" class="w-10 h-10 rounded-full bg-white hover:bg-purple-600 text-black hover:text-white flex items-center justify-center transition shadow-lg">
                    <i :class="isPlaying ? 'fa-solid fa-pause text-xs' : 'fa-solid fa-play text-xs ml-0.5'"></i>
                </button>
                <button x-on:click="playNextInQueue()" class="hover:text-white transition" title="Next Song"><i class="fa-solid fa-forward-step"></i></button>
            </div>
            <div class="w-full flex items-center gap-3 text-[10px] font-mono text-slate-400">
                <span id="current-time">0:00</span>
                <input type="range" id="progress-slider" min="0" max="100" value="0" step="0.1" oninput="seekAudio(this.value)" class="flex-1 h-1 bg-white/10 rounded-full appearance-none cursor-pointer accent-purple-500">
                <span id="total-duration">0:00</span>
            </div>
        </div>

        <div class="w-1/4 flex items-center justify-end gap-3 text-slate-400">
            <i class="fa-solid fa-volume-high text-xs"></i>
            <input type="range" min="0" max="100" value="70" oninput="changeVolume(event)" class="w-24 h-1 bg-white/10 rounded-full accent-purple-500 cursor-pointer">
        </div>
    </footer>

    <!-- SCRIPT UTAMA -->
    <script>
        const audio = document.getElementById('main-audio');
        let isSeeking = false;
        let isTransitioning = false;

        audio.addEventListener('loadedmetadata', () => {
            let alpineBody = Alpine.$data(document.body);
            if (alpineBody && alpineBody.currentSong && !isNaN(audio.duration)) {
                alpineBody.currentSong.duration = Math.floor(audio.duration);
            }
        });

        function musicApp() {
            return {
                isPlaying: false,
                hasSelectedSong: false,
                showExpandedPlayer: false, 
                searchQuery: '',
                songs: @js($newReleases ?? []),
                queue: @js($trendingTracks ?? []),
                currentSong: null,
                masterQueue: [], // DIUBAH: Kosong di awal agar antrean steril dan murni sesuai klik/tambah manual
                currentIndex: 0,  

                init() {
                    // Kosongkan masterQueue saat pertama kali dimuat
                    this.masterQueue = [];
                },

                get activeQueueList() {
                    if (this.masterQueue.length === 0) return [];
                    return [
                        ...this.masterQueue.slice(this.currentIndex),
                        ...this.masterQueue.slice(0, this.currentIndex)
                    ];
                },

                getSongDuration(song) {
                    if (song.duration) {
                        return formatTime(song.duration);
                    }
                    if (song.trackTimeMillis) {
                        return formatTime(Math.floor(song.trackTimeMillis / 1000));
                    }
                    if (this.currentSong && this.currentSong.id === song.id && !isNaN(audio.duration) && audio.duration > 0) {
                        return formatTime(audio.duration);
                    }
                    let seed = song.id ? song.id.toString() : 'muvibes';
                    let hash = 0;
                    for (let i = 0; i < seed.length; i++) {
                        hash = seed.charCodeAt(i) + ((hash << 5) - hash);
                    }
                    let randomSeconds = 170 + Math.abs(hash) % 75;
                    return formatTime(randomSeconds);
                },

                searchMusic() {
                    if (!this.searchQuery.trim()) return;
                    fetch(`/search-ajax?q=${encodeURIComponent(this.searchQuery)}`)
                        .then(r => r.json())
                        .then(data => {
                            this.songs = data;
                        })
                        .catch(err => console.error("Gagal melakukan pencarian:", err));
                },

                addToQueue(song) {
                    if (!this.masterQueue.some(s => s.id === song.id)) {
                        this.masterQueue.push(song);
                    }
                }
            }
        }

        async function playTrack(song) {
            let alpineBody = Alpine.$data(document.body);
            if (!alpineBody || isTransitioning) return;

            isTransitioning = true;

            audio.pause();
            audio.currentTime = 0;
            audio.src = "";
            audio.load();

            alpineBody.hasSelectedSong = true;
            alpineBody.currentSong = song;

            // Masukkan lagu ke antrean jika belum ada, lalu set index ke lagu tersebut
            let existingIndex = alpineBody.masterQueue.findIndex(s => s.id === song.id);
            if (existingIndex !== -1) {
                alpineBody.currentIndex = existingIndex;
            } else {
                alpineBody.masterQueue.push(song);
                alpineBody.currentIndex = alpineBody.masterQueue.length - 1;
            }

            document.getElementById('player-title').innerText = song.title;
            document.getElementById('player-artist').innerText = "Memuat Audio...";
            document.getElementById('player-cover').src = song.cover;

            try {
                let res = await fetch(`/get-direct-stream?title=${encodeURIComponent(song.title)}&artist=${encodeURIComponent(song.artist)}`);
                let data = await res.json();

                if (data.status === 'success' && data.url) {
                    audio.src = data.url;
                    audio.load();
                    
                    await audio.play();

                    alpineBody.isPlaying = true;
                    document.getElementById('player-title').innerText = song.title;
                    document.getElementById('player-artist').innerText = song.artist + " • Memutar";
                    document.getElementById('player-cover').src = song.cover;
                } else {
                    document.getElementById('player-artist').innerText = song.artist + " • Gagal Memuat Audio";
                }
            } catch (err) {
                console.error("Gagal memutar lagu:", err);
                document.getElementById('player-artist').innerText = "Gangguan Jaringan Server";
            } finally {
                isTransitioning = false;
            }
        }

        function playTrackByIndex(rotatedIndex) {
            let alpineBody = Alpine.$data(document.body);
            if (!alpineBody) return;
            let actualIndex = (alpineBody.currentIndex + rotatedIndex) % alpineBody.masterQueue.length;
            let targetSong = alpineBody.masterQueue[actualIndex];
            if (targetSong) {
                playTrack(targetSong);
            }
        }

        function playNextInQueue() {
            let alpineBody = Alpine.$data(document.body);
            if (!alpineBody || alpineBody.masterQueue.length === 0 || isTransitioning) return;

            alpineBody.currentIndex = (alpineBody.currentIndex + 1) % alpineBody.masterQueue.length;
            let nextSong = alpineBody.masterQueue[alpineBody.currentIndex];
            if (nextSong) {
                playTrack(nextSong);
            }
        }

        function playPreviousInQueue() {
            let alpineBody = Alpine.$data(document.body);
            if (!alpineBody || alpineBody.masterQueue.length === 0 || isTransitioning) return;

            alpineBody.currentIndex = (alpineBody.currentIndex - 1 + alpineBody.masterQueue.length) % alpineBody.masterQueue.length;
            let prevSong = alpineBody.masterQueue[alpineBody.currentIndex];
            if (prevSong) {
                playTrack(prevSong);
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
            let alpineBody = Alpine.$data(document.body);
            if (alpineBody) alpineBody.isPlaying = false;
            playNextInQueue();
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
            let alpineBody = Alpine.$data(document.body);
            if (!alpineBody) return;

            if (audio.paused) {
                audio.play();
                alpineBody.isPlaying = true;
            } else {
                audio.pause();
                alpineBody.isPlaying = false;
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