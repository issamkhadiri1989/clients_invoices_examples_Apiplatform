# APIPLATFORM

Ce projet consiste à mettre en main l'utilisation de base de APIPLATFORM. Ce projet a pour le but de mettre en place un
gestionnaire de Clients/Factures.

## Client:

Cette partie consiste à gérer les clients par un manager (l'utilisateur connecté). La resource en question est
`src/Entity/Client.php`.

Un utilisateur (src/Entity/User.php) gère 1 ou plusieurs clients.

Un utilisateur ne peut visualiser que sa liste des clients.

Ceci est assuré par l'extension `src/Extension/CurrentManagerExtension.php`.

La liste des clients a plusieurs features

- Pagination: la pagination est activée pour la resource clients `http://localhost/api/clients` en ajoutant le paramètre
  `_page`. Par exemple `http://localhost/api/clients?_page=1`.
- Controler le nombre de résultats à exposer grace à un paramètre spécial `_show`. Par exemple
  `http://localhost/api/clients?_show=10`. Ici nous allons afficher que 10 clients par page. Cette feature est activée
  avec l'option `paginationClientItemsPerPage` et la configuration du paramètre de nombre d'élément s dans la page peut
  être trouvée dans

```yaml
#config/packages/api_platform.yaml

api_platform:
    collection:
        pagination:
            items_per_page_parameter_name: _show
```

Bien que le nombre d'éléments peut être configuré par le client, mais il est limité à 50 grace à
`paginationMaximumItemsPerPage: 50`

- Filtre par `la raison sociale` et `nom du client` avec l'activation de 2 filtres :

```yaml
#config/filters.yaml

services:
    # Search filter
    client.search_filter:
        parent: 'api_platform.doctrine.orm.search_filter'
        arguments: [ { fullName: 'partial', companyName: 'partial' } ]
        tags: [ 'api_platform.filter' ]

    # Order filter
    client.order_filter:
        parent: 'api_platform.doctrine.orm.order_filter'
        arguments:
            $properties: { id: ~, fullName: ~, companyName: ~ }
            $orderParameterName: order
        tags: [ 'api_platform.filter' ]
```

- Nous allons aussi controller les opérations Get et Patch pour les laisser accessibles uniquement pour l'utilisateur
  connecté.
- Afin de ne pas exposer toutes les informations (password, ...) du manager, nous avons controllé les informations à
  retourner avec les groupes de serialisations.
- Pour éviter le problème de circular reference, nous avons configuré `MaxDepth` dans `src/Entity/Client.php`.

## Facture:

Pour chaque client, l'utilisateur ajoute des factures à ses clients. Les factures sont définies dans la ressource
`src/Entity/Invoice.php`.

Au moment de l'ajout de la facture, `src/State/Processor/InvoiceStateProcessor.php` est utilisé pour définir un état de
la facture (`created`) et la date de création.

Toutes les opérations sont activées pour la gestion des factures (Ajouter / Modifier / Supprimer). Une nouvelle resource
est activées pour faciliter la récupération des factures par client `http://localhost/api/clients/1/invoices. Cet
endpoint aura comme options:

- Tri des factures par montant global
- Filtrer la liste des factures par état

```yaml
#config/filters.yaml

services:
    invoice.order_filter:
        parent: 'api_platform.doctrine.orm.order_filter'
        arguments:
            $properties: { totalAmountDue: ~ }
            $orderParameterName: 'order'
        tags: [ 'api_platform.filter' ]

    invoice.status_filter:
        parent: 'api_platform.doctrine.orm.search_filter'
        arguments: [ { status: 'exact' } ]
        tags: [ 'api_platform.filter' ]

```

exemple `http://localhost/api/clients/1/invoices?order[totalAmountDue]=desc&status=created`. Ceci est géré par Link
`'clientId' => new Link(fromClass: Client::class, toProperty: 'client'),`

Les status des factures sont définies dans `src/Enum/InvoiceStatus.php`. Il existe 4 états :

- CREEE
- PARTIELLEMENT PAYEE
- NON PAYEE
- PAYEE

La collection Postman est prête `EHEI 2025 - Demo APIPLATFORM.postman_collection.json`. Importer la collection et lancer les
tests des differents endpoints.

# Installer le projet

- `composer install` : pour installer les dépendences
- `php bin/console doctrine:migrations:migrate` : pour lancer la migration de la base de données
- `php bin/console doctrine:fixtures:load` : pour charger les données de tests (Users et Clients)

## Utilisation du State Provider

Afin de comprendre l'utilisation du State Provider, `src/State/Provider/CurrentUserProfileProvider.php` est concu pour
exposer le profile de l'utilisateur connecté. Pour cela, une endpoint est exposé en GET `/api/users/me` et accessible en
mode connecté et capable d'exposer le profile public de l'utilisateur connecté (Ceci est une alternative de
l'utilisation des groups)