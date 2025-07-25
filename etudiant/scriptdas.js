// Navigation function
function navigateTo(section) {
    console.log(`Navigating to: ${section}`);
    
    // Add visual feedback
    const cards = document.querySelectorAll('.nav-card');
    cards.forEach(card => {
        card.style.transform = 'scale(1)';
    });
    
    // Find the clicked card and add animation
    event.currentTarget.style.transform = 'scale(0.98)';
    
    setTimeout(() => {
        event.currentTarget.style.transform = 'scale(1)';
    }, 150);
    
    // Here you would typically handle routing or show different content
    switch(section) {
        case 'courses':
            showNotification('Accès aux cours en cours de développement');
            break;
        case 'planning':
            showNotification('Fonctionnalité de planning bientôt disponible');
            break;
        case 'progression':
            showNotification('Suivi des progrès en cours de développement');
            break;
        case 'profil':
            showNotification('Gestion du profil bientôt disponible');
            break;
        default:
            showNotification('Section non trouvée');
    }
}

// Notification system
function showNotification(message) {
    // Remove existing notification
    const existingNotification = document.querySelector('.notification');
    if (existingNotification) {
        existingNotification.remove();
    }
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = 'notification';
    notification.textContent = message;
    
    // Add notification styles
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background-color: #3b82f6;
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        font-size: 14px;
        max-width: 300px;
        animation: slideIn 0.3s ease-out;
    `;
    
    // Add animation keyframes
    if (!document.querySelector('#notification-styles')) {
        const style = document.createElement('style');
        style.id = 'notification-styles';
        style.textContent = `
            @keyframes slideIn {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            
            @keyframes slideOut {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(100%);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    }
    
    document.body.appendChild(notification);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-in';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 300);
    }, 3000);
}

// Add hover effects for better interactivity
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.nav-card');
    
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    // Add click animation to avatar
    const avatar = document.querySelector('.avatar');
    avatar.addEventListener('click', function() {
        showNotification('Profil utilisateur - Fonctionnalité bientôt disponible');
    });
    
    avatar.style.cursor = 'pointer';
});

// Add keyboard navigation support
document.addEventListener('keydown', function(event) {
    if (event.key === 'Enter' || event.key === ' ') {
        const focusedElement = document.activeElement;
        if (focusedElement.classList.contains('nav-card')) {
            focusedElement.click();
            event.preventDefault();
        }
    }
});

// Make cards focusable for accessibility
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.nav-card');
    cards.forEach((card, index) => {
        card.setAttribute('tabindex', '0');
        card.setAttribute('role', 'button');
        card.setAttribute('aria-label', `Naviguer vers ${card.querySelector('h3').textContent}`);
    });
});