// Opiam service worker: shows push notifications (action plans due, assigned,
// alerts) and sets the app-icon badge. Opening a notification focuses Opiam.
self.addEventListener('install', function(){ self.skipWaiting(); });
self.addEventListener('activate', function(e){ e.waitUntil(self.clients.claim()); });

self.addEventListener('push', function(e){
  var d = {};
  try { d = e.data ? e.data.json() : {}; } catch (err) { d = { body: e.data ? e.data.text() : '' }; }
  var jobs = [self.registration.showNotification(d.title || 'Opiam', {
    body: d.body || '',
    icon: '/icons/opiam-192.png',
    badge: '/icons/opiam-192.png',
    tag: d.tag || undefined,
    data: { url: d.url || '/projectsmain/index' }
  })];
  if (typeof d.badge === 'number' && self.navigator && self.navigator.setAppBadge) {
    jobs.push(d.badge > 0 ? self.navigator.setAppBadge(d.badge) : self.navigator.clearAppBadge());
  }
  e.waitUntil(Promise.all(jobs).catch(function(){}));
});

self.addEventListener('notificationclick', function(e){
  e.notification.close();
  var url = (e.notification.data && e.notification.data.url) || '/projectsmain/index';
  e.waitUntil(self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function(list){
    for (var i = 0; i < list.length; i++) {
      if ('focus' in list[i]) { list[i].postMessage({ type: 'opiam-open-notifications' }); return list[i].focus(); }
    }
    return self.clients.openWindow(url + (url.indexOf('?') < 0 ? '?' : '&') + 'notif=1');
  }));
});
