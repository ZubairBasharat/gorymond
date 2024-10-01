@php

$tone = url('/storage') . '/' . $getState();

@endphp


<div>
    <audio controls>
        <source src="{{ $tone }}" type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>
</div>
