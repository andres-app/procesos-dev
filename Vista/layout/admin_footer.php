      </main>
    </div>
  </div>

  <script>
  (function(){
    const btn = document.getElementById('btnToggle');
    const overlay = document.getElementById('overlay');
    const isDesktop = () => window.innerWidth >= 1024;

    if (!btn) return;

    btn.addEventListener('click', () => {
      if (isDesktop()){
        // Desktop: rail
        document.body.classList.toggle('sb-rail');
      } else {
        // Mobile: drawer
        document.body.classList.toggle('sb-open');
        overlay?.classList.toggle('hidden');
      }
    });

    overlay?.addEventListener('click', () => {
      document.body.classList.remove('sb-open');
      overlay.classList.add('hidden');
    });

    window.addEventListener('resize', () => {
      // Normaliza estado al cambiar de tamaño
      if (isDesktop()){
        document.body.classList.remove('sb-open');
        overlay?.classList.add('hidden');
      } else {
        document.body.classList.remove('sb-rail');
      }
    });
  })();
  </script>
</body>
</html>