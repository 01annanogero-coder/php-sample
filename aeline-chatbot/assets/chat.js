// Chat widget (frontend). It never sees the API key:
// it only sends the conversation to OUR backend at backend/chat.php.
(function () {
  const ENDPOINT = 'backend/chat.php';

  const panel = document.getElementById('chat');
  const log = document.getElementById('chat-log');
  const form = document.getElementById('chat-form');
  const input = document.getElementById('chat-input');
  const sendBtn = form.querySelector('button');

  // Conversation history, sent with each request so the bot remembers context.
  const history = [];

  function addMessage(text, who) {
    const el = document.createElement('div');
    el.className = 'msg ' + who;
    el.textContent = text; // textContent, not innerHTML: avoids XSS from model output
    log.appendChild(el);
    log.scrollTop = log.scrollHeight;
    return el;
  }

  function showTyping() {
    const el = addMessage('', 'bot');
    el.innerHTML = '<span class="typing"><i></i><i></i><i></i></span>';
    return el;
  }

  function open() {
    panel.hidden = false;
    if (!log.children.length) {
      addMessage("Hi! I'm the Aeline assistant. Ask me about our AI and strategy services.", 'bot');
    }
    input.focus();
  }

  document.querySelectorAll('[data-open-chat]').forEach((btn) =>
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      panel.hidden ? open() : (panel.hidden = true);
    })
  );
  panel.querySelector('.chat-close').addEventListener('click', () => (panel.hidden = true));

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const text = input.value.trim();
    if (!text) return;

    addMessage(text, 'user');
    history.push({ role: 'user', content: text });
    input.value = '';
    sendBtn.disabled = true;
    const typing = showTyping();

    try {
      const res = await fetch(ENDPOINT, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ messages: history }),
      });
      const data = await res.json().catch(() => ({}));
      typing.remove();

      if (!res.ok || !data.reply) {
        history.pop(); // drop the failed user turn so the history stays valid
        addMessage(data.error || 'Something went wrong. Please try again.', 'error');
        return;
      }
      history.push({ role: 'assistant', content: data.reply });
      addMessage(data.reply, 'bot');
    } catch (err) {
      typing.remove();
      history.pop();
      addMessage('Network error. Check your connection and try again.', 'error');
    } finally {
      sendBtn.disabled = false;
      input.focus();
    }
  });
})();
