<?php
$titulo = 'Inicio | Procesos';
$appName = 'Seguimiento de Procesos';
$usuario = 'Andres';
require __DIR__ . '/../layout/header.php';
?>

<main class="page flex-1 px-5 pt-[calc(env(safe-area-inset-top)+24px)] pb-24">

  <div class="grid grid-cols-2 gap-4 w-full max-w-md mx-auto">

    <a href="<?= BASE_URL ?>/procesos"
       class="apple-card bg-blue-100/80 text-blue-800">
      <div class="icon">📂</div>
      <span>Procesos</span>
    </a>

    <a href="<?= BASE_URL ?>/pac"
       class="apple-card bg-slate-100/80 text-slate-800">
      <div class="icon">🗂️</div>
      <span>PAC</span>
    </a>

    <a href="<?= BASE_URL ?>/indicadores"
       class="apple-card bg-emerald-100/80 text-emerald-800">
      <div class="icon">📊</div>
      <span>Indicadores</span>
    </a>

    <a href="<?= BASE_URL ?>/reportes"
       class="apple-card bg-indigo-100/80 text-indigo-800">
      <div class="icon">📈</div>
      <span>Reportes</span>
    </a>

    <a href="<?= BASE_URL ?>/presupuesto"
       class="apple-card bg-amber-100/80 text-amber-800">
      <div class="icon">💳</div>
      <span>Presupuesto</span>
    </a>

    <a href="<?= BASE_URL ?>/alertas"
       class="apple-card bg-rose-100/80 text-rose-800">
      <div class="icon">🔔</div>
      <span>Alertas</span>
    </a>

  </div>

</main>

<?php require __DIR__ . '/../layout/bottom-nav.php'; ?>

<style>

/* ===== APPLE CARDS ===== */

.apple-card{
  aspect-ratio:1/1;
  border-radius:1.6rem;
  display:flex;
  flex-direction:column;
  align-items:center;
  justify-content:center;
  text-align:center;
  font-weight:600;
  padding:16px;

  backdrop-filter:blur(10px);

  box-shadow:
  0 10px 25px rgba(0,0,0,.12),
  inset 0 1px 0 rgba(255,255,255,.55);

  transition:transform .18s ease, box-shadow .18s ease;
}

/* icon */
.apple-card .icon{
  font-size:2.8rem;
  margin-bottom:.45rem;
}

/* texto */
.apple-card span{
  font-size:.95rem;
  letter-spacing:.2px;
}

/* efecto tap */
.apple-card:active{
  transform:scale(.96);
  box-shadow:
  0 6px 16px rgba(0,0,0,.18);
}

/* mejor espacio en pantallas grandes */
@media (min-width:640px){

  .grid{
    gap:18px;
  }

}

</style>