// Import Laravel Echo, the frontend library for listening to broadcasted events
import Echo from 'laravel-echo';

// Import Pusher JS client library (Reverb is Pusher-compatible)
import Pusher from 'pusher-js';

// Make Pusher globally available, required by Laravel Echo
window.Pusher = Pusher;

// Create a new Echo instance configured to connect to Reverb
const echo = new Echo({
    // Use Pusher protocol (Reverb is compatible with Pusher)
    broadcaster: 'pusher', 
    
    // Your public Reverb app key, loaded from environment variables
    key: import.meta.env.VITE_REVERB_APP_KEY,
    
    // WebSocket host (IP or domain), remove any quotes
    wsHost: import.meta.env.VITE_REVERB_HOST.replace(/['"]+/g, ''),
    
    // WebSocket port for ws:// (non-secure) connection
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
    
    // WebSocket port for wss:// (secure) connection
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
    
    // Force secure connection if scheme is https
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    
    // Disable Pusher stats reporting (not needed for Reverb)
    disableStats: true,
    
    // Only enable WebSocket connections (no long-polling)
    enabledTransports: ['ws', 'wss'],
    
    // Pusher requires a cluster option, but Reverb ignores it
    // Must provide a non-empty string to avoid errors
    cluster: 'mt1'
});

// Export the configured Echo instance for use in other JS files
export default echo;
