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

drop table alarm_blocks;
drop table alarm_type;
drop table alarms;
drop table apigroups;
drop table application;
drop table bigfix;
drop table business;
drop table changelog;
drop table checklist;
drop table company;
drop table config;
drop table contact;
drop table contacts;
drop table events;
drop table family;
drop table faq;
drop table faq_comment;
drop table faq_detail;
drop table faq_tags;
drop table faq_votes;
drop table firewall;
drop table handoff;
drop table intvuln;
drop table ip_addresses;
drop table ip_subnets;
drop table keywords;
drop table maint;
drop table message_group;
drop table issue_morning;
drop table networks;
drop table objects;
drop table oncall;
drop table outage;
drop table patching;
drop table policy;
drop table policy_description;
drop table policy_type;
drop table poll_answers;
drop table poll_questions;
drop table polls;
drop table psaps;
drop table psaps_arch;
drop table purchaseorder;
drop table report;
drop table repos;
drop table resources;
drop table retire;
drop table rh_groups;
drop table rh_packages;
drop table rh_selections;
drop table rights;
drop table rsdp_accept;
drop table rsdp_applications;
drop table rsdp_backups;
drop table rsdp_check;
drop table rsdp_comments;
drop table rsdp_datacenter;
drop table rsdp_designed;
drop table rsdp_filesystem;
drop table rsdp_infosec;
drop table rsdp_infrastructure;
drop table rsdp_interface;
drop table rsdp_osteam;
drop table rsdp_platform;
drop table rsdp_san;
drop table rsdp_server;
drop table rsdp_status;
drop table rsdp_tickets;
drop table rules;
drop table san;
drop table security;
drop table source_node;
drop table spectre;
drop table sudoers;
drop table swbackup;
drop table vlanz;
drop table vulnerabilities;
drop table vulnowner;
drop table west;


# keep these tables, for now; identify which ones would be pre-populated

x a_groups - should be empty on fresh installation
x backups - should be empty on fresh installation
x bugs - empty
x bugs_detail - empty
x business_unit - empty
x certs - empty
x chkerrors - empty - delete as part of the chkserver script?
x chkserver - empty - same as chkerrors.
x cities - no reason this can't be populated.
x cluster - unused however could be used to identify server parents...
x comments - should be empty
x country - should save
x department - should be empty
x device - should be empty; part of the server nameing process
x email - should be empty
x environment - was from snow; should be empty though. maybe part of the location table?
excludes - should be deleted
x features - should be empty
x features_detail - should be empty
x filesystem - should be empty;
x grouplist - connects users with groups
x hardware - hardware table; probably can be empty
x help - defines which help screen the user has already seen. should be empty
x images - image descriptions for uploaded impages; should be empty
x int_duplex - list of interface duplex types
x int_media - list of interface media types
x int_plugtype - list of system plug types
x int_redundancy - interface redundancy listing
x int_role - interface role
x int_speed - interface speeds
x int_volts - system voltage
x interface - main interface table for systems; should be empty
x inttype - interface type; management, application, etc.
x inventory - main list of servers; should be empty
x ip_zones - list of ip network zones - probably should be emptyu
x issue - issue tracker - should be empty
x issue_detail - detail record - should be empty
x issue_support - who did you call for support; details about the call. should be empty;
x levels - access levels for the inventory
x licenses - license manager sub table for software; should be empty
x loc_types - location types; probably should be empty
x locations - location manager; should be empty
x log - might make this an external file vs a table;
x maint_window - list of maintenance windows used for servers; should be empty
x manageusers
x models - hardware table define model information
x modified - notes who changed what. need to review this
x modules - system modules; bug tracker, feature tracker.
x mon_system - list of monitoring systems - 
x mon_type - list of things to monitor.
x monitoring - new monitoring system. should be empty.
x operatingsystem - should be in the software manager
x organizations - company organizations should be empty
x packages - installed packages
x parts -  hardware stuff; list of hardware types.
x products - list of products for this installation; should be empty
x projects - list of projects associated with products; should be empty
x roles - roles groups have. should be empty probably.
x routing - routing information for each server; should be empty
x service - service class information. can probably stick around
x severity - severity of a bug report or feature
x software - server associations for software. much xfered to the software manager
x states - part of locations
x support - hardware support contract information. should be empty
x supportlevel - response level from the vendor. should be empty
x sw_support - software support contracts. should be empty
x sysgrp - group imports from servers; should be empty
x sysgrp_members - associations between users and what groups they're in
x syspwd - user imports from servers; should be empty
x tag_types - describes each management module type.
x tags - tags for the various modules; should be empty
x themes - system themes from jquery
x titles - possible user titles. can probably be removed
x users - user information
x vlans - vlan table; part of the ip manager. should be empty
x zones - time zones.


table cleanup; where we drop columns.

a_groups:

alter table a_groups drop column grp_snow;
alter table a_groups drop column grp_magic;
alter table a_groups drop column grp_category;
alter table a_groups drop column grp_changelog;
alter table a_groups drop column grp_clfile;
alter table a_groups drop column grp_clserver;
alter table a_groups drop column grp_report;
alter table a_groups drop column grp_clscript;


inventory table:

alter table inventory drop column inv_bigfix;
alter table inventory drop column inv_ciscoamp;
alter table inventory drop column inv_managebigfix;
alter table inventory drop column inv_centrify;
alter table inventory drop column inv_adzone;
alter table inventory drop column inv_domain;
alter table inventory drop column inv_rsdp;

users table:

alter table users drop column usr_bigfix;


Remove bigfix and ciscoamp from inventory edit page.
remove centrify info




table purging; these tables should be empty upon new installation

backups:

delete from backups;

bugs:

delete from bugs;
delete from bugs_detail;

business_unit;

delete from business_unit;

certs:

delete from certs;

cluster:

delete from cluster;

log:

delete from log; 

system info;

delete from syspwd;
delete from sysgrp;
delete from sysgrp_members;

users table;

delete from users where usr_id > 2; > 1 for admin only

manageusers table;

delete from manageusers;

filesystems table;

delete from filesystem; except for existing installations;


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

