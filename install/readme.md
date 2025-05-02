### Instllation process

If you're cloning/pulling, you should already have git but yea, you'll need to have git installed.

Install the following packages:

* git
* httpd
* mysql
* mysql-server
* php

### Getting Started

```
systemctl enable mysqld
systemctl enable httpd
systemctl start mysqld
systemctl start httpd
```

#### MariaDB

If you've installed Mariadb vs 8.0, this system using mysqli so you'll need to install php_mysqlnd

    dnf install -y php_mysqlnd

#### SELinux

If SELinux is installed, in the inventory directory, run:

    restorecon -R -v inventory

To manage selinux, install setroubleshoot

    dnf install -y setroubleshoot

#### MySQL/MariaDB

Once installed, run mysql_secure_installation to get it set up.

For the database, create the inventory database.

    create database inventory;

Create an inventory admin user with full rights to the inventory database.

```
CREATE USER 'invadmin'@'localhost' IDENTIFIED BY '[password]';
GRANT ALL PRIVILEGES ON inventory.* TO 'invadmin'@'localhost';
FLUSH PRIVILEGES;
```

In the sql directory, loop through the files and import them into the inventory database.

```
for IMPORT in $(ls *sql)
do
  echo ${IMPORT}
  mysql --user=root -p inventory < ${IMPORT}
done
```

You'll have to enter the password for each file.

#### Data Files

In the txt directory are multiple files used to prepopulate the inventory database. This data is required to set up an admin account then update various tables with expected defaults.

For now, you'll need to log into mysql and use the database, then just copy and paste in the information in the files.

#### Settings File

The settings.php file contains server information, mysql connection details, path variables and a few other settings. You mainly have to update the server information and connection details such as username and password to the database.

Once done, copy the settings.php file and fixsettings script into the invroot directory and run the script. It will link the settings.php file into each directory.

Note that you can change the debugging option in the settings.php file. If you make it write errors to the screen, some aspects of the inventory won't work quite as expected as the version of PHP might generate Warning messages that I haven't identified yet.

### Cascading Style Sheets

In the css directory, I have jquery.js 3.6.0, jquery-ui 1.13.1 in a jquery-ui directory, and jQuery-ui-themes in a jquery-ui-themes directory installed.

You should be able to locate a tar file in http://schelin.org/inventory/css.tar

### Images

In the imgs directory, I have several image files used in the system. All are necessary

* Inventory image header. This can be changed to a different branded value if you like and change the name in the settings.php file.
* Progress Bar images
* Pencil image to indicate editable text
* IPAM documentation images

You should be able to locate a tar file in http://schelin.org/inventory/imgs.tar

### Finished

With these tasks done, you should be able to log in to the new install with the admin:admin credentials and start adding devices.

