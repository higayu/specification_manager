<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Markdownファイル一覧</h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 px-4">
        {{-- ステータスメッセージ --}}
        @if (session('status'))
            <div class="p-3 rounded bg-green-50 border border-green-200 text-green-800">
                {{ session('status') }}
            </div>
        @endif

        {{-- アップロードフォーム --}}
        <form action="{{ route('spec-md.upload') }}" method="POST" enctype="multipart/form-data" class="mb-4">
            @csrf
            <input type="file" name="mdfile" class="border p-2">
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">アップロード</button>
        </form>

        {{-- 一覧表示 --}}
        @if (empty($docs))
            <p class="text-gray-500">まだファイルがありません。</p>
        @else
            <ul class="list-disc pl-6 space-y-2">
                @foreach ($docs as $filename)
                    <li class="flex items-center gap-4">
                        {{-- 表示（コントローラ経由でUTF-8強制） --}}
                        <a href="{{ route('spec-md.show', ['filename' => $filename]) }}"
                           target="_blank"
                           class="text-indigo-600 hover:underline">
                            {{ $filename }}
                        </a>

                        {{-- ダウンロード用リンク --}}
                        <a href="{{ route('spec-md.download', ['filename' => $filename]) }}"
                           class="text-sm text-gray-600 hover:underline">
                            ダウンロード
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</x-app-layout>
