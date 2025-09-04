<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">テキスト取り込み：{{ $project->name }}</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <form method="POST" action="{{ route('bullet-cases.store', ['project' => $project]) }}" class="space-y-4">
            @csrf

            {{-- 追加：グループタイトル --}}
            <div>
                <label class="block text-sm font-medium mb-1">グループタイトル</label>
                <input type="text" name="title" value="{{ old('title') }}"
                       class="w-full border rounded px-3 py-2" maxlength="255" placeholder="例）1. 初期表示">
                <p class="text-xs text-gray-500 mt-1">未入力の場合はテキスト先頭の見出し（# ...）か「インポート」を自動採用します。</p>
                @error('title')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">取り込みテキスト</label>
                <textarea name="text" rows="12" required
                          class="w-full border rounded px-3 py-2">{{ old('text') }}</textarea>
                @error('text')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 border rounded bg-gray-900 text-white">取り込み</button>
                <a href="{{ route('bullet-cases.index', ['project' => $project]) }}" class="px-4 py-2 border rounded">戻る</a>
            </div>
        </form>
    </div>

    {{-- 便利機能：テキストの先頭「# 見出し」から自動補完（タイトル未入力時だけ） --}}
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const $text  = document.querySelector('textarea[name="text"]');
        const $title = document.querySelector('input[name="title"]');
        if (!$text || !$title) return;
        const fill = () => {
          if ($title.value.trim() !== '') return;
          const val = $text.value.replace(/\r\n|\r|rn/g, '\n');
          const m = val.match(/^\s*#\s*(.+)$/m);
          if (m) $title.value = m[1].trim().slice(0, 255);
        };
        $text.addEventListener('input', fill);
        fill(); // 初期表示時にも一回
      });
    </script>
</x-app-layout>
