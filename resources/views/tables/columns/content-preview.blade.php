<?php
use Illuminate\Support\Facades\Storage;
?>
@php
    $file = $getState();
    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $url = Storage::url($file);
@endphp

@if(in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
    <img src="{{ $url }}" alt="Prévia" class="max-w-20 max-h-20 rounded object-cover" />
@elseif(in_array($extension, ['mp4', 'webm', 'ogg']))
    <video src="{{ $url }}" controls class="max-w-28 max-h-20 rounded" preload="metadata">
        Seu navegador não suporta vídeo.
    </video>
@else
    <div class="flex items-center justify-center w-20 h-20 rounded">
        <x-heroicon-o-document class="w-8 h-8 text-gray-400" />
    </div>
@endif
