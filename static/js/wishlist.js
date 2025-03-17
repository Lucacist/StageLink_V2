function toggleLikeSimple(button, offreId) {
    // Empêcher la propagation de l'événement
    event.preventDefault();
    event.stopPropagation();

    // Référence au SVG
    const svg = button.querySelector('svg');
    
    // Vérifier si le bouton a déjà la classe "liked"
    const isLiked = button.classList.contains('liked');
    
    // Changer visuellement l'état immédiatement (pour une réponse instantanée)
    if (isLiked) {
        button.classList.remove('liked');
        svg.setAttribute('fill', 'none');
        svg.setAttribute('stroke', '#000000');
    } else {
        button.classList.add('liked');
        svg.setAttribute('fill', 'red');
        svg.setAttribute('stroke', 'red');
    }
    
    // Envoyer la requête AJAX pour la persistance en BDD
    fetch('index.php?route=like', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'offre_id=' + offreId
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Erreur réseau');
        }
        // Utiliser text() au lieu de json() pour pouvoir inspecter en cas d'erreur
        return response.text(); 
    })
    .then(text => {
        try {
            // Essayer de parser le JSON
            const data = JSON.parse(text);
            console.log('Réponse du serveur:', data);
            
            // Si l'état retourné est différent de l'état supposé, corriger l'interface
            if (data.liked !== !isLiked) {
                console.log('Correction de l\'interface...');
                if (data.liked) {
                    button.classList.add('liked');
                    svg.setAttribute('fill', 'red');
                    svg.setAttribute('stroke', 'red');
                } else {
                    button.classList.remove('liked');
                    svg.setAttribute('fill', 'none');
                    svg.setAttribute('stroke', '#000000');
                }
            }
        } catch (e) {
            console.error('Erreur de parsing JSON:', e);
            console.log('Contenu reçu:', text);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        // En cas d'erreur, remettre l'état d'origine
        if (isLiked) {
            button.classList.add('liked');
            svg.setAttribute('fill', 'red');
            svg.setAttribute('stroke', 'red');
        } else {
            button.classList.remove('liked');
            svg.setAttribute('fill', 'none');
            svg.setAttribute('stroke', '#000000');
        }
    });
}

function toggleLike(event, button, offreId) {
    // Empêcher la propagation pour éviter de suivre le lien parent
    event.preventDefault();
    event.stopPropagation();
    
    // Utiliser notre script de débogage
    fetch('debug-like.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'offre_id=' + offreId
    })
    .then(response => {
        console.log("Réponse brute:", response);
        return response.text();
    })
    .then(text => {
        console.log("Texte de réponse:", text);
        try {
            return JSON.parse(text);
        } catch (e) {
            console.error("Erreur de parsing JSON:", e);
            console.log("Contenu non-JSON reçu:", text);
            throw new Error("Réponse invalide du serveur");
        }
    })
    .then(data => {
        console.log("Données JSON:", data);
        
        if (data && data.success) {
            // Mettre à jour l'interface utilisateur directement
            const svg = button.querySelector('svg');
            
            if (data.liked) {
                // Appliquer le style directement sur le SVG
                button.classList.add('liked');
                svg.setAttribute('fill', 'red');
                svg.setAttribute('stroke', 'red');
                console.log('Coeur liké - attributs appliqués');
            } else {
                // Retirer le style
                button.classList.remove('liked');
                svg.setAttribute('fill', 'none');
                svg.setAttribute('stroke', '#000000');
                console.log('Coeur non liké - attributs retirés');
            }
        } else if (data) {
            console.error('Erreur:', data.message);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
    });
}
