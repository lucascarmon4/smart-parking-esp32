// Busca o estado do estacionamento a cada 1 s e redesenha a página.

async function atualizar() {
    try {
        const resposta = await fetch('/api/estado', { headers: { Accept: 'application/json' } });
        desenhar(await resposta.json());
    } catch {
        mostrarEsp32(false, 'Sem conexão com o servidor');
    }
}

function desenhar(estado) {
    document.getElementById('livres').textContent = estado.livres;
    document.getElementById('total').textContent = estado.total;
    document.getElementById('cancela').textContent = estado.cancela_aberta ? 'Aberta' : 'Fechada';

    mostrarEsp32(
        estado.esp32_online,
        estado.esp32_online ? 'ESP32 online' : `ESP32 offline${estado.ultimo_contato ? ` · último contato ${estado.ultimo_contato}` : ''}`,
    );

    document.getElementById('vagas').innerHTML = estado.vagas.map((vaga) => `
        <article class="vaga ${vaga.ocupada ? 'ocupada' : 'livre'}">
            <h2>Vaga ${vaga.numero}</h2>
            <p class="status">${vaga.ocupada ? 'Ocupada' : 'Livre'}</p>
            <p class="distancia">${vaga.distancia_cm === null ? 'sem leitura' : `${vaga.distancia_cm} cm`}</p>
        </article>
    `).join('');

    document.getElementById('eventos').innerHTML = estado.eventos.length
        ? estado.eventos.map((evento) => `<li><time>${evento.horario}</time> ${evento.descricao}</li>`).join('')
        : '<li class="vazio">Nenhuma movimentação ainda.</li>';
}

function mostrarEsp32(online, texto) {
    const elemento = document.getElementById('esp32');
    elemento.textContent = texto;
    elemento.classList.toggle('online', online);
}

atualizar();
setInterval(atualizar, 1000);
