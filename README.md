Hello, this code help you to create live thumbnail on your custom path. All existing thumbnail packages for laravel made thumbnails output via php, using the laravel router that increased the consume of memory.
This code generate live thumbnail and output created thumbnail via your default web server.

1. Create new Trait in your project (Copy ``Traits/Thumbnails.php`` to your ``./app/Traits/Thumbnails.php``)
2. Add this Trait into your Model like example below
```
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Thumbnails;

class Order extends Model
{	
	use Thumbnails;
  protected $table = 'order';
}
```
3. Create the "thumbnails" folder into your storage - ``./storage/app/public/thumbnails``
4. Using the render inside your blade template , example
```
<img src="{{ $order->urlThumbnails('avatar', '60x60' , 'fit') }}" width="60" height="60" />
```
Where - 'avatar' - it is column name that contain the path to source image;
        '60x60' - it is thumbnail size;
        'fit' - it is a resize method ( allows methods: fit , resize , crop )

6. Done. Congrantulations. If you have any promlems, then you need to check up your path to source file inside the ``Traits/Thumbnails.php``
