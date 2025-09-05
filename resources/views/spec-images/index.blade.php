<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">保存済み画像</h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        @if (session('status'))
            <div class="p-3 rounded bg-green-50 border border-green-200 text-green-800">
                {{ session('status') }}
            </div>
        @endif

        @if (empty($images))
            <div class="text-gray-500">まだ画像がありません。</div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                @foreach ($images as $url)
                    <div class="bg-white shadow rounded p-2">
                        <img src="{{ $url }}" alt="" class="w-full h-40 object-cover rounded">
                        <div class="text-xs mt-2 break-all">{{ $url }}</div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="mt-6">
            <a href="{{ route('dashboard') }}" class="text-indigo-600 hover:underline">ダッシュボードへ戻る</a>
        </div>
    </div>
</x-app-layout>
