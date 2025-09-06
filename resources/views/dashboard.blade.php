<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">ダッシュボード</h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 space-y-8">
        {{-- フラッシュメッセージ（任意） --}}
        @if (session('status'))
            <div class="p-3 rounded bg-green-50 border border-green-200 text-green-800">
                {{ session('status') }}
            </div>
        @endif

        {{-- サマリーカード --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white shadow rounded-lg p-4">
                <div class="text-gray-500 text-sm">プロジェクト</div>
                <div class="text-2xl font-semibold">{{ $projectsCount ?? 0 }}</div>
            </div>
            <div class="bg-white shadow rounded-lg p-4">
                <div class="text-gray-500 text-sm">テストグループ</div>
                <div class="text-2xl font-semibold">{{ $groupsCount ?? 0 }}</div>
            </div>
            <div class="bg-white shadow rounded-lg p-4">
                <div class="text-gray-500 text-sm">テスト行（総数）</div>
                <div class="text-2xl font-semibold">{{ $rowsCount ?? 0 }}</div>
            </div>
            <div class="bg-white shadow rounded-lg p-4">
                <div class="text-gray-500 text-sm">完了行</div>
                <div class="text-2xl font-semibold">{{ $rowsDoneCount ?? 0 }}</div>
            </div>
        </div>

        {{-- クイック操作 --}}
        <div class="bg-white shadow rounded-lg p-4">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-semibold">クイック操作</h3>
                <a href="{{ route('projects.select') }}" class="text-indigo-600 hover:underline">
                    プロジェクト選択へ
                </a>
            </div>
            <div class="space-y-2"> {{-- プロジェクトごとに行を作る --}}
                @foreach(($projects ?? []) as $p)
                    <div class="flex gap-2 items-center"> {{-- 1プロジェクト＝1行 --}}
                        <a href="{{ route('bullet-cases.index', ['project' => $p]) }}"
                        class="px-3 py-1.5 border rounded text-sm hover:bg-gray-50">
                            {{ $p->key }}：{{ $p->name }}
                        </a>
                        <a href="{{ route('specifications.index', ['project' => $p]) }}"
                        class="px-3 py-1.5 border rounded text-sm hover:bg-gray-50 text-indigo-600">
                            仕様一覧
                        </a>
                        <a href="{{ route('specifications.create', ['project' => $p->id]) }}"
                        class="px-3 py-1.5 border rounded bg-indigo-600 text-black text-sm hover:bg-indigo-700">
                            仕様追加
                        </a>
                    </div>
                @endforeach
                @if(empty($projects) || count($projects ?? []) === 0)
                    <span class="text-gray-500 text-sm">プロジェクトがありません。</span>
                @endif
            </div>
        </div>

        {{-- 画像アップロード（追加） --}}
        <div class="bg-white shadow rounded-lg p-4">
            <h3 class="font-semibold mb-3">画像アップロード</h3>

            <form action="{{ route('spec-images.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <input type="file" name="image" accept="image/*"
                       class="block w-full text-sm file:mr-3 file:py-1.5 file:px-3 file:rounded file:border file:bg-gray-50 file:hover:bg-gray-100" required>
                @error('image')
                    <div class="text-red-600 text-sm">{{ $message }}</div>
                @enderror

                <div class="flex items-center gap-2">
                    <button type="submit"
                            class="px-4 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-700">
                        アップロード
                    </button>
                    <a href="{{ route('spec-images.index') }}" class="text-indigo-600 hover:underline">
                        保存済み画像を見る
                    </a>
                </div>
            </form>
        </div>

        {{-- mdファイルの処理 --}}
        <div class="card mb-4 bg-white shadow rounded-lg p-4">
            {{-- アップロードフォーム --}}
            <div class="card mb-6">
            <div class="card-header">Markdownアップロード</div>
            <div class="card-body">
                @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                <form action="{{ route('spec-md.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="mdfile" class="form-control mb-3" required>
                {{-- ↑ controller の validate キー 'mdfile' に合わせる --}}
                <button type="submit" class="btn btn-primary">アップロード</button>
                </form>
            </div>
            </div>

            {{-- 一覧ページへ遷移するボタン --}}
            <a href="{{ route('spec-md.index') }}" class="btn btn-secondary">
            保存済みMarkdownを見る
            </a>
        </div>

        {{-- 仕様書セットカード --}}
        <div class="bg-white shadow rounded-lg p-4">
            <h3 class="text-lg font-semibold mb-2">仕様書セット</h3>
            <p class="text-sm text-gray-600 mb-3">
                Markdown と画像を含む仕様書セットをアップロード・閲覧できます。
            </p>
            <div class="flex gap-3">
                <a href="{{ route('specsets.index') }}"
                    class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                    一覧を見る
                </a>
                <form action="{{ route('specsets.upload') }}"
                        method="POST"
                        enctype="multipart/form-data"
                        class="flex items-center space-x-2">
                    @csrf
                    <input type="file" name="zip"
                            accept=".zip"
                            class="text-sm border rounded p-1">
                    <button type="submit"
                            class="px-3 py-1 bg-gray-600 text-white rounded hover:bg-gray-700">
                        アップロード
                    </button>
                </form>
            </div>
        </div>

        {{-- 最近作成したグループ --}}
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-3 border-b">
                <h3 class="font-semibold">最近のテストグループ</h3>
            </div>
            <div class="p-0 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left">プロジェクト</th>
                            <th class="px-4 py-2 text-left">グループタイトル</th>
                            <th class="px-4 py-2 text-left">行(完了/総数)</th>
                            <th class="px-4 py-2 text-left">作成日時</th>
                            <th class="px-4 py-2 text-left">操作</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse(($recentGroups ?? []) as $g)
                            <tr>
                                <td class="px-4 py-2">
                                    @if(isset($g->project))
                                        <a class="text-indigo-600 hover:underline"
                                           href="{{ route('bullet-cases.index', ['project' => $g->project]) }}">
                                            {{ $g->project->key }}
                                        </a>
                                    @else
                                        <span class="text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2">{{ $g->title ?? '-' }}</td>
                                <td class="px-4 py-2">
                                    {{ $g->rows_done_count ?? 0 }}/{{ $g->rows_count ?? 0 }}
                                </td>
                                <td class="px-4 py-2">{{ optional($g->created_at)->format('Y-m-d H:i') }}</td>
                                <td class="px-4 py-2">
                                    @if(isset($g->project))
                                        <a class="px-3 py-1 border rounded text-sm hover:bg-gray-50"
                                           href="{{ route('bullet-cases.index', ['project' => $g->project]) }}">
                                            行を見る
                                        </a>
                                    @else
                                        <span class="text-gray-400 text-sm">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-4 py-6 text-center text-gray-500" colspan="5">まだデータがありません。</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
