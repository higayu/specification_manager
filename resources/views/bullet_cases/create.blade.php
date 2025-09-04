<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">テキスト取り込み：{{ $project->name }}</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <form method="POST" action="{{ route('bullet-cases.store', ['project' => $project]) }}" class="space-y-4">
            @csrf

            {{-- 取り込みモード --}}
            <div>
                <label class="block text-sm font-medium mb-1">取り込みモード</label>
                <div class="flex gap-6 items-center">
                    <label class="inline-flex items-center">
                        <input type="radio" name="mode" value="single" {{ old('mode','single')==='single'?'checked':'' }}>
                        <span class="ml-2">１つずつ（このテキスト＝1テーブル）</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="mode" value="bulk" {{ old('mode')==='bulk'?'checked':'' }}>
                        <span class="ml-2">一括実行（見出しごとに複数テーブルを生成）</span>
                    </label>
                </div>
                @error('mode')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>

            {{-- グループタイトル（single時のみ表示） --}}
            <div id="title-block" class="{{ old('mode','single')==='bulk' ? 'hidden' : '' }}">
                <label class="block text-sm font-medium mb-1">グループタイトル</label>
                <input type="text" name="title" value="{{ old('title') }}"
                       class="w-full border rounded px-3 py-2" maxlength="255" placeholder="例）1. 初期表示">
                <p class="text-xs text-gray-500 mt-1">
                    「１つずつ」モードのみ対象。未入力の場合はテキスト先頭の見出し（# ...）か「インポート」を自動採用します。
                    「一括実行」モードでは各見出しがテーブルタイトルになります。
                </p>
                @error('title')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>

            {{-- 取り込みテキスト --}}
            <div>
                <label class="block text-sm font-medium mb-1">取り込みテキスト</label>
                <textarea name="text" rows="12" required
                          class="w-full border rounded px-3 py-2"
                          placeholder="# 1. 初期表示
- TC1-1 | 初期表示 | 条件 | 期待結果
# 2. 再計算の実行時の判定処理
- TC2-1 | 必須入力チェック | 条件 | 期待結果">{{ old('text') }}</textarea>
                <p class="text-xs text-gray-500 mt-1">
                    行形式：<code>- No | 機能 | 入力条件 | 期待結果</code>（<code>|</code>区切り）。
                    期待結果はHTML可（例：<code>&lt;div class=&quot;aka&quot;&gt;…&lt;/div&gt;</code>）。
                </p>
                @error('text')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 border border-black rounded bg-white text-black">取り込み</button>
                <a href="{{ route('bullet-cases.index', ['project' => $project]) }}" class="px-4 py-2 border border-black bg-white rounded">戻る</a>
            </div>
        </form>
    </div>

    {{-- タイトル自動補完 & モード連動で表示/非表示切替 --}}
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const $text       = document.querySelector('textarea[name="text"]');
        const $title      = document.querySelector('input[name="title"]');
        const $titleBlock = document.getElementById('title-block');
        const $modes      = document.querySelectorAll('input[name="mode"]');

        const setTitleBlockVisible = (visible) => {
          if (!$titleBlock) return;
          // Tailwind の hidden クラス + 非Tailwind環境の保険で display を直接制御
          $titleBlock.classList.toggle('hidden', !visible);
          $titleBlock.style.display = visible ? '' : 'none';
          if ($title) $title.disabled = !visible;
        };

        const fillTitleIfEmpty = () => {
          if (!$title || !$text) return;
          if ($title.value.trim() !== '') return;
          const val = ($text.value || '').replace(/\r\n|\r|rn/g, '\n');
          const m = val.match(/^\s*#\s*(.+)$/m); // 先頭の # 見出し
          if (m) $title.value = m[1].trim().slice(0, 255);
        };

        const sync = () => {
          const mode = document.querySelector('input[name="mode"]:checked')?.value || 'single';
          const single = (mode === 'single');
          setTitleBlockVisible(single);
          if (single) fillTitleIfEmpty();
        };

        $modes.forEach(r => r.addEventListener('change', sync));
        $text?.addEventListener('input', () => {
          const mode = document.querySelector('input[name="mode"]:checked')?.value || 'single';
          if (mode === 'single') fillTitleIfEmpty();
        });

        sync(); // 初期表示
      });
    </script>
</x-app-layout>
