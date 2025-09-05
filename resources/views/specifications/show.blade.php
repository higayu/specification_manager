{{-- resources/views/specifications/show.blade.php --}}
@php
    use Illuminate\Support\Str;
    /** @var \App\Models\Specification $specification */
    /** @var \App\Models\Project $project */
    /** @var \App\Models\SpecificationVersion|null $ver */
    $md = $ver->body_md ?? '';  // 版が無ければ空
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">
            仕様詳細：{{ $specification->title }}
            <small class="text-gray-500">({{ $project->key ?? $project->id }})</small>
        </h2>
    </x-slot>

    <div class="flex justify-between">
        <a href="{{ route('specifications.index', ['project' => $project->id]) }}"
        class="text-indigo-600 hover:underline">← 一覧へ戻る</a>

        <a href="{{ route('specifications.edit', ['project' => $project->id, 'specification' => $specification->id]) }}"
        class="inline-block px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
            編集する
        </a>
    </div>


    <div class="max-w-5xl mx-auto py-6 px-4 space-y-6">
        {{-- メタ --}}
        <div class="bg-white shadow rounded p-4 text-sm">
            <div class="flex flex-wrap gap-4">
                <div>コード：<span class="font-mono">{{ $specification->code }}</span></div>
                <div>状態：<span class="px-2 py-0.5 rounded bg-gray-200 text-gray-800">{{ $specification->status }}</span></div>
                <div>現在版：v{{ $ver->version_no ?? '-' }}</div>
            </div>
        </div>

        {{-- 本文（Markdown が無ければプレーン） --}}
        <div class="bg-white shadow rounded p-6 prose max-w-none">
        @if($md !== '')
            @if(class_exists(\League\CommonMark\CommonMarkConverter::class))
            {!! \Illuminate\Support\Str::markdown($md) !!}
            @else
            {!! nl2br(e($md)) !!}
            @endif
        @else
            <p class="text-gray-500">本文が登録されていません。</p>
        @endif
        </div>


        <div>
            <a href="{{ route('specifications.index', ['project' => $project->id]) }}"
               class="text-indigo-600 hover:underline">← 一覧へ戻る</a>
        </div>
    </div>
</x-app-layout>
