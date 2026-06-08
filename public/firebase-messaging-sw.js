// Service Worker Firebase Messaging — gère les notifications en arrière-plan
importScripts('https://www.gstatic.com/firebasejs/10.12.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.12.0/firebase-messaging-compat.js');

// Ces valeurs sont injectées au moment de l'enregistrement du SW (voir usePushNotifications.ts)
// Elles doivent correspondre exactement à celles du projet Firebase
self.addEventListener('message', (event) => {
  if (event.data?.type === 'FIREBASE_CONFIG') {
    firebase.initializeApp(event.data.config);
    const messaging = firebase.messaging();

    // Notification reçue quand l'app est en arrière-plan
    messaging.onBackgroundMessage((payload) => {
      const { title, body, icon } = payload.notification ?? {};
      self.registration.showNotification(title ?? 'SmartSMS', {
        body:    body   ?? '',
        icon:    icon   ?? '/icon-192x192.png',
        badge:          '/badge-72x72.png',
        data:    payload.data ?? {},
        actions: [{ action: 'open', title: 'Ouvrir' }],
      });
    });
  }
});

// Clic sur la notification → ouvre / focus l'onglet SmartSMS
self.addEventListener('notificationclick', (event) => {
  event.notification.close();
  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then((list) => {
      const existing = list.find((c) => c.url.includes(self.location.origin));
      if (existing) return existing.focus();
      return clients.openWindow('/dashboard');
    })
  );
});
