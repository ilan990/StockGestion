# Gestion de Stock API

API de gestion de stock développée avec Symfony 7.2

## Environnement de développement

### Prérequis

* Docker
* Docker Compose

### Technologies

* Symfony 7.2
* PHP 8.2
* MySQL 8.0
* Nginx
* Docker

### Lancer l'environnement de développement

```bash
docker-compose up -d
```

### Accès

* Application : http://localhost
* Base de données
    * Hôte : localhost
    * Port : 3307
    * Base de données : app
    * Utilisateur : app
    * Mot de passe : app

### Commandes utiles

Exécuter les commandes Symfony :
```bash
docker-compose exec app symfony console [commande]
```

Accéder au bash du conteneur :
```bash
docker-compose exec app bash
```
