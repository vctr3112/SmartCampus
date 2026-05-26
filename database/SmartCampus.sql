-- création de la base de données
CREATE DATABASE IF NOT EXISTS SmartCampus;
USE SmartCampus;

-- création de la table utilisateurs
CREATE TABLE IF NOT EXISTS Utilisateurs (
    id_utilisateur INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(30) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(100) NOT NULL,
    role VARCHAR(20) NOT NULL
);

-- création de la table etudiant
CREATE TABLE IF NOT EXISTS Etudiant (
    id_etudiant INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
    id_utilisateur INT NOT NULL,
    numero_etudiant INT NOT NULL UNIQUE,
    filiere VARCHAR(50),
    niveau CHAR(2),
    annee_inscription INT, 
    FOREIGN KEY (id_utilisateur) REFERENCES Utilisateurs(id_utilisateur)
);

-- création de la table enseignant
CREATE TABLE IF NOT EXISTS Enseignant (
    id_enseignant INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
    id_utilisateur INT NOT NULL,
    departement VARCHAR(50),
    specialite VARCHAR(50),
    FOREIGN KEY (id_utilisateur) REFERENCES Utilisateurs(id_utilisateur)
);

-- création de la table cours
CREATE TABLE IF NOT EXISTS Cours (
    id_cours INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
    id_enseignant INT NOT NULL,
    nom_cours VARCHAR(50) NOT NULL,
    code_cours VARCHAR(20) NOT NULL UNIQUE,
    credits CHAR(2),
    semestre CHAR(2),
    capacite_max INT NOT NULL,
    FOREIGN KEY (id_enseignant) REFERENCES Enseignant(id_enseignant)
);

-- création de la table inscription
CREATE TABLE IF NOT EXISTS Inscription (
    id_inscription INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
    id_etudiant INT NOT NULL,
    id_cours INT NOT NULL,
    date_inscription DATE NOT NULL,
    statut VARCHAR(20) NOT NULL DEFAULT 'actif',
    FOREIGN KEY (id_etudiant) REFERENCES Etudiant(id_etudiant),
    FOREIGN KEY (id_cours) REFERENCES Cours(id_cours)
);

-- creation de la table note
CREATE TABLE IF NOT EXISTS Note (
    id_note INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
    id_etudiant INT NOT NULL,
    id_cours INT NOT NULL,
    valeur DOUBLE,
    coefficient DOUBLE NOT NULL,
    date_saisie DATE,
    verrouille BOOLEAN NOT NULL DEFAULT FALSE,
    FOREIGN KEY (id_etudiant) REFERENCES Etudiant(id_etudiant),
    FOREIGN KEY (id_cours) REFERENCES Cours(id_cours)
);

-- création de la table séance
CREATE TABLE IF NOT EXISTS Seance (
    id_seance INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
    id_cours INT NOT NULL,
    date DATE NOT NULL,
    heure_debut TIME NOT NULL,
    heure_fin TIME NOT NULL,
    salle CHAR(5),
    FOREIGN KEY (id_cours) REFERENCES Cours(id_cours)
);

-- création de la table notification
CREATE TABLE IF NOT EXISTS Notification (
    id_notification INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
    id_utilisateur INT NOT NULL,
    message VARCHAR(200) NOT NULL,
    date_envoi DATETIME NOT NULL,
    lu BOOLEAN NOT NULL DEFAULT FALSE,
    FOREIGN KEY (id_utilisateur) REFERENCES Utilisateurs(id_utilisateur)
);
