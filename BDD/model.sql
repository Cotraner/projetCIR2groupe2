#------------------------------------------------------------
#        Script MySQL.
#------------------------------------------------------------

#------------------------------------------------------------
#        DROP TABLE IF EXISTS.
#------------------------------------------------------------
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


#------------------------------------------------------------
# Table: pays
#------------------------------------------------------------

CREATE TABLE pays(
        id_pays  Int  Auto_increment  NOT NULL ,
        nom_pays Char (50) NOT NULL
	,CONSTRAINT pays_PK PRIMARY KEY (id_pays)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: region
#------------------------------------------------------------

CREATE TABLE region(
        reg_code Int NOT NULL ,
        reg_nom  Char (50) NOT NULL ,
        id_pays  Int NOT NULL
	,CONSTRAINT region_PK PRIMARY KEY (reg_code)

	,CONSTRAINT region_pays_FK FOREIGN KEY (id_pays) REFERENCES pays(id_pays)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: departement
#------------------------------------------------------------

CREATE TABLE departement(
        dep_code Varchar (5) NOT NULL ,
        dep_nom  Char (50) NOT NULL ,
        reg_code Int NOT NULL
	,CONSTRAINT departement_PK PRIMARY KEY (dep_code)

	,CONSTRAINT departement_region_FK FOREIGN KEY (reg_code) REFERENCES region(reg_code)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: commune
#------------------------------------------------------------

CREATE TABLE commune(
        code_INSEE  Varchar (5) NOT NULL ,
        nom_commune Char (50) NOT NULL ,
        population  Int NOT NULL ,
        code_pos    Int NOT NULL ,
        dep_code    Varchar (5) NOT NULL
	,CONSTRAINT commune_PK PRIMARY KEY (code_INSEE)

	,CONSTRAINT commune_departement_FK FOREIGN KEY (dep_code) REFERENCES departement(dep_code)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: localisation
#------------------------------------------------------------

CREATE TABLE localisation(
        id_localistation Int  Auto_increment  NOT NULL ,
        latitude         Float NOT NULL ,
        longitude        Float NOT NULL ,
        code_INSEE       Varchar (5) NOT NULL
	,CONSTRAINT localisation_PK PRIMARY KEY (id_localistation)

	,CONSTRAINT localisation_commune_FK FOREIGN KEY (code_INSEE) REFERENCES commune(code_INSEE)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: installateur
#------------------------------------------------------------

CREATE TABLE installateur(
        id_installateur  Int  Auto_increment  NOT NULL ,
        nom_installateur Char (50) NOT NULL
	,CONSTRAINT installateur_PK PRIMARY KEY (id_installateur)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: modele_onduleur
#------------------------------------------------------------

CREATE TABLE modele_onduleur(
        id_modele       Int  Auto_increment  NOT NULL ,
        modele_onduleur Char (50) NOT NULL
	,CONSTRAINT modele_onduleur_PK PRIMARY KEY (id_modele)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: marque_panneau
#------------------------------------------------------------

CREATE TABLE marque_panneau(
        id_marque Int  Auto_increment  NOT NULL ,
        marque    Char (50) NOT NULL
	,CONSTRAINT marque_panneau_PK PRIMARY KEY (id_marque)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: marque_onduleur
#------------------------------------------------------------

CREATE TABLE marque_onduleur(
        id_marque Int  Auto_increment  NOT NULL ,
        marque    Char (50) NOT NULL
	,CONSTRAINT marque_onduleur_PK PRIMARY KEY (id_marque)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: onduleur
#------------------------------------------------------------

CREATE TABLE onduleur(
        id_onduleur Int  Auto_increment  NOT NULL ,
        id_modele   Int ,
        id_marque   Int
	,CONSTRAINT onduleur_PK PRIMARY KEY (id_onduleur)

	,CONSTRAINT onduleur_modele_onduleur_FK FOREIGN KEY (id_modele) REFERENCES modele_onduleur(id_modele)
	,CONSTRAINT onduleur_marque_onduleur0_FK FOREIGN KEY (id_marque) REFERENCES marque_onduleur(id_marque)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: installation
#------------------------------------------------------------

CREATE TABLE installation(
        id_installation   Int  Auto_increment  NOT NULL ,
        date_installation Date NOT NULL ,
        nb_panneaux       Int NOT NULL ,
        surface           Int NOT NULL ,
        puissance_crete   Int NOT NULL ,
        nb_ondulateur     Int NOT NULL ,
        pente             Int NOT NULL ,
        pente_opti        Int NOT NULL ,
        orientation       Int NOT NULL ,
        orientation_opti  Int NOT NULL ,
        prod_pvgis        Int NOT NULL ,
        id_onduleur       Int NOT NULL ,
        id_localistation  Int NOT NULL ,
        id_installateur   Int NOT NULL
	,CONSTRAINT installation_PK PRIMARY KEY (id_installation)

	,CONSTRAINT installation_onduleur_FK FOREIGN KEY (id_onduleur) REFERENCES onduleur(id_onduleur)
	,CONSTRAINT installation_localisation0_FK FOREIGN KEY (id_localistation) REFERENCES localisation(id_localistation)
	,CONSTRAINT installation_installateur1_FK FOREIGN KEY (id_installateur) REFERENCES installateur(id_installateur)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: modele_panneau
#------------------------------------------------------------

CREATE TABLE modele_panneau(
        id_modele Int  Auto_increment  NOT NULL ,
        modele    Char (50) NOT NULL
	,CONSTRAINT modele_panneau_PK PRIMARY KEY (id_modele)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: panneau
#------------------------------------------------------------

CREATE TABLE panneau(
        id_panneau      Int  Auto_increment  NOT NULL ,
        id_installation Int NOT NULL ,
        id_marque       Int ,
        id_modele       Int
	,CONSTRAINT panneau_PK PRIMARY KEY (id_panneau)

	,CONSTRAINT panneau_installation_FK FOREIGN KEY (id_installation) REFERENCES installation(id_installation)
	,CONSTRAINT panneau_marque_panneau0_FK FOREIGN KEY (id_marque) REFERENCES marque_panneau(id_marque)
	,CONSTRAINT panneau_modele_panneau1_FK FOREIGN KEY (id_modele) REFERENCES modele_panneau(id_modele)
)ENGINE=InnoDB;

