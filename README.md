
## ✅ Prérequis

- SGBD : **MySQL**
- Serveur web
- PHP

---

## 🗄️ Installation de la base de données

Créer une base de données et importer les fichiers SQL :

```sql
CREATE DATABASE [votreBase];
connect [votreBase];
source projetCIR2groupe2/BDD/model.sql;
source projetCIR2groupe2/BDD/data.sql;
```

---

## 🚀 Déploiement

Positionner le dossier `projetCIR2groupe2` sur un serveur web.

Accéder à la page d’accueil :  
```
/front/php/accueil.php
```

---

## 🧭 Navigation

Vous pouvez maintenant naviguer librement via le menu en haut.

Une **page de connexion** est disponible pour accéder à la version **administrateur** (back).

---

## 🛠️ Fonctionnalités principales

### Front (client) :
- Page d’accueil avec statistiques
- Formulaire de recherche avec filtres
- Affichage des résultats sous forme de tableau
- Détail d’une installation
- Visualisation des installations sur une carte intéractive

### Back (admin) :
- Tableau de gestion des données
- Ajout (POST) / modification (PUT) / suppression d’installations (DELETE)
- Accès restreint via une page de connexion

---

## 👨‍💻 Auteurs

Projet réalisé par le **Groupe 2** :  
- Clément Robin  
- Louis Lacoste  

