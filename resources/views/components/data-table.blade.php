@props([
    'columns' => [],
])

<table>
    <thead>
        <tr>
            @foreach ($columns as $column)
                <th style="width: {{ $column['width'] ?? 'auto' }}">
                    {{ $column['label'] }}
                </th>
            @endforeach
        </tr>
    </thead>

    <tbody>
        {{ $slot }}
    </tbody>
</table>
