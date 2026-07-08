<x-layouts.admin title="Manage Kategori — SI-Pedia" section="categories">
    <main class="mx-auto max-w-[1440px] px-8 py-8">
        <div class="flex items-start justify-between">
            <div>
                <h1 class="page-title">Manage Category</h1>
                <p class="page-subtitle">Manage article categories.</p>
            </div>
            <a href="{{ route('admin.panel') }}" class="rounded-lg bg-gray-600 px-5 py-2.5 text-sm font-bold text-white shadow hover:bg-gray-700">Back to Panel</a>
        </div>

        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Add Form -->
            <div class="card">
                <div class="card-header">📂 Add Category</div>
                <div class="card-body">
                    <form action="{{ route('admin.categories.store') }}" method="POST" data-validate>
                        @csrf
                        <div class="mb-4">
                            <label class="form-label">Category Name</label>
                            <input type="text" name="name" required class="form-input">
                        </div>
                        <button type="submit" class="btn btn-primary w-full justify-center">Add</button>
                    </form>
                </div>
            </div>

            <!-- List -->
            <div class="md:col-span-2 space-y-3">
                @foreach($categories as $category)
                <div class="card flex items-center justify-between px-4 py-3">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900">{{ $category->name }}</h3>
                        <p class="text-xs text-gray-500">{{ $category->articles_count ?? 0 }} articles</p>
                    </div>
                    <div class="flex gap-2">
                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Hapus kategori ini? Artikel terkait akan kehilangan kategorinya.');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </main>
</x-layouts.admin>
