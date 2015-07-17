# EasyDatabaseHandler
PHP class for ease of MySQL database use using MySQLi.

## How-to

#### Initialization
```php
<?php
  require("Database.php"); // Including the main class
  $db = new Database(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE); // Initializing the Database object
  /*
   * Do everything here
   */
   
  $db->close(); // Closing the connection when use is complete
  
?>
```
