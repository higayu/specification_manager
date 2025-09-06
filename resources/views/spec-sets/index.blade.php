{{-- resources/views/spec-sets/index.blade.php --}}
<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl">仕様書セット一覧</h2>
  </x-slot>

  <div class="max-w-5xl mx-auto py-6 px-4 space-y-6">
    @if (session('status'))
      <div class="p-3 rounded bg-green-50 border border-green-200 text-green-800">
        {{ session('status') }}
      </div>
    @endif

    <form action="{{ route('specsets.upload') }}" method="POST" enctype="multipart/form-data" class="space-x-2">
      @csrf
      <input type="file" name="zip" accept=".zip" class="border p-2" required>
      <input type="text" name="set_name" placeholder="セット名（任意）" class="border p-2">
      <button class="px-4 py-2 bg-indigo-600 text-white rounded">アップロード</button>
    </form>

    <ul class="list-disc pl-6 space-y-3">
      @forelse ($sets as $s)
        <li>
          @if ($s['existsIndex'])
            {{-- 閲覧リンク --}}
            <a class="text-indigo-600 hover:underline" href="{{ route('specsets.show', $s['slug']) }}">
              {{ $s['slug'] }}
            </a>
          @else
            <span class="text-gray-500">{{ $s['slug'] }}（index.md なし）</span>
          @endif

          {{-- 名前変更フォーム（インライン） --}}
          <form action="{{ route('specsets.rename', $s['slug']) }}" method="POST" class="inline-flex items-center gap-2 ml-4">
            @csrf
            @method('PATCH')
            <input
              type="text"
              name="new_name"
              value="{{ $s['slug'] }}"
              class="border rounded px-2 py-1 text-sm"
              {{-- pattern は外す or こうする（スラッシュ禁止だけ） --}}
              pattern="[^/]+"
              title="スラッシュ「/」は使えません"
              required
            >
            <button class="px-2 py-1 bg-white text-black rounded text-sm">名前変更</button>
          </form>
        </li>
      @empty
        <li class="text-gray-500">まだセットがありません。</li>
      @endforelse
    </ul>
  </div>
</x-app-layout>
