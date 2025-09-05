<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">
            仕様編集（{{ $project->name }}）：{{ $specification->title }}
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto py-6 px-4">
        {{-- 変更は新バージョン＋CRを起票するupdate()に送る --}}
        <form method="POST"
              action="{{ route('specifications.update', ['specification' => $specification->id, 'project' => $project->id]) }}"
              enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            {{-- 表示用：コード（編集しないなら disabled） --}}
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">コード</label>
                <input type="text" value="{{ $specification->code }}" class="w-full border rounded px-3 py-2 bg-gray-50" disabled>
            </div>

            {{-- タイトル（updateで必須バリデーションあり） --}}
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">タイトル</label>
                <input type="text" name="title" class="w-full border rounded px-3 py-2"
                       value="{{ old('title', $specification->title) }}" required>
                @error('title')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- 本文（Markdown）現行版を初期表示 --}}
            <div class="mb-6">
                <label class="block text-sm font-medium mb-1">本文 (Markdown)</label>
                <textarea name="body_md" rows="12" class="w-full border rounded px-3 py-2" required>{{ old('body_md', $ver->body_md ?? '') }}</textarea>
                @error('body_md')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- 追加属性（必要なら）--}}
            {{-- <input type="hidden" name="attributes[foo]" value="bar"> --}}

            {{-- 変更理由・影響（update()で必須/任意にしている項目） --}}
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">変更理由</label>
                <input type="text" name="reason" class="w-full border rounded px-3 py-2"
                       value="{{ old('reason') }}" required>
                @error('reason')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium mb-1">影響範囲（任意）</label>
                <textarea name="impact" rows="3" class="w-full border rounded px-3 py-2">{{ old('impact') }}</textarea>
                @error('impact')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- （任意）編集時にも画像追加したい場合 --}}
            <details class="mb-6">
                <summary class="cursor-pointer select-none text-sm text-gray-700">画像を追加アップロード（任意）</summary>
                <div class="mt-3">
                    <input type="file" name="images[]" multiple accept="image/*"
                           class="block w-full text-sm text-gray-700 file:mr-3 file:py-2 file:px-3 file:border file:rounded file:border-gray-300 file:bg-white hover:file:bg-gray-50">
                    <label class="inline-flex items-center gap-2 mt-2">
                        <input type="checkbox" name="append_images" value="1" class="rounded border-gray-300" checked>
                        <span class="text-sm">アップロードした画像を本文末尾に自動挿入する</span>
                    </label>
                </div>
            </details>

            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">保存（新バージョン作成）</button>
                <a href="{{ route('specifications.show', ['specification' => $specification->id, 'project' => $project->id]) }}"
                   class="px-4 py-2 border rounded">戻る</a>
            </div>
        </form>
    </div>
</x-app-layout>
