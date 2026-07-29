@props([
    'colspan' => 1,
    'message' => 'No existen registros.',
])

<tr>
    <td colspan="{{ $colspan }}" class="px-6 py-10 text-center text-slate-500">
        {{ $message }}
    </td>
</tr>
