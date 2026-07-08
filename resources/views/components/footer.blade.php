<footer class="bg-ink-900 text-white">
    <div class="mx-auto grid max-w-[1440px] grid-cols-1 gap-8 px-12 py-8 md:grid-cols-[1.4fr_1fr_1fr_1.3fr]">
        <div>
            <div class="flex items-center gap-3"><x-cap class="h-8 w-8" /><span class="text-2xl font-extrabold">SI-Pedia</span></div>
            <p class="mt-1 text-base font-bold">Universitas Indraprasta PGRI</p>
            <p class="mt-3 max-w-xs text-sm leading-relaxed text-white/70">SI-Pedia is a digital platform for exploring and managing academic and creative resources easily and efficiently.</p>
            <div class="mt-4 flex gap-3">
                <a href="#" class="grid h-8 w-8 place-items-center rounded-full bg-white/10"><svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M13 10h3l1-4h-4V4c0-1 0-2 2-2h2V0h-3c-3 0-4 2-4 4v2H7v4h2v10h4V10z"/></svg></a>
                <a href="#" class="grid h-8 w-8 place-items-center rounded-full bg-white/10"><svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2c2.7 0 3 0 4.1.1 2.7.1 4 1.4 4.1 4.1.1 1.1.1 1.4.1 4.1s0 3-.1 4.1c-.1 2.7-1.4 4-4.1 4.1-1.1.1-1.4.1-4.1.1s-3 0-4.1-.1c-2.7-.1-4-1.4-4.1-4.1C3.7 13.4 3.7 13 3.7 10s0-3 .1-4.1C4 3.2 5.3 1.9 8 1.8 9.1 1.7 9.4 1.7 12 1.7zM12 6.4a5.6 5.6 0 100 11.2 5.6 5.6 0 000-11.2zm0 9.2a3.6 3.6 0 110-7.2 3.6 3.6 0 010 7.2zm5.8-9.4a1.3 1.3 0 11-2.6 0 1.3 1.3 0 012.6 0z"/></svg></a>
                <a href="#" class="grid h-8 w-8 place-items-center rounded-full bg-white/10"><svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23 7.5a3 3 0 00-2.1-2.1C19 5 12 5 12 5s-7 0-8.9.4A3 3 0 001 7.5 31 31 0 00.6 12 31 31 0 001 16.5a3 3 0 002.1 2.1C5 19 12 19 12 19s7 0 8.9-.4a3 3 0 002.1-2.1A31 31 0 0023.4 12 31 31 0 0023 7.5zM9.8 15.3V8.7l5.7 3.3-5.7 3.3z"/></svg></a>
            </div>
        </div>
        <div>
            <h4 class="mb-3 text-base font-bold">Explore</h4>
            <ul class="space-y-1.5 text-sm text-white/80">
                <li><a href="{{ route('home') }}">Homepage</a></li>
                <li><a href="{{ route('about') }}">About us</a></li>
                <li><a href="{{ route('review.index') }}">Review</a></li>
                <li><a href="{{ route('catalog') }}">Catalog</a></li>
            </ul>
        </div>
        <div>
            <h4 class="mb-3 text-base font-bold">Support</h4>
            <ul class="space-y-1.5 text-sm text-white/80"><li><a href="{{ route('faq') }}">Help Center</a></li><li><a href="{{ route('contact') }}">Contact Us</a></li><li><a href="{{ route('terms') }}">Terms of Service</a></li><li><a href="{{ route('privacy') }}">Privacy Policy</a></li></ul>
        </div>
        <div>
            <h4 class="mb-3 text-base font-bold">Contact</h4>
            <ul class="space-y-2 text-sm text-white/80">
                <li class="flex gap-3"><span>📍</span>Jl. Nangka No 58 Tanjung Barat,<br>Jakarta Selatan, 12530.</li>
                <li class="flex gap-3"><span>📞</span>(021) 7818718</li>
                <li class="flex gap-3"><span>✉</span>kampus@unindra.ac.id</li>
            </ul>
        </div>
    </div>
    <p class="border-t border-white/10 py-3 text-center text-xs text-white/60">@2024 SI-Pedia Universitas Indraprasta PGRI. All rights reserved.</p>
</footer>
