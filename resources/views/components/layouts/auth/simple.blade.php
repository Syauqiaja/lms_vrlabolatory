<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
    <style>
        .bg-dna {
            background-image: url('{{ asset("images/bg-dna.jpg") }}');
        }
    </style>
</head>

<body class="min-h-screen bg-cover bg-center bg-dna">
    <div class="bg-black/70 min-h-screen bg-cover">
        <div class="bg-background flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
            <div class="flex w-full max-w-sm flex-col gap-2">
                <div class="flex flex-col gap-6">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
    @fluxScripts
</body>

</html>