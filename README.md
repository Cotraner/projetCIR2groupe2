# ☀️ Projet CIR2 - Gestion des Installations Photovoltaïques

## ✅ Prérequis

- **SGBD** : MySQL  
- **Serveur web** compatible (ex : Apache)  
- **PHP** installé

---

## 🐍 Scripts Python

Dans le dossier `/BDD/python`, deux scripts sont disponibles :

- `cleaner.py` : corrige les problèmes d'encodage et d’écriture dans les fichiers CSV (Assurez-vous que le fichier data.csv contient les bons noms de colonnes.)
- `generate_sql.py` : génère le fichier `data.sql` à partir des fichiers nettoyés

> ⚠️ Assurez-vous que les noms des fichiers CSV soient corrects avant exécution.

### Étapes :
```bash
python cleaner.py
python generate_sql.py
```

---

## 🗄️ Installation de la base de données

1. Créez la base de données MySQL :
   ```sql
   CREATE DATABASE [votreBase];
   connect [votreBase];
   ```

2. Importez les fichiers SQL :
   ```sql
   source projetCIR2groupe2/BDD/model.sql;
   source projetCIR2groupe2/BDD/data.sql;
   ```

---

## 🚀 Déploiement

1. Positionnez le dossier `projetCIR2groupe2` sur un **serveur web**.
2. Accédez à l’application via :  
   ```
   /front/php/accueil.php
   ```

---

## 🧭 Navigation

L’interface permet une navigation fluide via la barre de menu.  
Une page de connexion permet d’accéder à la partie **administration**.

---

## 🛠️ Fonctionnalités

### 👤 Front (Client) :
- Page d’accueil avec statistiques
- Formulaire de recherche avec filtres
- Résultats affichés sous forme de tableau
- Détails d’une installation
- Carte interactive des installations

### 🔐 Back (Admin) :
- Gestion complète des installations (CRUD)
- Accès sécurisé via authentification
- Interface d’administration dédiée

---

## 👨‍💻 Auteurs

Projet réalisé par le **Groupe 2** :
- Clément Robin  
- Louis Lacoste