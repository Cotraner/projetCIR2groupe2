-- Script MySQL corrigé avec VARCHAR et utf8mb4

-- Désactivation temporaire des vérifications de clés étrangères
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS installer;
DROP TABLE IF EXISTS fait_par;
DROP TABLE IF EXISTS possede;
DROP TABLE IF EXISTS panneau;
DROP TABLE IF EXISTS modele_panneau;
DROP TABLE IF EXISTS marque_panneau;
DROP TABLE IF EXISTS installation;
DROP TABLE IF EXISTS onduleur;
DROP TABLE IF EXISTS modele_onduleur;
DROP TABLE IF EXISTS marque_onduleur;
DROP TABLE IF EXISTS localisation;
DROP TABLE IF EXISTS installateur;
DROP TABLE IF EXISTS commune;
DROP TABLE IF EXISTS departement;
DROP TABLE IF EXISTS region;
DROP TABLE IF EXISTS pays;

-- Réactivation des contraintes
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE pays (
    id_pays INT AUTO_INCREMENT NOT NULL,
    nom_pays VARCHAR(50) NOT NULL,
    CONSTRAINT pays_PK PRIMARY KEY (id_pays)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE region (
    reg_code INT NOT NULL,
    reg_nom VARCHAR(50) NOT NULL,
    id_pays INT NOT NULL,
    CONSTRAINT region_PK PRIMARY KEY (reg_code),
    CONSTRAINT region_pays_FK FOREIGN KEY (id_pays) REFERENCES pays(id_pays)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE departement (
    dep_code VARCHAR(5) NOT NULL,
    dep_nom VARCHAR(50) NOT NULL,
    reg_code INT NOT NULL,
    CONSTRAINT departement_PK PRIMARY KEY (dep_code),
    CONSTRAINT departement_region_FK FOREIGN KEY (reg_code) REFERENCES region(reg_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE commune (
    code_INSEE VARCHAR(5) NOT NULL,
    nom_commune VARCHAR(50) NOT NULL,
    population INT NOT NULL,
    code_pos INT NOT NULL,
    dep_code VARCHAR(5) NOT NULL,
    CONSTRAINT commune_PK PRIMARY KEY (code_INSEE),
    CONSTRAINT commune_departement_FK FOREIGN KEY (dep_code) REFERENCES departement(dep_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE localisation (
    id_localisation INT AUTO_INCREMENT NOT NULL,
    latitude FLOAT NOT NULL,
    longitude FLOAT NOT NULL,
    code_INSEE VARCHAR(5) NOT NULL,
    CONSTRAINT localisation_PK PRIMARY KEY (id_localisation),
    CONSTRAINT localisation_commune_FK FOREIGN KEY (code_INSEE) REFERENCES commune(code_INSEE)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE installateur (
    id_installateur INT AUTO_INCREMENT NOT NULL,
    nom_installateur VARCHAR(50) NOT NULL,
    CONSTRAINT installateur_PK PRIMARY KEY (id_installateur)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE modele_onduleur (
    id_modele INT AUTO_INCREMENT NOT NULL,
    modele_onduleur VARCHAR(50) NOT NULL,
    CONSTRAINT modele_onduleur_PK PRIMARY KEY (id_modele)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE marque_panneau (
    id_marque INT AUTO_INCREMENT NOT NULL,
    marque VARCHAR(50) NOT NULL,
    CONSTRAINT marque_panneau_PK PRIMARY KEY (id_marque)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE marque_onduleur (
    id_marque INT AUTO_INCREMENT NOT NULL,
    marque VARCHAR(50) NOT NULL,
    CONSTRAINT marque_onduleur_PK PRIMARY KEY (id_marque)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE onduleur (
    id_onduleur INT AUTO_INCREMENT NOT NULL,
    id_modele INT,
    id_marque INT,
    CONSTRAINT onduleur_PK PRIMARY KEY (id_onduleur),
    CONSTRAINT onduleur_modele_onduleur_FK FOREIGN KEY (id_modele) REFERENCES modele_onduleur(id_modele),
    CONSTRAINT onduleur_marque_onduleur0_FK FOREIGN KEY (id_marque) REFERENCES marque_onduleur(id_marque)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE installation (
    id_installation INT AUTO_INCREMENT NOT NULL,
    date_installation DATE NOT NULL,
    nb_panneaux INT NOT NULL,
    surface INT NOT NULL,
    puissance_crete INT NOT NULL,
    nb_ondulateur INT NOT NULL,
    pente INT NOT NULL,
    pente_opti INT NOT NULL,
    orientation INT NOT NULL,
    orientation_opti INT NOT NULL,
    prod_pvgis INT NOT NULL,
    id_onduleur INT NOT NULL,
    id_localisation INT NOT NULL,
    id_installateur INT NOT NULL,
    CONSTRAINT installation_PK PRIMARY KEY (id_installation),
    CONSTRAINT installation_onduleur_FK FOREIGN KEY (id_onduleur) REFERENCES onduleur(id_onduleur),
    CONSTRAINT installation_localisation0_FK FOREIGN KEY (id_localisation) REFERENCES localisation(id_localisation),
    CONSTRAINT installation_installateur1_FK FOREIGN KEY (id_installateur) REFERENCES installateur(id_installateur)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE modele_panneau (
    id_modele INT AUTO_INCREMENT NOT NULL,
    modele VARCHAR(50) NOT NULL,
    CONSTRAINT modele_panneau_PK PRIMARY KEY (id_modele)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE panneau (
    id_panneau INT AUTO_INCREMENT NOT NULL,
    id_installation INT NOT NULL,
    id_marque INT,
    id_modele INT,
    CONSTRAINT panneau_PK PRIMARY KEY (id_panneau),
    CONSTRAINT panneau_installation_FK FOREIGN KEY (id_installation) REFERENCES installation(id_installation),
    CONSTRAINT panneau_marque_panneau0_FK FOREIGN KEY (id_marque) REFERENCES marque_panneau(id_marque),
    CONSTRAINT panneau_modele_panneau1_FK FOREIGN KEY (id_modele) REFERENCES modele_panneau(id_modele)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
