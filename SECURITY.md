# Politique de sécurité

## Versions prises en charge

Seule la dernière version de la branche principale reçoit les correctifs de sécurité.

## Signaler une vulnérabilité

N’ouvrez pas d’issue publique contenant des données sensibles. Envoyez un rapport à
`hello@toscani-tenekeu.com` avec les étapes de reproduction et l’impact estimé.

## Protection des identifiants

Les identifiants MySQL doivent être conservés uniquement dans le fichier `.env`.
Toute valeur déjà publiée doit être considérée comme exposée et remplacée.

Avant tout déploiement :

1. remplacez tout ancien mot de passe MySQL ou supprimez le compte concerné ;
2. créez un utilisateur MySQL dédié avec accès uniquement à la base IMS ;
3. placez les nouveaux identifiants exclusivement dans `.env` ;
4. ne réutilisez jamais une valeur qui a déjà été publiée.

Ne publiez jamais le fichier `.env`, les sauvegardes SQL de production ou
les journaux contenant des informations personnelles.
