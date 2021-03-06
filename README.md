# dynamica
My school CS project for an emulated social media platform. Built in HTML, CSS, JQuery, PHP, and MySQL.


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
6. Write `UPDATE mysql.user SET Password=PASSWORD('MyPassword') WHERE User='root';` - update "MyPassword" to your password. If works, skip to 8. If doesn't work, see 7. 
7. Instead, write `ALTER USER 'root'@'localhost' IDENTIFIED BY 'MyPassword';` - update "MyPassword" to your password.
8. Write `flush privileges;`


## extra config

Some extra things may have to be set up for the same server experience as on my home PC

`C:\Windows\System32\drivers\etc`
hosts --> :

```
# localhost name resolution is handled within DNS itself.
	127.0.0.1       localhost
	::1             localhost
	127.0.0.1	dynamica.com
```


`c:\xampp\apache\conf\extra`
httpd-vhosts.conf --> :

```
<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs/dynamica-main/"
    ServerName dynamica.com
</VirtualHost>
```


`c:\xampp\apache\conf\`
httpd.conf --> :

```
<Directory "C:/xampp/htdocs/dynamica-main/">
    Options -Indexes +FollowSymLinks
    AllowOverride All
    Order allow,deny
    Allow from all
</Directory>
```
