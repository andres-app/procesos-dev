self.addEventListener('install', event => {
    event.waitUntil(
      caches.open('Seguimiento de procesos-v1').then(cache => {
        return cache.addAll([
          '/public/dashboard',
          '/public/gastos',
          '/public/css/app.css',
          '/public/js/app.js'
        ]);
      })
    );
  });
  
  self.addEventListener('fetch', event => {
    event.respondWith(
      caches.match(event.request).then(response => {
        return response || fetch(event.request);
      })
    );
  });
  