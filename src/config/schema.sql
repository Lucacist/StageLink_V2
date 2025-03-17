CREATE DATABASE StageLink;
USE StageLink;

-- Table des rôles
CREATE TABLE Roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    nom VARCHAR(100) NOT NULL,
    description TEXT
);

-- Table des utilisateurs
CREATE TABLE Utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(100) NOT NULL,
    role_id INT NOT NULL,
    FOREIGN KEY (role_id) REFERENCES Roles(id)
);

-- Table des permissions (définit toutes les actions possibles dans l'application)
CREATE TABLE Permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    CONSTRAINT valid_permission CHECK (
        code IN (
            -- Permissions liées aux offres
            'VOIR_OFFRE',
            'CREER_OFFRE',
            'MODIFIER_OFFRE',
            'SUPPRIMER_OFFRE',
            
            -- Permissions liées aux entreprises
            'VOIR_ENTREPRISE',
            'GERER_ENTREPRISES',
            'EVALUER_ENTREPRISE',
            
            -- Permissions liées aux utilisateurs
            'GERER_UTILISATEURS',
            
            -- Permissions liées aux candidatures
            'VOIR_CANDIDATURES',
            'GERER_CANDIDATURES',
            
            -- Permissions spéciales admin
            'ACCES_ADMIN'
        )
    )
);

-- Table de liaison Roles-Permissions 
CREATE TABLE Role_Permissions (
    role_id INT NOT NULL,
    permission_id INT NOT NULL,
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES Roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES Permissions(id) ON DELETE CASCADE
);

-- Table des entreprises
CREATE TABLE Entreprises (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    email VARCHAR(100) UNIQUE NOT NULL,
    telephone VARCHAR(20) NOT NULL
);

-- Table des évaluations des entreprises
CREATE TABLE Evaluations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    entreprise_id INT NOT NULL,
    utilisateur_id INT NOT NULL,
    note INT CHECK (note BETWEEN 1 AND 5),
    commentaire TEXT,
    FOREIGN KEY (entreprise_id) REFERENCES Entreprises(id) ON DELETE CASCADE,
    FOREIGN KEY (utilisateur_id) REFERENCES Utilisateurs(id) ON DELETE CASCADE
);

-- Table des offres de stage
CREATE TABLE Offres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    entreprise_id INT NOT NULL,
    titre VARCHAR(100) NOT NULL,
    description TEXT,
    base_remuneration DECIMAL(10,2),
    date_debut DATE,
    date_fin DATE,
    FOREIGN KEY (entreprise_id) REFERENCES Entreprises(id) ON DELETE CASCADE
);

-- Table des compétences
CREATE TABLE Competences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) UNIQUE NOT NULL
);

-- Relation entre Offres et Compétences
CREATE TABLE Offres_Competences (
    offre_id INT NOT NULL,
    competence_id INT NOT NULL,
    PRIMARY KEY (offre_id, competence_id),
    FOREIGN KEY (offre_id) REFERENCES Offres(id) ON DELETE CASCADE,
    FOREIGN KEY (competence_id) REFERENCES Competences(id) ON DELETE CASCADE
);

-- Table des candidatures
CREATE TABLE Candidatures (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    offre_id INT NOT NULL,
    date_candidature DATE NOT NULL,
    cv VARCHAR(100) NOT NULL,
    lettre_motivation TEXT,
    FOREIGN KEY (utilisateur_id) REFERENCES Utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (offre_id) REFERENCES Offres(id) ON DELETE CASCADE
);

-- Table de la wish-list des offres
CREATE TABLE WishList (
    utilisateur_id INT NOT NULL,
    offre_id INT NOT NULL,
    PRIMARY KEY (utilisateur_id, offre_id),
    FOREIGN KEY (utilisateur_id) REFERENCES Utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (offre_id) REFERENCES Offres(id) ON DELETE CASCADE
);

-- Insertion des rôles par défaut
INSERT INTO Roles (code, nom, description) VALUES
('ADMIN', 'Administrateur', 'Accès complet au système'),
('PILOTE', 'Pilote', 'Gestion des offres et des entreprises'),
('ETUDIANT', 'Étudiant', 'Accès aux offres et entreprises');

-- Insertion des permissions
INSERT INTO Permissions (code, nom, description) VALUES
('VOIR_OFFRE', 'Voir les offres', 'Permet de voir les offres de stage'),
('CREER_OFFRE', 'Créer une offre', 'Permet de créer une offre de stage'),
('MODIFIER_OFFRE', 'Modifier une offre', 'Permet de modifier une offre de stage'),
('SUPPRIMER_OFFRE', 'Supprimer une offre', 'Permet de supprimer une offre de stage'),
('VOIR_ENTREPRISE', 'Voir les entreprises', 'Permet de voir les entreprises'),
('GERER_ENTREPRISES', 'Gérer les entreprises', 'Permet de gérer les entreprises'),
('EVALUER_ENTREPRISE', 'Évaluer une entreprise', 'Permet d\'évaluer une entreprise'),
('GERER_UTILISATEURS', 'Gérer les utilisateurs', 'Permet de gérer les utilisateurs'),
('VOIR_CANDIDATURES', 'Voir les candidatures', 'Permet de voir les candidatures'),
('GERER_CANDIDATURES', 'Gérer les candidatures', 'Permet de gérer les candidatures'),
('ACCES_ADMIN', 'Accès admin', 'Permet d\'accéder à l\'interface d\'administration');

-- Admin : toutes les permissions
INSERT INTO Role_Permissions (role_id, permission_id)
SELECT r.id, p.id 
FROM Roles r, Permissions p 
WHERE r.code = 'ADMIN';

-- Pilote : gestion des offres, entreprises, et candidatures
INSERT INTO Role_Permissions (role_id, permission_id)
SELECT r.id, p.id
FROM Roles r, Permissions p
WHERE r.code = 'PILOTE'
AND p.code IN (
    'VOIR_OFFRE',
    'CREER_OFFRE',
    'MODIFIER_OFFRE',
    'SUPPRIMER_OFFRE',
    'VOIR_ENTREPRISE',
    'GERER_ENTREPRISES',
    'VOIR_CANDIDATURES',
    'GERER_CANDIDATURES',
    'GERER_UTILISATEURS'
);

-- Étudiant : voir les offres et entreprises, postuler et évaluer
INSERT INTO Role_Permissions (role_id, permission_id)
SELECT r.id, p.id
FROM Roles r, Permissions p
WHERE r.code = 'ETUDIANT'
AND p.code IN ('VOIR_OFFRE', 'VOIR_ENTREPRISE', 'EVALUER_ENTREPRISE', 'VOIR_CANDIDATURES');

-- Mettre à jour les utilisateurs existants pour leur donner un rôle par défaut (ETUDIANT)
UPDATE Utilisateurs SET role_id = (SELECT id FROM Roles WHERE code = 'ETUDIANT');
