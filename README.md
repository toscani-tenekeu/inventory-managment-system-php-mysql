# Inventory Management System

Inventory Management System (IMS) est une application web de gestion de stock conçue pour suivre les produits, les mouvements, les partenaires et les besoins de réapprovisionnement.

## Fonctionnalités

- Tableau de bord : ruptures, alertes, valeur du stock, achats et ventes du mois
- Catalogue avec SKU, code-barres, catégorie, emplacement, unité, prix et stock cible
- Registre chronologique par article avec solde recalculé après chaque opération
- Approvisionnements, ventes, retours client/fournisseur et ajustements d’inventaire
- Contrôle du stock disponible et refus des écritures qui rendraient l’historique négatif
- Suggestions de réapprovisionnement et rapports de valeur par catégorie
- Annulation sécurisée par contre-écriture, sans suppression de l’historique
- Clients, fournisseurs, reçus imprimables, recherche globale et exports CSV
- Comptes administrateur/opérateur, audit, thème clair/sombre et français/anglais

## Sécurité et fiabilité

- Identifiants MySQL exclusivement dans `.env`
- PDO avec requêtes préparées et émulation désactivée
- Mots de passe via `password_hash()` / `password_verify()`
- Sessions régénérées, cookies `HttpOnly` et `SameSite=Lax`
- Protection CSRF sur toutes les écritures
- Autorisation par rôle et limitation des tentatives de connexion
- Transactions MySQL et verrouillage des articles pendant les mouvements
- Le stock courant est modifiable uniquement par des mouvements traçables
- Archivage interdit tant que le stock d’un article n’est pas nul
- Unité verrouillée dès qu’un article possède un historique
- Échappement HTML, en-têtes de sécurité et protection contre les formules CSV

## Prérequis

- PHP 8.2 ou supérieur
- Extensions PHP `pdo_mysql` et `mbstring`
- MySQL 8.0+ ou MariaDB 10.6+
- Apache ou Nginx

Composer et Node.js ne sont pas nécessaires en production.

## Installation

```bash
git clone https://github.com/toscani-tenekeu/inventory-managment-system-php-mysql.git
cd inventory-managment-system-php-mysql
cp .env.example .env
```

Créez une base et un utilisateur MySQL dédiés :

```sql
CREATE DATABASE ims CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'ims'@'localhost' IDENTIFIED BY 'mot_de_passe_solide';
GRANT ALL PRIVILEGES ON ims.* TO 'ims'@'localhost';
FLUSH PRIVILEGES;
```

Modifiez ensuite `.env`, puis importez le schéma :

```bash
mysql -u ims -p ims < db/schema.sql
mysql -u ims -p ims < db/seed.sql
php bin/create-admin.php
php bin/check.php
php tests/smoke.php
php tests/movement-type.php
php tests/view-smoke.php
```

Pour tester localement :

```bash
php -S 127.0.0.1:8080 -t public
```

Ouvrez `http://127.0.0.1:8080`.

### Mise à jour d’une installation existante

Si la base a été créée avec une version précédente, sauvegardez-la puis exécutez une seule fois :

```bash
mysql -u ims -p ims < db/upgrade_2026_07_classic_inventory.sql
php bin/check.php
```

Les nouvelles installations doivent uniquement importer `db/schema.sql`.

## Déploiement

Le document root doit pointer vers le dossier `public/`. Exemple Nginx :

```nginx
server {
    listen 80;
    server_name stock.example.com;
    root /var/www/ims/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
    }

    location ~ /\. {
        deny all;
    }
}
```

En HTTPS, définissez `SESSION_SECURE=true` dans `.env`.

## Import d’une base existante

Pour importer une base contenant les tables `utilisateur`, `categorie`, `article`, `client`, `fournisseur`, `commande` et `vente` :

1. Conservez ces tables dans la base.
2. Importez `db/schema.sql`.
3. Exécutez `db/migrate_legacy.sql`.
4. Vérifiez les données et exécutez `php bin/check.php`.

L’import conserve les mots de passe déjà hachés, les stocks courants et les opérations historiques. Une écriture de rapprochement aligne le registre importé avec le stock réel ; les tables sources peuvent ensuite être archivées après vérification. Les identifiants importés deviennent des références `LEGACY-*`.

## Règles métier du stock

- Une entrée augmente le stock : approvisionnement, retour client ou ajustement positif.
- Une sortie diminue le stock : vente, retour fournisseur ou ajustement négatif.
- Une vente ou un retour client exige un client actif ; un achat ou un retour fournisseur exige un fournisseur actif.
- Le stock ne peut jamais être négatif, y compris lorsqu’un mouvement est antidaté.
- Une opération validée n’est ni modifiée ni supprimée : une annulation crée une contre-écriture.
- Le registre et le stock courant doivent toujours être égaux ; `php bin/check.php` contrôle cet invariant.

## Structure

```text
app/
  Core/          Configuration, PDO, authentification, CSRF et traduction
  Domain/        Accès aux données et logique transactionnelle du stock
  Views/         Pages, layouts et composants PHP
bin/             Création d’administrateur et diagnostic
db/              Schéma, données initiales et migration historique
lang/            Traductions françaises et anglaises
public/          Point d’entrée et ressources publiques
storage/logs/    Journaux techniques
```

## Rôles

- **Administrateur** : accès complet, gestion des comptes, archivage et annulation.
- **Opérateur** : catalogue, partenaires, mouvements, reçus, recherche et exports.

Il n’existe aucun compte ou mot de passe par défaut. Le premier administrateur est créé avec `php bin/create-admin.php`.

## Licence

MIT — usage personnel et commercial autorisé avec conservation de la licence.

Copyright © 2023–2026 Toscani Tenekeu.
