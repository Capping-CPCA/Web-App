# CPCA Web Application

[Important stuff goes here]

### Using the template files

When creating a new page in the application (or updating an existing one),
make sure that the `header.php` and `footer.php` files are included.

The format of each page file should be as follows:

```php
<?php
$title = '[Some title]';        // used for browser tab
$pageTitle = '[Page title]';    // title in header
include('shared/header.php');
?>

<!--
Put HTML and other PHP code here. Please note that the <body>
tag is already used in the header.php and footer.php files.

All of the content that goes here will be displayed between the
header and the footer. The scrolling is already set up for overflow.
-->

<?php include('shared/footer.php'); ?>
```