// Dashboard functionality
document.addEventListener('DOMContentLoaded', function() {
    console.log('Dashboard loaded successfully');
    
    // Add smooth scroll behavior
    document.documentElement.style.scrollBehavior = 'smooth';
    
    // Initialize dashboard
    initializeDashboard();
});

function initializeDashboard() {
    // Add hover effects to cards
    const cards = document.querySelectorAll('.nav-card, .stats-card');
    
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-4px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    // Profile icon click handler
    const profileIcon = document.querySelector('.profile-icon');
    profileIcon.addEventListener('click', function() {
        showProfileMenu();
    });
}

function handleCardClick(cardType) {
    const card = event.currentTarget;
    
    // Add loading state
    card.classList.add('loading');
    
    // Simulate navigation delay
    setTimeout(() => {
        card.classList.remove('loading');
        navigateToSection(cardType);
    }, 800);
}

function navigateToSection(section) {
    const messages = {
        'formations': 'Navigation vers Mes Formations...',
        'lesson': 'Ouverture de l\'éditeur de leçon...',
        'students': 'Chargement de la liste des étudiants...',
        'planning': 'Affichage du planning...'
    };
    
    showNotification(messages[section] || 'Navigation en cours...');
    
    // Here you would typically navigate to the actual page
    console.log(`Navigating to: ${section}`);
}

function showProfileMenu() {
    const menu = document.createElement('div');
    menu.className = 'profile-menu';
    menu.innerHTML = `
        <div class="menu-item" onclick="editProfile()">
            <i class="fas fa-user"></i> Modifier le profil
        </div>
        <div class="menu-item" onclick="showSettings()">
            <i class="fas fa-cog"></i> Paramètres
        </div>
        <div class="menu-item" onclick="logout()">
            <i class="fas fa-sign-out-alt"></i> Déconnexion
        </div>
    `;
    
    // Position menu
    const profileIcon = document.querySelector('.profile-icon');
    const rect = profileIcon.getBoundingClientRect();
    menu.style.position = 'fixed';
    menu.style.top = `${rect.bottom + 10}px`;
    menu.style.right = `${window.innerWidth - rect.right}px`;
    menu.style.background = 'white';
    menu.style.borderRadius = '8px';
    menu.style.boxShadow = '0 4px 20px rgba(0, 0, 0, 0.15)';
    menu.style.padding = '0.5rem 0';
    menu.style.minWidth = '180px';
    menu.style.zIndex = '1000';
    
    // Add menu styles
    const style = document.createElement('style');
    style.textContent = `
        .profile-menu .menu-item {
            padding: 0.75rem 1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: background 0.2s ease;
        }
        .profile-menu .menu-item:hover {
            background: #f8fafc;
        }
        .profile-menu .menu-item i {
            width: 16px;
            color: #6b7280;
        }
    `;
    document.head.appendChild(style);
    
    document.body.appendChild(menu);
    
    // Close menu when clicking outside
    setTimeout(() => {
        document.addEventListener('click', function closeMenu(e) {
            if (!menu.contains(e.target) && !profileIcon.contains(e.target)) {
                menu.remove();
                style.remove();
                document.removeEventListener('click', closeMenu);
            }
        });
    }, 100);
}

function editProfile() {
    showNotification('Ouverture de l\'éditeur de profil...');
    document.querySelector('.profile-menu')?.remove();
}

function showSettings() {
    showNotification('Chargement des paramètres...');
    document.querySelector('.profile-menu')?.remove();
}

function logout() {
    showNotification('Déconnexion en cours...');
    document.querySelector('.profile-menu')?.remove();
    
    // Simulate logout
    setTimeout(() => {
        alert('Vous avez été déconnecté avec succès');
    }, 1000);
}

function showNotification(message) {
    // Remove existing notification
    const existing = document.querySelector('.notification');
    if (existing) existing.remove();
    
    const notification = document.createElement('div');
    notification.className = 'notification';
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        z-index: 1000;
        animation: slideIn 0.3s ease;
    `;
    
    // Add animation
    const style = document.createElement('style');
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
    `;
    document.head.appendChild(style);
    
    document.body.appendChild(notification);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideIn 0.3s ease reverse';
        setTimeout(() => {
            notification.remove();
            style.remove();
        }, 300);
    }, 3000);
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey || e.metaKey) {
        switch(e.key) {
            case '1':
                e.preventDefault();
                handleCardClick('formations');
                break;
            case '2':
                e.preventDefault();
                handleCardClick('lesson');
                break;
            case '3':
                e.preventDefault();
                handleCardClick('students');
                break;
            case '4':
                e.preventDefault();
                handleCardClick('planning');
                break;
        }
    }
});

// Add some sample data animation
function animateStats() {
    const summaryText = document.querySelector('.summary-text');
    if (summaryText) {
        let formations = 0;
        let students = 0;
        
        const interval = setInterval(() => {
            formations = Math.min(formations + 1, 5);
            students = Math.min(students + 3, 42);
            
            summaryText.innerHTML = `Vous animez actuellement <span class="highlight">${formations} formations</span> avec <span class="highlight">${students} étudiants</span> au total.`;
            
            if (formations >= 5 && students >= 42) {
                clearInterval(interval);
            }
        }, 200);
    }
}
  function handleCardClick(section) {
    if(section === 'students') {
        window.location.href = 'mes_etudiants.php'; // page qui liste les étudiants
    }
}

// Uncomment to see animated stats
// setTimeout(animateStats, 2000);