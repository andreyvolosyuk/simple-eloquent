Simple eloquent extension for Laravel
===========================

[![Latest Version](https://img.shields.io/packagist/v/volosyuk/simple-eloquent.svg?style=flat-square)](https://packagist.org/packages/volosyuk/simple-eloquent)
[![Software License](https://img.shields.io/github/license/andreyvolosyuk/simple-eloquent.svg?style=flat-square)](https://github.com/andreyvolosyuk/simple-eloquent/blob/master/LICENSE.txt)
[![Quality Score](https://img.shields.io/scrutinizer/g/andreyvolosyuk/simple-eloquent.svg?style=flat-square)](https://scrutinizer-ci.com/g/andreyvolosyuk/simple-eloquent/)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/andreyvolosyuk/simple-eloquent.svg?style=flat-square)](https://scrutinizer-ci.com/g/andreyvolosyuk/simple-eloquent/code-structure)


This extension presents some methods for eloquent ORM in order to reduce time and memory consuming.
Sometimes application doesn't need all of eloquent overhead. It just requires to retrieve model's attributes with relations and nothing more.
In this case this methods might be enough useful for you.
<br><br>

### Extension supports:

    - eloquent relations
    - illuminate pagination
    - PDO::FETCH_OBJ and PDO::FETCH_ASSOC fetch modes


### Requirements

    laravel >= 5.3
    
### Installation

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

  * allSimple - see [all](https://laravel.com/api/5.6/Illuminate/Database/Eloquent/Model.html#method_all) method
  * getSimple - see [get](https://laravel.com/api/5.6/Illuminate/Database/Eloquent/Builder.html#method_get) method
  * findSimple - see [find](https://laravel.com/api/5.6/Illuminate/Database/Eloquent/Builder.html#method_find) method
  * findSimpleOrFail - see [findOrFail](https://laravel.com/api/5.6/Illuminate/Database/Eloquent/Builder.html#method_findOrFail) method
  * firstSimple - see [first](https://laravel.com/api/5.6/Illuminate/Database/Eloquent/Builder.html#method_first) method
  * firstSimpleOrFail - see [firstOrFail](https://laravel.com/api/5.6/Illuminate/Database/Eloquent/Builder.html#method_firstOrFail) method
  * findManySimple - see [findMany](https://laravel.com/api/5.6/Illuminate/Database/Eloquent/Builder.html#method_findMany) method
  * paginateSimple - see [paginate](https://laravel.com/api/5.6/Illuminate/Database/Eloquent/Builder.html#method_paginate) method
  * simplePaginateSimple - see [simplePaginate](https://laravel.com/api/5.6/Illuminate/Database/Eloquent/Builder.html#method_simplePaginate) method

### Profit

This extension was tested on real project. 

##### Example 1 - users with details; 50 per page

```php
$users = User::with([
    'units.leaders.performance',
    'teams.leaders.performance',
    'programs.courses'
])->limit(50)->get()
```

|                   | Time          | Memory consumption  |
| :---              |          ---: |          ---:       |
| get()             | 0.62s         | 6.0mb               |
| getSimple()       | 0.19s         | 3.0mb               |

##### Example 2 - select models with 5-level relation

```php
$goals = Goal::with('goalUser.user.courses.points.user')->limit(20)->get()
```

|                   | Time          | Memory consumption  |
| :---              |          ---: |          ---:       |
| get()             | 1.48s         | 28.5mb              |
| getSimple()       | 0.47s         | 15.5mb              |

##### Example 3 - let's select 1000 models

```php
$performance = Performance::whereHas('user')->with('goal.goalUser')->limit(1000)->get()
```

|                   | Time          | Memory consumption  |
| :---              |          ---: |          ---:       |
| get()             | 0.22s         | 2.0mb               |
| getSimple()       | 0.06s         | 1.1mb               |

### What do you lose?

Since this extension provides less expensive methods you'll definitely lose some functionality. Basic methods return collection of eloquent models in contrast to new additional methods which return collection of stdClasses|arrays.
This example will show the difference between results.

```php
$categories = Category::with('articles')->get() // want to grab all categories with articles
```

##### Method _get_ returns

```php
Illuminate\Database\Eloquent\Collection::__set_state([
    'items' => [
        0 => Category::__set_state([
            'guarded' => [],
            'connection' => 'default',
            'table' => NULL,
            'primaryKey' => 'id',
            'keyType' => 'int',
            'incrementing' => true,
            'with' => [],
            'withCount' => [],
            'perPage' => 15,
            'exists' => true,
            'wasRecentlyCreated' => false,
            'attributes' => [
                'id' => '1',
                'name' => 'Test category',
                'created_at' => '2017-12-21 12:33:34',
                'updated_at' => '2017-12-21 12:33:34'
            ],
            'original' => [
                'id' => '1',
                'name' => 'Test category',
                'created_at' => '2017-12-21 12:33:34',
                'updated_at' => '2017-12-21 12:33:34',
            ],
            'changes' => [],
            'casts' => [],
            'dates' => [],
            'dateFormat' => NULL,
            'appends' => [],
            'dispatchesEvents' => [],
            'observables' => [],
            'relations' => [
                'articles' => Illuminate\Database\Eloquent\Collection::__set_state([
                    'items' => [
                        0 => Article::__set_state([
                            'guarded' => [],
                            'connection' => 'default',
                            'table' => NULL,
                            'primaryKey' => 'id',
                            'keyType' => 'int',
                            'incrementing' => true,
                            'with' => [],
                            'withCount' => [],
                            'perPage' => 15,
                            'exists' => true,
                            'wasRecentlyCreated' => false,
                            'attributes' => [
                                'id' => '1',
                                'category_id' => '1',
                                'title' => 'Test article',
                                'created_at' => '2017-12-21 12:33:34',
                                'updated_at' => '2017-12-21 12:33:34',
                            ],
                            'original' => [
                                'id' => '1',
                                'category_id' => '1',
                                'title' => 'Test article',
                                'created_at' => '2017-12-21 12:33:34',
                                'updated_at' => '2017-12-21 12:33:34',
                            ],
                            'changes' => [],
                            'casts' => [],
                            'dates' => [],
                            'dateFormat' => NULL,
                            'appends' => [],
                            'dispatchesEvents' => [],
                            'observables' => [],
                            'relations' => [],
                            'touches' => [],
                            'timestamps' => true,
                            'hidden' => [],
                            'visible' => [],
                            'fillable' => [],
                        ]),
                    ],
                ]),
            ],
            'touches' => [],
            'timestamps' => true,
            'hidden' => [],
            'visible' => [],
            'fillable' => [],
        ])
    ]
]);
```

##### Method _getSimple_ returns

```php
Illuminate\Support\Collection::__set_state([
    'items' => [
        0 => stdClass::__set_state([
            'id' => '1',
            'name' => 'Test category',
            'created_at' => '2017-12-21 12:43:44',
            'updated_at' => '2017-12-21 12:43:44',
            'articles' => Illuminate\Support\Collection::__set_state([
                'items' => [
                    0 => stdClass::__set_state([
                        'id' => '1',
                        'category_id' => '1',
                        'title' => 'Test article',
                        'created_at' => '2017-12-21 12:43:44',
                        'updated_at' => '2017-12-21 12:43:44',
                    ]),
                ],
            ]),
        ]),
    ]
]);
```

Since you'll get stdClasses|arrays you won't reach out to casting, appends, guarded/fillable, crud and another possibilities.
That's why new methods are much faster :smirk:
