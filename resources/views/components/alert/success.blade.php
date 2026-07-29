@if (session('success'))
    <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-700">
        {{ session('success') }}
    </div>
@endif
