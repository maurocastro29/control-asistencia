<div {{ $attributes->merge([
    'class' => 'bg-white rounded-xl shadow-sm border border-slate-200',
]) }}>
    <div class="p-6">
        {{ $slot }}
    </div>
</div>
