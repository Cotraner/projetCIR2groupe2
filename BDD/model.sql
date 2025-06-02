------------------------------------------------------------
--        Script Postgre 
------------------------------------------------------------



------------------------------------------------------------
-- Table: installation
------------------------------------------------------------
CREATE TABLE public.installation(
	id_installation     SERIAL NOT NULL ,
	date_installation   DATE  NOT NULL ,
	nb_panneaux         INT  NOT NULL ,
	surface             INT  NOT NULL ,
	puissance_crete     INT  NOT NULL  ,
	CONSTRAINT installation_PK PRIMARY KEY (id_installation)
)WITHOUT OIDS;


------------------------------------------------------------
-- Table: onduleur
------------------------------------------------------------
CREATE TABLE public.onduleur(
	id_onduleur       SERIAL NOT NULL ,
	marque_onduleur   CHAR (5)  NOT NULL ,
	id_installation   INT  NOT NULL  ,
	CONSTRAINT onduleur_PK PRIMARY KEY (id_onduleur)

	,CONSTRAINT onduleur_installation_FK FOREIGN KEY (id_installation) REFERENCES public.installation(id_installation)
)WITHOUT OIDS;


------------------------------------------------------------
-- Table: localisation
------------------------------------------------------------
CREATE TABLE public.localisation(
	id_localistation   SERIAL NOT NULL ,
	latitude           FLOAT  NOT NULL ,
	longitude          FLOAT  NOT NULL ,
	id_installation    INT  NOT NULL  ,
	CONSTRAINT localisation_PK PRIMARY KEY (id_localistation)

	,CONSTRAINT localisation_installation_FK FOREIGN KEY (id_installation) REFERENCES public.installation(id_installation)
)WITHOUT OIDS;


------------------------------------------------------------
-- Table: panneau
------------------------------------------------------------
CREATE TABLE public.panneau(
	id_panneau        SERIAL NOT NULL ,
	id_installation   INT  NOT NULL  ,
	CONSTRAINT panneau_PK PRIMARY KEY (id_panneau)

	,CONSTRAINT panneau_installation_FK FOREIGN KEY (id_installation) REFERENCES public.installation(id_installation)
)WITHOUT OIDS;


------------------------------------------------------------
-- Table: commune
------------------------------------------------------------
CREATE TABLE public.commune(
	code_INSEE         INT  NOT NULL ,
	nom_commune        CHAR (50)  NOT NULL ,
	id_localistation   INT  NOT NULL  ,
	CONSTRAINT commune_PK PRIMARY KEY (code_INSEE)

	,CONSTRAINT commune_localisation_FK FOREIGN KEY (id_localistation) REFERENCES public.localisation(id_localistation)
)WITHOUT OIDS;


------------------------------------------------------------
-- Table: departement
------------------------------------------------------------
CREATE TABLE public.departement(
	dep_code     INT  NOT NULL ,
	dep_nom      CHAR (50)  NOT NULL ,
	code_INSEE   INT  NOT NULL  ,
	CONSTRAINT departement_PK PRIMARY KEY (dep_code)

	,CONSTRAINT departement_commune_FK FOREIGN KEY (code_INSEE) REFERENCES public.commune(code_INSEE)
)WITHOUT OIDS;


------------------------------------------------------------
-- Table: region
------------------------------------------------------------
CREATE TABLE public.region(
	reg_code   INT  NOT NULL ,
	reg_nom    CHAR (50)  NOT NULL ,
	dep_code   INT  NOT NULL  ,
	CONSTRAINT region_PK PRIMARY KEY (reg_code)

	,CONSTRAINT region_departement_FK FOREIGN KEY (dep_code) REFERENCES public.departement(dep_code)
)WITHOUT OIDS;


------------------------------------------------------------
-- Table: pays
------------------------------------------------------------
CREATE TABLE public.pays(
	id_pays    SERIAL NOT NULL ,
	nom_pays   CHAR (50)  NOT NULL ,
	reg_code   INT  NOT NULL  ,
	CONSTRAINT pays_PK PRIMARY KEY (id_pays)

	,CONSTRAINT pays_region_FK FOREIGN KEY (reg_code) REFERENCES public.region(reg_code)
)WITHOUT OIDS;


------------------------------------------------------------
-- Table: installateur
------------------------------------------------------------
CREATE TABLE public.installateur(
	id_installateur    SERIAL NOT NULL ,
	nom_installateur   CHAR (50)  NOT NULL ,
	id_installation    INT    ,
	CONSTRAINT installateur_PK PRIMARY KEY (id_installateur)

	,CONSTRAINT installateur_installation_FK FOREIGN KEY (id_installation) REFERENCES public.installation(id_installation)
)WITHOUT OIDS;


------------------------------------------------------------
-- Table: modele
------------------------------------------------------------
CREATE TABLE public.modele(
	id_modele   SERIAL NOT NULL ,
	modele      CHAR (50)  NOT NULL  ,
	CONSTRAINT modele_PK PRIMARY KEY (id_modele)
)WITHOUT OIDS;


------------------------------------------------------------
-- Table: marque_panneau
------------------------------------------------------------
CREATE TABLE public.marque_panneau(
	id_marque   SERIAL NOT NULL ,
	marque      CHAR (50)  NOT NULL  ,
	CONSTRAINT marque_panneau_PK PRIMARY KEY (id_marque)
)WITHOUT OIDS;


------------------------------------------------------------
-- Table: possede
------------------------------------------------------------
CREATE TABLE public.possede(
	id_modele     INT  NOT NULL ,
	id_onduleur   INT  NOT NULL  ,
	CONSTRAINT possede_PK PRIMARY KEY (id_modele,id_onduleur)

	,CONSTRAINT possede_modele_FK FOREIGN KEY (id_modele) REFERENCES public.modele(id_modele)
	,CONSTRAINT possede_onduleur0_FK FOREIGN KEY (id_onduleur) REFERENCES public.onduleur(id_onduleur)
)WITHOUT OIDS;


------------------------------------------------------------
-- Table: fait_par
------------------------------------------------------------
CREATE TABLE public.fait_par(
	id_marque    INT  NOT NULL ,
	id_panneau   INT  NOT NULL  ,
	CONSTRAINT fait_par_PK PRIMARY KEY (id_marque,id_panneau)

	,CONSTRAINT fait_par_marque_panneau_FK FOREIGN KEY (id_marque) REFERENCES public.marque_panneau(id_marque)
	,CONSTRAINT fait_par_panneau0_FK FOREIGN KEY (id_panneau) REFERENCES public.panneau(id_panneau)
)WITHOUT OIDS;



