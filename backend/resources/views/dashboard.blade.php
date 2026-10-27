<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} · Garagem</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-asfalto text-neutral-200 antialiased">
    <main class="mx-auto max-w-5xl px-4 py-8 sm:py-12">
        <header class="flex flex-wrap items-end justify-between gap-6">
            <div>
                <p class="text-xs font-medium tracking-[0.25em] text-neutral-500 uppercase">Estacionamento inteligente</p>
                <h1 class="mt-1 text-3xl font-semibold text-white sm:text-4xl">Smart Parking ESP32</h1>
            </div>

            <div class="flex items-center gap-2 rounded-full border border-white/10 px-3 py-1.5 text-sm">
                <span data-online-dot class="size-2 rounded-full bg-neutral-600"></span>
                <span data-online-label class="text-neutral-400">Aguardando ESP32…</span>
            </div>
        </header>

        <section class="mt-10 grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-white/10 bg-white/[0.03] p-5 sm:col-span-2">
                <p class="text-sm text-neutral-400">Vagas livres</p>
                <p class="mt-2 flex items-baseline gap-2">
                    <span data-livres class="text-6xl font-semibold text-white tabular-nums">–</span>
                    <span class="text-xl text-neutral-500">/ <span data-total>–</span></span>
                </p>
                <div class="mt-5 h-1.5 overflow-hidden rounded-full bg-white/10">
                    <div data-barra class="h-full w-0 rounded-full bg-livre transition-all duration-500"></div>
                </div>
            </div>

            <div class="flex flex-col justify-between rounded-2xl border border-white/10 bg-white/[0.03] p-5">
                <p class="text-sm text-neutral-400">Cancela</p>
                <div class="mt-4 flex items-center gap-4">
                    <div class="relative h-10 w-24">
                        <div class="absolute bottom-0 left-0 size-3 rounded-sm bg-neutral-500"></div>
                        <div data-cancela-braco class="absolute bottom-1 left-1.5 h-1.5 w-20 origin-left rounded-full bg-[repeating-linear-gradient(90deg,#facc15_0_8px,#262626_8px_16px)] transition-transform duration-700"></div>
                    </div>
                    <span data-cancela-label class="text-lg font-medium text-white">–</span>
                </div>
            </div>
        </section>

        <section class="mt-10">
            <h2 class="text-sm font-medium tracking-wide text-neutral-400">Mapa da garagem</h2>
            <div class="mt-4 rounded-3xl border border-white/10 bg-[#1b1d21] p-4 sm:p-6">
                <div data-vagas class="grid grid-cols-2 gap-3 sm:grid-cols-4"></div>
                <div class="mt-5 flex items-center gap-3 border-t-2 border-dashed border-white/15 pt-4 text-xs tracking-[0.3em] text-neutral-500 uppercase">
                    <span>Entrada</span>
                    <span class="h-px flex-1 bg-white/10"></span>
                    <span>→</span>
                </div>
            </div>
        </section>

        <section class="mt-10">
            <h2 class="text-sm font-medium tracking-wide text-neutral-400">Movimentação recente</h2>
            <ol data-eventos class="mt-4 divide-y divide-white/5 rounded-2xl border border-white/10 bg-white/[0.03]">
                <li class="px-5 py-4 text-sm text-neutral-500">Nenhuma movimentação ainda.</li>
            </ol>
        </section>
    </main>
</body>
</html>
