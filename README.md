# petstore

[![Build Status](https://api.travis-ci.org/chubbyphp/petstore.png?branch=chubbyphp)](https://travis-ci.org/chubbyphp/petstore)
[![Coverage Status](https://coveralls.io/repos/github/chubbyphp/petstore/badge.svg?branch=chubbyphp)](https://coveralls.io/github/chubbyphp/petstore?branch=chubbyphp)

## Description

A simple skeleton to build api's based on the [chubbyphp-framework][1].

## Requirements

 * php: ^7.2
 * [chubbyphp/chubbyphp-api-http][3]: ^3.4
 * [chubbyphp/chubbyphp-config][4]: ^2.1.1
 * [chubbyphp/chubbyphp-container][5]: ^1.0.3
 * [chubbyphp/chubbyphp-cors][6]: ^1.1.1
 * [chubbyphp/chubbyphp-deserialization][7]: ^2.17
 * [chubbyphp/chubbyphp-doctrine-db-service-provider][8]: ^2.0
 * [chubbyphp/chubbyphp-framework][9]: ^3.1
 * [chubbyphp/chubbyphp-framework-router-fastroute][10]: ^1.0
 * [chubbyphp/chubbyphp-negotiation][11]: ^1.5.3
 * [chubbyphp/chubbyphp-serialization][12]: ^2.13.1
 * [chubbyphp/chubbyphp-validation][13]: ^3.9
 * [doctrine/orm][14]: ^2.7.2
 * [monolog/monolog][15]: ^2.0.2
 * [nikic/fast-route][16]: ^1.3
 * [ocramius/proxy-manager][17]: ^2.2.3
 * [ramsey/uuid][18]: ^4.0.1
 * [slim/psr7][19]: ^1.0
 * [swagger-api/swagger-ui][20]: ^3.25
 * [symfony/console][21]: ^4.4.7|^5.0.7

## Environment

Add the following environment variable to your system, for example within `~./bash_aliases`:

```sh
export USER_ID=$(id -u)
export GROUP_ID=$(id -g)
```

### Docker

```sh
docker-compose up -d
docker-compose exec php bash
```

### Urls

* http://localhost:10080
* https://localhost:10443

### DBs

 * jdbc:postgresql://localhost:15432/petstore?user=root&password=root

## Installation

Through [Composer](http://getcomposer.org) as [chubbyphp/petstore][40].

```bash
composer create-project chubbyphp/petstore myproject "dev-chubbyphp"
```

## Setup

```sh
composer setup:dev
```

## Structure

### Collection

Collections are sortable, filterable paginated lists of models.

 * [App\Collection][60]

### Config

Enviroment based configurations, dev, phpunit, prod. Credentials where used fom enviroment variables.

 * [App\Config][70]

### RequestHandler

RequestHandler alias Controller, or Controller actions to be more precise.
There is a directory with generic crud controllers. If you like the idea adapt them for your generic use case, if not drop them.
I highly recommend to not extend them.

 * [App\RequestHandler][80]

### Factory

Factories to create collections, model or whatever you need to be created.

 * [App\Factory][90]

### Mapping

Mappings are used for deserialization, orm, serialization and validation defintions. They are all done in PHP.

 * [App\Mapping][100]

### Model

Models, entities, documents what ever fits your purpose the best.

 * [App\Model][110]

### Repository

Repositories get data from storages like databases, elasticsearch, redis or whereever your models are stored or cached.

 * [App\Repository][120]

### ServiceFactory

Service factories are the glue code of the dependeny injection container.

 * [App\ServiceFactory][130]

## Copyright

Dominik Zogg 2020

[1]: https://github.com/chubbyphp/chubbyphp-framework

[3]: https://packagist.org/packages/chubbyphp/chubbyphp-api-http
[4]: https://packagist.org/packages/chubbyphp/chubbyphp-config
[5]: https://packagist.org/packages/chubbyphp/chubbyphp-container
[6]: https://packagist.org/packages/chubbyphp/chubbyphp-cors
[7]: https://packagist.org/packages/chubbyphp/chubbyphp-deserialization
[8]: https://packagist.org/packages/chubbyphp/chubbyphp-doctrine-db-service-provider
[9]: https://packagist.org/packages/chubbyphp/chubbyphp-framework
[10]: https://packagist.org/packages/chubbyphp/chubbyphp-framework-router-fastroute
[11]: https://packagist.org/packages/chubbyphp/chubbyphp-negotiation
[12]: https://packagist.org/packages/chubbyphp/chubbyphp-serialization
[13]: https://packagist.org/packages/chubbyphp/chubbyphp-validation
[14]: https://packagist.org/packages/doctrine/orm
[15]: https://packagist.org/packages/monolog/monolog
[16]: https://packagist.org/packages/nikic/fast-route
[17]: https://packagist.org/packages/ocramius/proxy-manager
[18]: https://packagist.org/packages/ramsey/uuid
[19]: https://packagist.org/packages/slim/psr7
[20]: https://packagist.org/packages/swagger-api/swagger-ui
[21]: https://packagist.org/packages/symfony/console

[40]: https://packagist.org/packages/chubbyphp/petstore

[60]: app/Collection

[70]: app/Config

[80]: app/RequestHandler

[90]: app/Factory

[100]: app/Mapping

[110]: app/Model

[120]: app/Repository

[130]: app/ServiceFactory
