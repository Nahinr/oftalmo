@props(['url'])

<tr>
<td class="header">
    <a href="{{ $url }}" style="display: inline-block;">
        <img src="{{ asset('images/logo-app.png') }}" class="logo" alt="{{ config('app.name') }}" style="height: 48px;">
    </a>
</td>
</tr>
