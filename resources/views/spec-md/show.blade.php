<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">{{ $filename }}</h2>
    </x-slot>

    <div class="max-w-4xl mx-auto py-6 px-4 prose prose-indigo">
        {{-- MarkdownをHTMLとして描画（html_input: strip でサニタイズ済） --}}
        {!! $html !!}
    </div>
</x-app-layout>
