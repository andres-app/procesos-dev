<header class="sticky top-0 z-30 bg-white/90 backdrop-blur border-b border-slate-200">
  <div class="px-4 lg:px-6 py-2.5 flex items-center gap-3">

    <!-- ÚNICO botón: hamburguesa -->
    <button id="btnToggle"
      class="rounded-xl border border-slate-200 px-3 py-2 text-base hover:bg-slate-50"
      type="button"
      aria-label="Abrir menú">
      ☰
    </button>

    <div class="flex-1 header-title">
      <div class="text-xs text-slate-500 leading-none">Admin</div>
      <div class="text-[15px] font-semibold truncate leading-tight">
        <?= htmlspecialchars($titulo) ?>
      </div>
    </div>

    <div class="flex items-center gap-2">

      <!-- Notificaciones -->
      <button class="relative rounded-xl border border-slate-200 px-3 py-2 hover:bg-slate-50"
        title="Notificaciones">
        🔔
        <span class="absolute -top-1 -right-1 bg-rose-500 text-white text-[10px] px-1.5 rounded-full">
          3
        </span>
      </button>

      <!-- Tipo de cambio -->
      <button class="rounded-xl border border-slate-200 px-3 py-2 hover:bg-slate-50"
        title="Tipo de cambio">
        💱
      </button>

      <!-- Configuración -->
      <button class="rounded-xl border border-slate-200 px-3 py-2 hover:bg-slate-50"
        title="Configuración">
        ⚙️
      </button>

      <!-- Usuario dropdown simple -->
      <div class="relative group">
        <button class="h-9 w-9 rounded-xl bg-slate-900 text-white flex items-center justify-center text-xs font-semibold">
          <?= strtoupper(substr((string)$adminUser, 0, 1)) ?>
        </button>

        <div class="hidden group-hover:block absolute right-0 mt-2 w-40 bg-white border border-slate-200 rounded-xl shadow-lg">
          <div class="px-4 py-2 text-sm text-slate-700 truncate">
            <?= htmlspecialchars($adminUser) ?>
          </div>
          <div class="border-t border-slate-200"></div>
          <a href="<?= $hrefLogout ?>"
            class="block px-4 py-2 text-sm hover:bg-slate-50">
            Cerrar sesión
          </a>
        </div>
      </div>

    </div>
  </div>
</header>