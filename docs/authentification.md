# Guide d'authentification

Pour plus d'informations sur concernant la sécurisation vous pouvez-vous rendre sur la [documentation officielle](https://symfony.com/doc/current/security.html)

## Création de la classe user

Tout d'abord, il fau créer un user

```shell
    php bin/console make:user
```

Une fois cette commande lancer, il y aura:

- Un fichier src/Entity/User.php crée
- Un fichier src/Repository/UserRepository.php crée
- Le fichier config/packages/security.yaml mise à jour

Il est important que la classe User implémentera la classe UserInferface.

## Le provider

En association avec la classe User, il est important de définir un provider.
Ce provider est utile pour aider dans certaines tâches, comme le rechargement des données utilisateur de la session et certaines fonctionnalités facultatives, comme se souvenir de moi et l'usurpation d'identité.

Grâce à la commande `make:user`, cette partie est déjà mise à jour.

La connexion se faisant grâce au username et non pas avec l'email.
Il faut changer la partie `property`.

### Avant

```yaml
# config/packages/security.yaml
security:
    # ...

    providers:
        # used to reload user from session & other features (e.g. switch_user)
        app_user_provider:
            entity:
                class: App\Entity\User
                property: email
```

### Après

```yaml
# config/packages/security.yaml
security:
    # ...

    providers:
        # used to reload user from session & other features (e.g. switch_user)
        app_user_provider:
            entity:
                class: App\Entity\User
                property: username
```

## Encoder

L'encoder est une autre partie importante pour la sécurisation.
Si nos utilisateurs ont un mot de passe, il est important de définir l'algorithme d'encryption.
Il y a plusieurs mode, le mode le plus optimiser est auto il choisiera l'aglorithme le plus sécuriser en fonction des différentes versions de symfony.

NB: Cette partie est aussi générer par la commande `make:user`

```yaml
# config/packages/security.yaml
security:
    # ...
    encoders:
        App\Entity\User:
            algorithm: auto
```

## Firewalls & Authentification

Le pare-feu est le système d'authentification, il est possible de le configurer de différentes façon par exemple:

- formulaire de connexion
- jeton API
- etc

Dans le code suivant nous utilisons un formulaire de connexion.

```yaml
main:
    anonymous: true
    lazy: true
    pattern: ^/
    form_login:
        login_path: login
        check_path: login_check
        always_use_default_target_path:  true
        default_target_path:  /
    provider: app_user_provider
    logout:
        path: logout
```