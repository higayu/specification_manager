<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl">{{ $path }}</h2>
  </x-slot>

  <div class="max-w-4xl mx-auto py-6 px-4">
    <div class="prose prose-indigo max-w-none">
      {!! $html !!}
    </div>
  </div>
</x-app-layout>
