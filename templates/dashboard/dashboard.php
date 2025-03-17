<?php
include(ROOT_PATH . '/templates/layout/header.php');
?>

<head>
    <link rel="stylesheet" href="static/css/variable.css">
    <link rel="stylesheet" href="static/css/dashboard.css">
</head>

<div class="dashboard">
    <div class="dashboard-header">
        <h1>Tableau de bord</h1>
    </div>

    <div class="dashboard-content">
        <?php if (isset($userPermissions['GERER_ENTREPRISES']) && $userPermissions['GERER_ENTREPRISES']): ?>
        <div class="card">
            <h2>Créer une entreprise</h2>
            <form action="index.php?route=traiter_entreprise" method="POST" class="form-entreprise">
                <input type="hidden" name="action" value="create">
                <div class="form-group">
                    <label for="nom">Nom de l'entreprise</label>
                    <input type="text" id="nom" name="nom" required>
                </div>

                <div class="form-group">
                    <label for="description">Description de l'entreprise</label>
                    <textarea id="description" name="description" rows="4" required></textarea>
                </div>

                <div class="form-group">
                    <label for="email">Email de contact</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="telephone">Téléphone de contact</label>
                    <input type="tel" id="telephone" name="telephone" pattern="[0-9]{10}" required>
                    <small>Format : 0123456789</small>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit">Créer l'entreprise</button>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <?php if (isset($userPermissions['CREER_OFFRE']) && $userPermissions['CREER_OFFRE']): ?>
        <div class="card">
            <h2>Créer une offre</h2>
            <form action="index.php?route=traiter_offre" method="POST" class="form-offre">
                <input type="hidden" name="action" value="create">
                
                <div class="form-group">
                    <label for="entreprise_id">Entreprise*</label>
                    <select id="entreprise_id" name="entreprise_id" required>
                        <option value="">Sélectionnez une entreprise</option>
                        <?php foreach ($entreprises as $entreprise): ?>
                        <option value="<?= $entreprise['id'] ?>"><?= htmlspecialchars($entreprise['nom']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="titre">Titre de l'offre*</label>
                    <input type="text" id="titre" name="titre" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description*</label>
                    <textarea id="description" name="description" rows="4" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="base_remuneration">Rémunération (€)*</label>
                    <input type="number" id="base_remuneration" name="base_remuneration" step="0.01" required>
                </div>
                
                <div class="form-group">
                    <label for="date_debut">Date de début</label>
                    <input type="date" id="date_debut" name="date_debut">
                </div>
                
                <div class="form-group">
                    <label for="date_fin">Date de fin</label>
                    <input type="date" id="date_fin" name="date_fin">
                </div>
                
                <div class="form-group">
                    <label>Compétences requises*</label>
                    <div class="checkbox-grid">
                        <?php if (empty($competences)): ?>
                            <p style="grid-column: 1 / -1; text-align: center;">
                                Aucune compétence disponible. Veuillez en ajouter d'abord.
                            </p>
                        <?php else: ?>
                            <?php foreach ($competences as $competence): ?>
                            <div class="checkbox-item">
                                <input type="checkbox" 
                                       id="comp_<?= $competence['id'] ?>" 
                                       name="competences[]" 
                                       value="<?= $competence['id'] ?>">
                                <label for="comp_<?= $competence['id'] ?>"><?= htmlspecialchars($competence['nom']) ?></label>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <p class="info-text">Sélectionnez au moins une compétence requise pour l'offre.</p>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn-submit">Créer l'offre</button>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include(ROOT_PATH . '/templates/layout/footer.php'); ?>