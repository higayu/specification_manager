<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">
            テストケース一覧：{{ $project->name }}
            <small class="text-gray-500">({{ $project->key }})</small>
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 space-y-4">
        <div class="flex justify-end">
            <a class="inline-flex items-center px-3 py-1.5 border rounded-md text-sm"
               href="{{ route('bullet-cases.create', ['project' => $project]) }}">
                テキスト取り込み
            </a>
        </div>

        @forelse ($groups as $group)
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-3 border-b">
                    <strong>{{ $group->name }}</strong>
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
                                <th class="px-3 py-2 text-left">状態</th>
                                <th class="px-3 py-2 text-left">操作</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                        @forelse ($group->rows as $row)
                            <tr>
                                <td class="px-3 py-2">{{ $row->order_no }}</td>
                                <td class="px-3 py-2">{{ $row->no }}</td>
                                <td class="px-3 py-2">{{ $row->feature }}</td>
                                <td class="px-3 py-2">{{ $row->input_condition }}</td>
                                <td class="px-3 py-2 whitespace-pre-wrap">{{ $row->expected }}</td>
                                <td class="px-3 py-2">
                                    @if($row->is_done)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-green-100 text-green-800">完了</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-gray-200 text-gray-800">未了</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2">
                                    <form method="POST" action="{{ route('bullet-cases.rows.toggle', ['row' => $row->id]) }}">
                                        @csrf
                                        <button type="submit" class="px-2 py-1 border rounded text-sm">切替</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-3 py-6 text-center text-gray-500">行がありません</td></tr>
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
