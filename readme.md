# Laravel admin

###Installation:
Add to composer.json:

```json
    "repositories":
    [
      {
        "type": "vcs",
        "url": "https://github.com/takoyta/laravel_kirya_admin"
      }
    ]
```
Then run:
```
$ composer require takoyta/laravel_kirya_admin
$ mkdir app/Admin
$ php artisan vendor:publish --provider="KiryaDev\Admin\AdminServiceProvider"
```

And add `/public/static-cache/` to `.gitignore`
