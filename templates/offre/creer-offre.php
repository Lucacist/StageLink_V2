<?php
include(ROOT_PATH . '/templates/layout/header.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>

<div class="container">
    <h1>Créer une nouvelle offre de stage</h1>
    
    <form action="traiter_offre.php" method="POST" class="form-offre">
        <div class="form-group">
            <label for="entreprise_id">Entreprise</label>
            <select id="entreprise_id" name="entreprise_id" required>
                <option value="">Sélectionnez une entreprise</option>
                <?php
                require_once 'config/config.php';
                
                $sql = "SELECT id, nom FROM Entreprises ORDER BY nom";
                $entreprises = $conn->query($sql);
                
                while ($entreprise = $entreprises->fetch_assoc()): 
                ?>
                <option value="<?= $entreprise['id'] ?>"><?= htmlspecialchars($entreprise['nom']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="titre">Titre</label>
            <input type="text" id="titre" name="titre" required>
        </div>
        
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="6" required></textarea>
        </div>
        
        <div class="form-group">
            <label for="competences">Compétences requises</label>
            <input type="text" id="competences" name="competences" placeholder="Ex: PHP, MySQL, JavaScript">
            <p class="info">Séparez les compétences par des virgules</p>
        </div>
        
        <div class="form-group">
            <label for="date_debut">Date de début</label>
            <input type="date" id="date_debut" name="date_debut" required>
        </div>
        
        <div class="form-group">
            <label for="date_fin">Date de fin</label>
            <input type="date" id="date_fin" name="date_fin" required>
        </div>
        
        <div class="form-group">
            <label for="base_remuneration">Rémunération (€ par mois)</label>
            <input type="number" id="base_remuneration" name="base_remuneration" step="0.01" min="0" required>
        </div>
        
        <div class="form-actions">
            <a href="index.php" class="btn-cancel">Annuler</a>
            <button type="submit" class="btn-submit">Créer l'offre</button>
        </div>
    </form>
</div>

<?php include(ROOT_PATH . '/templates/layout/footer.php'); ?>