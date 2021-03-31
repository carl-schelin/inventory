#Inventory Database

##Overview

The Inventory database is a long term grass roots project specific to the environment. Much of the Inventory was created in response 
to identified issues or requests for information. In addition, many parts were created as I saw a need for data correlation between 
various parts of the environment.

This document will try to provide information on how to take the git repo and associate the necessary files in order to 
get it deployed and functioning.

##Disclaimer

As noted, this has been created over the past 10 years or so and much has been added to it as circumstances mandated. It's been 
upgraded three times so far and I'm working on a 4th upgrade. So no, this code isn't perfect. It's a collection of data managed by 
a framework I sort of hacked together.

#Build Configuration

The build preparation environment consists of two main directories. The Source Code itself. What's cloned from the github repository. 
And the Static Files.

#Static Files

The Static files consist of a copy of the configured **settings.php** file in each directory plus the jQuery installation in the css 
directory. Additional files might be placed in the pictures directory such as pictures of the racks which will be displayed when viewing 
the detail record of a server, and any icons or wait bars used by the Inventory to x show scripts are loading data.

#Configuration

The main configuration file is the **settings.php** file. This file contains host specific information in addition to several common 
settings used within the Inventory. When you set up your environment, you'll need to take the **settings.php** template and add in the 
information specific to the host you'll be hosting it on along with the access to the mysql back end database.

#Build Process

The build process combines the two directories into a single complete directory which is then synchronized with the target server.

My process:

1. git pull the inventory repository into an inventory directory
2. Copy the files into a staging directory
3. Sync the files from the static directory into the staging directory
4. Sync the full site to the target server




database cleanup:

remove the following tables:

no more RSDP:

There's an xxxx    edit/config set of files that need to be edited

x drop table alarm_blocks;
x drop table alarm_type;
x drop table alarms;
x drop table apigroups;
x drop table application;
X drop table bigfix;
x drop table business;
x drop table checklist;
x drop table company;
x drop table config;
x drop table contact;
x drop table contacts;
x drop table events;
x drop table family;
x drop table faq;
x drop table faq_comment;
x drop table faq_detail;
x drop table faq_tags;
x drop table faq_votes;
x drop table firewall;
x drop table handoff;
x drop table intvuln;
x drop table ip_addresses;
x drop table ip_subnets;
x drop table keywords;
x drop table maint;
x drop table message_group;
x drop table objects;
x drop table oncall;
x drop table outage;
x drop table policy;
x drop table policy_description;
x drop table policy_type;
x drop table poll_answers;
x drop table poll_questions;
x drop table polls;
x drop table psaps;
x drop table psaps_arch;
x drop table repos;
x drop table resources;
x drop table retire;
x drop table rh_groups;
x drop table rh_packages;
x drop table rh_selections;
x drop table rights;
x drop table rsdp_accept;
x drop table rsdp_applications;
x drop table rsdp_backups;
x drop table rsdp_check;
x drop table rsdp_comments;
x drop table rsdp_datacenter;
x drop table rsdp_designed;
x drop table rsdp_filesystem;
x drop table rsdp_infosec;
x drop table rsdp_infrastructure;
x drop table rsdp_interface;
x drop table rsdp_osteam;
x drop table rsdp_platform;
x drop table rsdp_san;
x drop table rsdp_server;
x drop table rsdp_status;
x drop table rsdp_tickets;
x drop table rules;
x drop table san;
x drop table security;
x drop table source_node;
x drop table spectre;
x drop table sudoers;
x drop table swbackup;
x drop table vlanz;
x drop table vulnerabilities;
x drop table vulnowner;
x drop table west;


# keep these tables, for now;

a_groups
backups
bugs
bugs_detail
business_unit
certs
changelog
chkerrors
chkserver
cities
cluster
comments
country
department
device
email
environment
excludes
features
features_detail
filesystem
grouplist
hardware
help
images
int_duplex
int_media
int_plugtype
int_redundancy
int_role
int_speed
int_volts
interface
inttype
inventory
ip_zones
issue
issue_detail
issue_morning
issue_support
levels
licenses
loc_types
locations
log
maint_window
manageusers
models
modified
modules
mon_system
mon_type
monitoring
networks
operatingsystem
organizations
packages
parts
patching
products
projects
purchaseorder
report
roles
routing
service
severity
software
states
support
supportlevel
sw_support
sysgrp
sysgrp_members
syspwd
tag_types
tags
themes
titles
users
vlans
zones


table cleanup; where we drop columns.

inventory table:

x alter table inventory drop column inv_bigfix;
x alter table inventory drop column inv_ciscoamp;
x alter table inventory drop column inv_managebigfix;
x alter table inventory drop column inv_centrify;
x alter table inventory drop column inv_adzone;
x alter table inventory drop column inv_domain;
x alter table inventory drop column inv_rsdp;

users table:

x alter table users drop column usr_bigfix;


Remove bigfix and ciscoamp from inventory edit page.
remove centrify info


purge the users table;

x delete from users where usr_id > 2;

purge the system info;

x delete from syspwd;
x delete from sysgrp;
x delete from sysgrp_members;


done checking databases:

x ./accounts/research/database.output
x ./admin/research/database.output
x ./api/research/database.output
x ./api/v1/research/database.output
x ./articles/research/database.output
x ./assets/research/database.output - may not be in use. no assets table.
x ./bugs/research/database.output
x ./certs/research/database.output
x ./css/research/database.output
x ./datacenter/research/database.output
x ./edit/research/database.output
x ./exclude/research/database.output
x ./faq/research/database.output
x ./features/research/database.output
x ./functions/research/database.output
x ./hardware/research/database.output
x ./hwm/research/database.output
x ./image/research/database.output
x ./imgs/research/database.output
x ./inventory/research/database.output
x ./ipam/research/database.output
x ./issue/research/database.output
x ./license/research/database.output
x ./listings/research/database.output
x ./login/research/database.output
x ./manage/research/database.output
x ./monitoring/research/database.output
x ./pictures/research/database.output
x ./reports/research/database.output
x ./research/research/database.output
x ./show/research/database.output
x ./swm/research/database.output
x ./tm/research/database.output

