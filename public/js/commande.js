fetch('/commandes', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: 'items=' + encodeURIComponent(JSON.stringify(items)) // "items" = votre payload JS
})
.then(response => response.text().then(text => {
    if (!response.ok) {
        console.log('Erreur serveur :', text); // Affiche la réponse PHP dans la console JS
    } else {
        // Traitement normal si succès
    }
}));
