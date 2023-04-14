# Initialisation du projet

## Prérequis

Avoir une base de données Mysql installé sur votre machine.
Assurez-vous d'avoir installé composer sur votre machine.

## Commandes

- Le projet est fourni sans le vendor. Afin d'avoir les dépendances, vous devez exécutez la commande suivante :
```
composer install
```

- Maintenant il vous faut créer le schéma de données. La commande suivante va créer la base de données : 
```
php bin/console doctrine:database:create
```

- Ensuite nous allons implémenter le schéma de données :
```
php bin/console doctrine:schema:create
```

- Puis enfin pour créer de nouvelles données, exécutez la commande suivante :
```
php bin/console doctrine:fixtures:load
```

## Attention
Pour faire fonctionner le front de l'application vous devez vous assurer que l'url de l'api correspond bien à http://api.twitter.local/
Sans cela le front ne communiquera pas avec l'api.

# Liste des routes

## Nelmio Doc

- /

## User

**GET**
- /users
- /user/{id}

**POST**
- /user/create

**DELETE**
- /user/{id}

## Tweet

**GET**
- /tweets
- /tweet/{id}

**POST**
- /tweet/create

**DELETE**
- /tweet/{id}
