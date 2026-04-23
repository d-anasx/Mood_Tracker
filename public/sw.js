self.addEventListener('push', function(event) {
    let data = {};
    
    if (event.data) {
        try {
            data = event.data.json();
        } catch (e) {
            data = {
                title: 'MoodTrace',
                body: event.data.text()
            };
        }
    }
    
    const options = {
        body: data.body || 'Nouvelle notification',
        icon: '{{asset(assets/Mood_Tracker.png)}}',
        vibrate: [200, 100, 200],
        data: {
            url: data.url || '/dashboard'
        }
    };
    
    event.waitUntil(
        self.registration.showNotification(data.title || 'MoodTrace', options)
    );
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    
    const urlToOpen = event.notification.data?.url || '/';
    
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then(windowClients => {
                for (let client of windowClients) {
                    if (client.url === urlToOpen && 'focus' in client) {
                        return client.focus();
                    }
                }
                if (clients.openWindow) {
                    return clients.openWindow(urlToOpen);
                }
            })
    );
});