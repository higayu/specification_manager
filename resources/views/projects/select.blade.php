<x-app-layout>
  <h1 class="text-2xl font-bold mb-6">プロジェクトを選択してください</h1>

  <ul class="list-disc pl-6 space-y-2">
    @forelse($projects as $p)
      <li>
        <a href="{{ route('bullet-cases.index', $p) }}" class="text-blue-600 underline">
          {{ $p->id }} : {{ $p->name }}
        </a>
      </li>
    @empty
      <li>プロジェクトがまだ登録されていません。</li>
    @endforelse
  </ul>
</x-app-layout>
