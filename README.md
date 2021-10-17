### Laravel MySQL Fulltext search engine for the laravel scout



## Usage:



### Required

MySql store engine:

* MyISAM
* InnoDB: version ^5.5

To be able to search with 2 characters, configure Minimum Word Length:

* MyISAM: 

  ```sql
  ft_min_word_len = 2
  ```

* InnoDB:

  ```mysql
  innodb_ft_min_token_size = 2
  ```

​	

### Laravel Scout:

First, You need install [Laravel Scout](https://laravel.com/docs/8.x/scout) package.


### Install:

Composer:
```bash

$ composer require nin/mysql-ft-search

```

If you don't use auto-discovery, add the ServiceProvider to the providers array in config/app.php
```php
Nin\MySqlFtSearc\ServiceProvider::class,

```

Publish config:
```bash
$ php artisan vendor:publish --provider="Nin\MySqlFtSearc\ServiceProvider"

```

Laravel scout driver configuration:
```
SCOUT_DRIVER=mysql  # Refer to config('mysql-ft-search.scout_driver_name')
```


### Schema:

Make FullText column.

```php
use Illuminate\Database\Migrations\Migration;
use Nin\MySqlFtSearch\Facade as FtSchema;

class CreatePostsTable extends Migration
{
    public function up()
    {
        FtSchema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
        
            $table->fulltext(['title']);
        });
    }
}

```

### Configuring Model:

The columns of the full text index.

```php

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Post extends Model
{
    use Searchable;

    /**
     * The columns of the full text index
     */
    public $searchable = [
        'title',
    ];
}
```

### Searching:

```php

use App\Models\Post;

$rs = Post::search('Foo')->get();
```

## License

[MIT license](LICENSE.md)
