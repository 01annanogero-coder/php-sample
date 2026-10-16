<?php
// Frontend page. It contains NO API key — the chat widget only calls backend/chat.php.
$year = date('Y');
$logos = array_fill(0, 7, 'Logoipsum');
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Aeline — AI &amp; Strategy Consulting</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>

  <section class="hero">
    <nav class="nav">
      <a href="#" class="brand"><span class="brand-mark">▲▲</span> Aeline</a>
      <ul class="nav-links">
        <li><a href="#">Home</a></li>
        <li><a href="#services">Services</a></li>
        <li><a href="#about">About us</a></li>
        <li><a href="#">More links ⌄</a></li>
      </ul>
      <a href="#" class="btn btn-lime btn-sm">Buy template</a>
    </nav>

    <div class="hero-copy">
      <h1>Building the future with<br><span>AI and strategy</span></h1>
      <p>We help organizations unlock growth and efficiency through data-driven consulting and intelligent automation.</p>
      <div class="hero-actions">
        <a href="#" class="btn btn-ghost">View demo</a>
        <a href="#" class="btn btn-lime" data-open-chat>Get started <span class="arrow">↗</span></a>
      </div>
    </div>

    <div class="cards">
      <div class="card c1"><small>Intelligence in Every Decision</small><div class="bars"><i></i><i></i><i></i><i></i></div></div>
      <div class="card c2"><small>Revenue</small><b>$4,900</b><div class="lines"><i></i><i></i><i></i></div></div>
      <div class="card c3 photo"><div class="chip">$2,670</div><div class="chip">$1,200</div></div>
      <div class="card c4"><small>Intelligence in Every Decision</small><svg viewBox="0 0 100 40"><path d="M0 35 L20 28 L35 30 L55 15 L70 20 L100 5 L100 40 L0 40Z" fill="#b9e3ff"/><path d="M0 35 L20 28 L35 30 L55 15 L70 20 L100 5" stroke="#2b9af3" fill="none" stroke-width="1.5"/></svg></div>
      <div class="card c5 dark"><p><b>Expertise</b> that Combines Strategy, Data, and Artificial Intelligence</p></div>
      <div class="card c6 glass"><span class="plus">+</span><p>Data training</p></div>
      <div class="card c7"><small>Data Points</small><b>520k+</b></div>
      <div class="card c8 dark"><b>4%</b></div>
    </div>

    <div class="rating">Rated 4.9/5 by 4,900+ clients<div class="stars">★★★★★</div></div>
  </section>

  <div class="logos" aria-label="Clients">
    <?php foreach ($logos as $logo): ?>
      <span><i>✦</i> <?= htmlspecialchars($logo) ?></span>
    <?php endforeach; ?>
  </div>

  <section class="about" id="about">
    <p class="eyebrow">• About us</p>
    <h2>A global consulting partner<br>dedicated to building <span class="dot blue">◑</span> smarter<br><em>and <span class="dot lime">●</span> more adaptive</em></h2>

    <div class="stats">
      <div class="stat stat-photo">
        <span class="stat-logo">IPSUM</span>
        <div class="stat-inner"><b>120+</b><p>Collaborating with leading AI and cloud technology providers.</p></div>
      </div>
      <div class="stat stat-grey">
        <small>Commitment to measurable</small><b>100%</b>
        <div class="avatars"><i></i><i></i><i></i><i></i></div>
        <p>“Their automation strategy completely reshaped how we work. It's efficient, intelligent, and seamless.”</p>
      </div>
      <div class="stat-col">
        <div class="stat stat-lime"><small>Data Points</small><b>520k+</b><p>Analyzed monthly to power smarter business strategies.</p></div>
        <div class="stat stat-dark"><small>Continents</small><b>20+</b></div>
      </div>
    </div>
  </section>

  <footer class="footer">© <?= $year ?> Aeline. Demo site with an AI chatbot.</footer>

  <!-- Chat widget -->
  <button class="chat-toggle" aria-label="Open chat" data-open-chat>
    <svg viewBox="0 0 24 24" width="26" height="26"><path fill="currentColor" d="M4 4h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H8l-4 4V6a2 2 0 0 1 2-2z"/></svg>
  </button>

  <div class="chat" id="chat" hidden>
    <header class="chat-head">
      <div><b>Aeline Assistant</b><small>Usually replies in seconds</small></div>
      <button class="chat-close" aria-label="Close chat">×</button>
    </header>
    <div class="chat-log" id="chat-log" aria-live="polite"></div>
    <form class="chat-form" id="chat-form">
      <input id="chat-input" type="text" placeholder="Ask us anything…" autocomplete="off" maxlength="2000" required>
      <button type="submit" aria-label="Send">➤</button>
    </form>
  </div>

  <script src="assets/chat.js"></script>
</body>
</html>
