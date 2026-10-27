const INTERVALO_ESTADO_MS = 1000;
const INTERVALO_EVENTOS_MS = 2000;

const $ = (seletor) => document.querySelector(seletor);

const horario = new Intl.DateTimeFormat('pt-BR', {
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit',
});

async function buscarJson(url) {
    const resposta = await fetch(url, { headers: { Accept: 'application/json' } });

    if (!resposta.ok) {
        throw new Error(`${url} respondeu ${resposta.status}`);
    }

    return resposta.json();
}

function renderizarVaga(vaga) {
    const cor = vaga.ocupada ? 'text-ocupada' : 'text-livre';
    const rotulo = vaga.ocupada ? 'Ocupada' : 'Livre';
    const distancia = vaga.distancia_cm == null ? '—' : `${vaga.distancia_cm} cm`;

    return `
        <article class="relative flex aspect-[3/4] flex-col justify-between rounded-xl border-x-2 border-t-2 border-white/25 p-3 sm:p-4">
            <div class="flex items-center justify-between">
                <span class="text-2xl font-semibold text-white/80 tabular-nums">${String(vaga.numero).padStart(2, '0')}</span>
                <span class="vaga-led size-3 rounded-full bg-current ${cor} transition-colors duration-300"></span>
            </div>

            <div class="flex flex-1 items-center justify-center">
                <div class="h-[55%] w-[60%] rounded-lg bg-gradient-to-b from-neutral-300 to-neutral-500 shadow-lg transition-all duration-500 ${vaga.ocupada ? 'scale-100 opacity-100' : 'scale-75 opacity-0'}"></div>
            </div>

            <div class="flex items-end justify-between text-xs">
                <span class="font-medium ${cor}">${rotulo}</span>
                <span class="text-neutral-500 tabular-nums">${distancia}</span>
            </div>
        </article>
    `;
}

function renderizarEstado(estado) {
    $('[data-livres]').textContent = estado.livres;
    $('[data-total]').textContent = estado.total;

    const percentualLivre = estado.total ? (estado.livres / estado.total) * 100 : 0;
    const barra = $('[data-barra]');
    barra.style.width = `${percentualLivre}%`;
    barra.classList.toggle('bg-livre', estado.livres > 0);
    barra.classList.toggle('bg-ocupada', estado.livres === 0);

    $('[data-vagas]').innerHTML = estado.vagas.map(renderizarVaga).join('');

    $('[data-cancela-braco]').style.transform = estado.cancela_aberta ? 'rotate(-75deg)' : 'rotate(0deg)';
    $('[data-cancela-label]').textContent = estado.cancela_aberta ? 'Aberta' : 'Fechada';

    const dot = $('[data-online-dot]');
    dot.classList.toggle('bg-livre', estado.dispositivo_online);
    dot.classList.toggle('bg-neutral-600', !estado.dispositivo_online);
    $('[data-online-label]').textContent = estado.dispositivo_online
        ? 'ESP32 online'
        : estado.ultimo_contato
            ? `ESP32 offline · último contato ${horario.format(new Date(estado.ultimo_contato))}`
            : 'Aguardando ESP32…';
}

function renderizarEventos({ data: eventos }) {
    if (eventos.length === 0) {
        return;
    }

    $('[data-eventos]').innerHTML = eventos
        .map(
            (evento) => `
                <li class="flex items-center justify-between px-5 py-3 text-sm">
                    <span class="flex items-center gap-3">
                        <span class="size-2 rounded-full ${evento.ocupada ? 'bg-ocupada' : 'bg-livre'}"></span>
                        Vaga ${String(evento.vaga).padStart(2, '0')} ${evento.ocupada ? 'ocupada' : 'liberada'}
                    </span>
                    <time class="text-neutral-500 tabular-nums">${horario.format(new Date(evento.ocorreu_em))}</time>
                </li>
            `,
        )
        .join('');
}

function agendar(fn, intervalo) {
    const executar = async () => {
        try {
            await fn();
        } catch (erro) {
            console.error(erro);
        } finally {
            setTimeout(executar, intervalo);
        }
    };

    executar();
}

agendar(async () => renderizarEstado(await buscarJson('/api/garagem')), INTERVALO_ESTADO_MS);
agendar(async () => renderizarEventos(await buscarJson('/api/garagem/eventos')), INTERVALO_EVENTOS_MS);
