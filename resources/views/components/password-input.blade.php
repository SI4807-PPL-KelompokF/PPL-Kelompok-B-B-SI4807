@props([
    'id' => 'password',
    'name' => 'password',
    'autocomplete' => 'current-password',
    'invalid' => false,
    'autofocus' => false,
])

<div class="tu-pass">
    <input
        id="{{ $id }}"
        type="password"
        name="{{ $name }}"
        required
        autocomplete="{{ $autocomplete }}"
        @if($autofocus) autofocus @endif
        {{ $attributes->merge(['class' => 'tu-input' . ($invalid ? ' is-invalid' : '')]) }}
    >
    <button type="button" data-toggle-password="{{ $id }}" aria-pressed="false">Lihat</button>
</div>
