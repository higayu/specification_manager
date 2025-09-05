<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">
            仕様追加（{{ $project->name }}）
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto py-6 px-4">
        {{-- 画像をアップロードするので enctype を必ず付与 --}}
        <form method="POST" action="{{ route('specifications.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="project_id" value="{{ $project->id }}">

            {{-- コード --}}
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">コード</label>
                <input type="text" name="code" class="w-full border rounded px-3 py-2"
                       value="{{ old('code') }}" required>
                @error('code')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- タイトル --}}
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">タイトル</label>
                <input type="text" name="title" class="w-full border rounded px-3 py-2"
                       value="{{ old('title') }}" required>
                @error('title')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- 本文（Markdown） --}}
            <div class="mb-6">
                <label class="block text-sm font-medium mb-1">本文 (Markdown)</label>
                <textarea id="body_md" name="body_md" rows="12" class="w-full border rounded px-3 py-2" required>{{ old('body_md') }}</textarea>
                @error('body_md')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                <p class="text-xs text-gray-500 mt-1">見出し: <code># タイトル</code>、画像: <code>![説明](/storage/...)</code></p>
            </div>

            {{-- 画像アップロード --}}
            <div class="mb-6">
                <label class="block text-sm font-medium mb-1">画像アップロード（複数可）</label>
                <input id="images" type="file" name="images[]" accept="image/*" multiple
                       class="block w-full text-sm text-gray-700 file:mr-3 file:py-2 file:px-3 file:border file:rounded file:border-gray-300 file:bg-white hover:file:bg-gray-50" />
                @error('images')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                @error('images.*')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror>

                {{-- 本文への自動追記オプション --}}
                <label class="inline-flex items-center gap-2 mt-3">
                    <input type="checkbox" name="append_images" value="1" class="rounded border-gray-300" checked>
                    <span class="text-sm">アップロードした画像を本文末尾に Markdown で自動挿入する</span>
                </label>

                {{-- 選択プレビュー --}}
                <div id="preview" class="mt-3 grid grid-cols-2 sm:grid-cols-3 gap-3"></div>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">保存</button>
                <a href="{{ route('specifications.index', ['project' => $project->id]) }}"
                   class="px-4 py-2 border rounded">戻る</a>
            </div>
        </form>
    </div>

    <script>
      // かんたんプレビュー
      document.getElementById('images')?.addEventListener('change', (e) => {
        const wrap = document.getElementById('preview');
        if (!wrap) return;
        wrap.innerHTML = '';
        [...e.target.files].forEach(file => {
          const url = URL.createObjectURL(file);
          const fig = document.createElement('figure');
          fig.className = 'border rounded p-2';
          fig.innerHTML = `
            <img src="${url}" alt="${file.name}" class="max-w-full h-32 object-contain mx-auto">
            <figcaption class="mt-1 text-xs text-gray-600 truncate">${file.name}</figcaption>
          `;
          wrap.appendChild(fig);
        });
      });
    </script>
</x-app-layout>
