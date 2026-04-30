<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin | Login</title>

<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
/* PRELOADER */
#preloader {
    position: fixed;
    inset: 0;
    z-index: 99999;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255,255,255,0.4);
    backdrop-filter: blur(6px);
    transition: opacity .4s ease;
}
.loader-hidden { opacity: 0; pointer-events: none; }

.loader {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    border: 3px solid transparent;
    border-top-color: #7A0C19;
    animation: spin 1s linear infinite;
}
@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>
</head>

<body class="bg-slate-50 h-screen flex font-sans">

<!-- PRELOADER -->
<div id="preloader">
    <div class="loader"></div>
</div>

<!-- PANEL IZQUIERDO -->
<div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-[#2a0303] via-[#4c0505] to-[#7A0C19] items-center justify-center relative overflow-hidden">
    
    <div class="absolute w-96 h-96 bg-white/5 rounded-full -top-40 -left-40"></div>
    <div class="absolute w-96 h-96 bg-red-500/10 rounded-full -bottom-40 -right-40"></div>

    <div class="relative z-10 text-center px-10">
        <div class="w-20 h-20 bg-white rounded-2xl mx-auto mb-6 flex items-center justify-center shadow-xl">
            <span class="text-2xl font-bold text-[#7A0C19]">ADM</span>
        </div>

        <h1 class="text-4xl font-bold text-white mb-3 tracking-tight">
            Panel Administrativo
        </h1>

        <p class="text-red-200 text-sm max-w-md mx-auto">
            Accede al sistema de gestión de procesos y controla la información institucional.
        </p>
    </div>
</div>

<!-- PANEL DERECHO -->
<div class="w-full lg:w-1/2 flex items-center justify-center bg-white p-8">

    <div class="w-full max-w-md">

        <div class="mb-8">
            <div class="text-xs text-slate-400 uppercase tracking-widest">PROCESOS-DEV</div>
            <h2 class="text-3xl font-semibold text-slate-800">Acceso Administrador</h2>
            <p class="text-slate-500 text-sm mt-1">Ingresa tus credenciales</p>
        </div>

        <?php if (!empty($err)): ?>
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl p-3">
                <?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form method="post" id="loginForm" class="space-y-5">

            <div>
                <label class="text-sm text-slate-600 block mb-1">Usuario o correo</label>
                <input 
                    name="user"
                    value="<?= htmlspecialchars($_POST['user'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-[#7A0C19]/20 focus:border-[#7A0C19] outline-none transition"
                    required>
            </div>

            <div>
                <label class="text-sm text-slate-600 block mb-1">Contraseña</label>
                <input 
                    type="password"
                    name="pass"
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-[#7A0C19]/20 focus:border-[#7A0C19] outline-none transition"
                    required>
            </div>

            <button 
                type="submit"
                class="w-full bg-[#7A0C19] text-white py-3 rounded-xl font-medium hover:bg-[#5a0912] transition shadow-md">
                Ingresar
            </button>

            <!-- 2FA -->
            <div class="text-center text-xs text-slate-500 mt-3">
                ¿Primera vez?
                <a href="/public/admin/setup-2fa" class="text-blue-600 hover:underline font-medium">
                    Configura tu autenticador
                </a>
            </div>

        </form>

    </div>
</div>

<script>
(function(){
    const loader = document.getElementById('preloader');
    const form = document.getElementById('loginForm');

    window.addEventListener('load', () => {
        loader.classList.add('loader-hidden');
    });

    setTimeout(() => {
        loader.classList.add('loader-hidden');
    }, 1500);

    if(form){
        form.addEventListener('submit', () => {
            loader.classList.remove('loader-hidden');
        });
    }
})();
</script>

</body>
</html>