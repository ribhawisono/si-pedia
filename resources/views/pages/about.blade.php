<x-layouts.app title="Tentang Kami — SI-Pedia" active="About us" footer="full"
    meta_description="Tentang Program Studi Sistem Informasi Universitas Indraprasta PGRI — Sejarah, Visi, Misi, dan Dosen.">

<div class="bg-white dark:bg-gray-950">

    {{-- ============================================ --}}
    {{-- HERO: Profil Prodi                          --}}
    {{-- ============================================ --}}
    <section class="max-w-5xl mx-auto px-6 pt-16 pb-12">
        <div class="mb-4">
            <span class="inline-block rounded-full bg-brand-600/10 px-4 py-1.5 text-sm font-semibold text-brand-600 dark:text-brand-400 tracking-wide">
                TENTANG KAMI
            </span>
        </div>

        <h1 class="text-3xl sm:text-4xl lg:text-[2.75rem] font-extrabold text-gray-900 dark:text-white leading-tight mb-6">
            Profil Program Studi<br class="hidden sm:block">
            Sistem Informasi
        </h1>

        <div class="max-w-3xl">
            <p class="text-base sm:text-lg text-gray-600 leading-relaxed">
                Program Studi Sistem Informasi Universitas Indraprasta PGRI merupakan program studi baru yang berdiri pada tahun 2023 berdasarkan Keputusan Menteri Pendidikan, Kebudayaan, Riset, dan Teknologi Nomor: 411/E/O/2023 tanggal 16 Mei 2023. Program studi ini berfokus pada pengembangan keilmuan di bidang Business Intelligence dan Artificial Intelligence (AI) untuk menghasilkan lulusan yang kompeten dan siap bersaing di era digital.
            </p>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SEJARAH                                      --}}
    {{-- ============================================ --}}
    <section class="bg-gray-50 border-t border-gray-100">
        <div class="max-w-5xl mx-auto px-6 py-16">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-1.5 h-8 rounded-full bg-brand-600"></div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Sejarah</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pl-6">
                <div>
                    <div class="flex items-start gap-4 mb-6">
                        <div class="flex-shrink-0 w-12 h-12 rounded-full bg-brand-600 text-white font-bold text-sm flex items-center justify-center">2023</div>
                        <div>
                            <h3 class="font-bold text-gray-900 dark:text-white mb-1">Pendirian Program Studi</h3>
                            <p class="text-sm text-gray-600 leading-relaxed">Program Studi Sistem Informasi resmi dibuka berdasarkan SK Mendikbudristek Nomor 411/E/O/2023 tanggal 16 Mei 2023 di Universitas Indraprasta PGRI Jakarta.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 mb-6">
                        <div class="flex-shrink-0 w-12 h-12 rounded-full bg-brand-600 text-white font-bold text-sm flex items-center justify-center">2023</div>
                        <div>
                            <h3 class="font-bold text-gray-900 dark:text-white mb-1">Akreditasi Minimum Terpenuhi</h3>
                            <p class="text-sm text-gray-600 leading-relaxed">Berdasarkan keputusan tersebut, program studi dinyatakan telah memenuhi persyaratan akreditasi minimum untuk penyelenggaraan pendidikan tinggi.</p>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="flex items-start gap-4 mb-6">
                        <div class="flex-shrink-0 w-12 h-12 rounded-full bg-brand-600 text-white font-bold text-sm flex items-center justify-center">2024</div>
                        <div>
                            <h3 class="font-bold text-gray-900 dark:text-white mb-1">Pengembangan Kurikulum</h3>
                            <p class="text-sm text-gray-600 leading-relaxed">Kurikulum dikembangkan dengan fokus pada Business Intelligence dan Artificial Intelligence (AI) untuk menjawab kebutuhan industri digital.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-12 h-12 rounded-full bg-brand-600 text-white font-bold text-sm flex items-center justify-center">2025</div>
                        <div>
                            <h3 class="font-bold text-gray-900 dark:text-white mb-1">Pertumbuhan & Kolaborasi</h3>
                            <p class="text-sm text-gray-600 leading-relaxed">Menjalin kerjasama dengan berbagai pihak untuk pengabdian masyarakat, penelitian, dan peningkatan kualitas lulusan.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- PIMPINAN PROGRAM STUDI (jabatan struktural)   --}}
    {{-- ============================================ --}}
    <section class="max-w-5xl mx-auto px-6 py-16">
        <div class="flex items-center gap-3 mb-8">
            <div class="w-1.5 h-8 rounded-full bg-brand-600"></div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Pimpinan Program Studi</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

            {{-- Card 1 --}}
            <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#336cbc] to-[#1e4f8f] p-8 sm:p-10 flex flex-col items-center justify-center min-h-[280px] shadow-lg hover:shadow-xl transition-shadow duration-300">
                <div class="absolute -top-10 -right-10 w-40 h-40 rounded-full bg-white/5"></div>
                <div class="absolute -bottom-6 -left-6 w-24 h-24 rounded-full bg-white/5"></div>

                <div class="w-24 h-24 rounded-full overflow-hidden mb-5 ring-4 ring-white/30 bg-white/20">
                    <img src="{{ route('images.lecturers.zaimatun') }}?v=2" alt="Foto Dr. Za'imatun Niswati" class="w-full h-full object-cover">
                </div>

                <h3 class="text-lg sm:text-xl font-bold text-white text-center leading-snug">Dr. Za'imatun Niswati, S.Pt., M.Pd., S.Kom., M.Kom.</h3>
                <p class="mt-2 text-sm text-white/70 font-medium">Ketua Program Studi</p>
            </div>

            {{-- Card 2 --}}
            <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#336cbc] to-[#1e4f8f] p-8 sm:p-10 flex flex-col items-center justify-center min-h-[280px] shadow-lg hover:shadow-xl transition-shadow duration-300">
                <div class="absolute -top-10 -right-10 w-40 h-40 rounded-full bg-white/5"></div>
                <div class="absolute -bottom-6 -left-6 w-24 h-24 rounded-full bg-white/5"></div>

                <div class="w-24 h-24 rounded-full overflow-hidden mb-5 ring-4 ring-white/30 bg-white/20">
                    <img src="{{ route('images.lecturers.dwi') }}?v=2" alt="Foto Dr. Dwi Marlina" class="w-full h-full object-cover">
                </div>

                <h3 class="text-lg sm:text-xl font-bold text-white text-center leading-snug">Dr. Dwi Marlina, S.Kom., M.Kom.</h3>
                <p class="mt-2 text-sm text-white/70 font-medium">Sekretaris Program Studi</p>
            </div>

        </div>
    </section>

    {{-- ============================================ --}}
    {{-- VISI & MISI                                  --}}
    {{-- ============================================ --}}
    <section class="bg-gray-50 border-t border-gray-100">
        <div class="max-w-5xl mx-auto px-6 py-16">

            {{-- VISI --}}
            <div class="mb-12">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-1.5 h-8 rounded-full bg-brand-600"></div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">VISI</h2>
                </div>
                <p class="text-base sm:text-lg text-gray-600 leading-relaxed pl-6 max-w-3xl">
                    Mengembangkan keilmuan Sistem Informasi yang unggul di bidang <strong class="text-gray-800">Business Intelligence</strong> dan <strong class="text-gray-800">Artificial Intelligence (AI)</strong> yang berlandaskan pada peduli, mandiri, kreatif, dan adaptif.
                </p>
            </div>

            {{-- MISI --}}
            <div>
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-1.5 h-8 rounded-full bg-brand-600"></div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">MISI</h2>
                </div>

                <div class="space-y-5 pl-6">
                    <div class="flex gap-4 items-start">
                        <span class="flex-shrink-0 w-9 h-9 rounded-lg bg-brand-600 text-white font-bold text-sm flex items-center justify-center shadow-sm">1</span>
                        <p class="text-base text-gray-600 leading-relaxed pt-1">
                            Menyelenggarakan pendidikan dan pengajaran yang profesional di bidang Sistem Informasi dan Bisnis Intelligence.
                        </p>
                    </div>

                    <div class="flex gap-4 items-start">
                        <span class="flex-shrink-0 w-9 h-9 rounded-lg bg-brand-600 text-white font-bold text-sm flex items-center justify-center shadow-sm">2</span>
                        <p class="text-base text-gray-600 leading-relaxed pt-1">
                            Melaksanakan kegiatan penelitian dan kajian inovatif dalam pengembangan Sistem Informasi.
                        </p>
                    </div>

                    <div class="flex gap-4 items-start">
                        <span class="flex-shrink-0 w-9 h-9 rounded-lg bg-brand-600 text-white font-bold text-sm flex items-center justify-center shadow-sm">3</span>
                        <p class="text-base text-gray-600 leading-relaxed pt-1">
                            Melaksanakan kegiatan pengabdian kepada Masyarakat dan kerjasama di bidang Sistem Informasi yang dapat memenuhi kepentingan masyarakat (stakeholders).
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </section>

</div>
</x-layouts.app>
