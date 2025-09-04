{{-- resources/views/filament/auth/custom-login.blade.php --}}

<x-filament-panels::page>
    {{-- 全体に両モードの文字色を適用 --}}
    <div class="flex min-h-full flex-col justify-center py-12 sm:px-6 lg:px-8 text-gray-900 dark:text-gray-100">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            {{-- 見出しも両モード対応 --}}
            <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-gray-900 dark:text-gray-100 mb-5">
                {{ __('ログイン') }}
            </h2>
        </div>

        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            {{-- カード背景も両モード対応 --}}
            <div class="bg-white dark:bg-gray-800 px-4 py-8 shadow sm:rounded-lg sm:px-10">
                <form wire:submit="authenticate" class="space-y-6">
                    {{-- Filament のフォームコンポーネントはダーク対応済み。ラベル等を自前で書く場合は dark: を付与する --}}
                    {{ $this->form }}

                    <div>
                        <x-filament::button type="submit" class="w-full justify-center">
                            {{ __('ログイン') }}
                        </x-filament::button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ローディングオーバーレイも両モード対応 --}}
    <div id="loading-overlay" class="fixed inset-0 bg-gray-900/50 hidden z-50">
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2">
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-lg">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900 dark:border-gray-100 mx-auto"></div>
            </div>
        </div>
    </div>

    @script
    <script>
        const loadingOverlay = document.getElementById('loading-overlay');

        $wire.on('loading', () => {
            loadingOverlay.classList.remove('hidden');
        });

        $wire.on('loading-finished', () => {
            loadingOverlay.classList.add('hidden');
        });
    </script>
    @endscript
</x-filament-panels::page>
