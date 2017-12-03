Simple eloquent extension for Laravel
===========================

[![Latest Version](https://img.shields.io/packagist/v/volosyuk/simple-eloquent.svg?style=flat-square)](https://packagist.org/packages/volosyuk/simple-eloquent)
[![Software License](https://img.shields.io/github/license/andreyvolosyuk/simple-eloquent.svg?style=flat-square)](https://github.com/andreyvolosyuk/simple-eloquent/blob/master/LICENSE.txt)
[![Quality Score](https://img.shields.io/scrutinizer/g/andreyvolosyuk/simple-eloquent.svg?style=flat-square)](https://scrutinizer-ci.com/g/andreyvolosyuk/simple-eloquent/)


This extension presents some methods for eloquent ORM in order to reduce time and memory consuming.
Sometimes application doesn't need all of eloquent overhead. It just requires to retrieve model's attributes with relations and nothing more.
In this case this methods might be enough useful for you.
<br><br>


### Extension supports:

    * eloquent relations
    * illuminate pagination
    * PDO::FETCH_OBJ and PDO::FETCH_ASSOC fetch modes


### Requirements
    laravel >= 5.3
    
### Instalation
Run

```
composer require --prefer-dist volosyuk/simple-eloquent "*"
```

or add this code line to the `require` section of your `composer.json` file:

```
"volosyuk/simple-eloquent": "*"
```

### Usage

Just use *SimpleEloquent* trait in your model. If you want to get models with relations attach SimpleEloquent trait to related models as well.

```php
use Volosyuk\SimpleEloquent\SimpleEloquent;
class Department extends \Illuminate\Database\Eloquent\Model
{
    use SimpleEloquent;
}
```
Then use *getSimple()*(or another available) method instead of *get*.
All of available methods have the same signature as their default analogues. They have the same name but with _Simple_ suffix.
```php
$users = User::whereHas('units')->withCount('units')->with('units')->limit(10)->getSimple()
```

### List of available methods

  * allSimple - see [all](https://laravel.com/api/5.3/Illuminate/Database/Eloquent/Model.html#method_all) method
  * getSimple - see [get](https://laravel.com/api/5.3/Illuminate/Database/Eloquent/Builder.html#method_get) method
  * findSimple - see [find](https://laravel.com/api/5.3/Illuminate/Database/Eloquent/Builder.html#method_find) method
  * findSimpleOrFail - see [findOrFail](https://laravel.com/api/5.3/Illuminate/Database/Eloquent/Builder.html#method_findOrFail) method
  * firstSimple - see [first](https://laravel.com/api/5.3/Illuminate/Database/Eloquent/Builder.html#method_first) method
  * firstSimpleOrFail - see [firstOrFail](https://laravel.com/api/5.3/Illuminate/Database/Eloquent/Builder.html#method_firstOrFail) method
  * findManySimple - see [findMany](https://laravel.com/api/5.3/Illuminate/Database/Eloquent/Builder.html#method_findMany) method
  * paginateSimple - see [paginate](https://laravel.com/api/5.3/Illuminate/Database/Eloquent/Builder.html#method_paginate) method
  * simplePaginateSimple - see [simplePaginate](https://laravel.com/api/5.3/Illuminate/Database/Eloquent/Builder.html#method_simplePaginate) method


### Profit

This extesion was tested on real project. 

##### Exapmle 1 - users with details; 50 per page

```php
$users = User::with([
    'units.leaders.performance',
    'teams.leaders.performance',
    'programs.courses'
])->limit(50)->get()
```

|                   | Time          | Memory consuming  |
| :---              |          ---: |          ---:     |
| get()             | 0.62s         | 6.0mb             |
| getSimple()       | 0.19s         | 3.0mb             |

##### Exapmle 2 - select models with 5-level relation

```php
$goals = Goal::with('goalUser.user.courses.points.user')->limit(20)->get()
```

|                   | Time          | Memory consuming  |
| :---              |          ---: |          ---:     |
| get()             | 1.48s         | 28.5mb             |
| getSimple()       | 0.47s         | 15.5mb             |

##### Exapmle 3 - let's select 1000 models
```php
$performance = Performance::whereHas('user')->with('goal.goalUser')->limit(1000)->get()
```

|                   | Time          | Memory consuming  |
| :---              |          ---: |          ---:     |
| get()             | 0.22s         | 2.0mb             |
| getSimple()       | 0.06s         | 1.1mb             |