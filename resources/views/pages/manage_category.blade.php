<x-layouts.admin title="Manage Kategori — SI-Pedia" section="categories">
    <main class="mx-auto max-w-[1440px] px-8 py-8">
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-5xl font-black tracking-tight">Manage Category</h1>
                <p class="mt-1 text-gray-700">Manage article categories.</p>
            </div>
            <a href="{{ route('admin.panel') }}" class="rounded-lg bg-gray-600 px-5 py-2.5 text-sm font-bold text-white shadow hover:bg-gray-700">Back to Panel</a>
        </div>

        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Add Form -->
            <div class="bg-white p-6 rounded-2xl shadow-[0_2px_10px_rgba(0,0,0,0.06)] border border-gray-100">
                <h2 class="text-xl font-bold mb-4">Add Category</h2>
                <form action="{{ route('admin.categories.store') }}" method="POST" data-validate>
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-bold mb-2">Category Name</label>
                        <input type="text" name="name" required class="w-full rounded-xl border-2 border-gray-200 px-4 py-2 text-sm font-semibold text-gray-800 focus:border-brand-600 focus:ring-0">
                    </div>
                    <button type="submit" class="w-full rounded-lg bg-brand-600 px-5 py-2.5 text-sm font-bold text-white shadow hover:bg-brand-700">Add</button>
                </form>
            </div>

            <!-- List -->
            <div class="md:col-span-2 space-y-4">
                @foreach($categories as $category)
                <div class="flex items-center justify-between bg-white p-4 rounded-xl shadow-[0_2px_10px_rgba(0,0,0,0.06)] border border-gray-100">
                    <div>
                        <h3 class="font-bold text-lg">{{ $category->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $category->articles_count ?? 0 }} articles</p>
                    </div>
                    <div class="flex gap-2">
                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Hapus kategori ini? Artikel terkait akan kehilangan kategorinya.');">
                            @csrf @method('DELETE')
                            <button type="submit" class="rounded-md bg-red-500 px-3 py-1.5 text-xs font-bold text-white hover:bg-red-600">Delete</button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </main>
</x-layouts.admin>
