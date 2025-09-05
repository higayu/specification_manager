<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">
            テストケース一覧：{{ $project->name }}
            <small class="text-gray-500">({{ $project->key }})</small>
        </h2>
    </x-slot>

    <div class="max-w-9xl mx-auto py-6 px-4 sm:px-3 lg:px-8 space-y-4">
        <div class="flex justify-end">
            <a class="inline-flex items-center px-3 py-1.5 border rounded-md text-sm"
               href="{{ route('bullet-cases.create', ['project' => $project]) }}">
                テキスト取り込み
            </a>
        </div>

        @forelse ($groups as $group)
            <div class="bg-white shadow rounded-lg mb-4 mt-4">
                <div class="px-4 py-3 border-b">
                    <strong>{{ $group->title }}</strong>
                </div>
                <div class="p-0 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left">並び順</th>
                                <th class="px-3 py-2 text-left">No</th>
                                <th class="px-3 py-2 text-left">機能</th>
                                <th class="px-3 py-2 text-left">入力/条件</th>
                                <th class="px-3 py-2 text-left">期待結果</th>
                                <th class="px-3 py-2 text-left">メモ</th>
                                <th class="px-3 py-2 text-left">優先度</th>
                                <th class="px-3 py-2 text-left">状態</th>
                                <th class="px-3 py-2 text-left">操作</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                        @forelse ($group->rows as $row)
                            @php $formId = "row-form-{$row->id}"; @endphp

                            {{-- 行専用のフォーム（テーブル外にあってもOK） --}}
                            <form id="{{ $formId }}" method="POST" action="{{ route('bullet-cases.rows.update', $row->id) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="project_id" value="{{ $project->id }}">
                            </form>

                            <tr>
                                <td class="px-3 py-2">{{ $row->order_no }}</td>
                                <td class="px-3 py-2">{{ $row->no }}</td>
                                <td class="px-3 py-2">{{ $row->feature }}</td>
                                <td class="px-3 py-2">{{ $row->input_condition }}</td>
                                <td class="px-3 py-2 whitespace-pre-wrap">{{ $row->expected }}</td>

                                {{-- メモ（この行のフォームに紐づけ） --}}
                                <td class="px-3 py-2">
                                    <input form="{{ $formId }}" type="text" name="memo" value="{{ $row->memo }}"
                                           class="w-48 border rounded px-2 py-1 text-sm" />
                                </td>

                                {{-- 優先度（バッジ表示＋ボタン風ラジオ） --}}
                                <td class="px-3 py-2">
                                    @php
                                        $p = (int)$row->priority;
                                        $badgeClass = [
                                            1 => 'bg-red-100 text-red-800',
                                            2 => 'bg-yellow-100 text-yellow-800',
                                            3 => 'bg-gray-200 text-gray-800',
                                        ][$p] ?? 'bg-gray-200 text-gray-800';
                                        $label = [1 => '高', 2 => '中', 3 => '低'][$p] ?? '低';
                                    @endphp

                                    {{-- 表示用バッジ --}}
                                    <span class="inline-flex items-center px-2 py-0.5 rounded {{ $badgeClass }} mr-2 align-middle">
                                        {{ $label }}
                                    </span>

                                    {{-- 編集用（✓は出ないボタン風） --}}
                                    <div class="inline-flex rounded-md overflow-hidden border align-middle">
                                        @foreach ([1=>'高', 2=>'中', 3=>'低'] as $val => $text)
                                            <label class="px-3 py-1 text-sm cursor-pointer select-none
                                                          {{ $row->priority==$val ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}
                                                          {{ $val!==1 ? 'border-l' : '' }}">
                                                <input form="{{ $formId }}" type="radio" name="priority" value="{{ $val }}" class="hidden"
                                                       @checked($row->priority==$val)>
                                                {{ $text }}
                                            </label>
                                        @endforeach
                                    </div>
                                </td>

                                {{-- 状態表示 --}}
                                <td class="px-3 py-2">
                                    @if($row->is_done)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-green-100 text-green-800">完了</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-gray-200 text-gray-800">未了</span>
                                    @endif

                                    {{-- 完了切替 --}}
                                    <form method="POST" action="{{ route('bullet-cases.rows.toggle', ['row' => $row->id]) }}" class="inline">
                                        @csrf
                                        <input type="hidden" name="project_id" value="{{ $project->id }}">
                                        <button type="submit" class="px-2 py-1 border rounded text-sm">切替</button>
                                    </form>
                                </td>

                                {{-- 操作 --}}
                                <td class="px-3 py-2 space-x-1">
                                    {{-- 保存（この行のフォームを送信） --}}
                                    <button type="submit" form="{{ $formId }}"
                                            class="px-2 py-1 border rounded text-sm bg-indigo-600 text-blue">
                                        保存
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-3 py-6 text-center text-gray-500">
                                    行がありません
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="px-4 py-3 bg-blue-50 border border-blue-200 rounded text-blue-800">
                表示できるグループがありません。
            </div>
        @endforelse
    </div>
</x-app-layout>
