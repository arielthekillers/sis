// Carousel: <div class="k-carousel"> + .k-slides > .k-slide, tombol .prev .next, wadah .k-dots
document.querySelectorAll('.k-carousel').forEach(c => {
  const s = c.querySelector('.k-slides'), d = c.querySelector('.k-dots'), n = s.children.length;
  let i = 0, t;
  for (let j = 0; j < n; j++) { const b = document.createElement('button'); b.setAttribute('aria-label', 'Slide ' + (j + 1)); b.onclick = () => go(j); d.append(b); }
  const go = x => { i = (x + n) % n; s.style.transform = `translateX(-${i * 100}%)`; [...d.children].forEach((b, j) => b.classList.toggle('on', j === i)); };
  const run = () => t = setInterval(() => go(i + 1), 4500);
  c.querySelector('.prev').onclick = () => go(i - 1);
  c.querySelector('.next').onclick = () => go(i + 1);
  c.onmouseenter = () => clearInterval(t); c.onmouseleave = run; go(0); run();
});
// Aksi klik: data-toggle="#id" (collapse/menu/drawer/popup), data-open="#dialog", data-close, data-tab="#pane"
document.addEventListener('click', e => {
  const q = s => e.target.closest(s);
  let el;
  if (el = q('[data-toggle]')) document.querySelector(el.dataset.toggle).classList.toggle('open');
  if (el = q('[data-open]')) document.querySelector(el.dataset.open).showModal();
  if (el = q('[data-close]')) { const d = el.closest('dialog,.k-drawer,.k-alert'); d.tagName === 'DIALOG' ? d.close() : d.classList.contains('k-alert') ? d.remove() : d.classList.remove('open'); }
  if (el = q('.k-dd>a')) if (innerWidth < 860) { e.preventDefault(); el.parentElement.classList.toggle('open'); }
  if (el = q('[data-tab]')) {
    el.parentElement.querySelectorAll('[data-tab]').forEach(b => { b.classList.remove('on'); document.querySelector(b.dataset.tab).hidden = true; });
    el.classList.add('on'); document.querySelector(el.dataset.tab).hidden = false;
  }
});
