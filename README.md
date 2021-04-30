Serializable Laravel Query Builder
==============

[![Build Status](https://travis-ci.org/laravie/serialize-queries.svg?branch=master)](https://travis-ci.org/laravie/serialize-queries)
[![Latest Stable Version](https://poser.pugx.org/laravie/serialize-queries/v/stable)](https://packagist.org/packages/laravie/serialize-queries)
[![Total Downloads](https://poser.pugx.org/laravie/serialize-queries/downloads)](https://packagist.org/packages/laravie/serialize-queries)
[![Latest Unstable Version](https://poser.pugx.org/laravie/serialize-queries/v/unstable)](https://packagist.org/packages/laravie/serialize-queries)
[![License](https://poser.pugx.org/laravie/serialize-queries/license)](https://packagist.org/packages/laravie/serialize-queries)
[![Coverage Status](https://coveralls.io/repos/github/laravie/serialize-queries/badge.svg?branch=master)](https://coveralls.io/github/laravie/serialize-queries?branch=master)

Serialize Queries allows developer to serialize Query/Eloquent Builder to be used in Laravel Queues.

## Installation

To install through composer, run the following command from terminal:

```bash
composer require "laravie/serialize-queries"
```

## Usages

### Eloquent Query Builder

#### Serialize

```php
Laravie\SerializesQuery\Eloquent::serialize(
    \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\Relation $builder
): array;
```

The method provide simple interface to serialize Eloquent Query Builder.

```php
use App\Model\User;
use Laravie\SerializesQuery\Eloquent;

$query = User::has('posts')->where('age', '>', 25);

$serializedQuery = Eloquent::serialize($query);
```

#### Unserialize

```php
Laravie\SerializesQuery\Eloquent::unserialize(array $payload): \Illuminate\Database\Eloquent\Builder;
```

The method provide simple interface to unserialize Eloquent Query Builder.

```php
use Laravie\SerializesQuery\Eloquent;


$query = Eloquent::unserialize($serializedQuery);
```

### Fluent Query Builder

#### Serialize

```php
Laravie\SerializesQuery\Query::serialize(
    \Illuminate\Database\Query\Builder $builder
): array;
```

The method provide simple interface to serialize Fluent Query Builder.

```php
use Illuminate\Support\Facades\DB;
use Laravie\SerializesQuery\Query;

$query = DB::table('users')->where('age', '>', 25);

$serializedQuery = Query::serialize($query);
```

#### Unserialize 

```php
Laravie\SerializesQuery\Query::unserialize(array $payload): \Illuminate\Database\Query\Builder;
```

The method provide simple interface to unserialize Eloquent Builder.

```php
use Laravie\SerializesQuery\Query;


$query = Query::unserialize($serializedQuery);
```
