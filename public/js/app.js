document.addEventListener('DOMContentLoaded', () => {
    console.log('App.js loaded');
    
    // Auto-dismiss des alertes après 5 secondes
    setTimeout(() => {
        document.querySelectorAll('.alert.show').forEach(el => {
            bootstrap.Alert.getOrCreateInstance(el).close();
        });
    }, 5000);

    // Animation des cartes avec délais
    document.querySelectorAll('.card').forEach((card, i) => {
        card.style.animation = `fadeInUp .6s ${(i + 1) * 0.1}s both`;
    });
});
