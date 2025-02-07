self.addEventListener('push', function(event) {
    const options = {
        body: event.data.text(),
        icon: 'resources/images/logo.png',
        badge: 'resources/images/logo.png',
        vibrate: [200, 100, 200],
        data: {
            dateOfArrival: Date.now(),
            primaryKey: 1
        }
    };

    event.waitUntil(
        self.registration.showNotification('SINCO CAFE Order Update', options)
    );
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    event.waitUntil(
        clients.openWindow('/')
    );
});
