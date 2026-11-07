<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body>
    <header>
        <h1>{{ config('app.name') }}</h1>
        <p id="esp32" class="esp32">Conectando…</p>
    </header>

    <main>
        <section class="resumo">
            <div class="cartao">
                <p class="rotulo">Vagas livres</p>
                <p class="valor"><span id="livres">–</span> de <span id="total">–</span></p>
            </div>
            <div class="cartao">
                <p class="rotulo">Cancela</p>
                <p id="cancela" class="valor">–</p>
            </div>
        </section>

        {{-- Os cards das vagas e a lista de eventos são preenchidos pelo dashboard.js --}}
        <section id="vagas" class="vagas"></section>

        <section class="cartao">
            <h2>Últimas movimentações</h2>
            <ol id="eventos" class="eventos"></ol>
        </section>
    </main>

    <script src="{{ asset('js/dashboard.js') }}"></script>
</body>
</html>
