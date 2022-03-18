# dynamica
School CS project for emulated social media platform. Built in HTML, CSS, PHP, and MySQL.


## instructions

1. You'll need a web server, hopefully packaged with Apache, MySQL and PHP. An easy one to use for this is [XAMPP.](https://www.apachefriends.org/download.html)
2. Download the repo source code (.zip) and extract into a directory inside your web server.
  - If using XAMPP, this is typically `C:/xampp/htdocs/`
3. Run the Apache and MySQL modules in XAMPP (or your equivalent web server)
4. Visit `localhost/dynamica-main/` in your web browser.
5. Explore the website!


## troubleshooting

If php cannot connect to the MySQLi db, it is likely that the proper passwords have not been set. To fix this:

1. Navigate to `C:/xampp/phpmyadmin/config.inc.php`
2. Update username and password to "root" in the config file
3. Open shell/cmd
4. Write `cd C:/xampp/mysql/bin`
5. Write `mysql -u root`
6a. Write `UPDATE mysql.user SET Password=PASSWORD('MyPassword') WHERE User='root';` - update "MyPassword" to your password. If doesn't work, see 6b. If works, see 7.
6b. Instead, write `ALTER USER 'root'@'localhost' IDENTIFIED BY 'MyPassword';` - update "MyPassword" to your password.
7. Write `flush privileges;`
