<x-layouts.app title="Contact Us — SI-Pedia" active="Contact" footer="full">

<section class="bg-ink-900 py-16">
  <div class="mx-auto max-w-[1100px] px-8 text-center">
    <span class="inline-block rounded-full bg-brand-600/15 px-4 py-1.5 text-sm font-semibold text-brand-400 mb-4">HUBUNGI KAMI</span>
    <h1 class="text-2xl sm:text-3xl font-extrabold text-white">Contact Us</h1>
    <p class="mt-4 text-lg text-white/60 max-w-2xl mx-auto">
      Ada pertanyaan, masukan, atau ingin kolaborasi? Tim SI-Pedia siap membantu.
    </p>
  </div>
</section>

<section class="bg-white py-16">
  <div class="mx-auto grid max-w-[900px] gap-6 px-8 sm:grid-cols-2">
    <div class="rounded-2xl border border-gray-200 p-6 shadow-sm">
      <div class="grid h-11 w-11 place-items-center rounded-full bg-brand-600/10 text-lg">📍</div>
      <h2 class="mt-4 font-bold text-gray-900">Alamat</h2>
      <p class="mt-1 text-sm leading-relaxed text-gray-600">Jl. Nangka No 58 Tanjung Barat,<br>Jakarta Selatan, 12530.</p>
    </div>
    <div class="rounded-2xl border border-gray-200 p-6 shadow-sm">
      <div class="grid h-11 w-11 place-items-center rounded-full bg-brand-600/10 text-lg">📞</div>
      <h2 class="mt-4 font-bold text-gray-900">Telepon</h2>
      <p class="mt-1 text-sm leading-relaxed text-gray-600">(021) 7818718</p>
    </div>
    <div class="rounded-2xl border border-gray-200 p-6 shadow-sm sm:col-span-2">
      <div class="grid h-11 w-11 place-items-center rounded-full bg-brand-600/10 text-lg">✉</div>
      <h2 class="mt-4 font-bold text-gray-900">Email</h2>
      <p class="mt-1 text-sm leading-relaxed text-gray-600">kampus@unindra.ac.id</p>
    </div>
  </div>

  <div class="mx-auto mt-8 max-w-[900px] px-8">
    <div class="rounded-2xl bg-ink-900 p-8 text-center">
      <p class="text-lg font-bold text-white">Butuh bantuan lebih lanjut?</p>
      <p class="mt-2 text-sm text-white/60">Kunjungi Help Center kami untuk jawaban cepat atas pertanyaan umum.</p>
      <div class="mt-5 flex justify-center gap-4">
        <a href="{{ route('faq') }}" class="rounded-lg border border-white/20 px-6 py-2.5 text-sm font-semibold text-white hover:bg-white/10 transition">
          Help Center
        </a>
      </div>
    </div>
  </div>
</section>

</x-layouts.app>
