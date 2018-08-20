# PHP Migrator

Migrate from PHP Flat or HTML to Laravel




You can also:
  - Generate a laravel project with an existing PHP FLAT project
  - Generate automatically traductions
  - Get MetaTags with SQL syntax


### Usage

```sh
php -S localhost:80
```
## Documentation:

You have to check the container of your website that is the same in every page, like this:
```html
<html>
<head>
</head>
<body>
<div class="header">...</div>
<div class="different">...</div>
<div class="footer">...</div>
</body>
</html>
```

So, you have to put this CSS Selector in the tool:

```css
.different
```

### Complex Selector
Also, if you need put an complex selector.

```html
<html>
<head>
</head>
<body>
<div class="header">...</div>
<div class="different">
    <div id="different_section">
        <div class="container">...</div>
    </div>
</div>
<div class="footer">...</div>
</body>
</html>
```
 You should put this:
```css
.different #different_section .container
```
