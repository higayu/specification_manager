<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">
            仕様追加（{{ $project->name }}）
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto py-6 px-4">
        <form method="POST" action="{{ route('specifications.store') }}">
            @csrf
            <input type="hidden" name="project_id" value="{{ $project->id }}">

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">コード</label>
                <input type="text" name="code" class="w-full border rounded px-3 py-2"
                       value="{{ old('code') }}" required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">タイトル</label>
                <input type="text" name="title" class="w-full border rounded px-3 py-2"
                       value="{{ old('title') }}" required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">本文 (Markdown)</label>
                <textarea name="body_md" rows="10" class="w-full border rounded px-3 py-2" required>{{ old('body_md') }}</textarea>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">保存</button>
                <a href="{{ route('specifications.index', ['project' => $project->id]) }}"
                   class="px-4 py-2 border rounded">戻る</a>
            </div>
        </form>
    </div>
</x-app-layout>
