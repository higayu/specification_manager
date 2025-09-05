<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl">
      仕様一覧：{{ $project->name }}
    </h2>
  </x-slot>

  <div class="max-w-5xl mx-auto py-6 px-4">
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
            <td class="px-3 py-2">{{ $s->code }}</td>
            <td class="px-3 py-2">{{ $s->title }}</td>
            <td class="px-3 py-2">v{{ $s->currentVersion?->version_no }}</td>
            <td class="px-3 py-2">{{ $s->status }}</td>
            <td class="px-3 py-2">
              <a href="{{ route('specifications.show',['project'=>$project,'specification'=>$s]) }}"
                 class="text-indigo-600 hover:underline">詳細</a>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" class="px-3 py-6 text-center text-gray-500">仕様がありません</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</x-app-layout>
