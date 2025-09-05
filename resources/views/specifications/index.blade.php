<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl">
      仕様一覧：{{ $project->name }}
    </h2>
  </x-slot>

  <div class="max-w-5xl mx-auto py-6 px-4 space-y-4">
    {{-- 右上：新規作成 --}}
    <div class="flex justify-end">
      <a href="{{ route('specifications.create', ['project' => $project->id]) }}"
         class="px-3 py-1.5 border rounded text-sm hover:bg-gray-50">
        新規作成
      </a>
    </div>

    <table class="min-w-full text-sm">
      <thead>
        <tr class="bg-gray-50">
          <th class="px-3 py-2 text-left">コード</th>
          <th class="px-3 py-2 text-left">タイトル</th>
          <th class="px-3 py-2 text-left">現在版</th>
          <th class="px-3 py-2 text-left">状態</th>
          <th class="px-3 py-2"></th>
        </tr>
      </thead>
      <tbody class="divide-y">
        @forelse($specs as $s)
          <tr>
            <td class="px-3 py-2 font-mono">{{ $s->code }}</td>
            <td class="px-3 py-2">
              <a class="text-indigo-600 hover:underline"
                 href="{{ route('specifications.show', ['specification' => $s->id, 'project' => $project->id]) }}">
                {{ $s->title }}
              </a>
            </td>
            <td class="px-3 py-2">v{{ $s->currentVersion?->version_no ?? '-' }}</td>
            <td class="px-3 py-2">
              @php
                $st = $s->status ?? 'draft';
                $badge = [
                  'approved' => 'bg-green-100 text-green-800',
                  'proposed' => 'bg-yellow-100 text-yellow-800',
                  'draft'    => 'bg-gray-200 text-gray-800',
                ][$st] ?? 'bg-gray-200 text-gray-800';
              @endphp
              <span class="inline-flex items-center px-2 py-0.5 rounded {{ $badge }}">{{ $st }}</span>
            </td>
            <td class="px-3 py-2 text-right">
              <a href="{{ route('specifications.show', ['specification' => $s->id, 'project' => $project->id]) }}"
                 class="text-indigo-600 hover:underline">詳細</a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="px-3 py-6 text-center text-gray-500">仕様がありません</td>
          </tr>
        @endforelse
      </tbody>
    </table>

    {{-- ページネーション（検索等が入っても維持） --}}
    <div>
      {{ $specs->withQueryString()->links() }}
    </div>
  </div>
</x-app-layout>
