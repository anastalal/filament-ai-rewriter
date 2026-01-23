import Alpine from 'alpinejs';

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    Alpine.data('aiRewriter', () => ({
        isRewriting: false,
        
        init() {
            // Listen for global events
            this.$wire.on('ai-rewrite-success', (event) => {
                this.showNotification(event.message, 'success');
                this.isRewriting = false;
            });
            
            this.$wire.on('ai-rewrite-error', (event) => {
                this.showNotification(event.message, 'error');
                this.isRewriting = false;
            });
        },
        
        rewrite(statePath, style) {
            this.isRewriting = true;
            this.$wire.dispatch('ai-rewrite::' + statePath, style);
        },
        
        showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 px-4 py-2 rounded-lg shadow-lg ${
                type === 'success' 
                    ? 'bg-green-500 text-white' 
                    : 'bg-red-500 text-white'
            }`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.classList.add('opacity-0', 'transition-opacity', 'duration-300');
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }
    }));
});

Alpine.start();