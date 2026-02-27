<aside id="adminSidebar"
    class="bg-white border-r border-slate-200 flex flex-col
              lg:sticky lg:top-0 lg:h-screen lg:overflow-y-auto">
    <div class="px-4 py-4 border-b border-slate-200">
        <div class="text-xs text-slate-500 sb-brand-sub">PROCESOS-DEV</div>
        <div class="text-[15px] font-semibold truncate">Administrador</div>
    </div>

    <nav class="px-2 py-2 space-y-1 flex-1">
        <a href="<?= $hrefDashboard ?>"
            class="flex items-center gap-3 px-3 py-2 rounded-xl <?= acls($active === 'dashboard') ?>">
            <span class="sb-ico w-6 text-center">⌂</span>
            <span class="sb-label">Dashboard</span>
        </a>

        <a href="<?= $hrefPac ?>"
            class="flex items-center gap-3 px-3 py-2 rounded-xl <?= acls($active === 'pac') ?>">
            <span class="sb-ico w-6 text-center">▦</span>
            <span class="sb-label">PAC</span>
        </a>

        <a href="<?= $hrefProcesos ?>"
            class="flex items-center gap-3 px-3 py-2 rounded-xl <?= acls($active === 'procesos') ?>">
            <span class="sb-ico w-6 text-center">≡</span>
            <span class="sb-label">Procesos</span>
        </a>

        <a href="<?= $hrefPresupuesto ?>"
            class="flex items-center gap-3 px-3 py-2 rounded-xl <?= acls($active === 'presupuesto') ?>">
            <span class="sb-ico w-6 text-center">≡</span>
            <span class="sb-label">Presupuesto</span>
        </a>
    </nav>

    <div class="sb-footer border-t border-slate-200 p-3">
        <div class="flex items-center gap-3">
            <div class="h-9 w-9 rounded-xl bg-slate-900 text-white flex items-center justify-center text-sm font-semibold">
                <?= strtoupper(substr((string)$adminUser, 0, 1)) ?>
            </div>

            <div class="min-w-0 flex-1">
                <div class="text-sm font-semibold text-slate-900 truncate">
                    <?= htmlspecialchars($adminUser) ?>
                </div>
                <div class="text-xs text-slate-500 truncate">Sesión activa</div>
            </div>

            <a href="<?= $hrefLogout ?>"
                class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-medium hover:bg-slate-50">
                Salir
            </a>
        </div>
    </div>
</aside>