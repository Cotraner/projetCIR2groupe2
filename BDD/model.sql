#------------------------------------------------------------
#        Script MySQL.
#------------------------------------------------------------


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
        prod_pvgis        Int NOT NULL
	,CONSTRAINT installation_PK PRIMARY KEY (id_installation)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: onduleur
#------------------------------------------------------------

CREATE TABLE onduleur(
        id_onduleur     Int  Auto_increment  NOT NULL ,
        id_installation Int NOT NULL
	,CONSTRAINT onduleur_PK PRIMARY KEY (id_onduleur)

	,CONSTRAINT onduleur_installation_FK FOREIGN KEY (id_installation) REFERENCES installation(id_installation)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: localisation
#------------------------------------------------------------

CREATE TABLE localisation(
        id_localistation Int  Auto_increment  NOT NULL ,
        latitude         Float NOT NULL ,
        longitude        Float NOT NULL ,
        id_installation  Int NOT NULL
	,CONSTRAINT localisation_PK PRIMARY KEY (id_localistation)

	,CONSTRAINT localisation_installation_FK FOREIGN KEY (id_installation) REFERENCES installation(id_installation)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: panneau
#------------------------------------------------------------

CREATE TABLE panneau(
        id_panneau      Int  Auto_increment  NOT NULL ,
        id_installation Int NOT NULL
	,CONSTRAINT panneau_PK PRIMARY KEY (id_panneau)

	,CONSTRAINT panneau_installation_FK FOREIGN KEY (id_installation) REFERENCES installation(id_installation)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: commune
#------------------------------------------------------------

CREATE TABLE commune(
        code_INSEE       Varchar (5) NOT NULL ,
        nom_commune      Char (50) NOT NULL ,
        pop              Int NOT NULL ,
        code_pos         Int NOT NULL ,
        id_localistation Int NOT NULL
	,CONSTRAINT commune_PK PRIMARY KEY (code_INSEE)

	,CONSTRAINT commune_localisation_FK FOREIGN KEY (id_localistation) REFERENCES localisation(id_localistation)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: departement
#------------------------------------------------------------

CREATE TABLE departement(
        dep_code   Varchar (5) NOT NULL ,
        dep_nom    Char (50) NOT NULL ,
        code_INSEE Varchar (5) NOT NULL
	,CONSTRAINT departement_PK PRIMARY KEY (dep_code)

	,CONSTRAINT departement_commune_FK FOREIGN KEY (code_INSEE) REFERENCES commune(code_INSEE)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: region
#------------------------------------------------------------

CREATE TABLE region(
        reg_code Int NOT NULL ,
        reg_nom  Char (50) NOT NULL ,
        dep_code Varchar (5) NOT NULL
	,CONSTRAINT region_PK PRIMARY KEY (reg_code)

	,CONSTRAINT region_departement_FK FOREIGN KEY (dep_code) REFERENCES departement(dep_code)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: pays
#------------------------------------------------------------

CREATE TABLE pays(
        id_pays  Int  Auto_increment  NOT NULL ,
        nom_pays Char (50) NOT NULL ,
        reg_code Int NOT NULL
	,CONSTRAINT pays_PK PRIMARY KEY (id_pays)

	,CONSTRAINT pays_region_FK FOREIGN KEY (reg_code) REFERENCES region(reg_code)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: installateur
#------------------------------------------------------------

CREATE TABLE installateur(
        id_installateur  Int  Auto_increment  NOT NULL ,
        nom_installateur Char (50) NOT NULL ,
        id_installation  Int
	,CONSTRAINT installateur_PK PRIMARY KEY (id_installateur)

	,CONSTRAINT installateur_installation_FK FOREIGN KEY (id_installation) REFERENCES installation(id_installation)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: modele
#------------------------------------------------------------

CREATE TABLE modele(
        id_modele Int  Auto_increment  NOT NULL ,
        modele    Char (50) NOT NULL
	,CONSTRAINT modele_PK PRIMARY KEY (id_modele)
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
# Table: modele_panneau
#------------------------------------------------------------

CREATE TABLE modele_panneau(
        id_modele Int  Auto_increment  NOT NULL ,
        modele    Char (50) NOT NULL
	,CONSTRAINT modele_panneau_PK PRIMARY KEY (id_modele)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: possede OND_MOD
#------------------------------------------------------------

CREATE TABLE possede_OND_MOD(
        id_modele   Int NOT NULL ,
        id_onduleur Int NOT NULL
	,CONSTRAINT possede_OND_MOD_PK PRIMARY KEY (id_modele,id_onduleur)

	,CONSTRAINT possede_OND_MOD_modele_FK FOREIGN KEY (id_modele) REFERENCES modele(id_modele)
	,CONSTRAINT possede_OND_MOD_onduleur0_FK FOREIGN KEY (id_onduleur) REFERENCES onduleur(id_onduleur)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: fait_par
#------------------------------------------------------------

CREATE TABLE fait_par(
        id_marque  Int NOT NULL ,
        id_panneau Int NOT NULL
	,CONSTRAINT fait_par_PK PRIMARY KEY (id_marque,id_panneau)

	,CONSTRAINT fait_par_marque_panneau_FK FOREIGN KEY (id_marque) REFERENCES marque_panneau(id_marque)
	,CONSTRAINT fait_par_panneau0_FK FOREIGN KEY (id_panneau) REFERENCES panneau(id_panneau)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: possede OND_MAR
#------------------------------------------------------------

CREATE TABLE possede_OND_MAR(
        id_marque   Int NOT NULL ,
        id_onduleur Int NOT NULL
	,CONSTRAINT possede_OND_MAR_PK PRIMARY KEY (id_marque,id_onduleur)

	,CONSTRAINT possede_OND_MAR_marque_onduleur_FK FOREIGN KEY (id_marque) REFERENCES marque_onduleur(id_marque)
	,CONSTRAINT possede_OND_MAR_onduleur0_FK FOREIGN KEY (id_onduleur) REFERENCES onduleur(id_onduleur)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: possede
#------------------------------------------------------------

CREATE TABLE possede(
        id_modele  Int NOT NULL ,
        id_panneau Int NOT NULL
	,CONSTRAINT possede_PK PRIMARY KEY (id_modele,id_panneau)

	,CONSTRAINT possede_modele_panneau_FK FOREIGN KEY (id_modele) REFERENCES modele_panneau(id_modele)
	,CONSTRAINT possede_panneau0_FK FOREIGN KEY (id_panneau) REFERENCES panneau(id_panneau)
)ENGINE=InnoDB;

