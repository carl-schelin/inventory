### Instllation process

If you've installed Mariadb vs 8.0, this system using mysqli so you'll need to install php_mysqlnd

    dnf install -y php_mysqlnd

#### SELinux

If SELinux is installed, in the inventory directory, run:

    restorecon -R -v inventory

To manage selinux, install setroubleshoot

    dnf install -y setroubleshoot

#### MySQL/MariaDB

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

### Finished

With these tasks done, you should be able to log in to the new install with the admin:admin credentials and start adding devices.



