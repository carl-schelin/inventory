# Purpose

Currently the Inventory uses Interfaces and the interface table to manage monitoring of systems. There's a 
checkbox for the existing official tool but there's also a checkbox for the alternative tool, Nagios, which 
monitors all systems regardless of the environment.

There are also checkboxes for various things to be checked. Ping, ssh access, http, etc.

Right now the option checkbox only works for Nagios and not for the official monitoring. When there are 
multiple options for monitoring, nagios, zabbix, openview, etc, then the information needs to be pulled 
out and put into a separate table for better management.

The Purpose behind this bit of code is to pull that out of the interface table and into a separate table so 
that multiple monitoring options can also select multiple types of monitoring. Then use the table to manage 
alerting

# Tables

Here are the tables used for managing the information:

    MariaDB [inventory]> desc monitoring;
    +-----------------+---------+------+-----+---------+----------------+
    | Field           | Type    | Null | Key | Default | Extra          |
    +-----------------+---------+------+-----+---------+----------------+
    | mon_id          | int(10) | NO   | PRI | NULL    | auto_increment |
    | mon_interfaceid | int(10) | NO   |     | 0       |                |
    | mon_system      | int(10) | NO   |     | 0       |                |
    | mon_type        | int(10) | NO   |     | 0       |                |
    | mon_active      | int(10) | NO   |     | 0       |                |
    | mon_group       | int(10) | NO   |     | 0       |                |
    | mon_user        | int(10) | NO   |     | 0       |                |
    | mon_notify      | int(10) | NO   |     | 0       |                |
    | mon_hours       | int(10) | NO   |     | 0       |                |
    +-----------------+---------+------+-----+---------+----------------+
    9 rows in set (0.01 sec)

    MariaDB [inventory]> desc mon_type;
    +---------+----------+------+-----+---------+----------------+
    | Field   | Type     | Null | Key | Default | Extra          |
    +---------+----------+------+-----+---------+----------------+
    | mt_id   | int(10)  | NO   | PRI | NULL    | auto_increment |
    | mt_name | char(30) | NO   |     |         |                |
    +---------+----------+------+-----+---------+----------------+
    2 rows in set (0.00 sec)

    MariaDB [inventory]> desc mon_system;
    +---------+----------+------+-----+---------+----------------+
    | Field   | Type     | Null | Key | Default | Extra          |
    +---------+----------+------+-----+---------+----------------+
    | ms_id   | int(10)  | NO   | PRI | NULL    | auto_increment |
    | ms_name | char(40) | NO   |     |         |                |
    +---------+----------+------+-----+---------+----------------+
    2 rows in set (0.01 sec)


