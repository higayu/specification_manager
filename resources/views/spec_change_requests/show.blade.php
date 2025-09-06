@php use Illuminate\Support\Str; @endphp

<h1>変更要求 #{{ $cr->id }}（{{ $cr->status }}）</h1>
<p>Project: {{ $project->id }}</p>
<p>Spec: {{ optional($cr->specification)->title ?? '-' }}</p>

<h3>理由</h3>
<pre>{{ $cr->reason }}</pre>

<div style="display:flex; gap:2rem; align-items:flex-start;">
  <div style="flex:1;">
    <h2>変更後（提案: v{{ optional($cr->toVersion)->version_no ?? '?' }})</h2>
    {!! Str::markdown(optional($cr->toVersion)->body_md ?? '') !!}
  </div>
  <div style="flex:1;">
    <h2>変更前（現行: v{{ optional($cr->fromVersion)->version_no ?? '?' }})</h2>
    {!! Str::markdown(optional($cr->fromVersion)->body_md ?? '') !!}
  </div>
</div>

<form method="POST" action="{{ route('spec-change-requests.approve', $cr) }}">
  @csrf
  <button type="submit">承認して最新版に反映</button>
</form>
