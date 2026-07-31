<div class="flex items-center justify-between mb-6">

    <div>
        <h1 class="text-2xl font-bold text-slate-800">
            {{ $title }}
        </h1>

        @isset($subtitle)
            <p class="mt-1 text-sm text-slate-500">
                {{ $subtitle }}
            </p>
        @endisset
    </div>

    @isset($actions)
        <div>
            {{ $actions }}
        </div>
    @endisset

</div>
