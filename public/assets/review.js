// Повторение карточек: вопрос → вспомнить → открыть ответ → честно оценить себя.

const cards = JSON.parse(document.getElementById('cards-data').textContent);
const root = document.getElementById('review');
let position = 0;

function show() {
  if (position >= cards.length) {
    root.innerHTML = cards.length
      ? '<div class="card-box"><h2>На сегодня всё</h2><p class="muted">Карточки вернутся, когда придёт время их повторить.</p><a class="btn" href="/">К программе</a></div>'
      : '<div class="card-box"><h2>Повторять пока нечего</h2><p class="muted">Карточки появляются здесь после того, как ты открываешь урок, и возвращаются через 1, 3, 7, 14 и 30 дней.</p><a class="btn" href="/">К программе</a></div>';
    return;
  }
  const card = cards[position];
  root.innerHTML = `
    <div class="card-box">
      <div class="muted">Карточка ${position + 1} из ${cards.length} · <a href="${card.lessonUrl}">${escapeHtml(card.lessonTitle)}</a></div>
      <div class="card-q prose">${card.q}</div>
      <div class="card-a prose" hidden>${card.a}</div>
      <div class="toolbar">
        <button class="btn btn-primary" data-act="open">Показать ответ</button>
        <button class="btn" data-act="no" hidden>Не вспомнил</button>
        <button class="btn btn-primary" data-act="yes" hidden>Вспомнил</button>
      </div>
    </div>`;
  highlightIn(root);
}

root.addEventListener('click', (event) => {
  const act = event.target.dataset.act;
  if (act === 'open') {
    root.querySelector('.card-a').hidden = false;
    root.querySelector('[data-act=open]').hidden = true;
    root.querySelector('[data-act=no]').hidden = false;
    root.querySelector('[data-act=yes]').hidden = false;
  }
  if (act === 'yes' || act === 'no') {
    api('/api/card', { cardId: cards[position].id, remembered: act === 'yes' });
    position++;
    show();
  }
});

show();
