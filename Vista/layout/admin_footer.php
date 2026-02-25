      </main>
    </div>
  </div>

  <script>
    const btn = document.getElementById('btnMenu');
    const drawer = document.getElementById('drawer');
    if (btn && drawer) btn.addEventListener('click', () => drawer.classList.toggle('hidden'));
  </script>
</body>
</html>