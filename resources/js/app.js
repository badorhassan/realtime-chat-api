import './bootstrap';

    Echo.channel(`chat.${userId}`)
    .listen('MessageSent', (e) => {
        console.log('New message:', e.message);

    });