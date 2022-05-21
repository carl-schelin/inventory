### Updates

The following sections list out the changes in the mysql database during the transition over to Inventory 4.0.

As I identify changes, I'll start with a timestamp so we know when changes occurred.

When the changes are applied to the running systems, I'll drop another timestamp indicating databases are updated and which systems have been updated.


### Maintain Tables

We'll be keeping the following tables. After tables will be a list of modifications. Mostly changing the default date from 0000-00-00 00:00:00 to 
1971-01-01 00:00:00 where needed. The new MySQL/MariaDB doesn't like defaults of 0000. The code's already been changed.

Note that this is for existing installations. New installations can ignore this file and the delete.md file.

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
x net_zones - list of network zones - probably should be empty
x operatingsystem - should be in the software manager
x organizations - company organizations should be empty
x packages - installed packages
x parts -  hardware stuff; list of hardware types.
x patching - part of the maintenance windows form.
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


### New Tables

tag_types
CREATE TABLE `tag_types` (
  `type_id` int(10) NOT NULL AUTO_INCREMENT,
  `type_name` char(60) NOT NULL DEFAULT '',
  PRIMARY KEY (`type_id`)
);

add the following data and index to this table. There is code that uses this data as listed:
1: Servers
2: Locations
3: Products
4: Software
5: Hardware

new column in tags table for tag_types data:

alter table tags add column tag_type int(10) not null default 0 after tag_name;

And for existing servers to note entries as Servers:

update tags set tag_type = 1;


renamed tables due to changes in database reserved words:

groups was renamed to a_groups.
window was renamed to maint_window

probably going to simply rename all tables to inv_[tablename] eventually to prevent this from happening in the future.


renamed columns:

alter table locations change loc_west loc_identity char(10);


because of the date issue, all date fields are defaulted to 1971-01-01 and any existing entry changed.

alter table backups change bu_start bu_start char(15) not null default '1971-01-01';
alter table backups change bu_todate bu_todate timestamp not null default '1971-01-01 00:00:00';

alter table bugs change bug_discovered bug_discovered date not null default '1971-01-01';
alter table bugs change bug_closed bug_closed date not null default '1971-01-01';

alter table certs change cert_expire cert_expire date not null default '1971-01-01';

alter table chkserver change chk_closed chk_closed timestamp not null default '1971-01-01 00:00:00';

alter table device change dev_update dev_update date not null default '1971-01-01';

alter table email change mail_date mail_date date not null default '1971-01-01';

alter table excludes change ex_expiration ex_expiration date not null default '1971-01-01';

alter table features change feat_discovered feat_discovered date not null default '1971-01-01';
alter table features change feat_closed feat_closed date not null default '1971-01-01';

alter table filesystem change fs_update fs_update date not null default '1971-01-01';

alter table hardware change hw_purchased hw_purchased date not null default '1971-01-01';
alter table hardware change hw_built hw_built date not null default '1971-01-01';
alter table hardware change hw_active hw_active date not null default '1971-01-01';
alter table hardware change hw_retired hw_retired date not null default '1971-01-01';
alter table hardware change hw_reused hw_reused date not null default '1971-01-01';
alter table hardware change hw_update hw_update date not null default '1971-01-01';
alter table hardware change hw_supportstart hw_supportstart date not null default '1971-01-01';
alter table hardware change hw_supportend hw_supportend date not null default '1971-01-01';

alter table images change img_date img_date date not null default '1971-01-01';

alter table interface change int_update int_update date not null default '1971-01-01';
alter table interface change int_mondate int_mondate date not null default '1971-01-01';

alter table inventory change inv_kernel inv_kernel date not null default '1971-01-01';
alter table inventory change inv_patched inv_patched date not null default '1971-01-01';

alter table issue change iss_discovered iss_discovered date not null default '1971-01-01';
alter table issue change iss_closed iss_closed date not null default '1971-01-01';

alter table licenses change lic_date lic_date date not null default '1971-01-01';

alter table models change mod_eopur mod_eopur date not null default '1971-01-01';
alter table models change mod_eoship mod_eoship date not null default '1971-01-01';
alter table models change mod_eol mod_eol date not null default '1971-01-01';

alter table modified change mod_date mod_date date not null default '1971-01-01';

alter table packages change pkg_update pkg_update date not null default '1971-01-01';

alter table patching change patch_date patch_date date not null default '1971-01-01';

alter table routing change route_update route_update date not null default '1971-01-01';

alter table software change sw_eol sw_eol date not null default '1971-01-01';
alter table software change sw_eos sw_eos date not null default '1971-01-01';
alter table software change sw_update sw_update date not null default '1971-01-01';

alter table sw_support change sw_eol sw_eol date not null default '1971-01-01';
alter table sw_support change sw_eos sw_eos date not null default '1971-01-01';

alter table sysgrp_members change mem_update mem_update date not null default '1971-01-01';

alter table sysgrp change grp_update grp_update date not null default '1971-01-01';

alter table syspwd change pwd_update pwd_update date not null default '1971-01-01';

alter table users change usr_start usr_start date not null default '1971-01-01';
alter table users change usr_end usr_end date not null default '1971-01-01';
alter table users change usr_checkin usr_checkin date not null default '1971-01-01';



# just for the old inventory

alter table alarms change alarm_timestamp alarm_timestamp timestamp not null default "1971-01-01 00:00:00";

alter table bigfix change big_release big_release date not null default "1971-01-01";
alter table bigfix change big_scheduled big_scheduled date not null default "1971-01-01";

alter table events change evt_date evt_date date not null default "1971-01-01";

alter table hardware change hw_eol hw_eol date not null default "1971-01-01";

alter table intvuln change iv_date iv_date date not null default "1971-01-01";

alter table inventory change inv_centrify inv_centrify date not null default "1971-01-01";

alter table maint change man_start man_start date not null default "1971-01-01";
alter table maint change man_end man_end date not null default "1971-01-01";

alter table outage change out_closed out_closed timestamp not null default "1971-01-01 00:00:00";

alter table policy change pol_date pol_date date not null default "1971-01-01";

alter table polls change poll_expires poll_expires date not null default "1971-01-01";

alter table psaps_arch change psap_updated psap_updated date not null default "1971-01-01";

alter table psaps change psap_updated psap_updated date not null default "1971-01-01";

alter table report change rep_date rep_date date not null default "1971-01-01";

alter table retire change ret_date ret_date date not null default "1971-01-01";

alter table rsdp_backups change bu_start bu_start date not null default "1971-01-01";

alter table rsdp_server change rsdp_completion rsdp_completion date not null default "1971-01-01";

alter table sudoers change sudo_expire sudo_expire date not null default "1971-01-01";

alter table vulnerabilities change vuln_date vuln_date date not null default "1971-01-01";
alter table vulnerabilities change vuln_deldate vuln_deldate date not null default "1971-01-01";




Now we need to update the data. still not a problem but lots of changes.

update backups set bu_start = '1971-01-01' where bu_start = '0000-00-00';
update backups set bu_todate = '1971-01-01 00:00:00' where bu_todate = '0000-00-00 00:00:00';
update bugs set bug_discovered = '1971-01-01' where bug_discovered = '0000-00-00';
update bugs set bug_closed = '1971-01-01' where bug_closed = '0000-00-00';
update certs set cert_expire = '1971-01-01' where cert_expire = '0000-00-00';
update chkserver set chk_closed = '1971-01-01 00:00:00' where chk_closed = '0000-00-00 00:00:00';
update device set dev_update = '1971-01-01' where dev_update = '0000-00-00';
update email set mail_date = '1971-01-01' where mail_date = '0000-00-00';
update excludes set ex_expiration = '1971-01-01' where ex_expiration = '0000-00-00';
update features set feat_discovered = '1971-01-01' where feat_discovered = '0000-00-00';
update features set feat_closed = '1971-01-01' where feat_closed = '0000-00-00';
update filesystem set fs_update = '1971-01-01' where fs_update = '0000-00-00';
update hardware set hw_purchased = '1971-01-01' where hw_purchased = '0000-00-00';
update hardware set hw_built = '1971-01-01' where hw_built = '0000-00-00';
update hardware set hw_active = '1971-01-01' where hw_active = '0000-00-00';
update hardware set hw_retired = '1971-01-01' where hw_retired = '0000-00-00';
update hardware set hw_reused = '1971-01-01' where hw_reused = '0000-00-00';
update hardware set hw_update = '1971-01-01' where hw_update = '0000-00-00';
update hardware set hw_supportstart = '1971-01-01' where hw_supportstart = '0000-00-00';
update hardware set hw_supportend = '1971-01-01' where hw_supportend = '0000-00-00';
update images set img_date = '1971-01-01' where img_date = '0000-00-00';
update interface set int_update = '1971-01-01' where int_update = '0000-00-00';
update interface set int_mondate = '1971-01-01' where int_mondate = '0000-00-00';
update inventory set inv_kernel = '1971-01-01' where inv_kernel = '0000-00-00';
update inventory set inv_patched = '1971-01-01' where inv_patched = '0000-00-00';
update issue set iss_discovered = '1971-01-01' where iss_discovered = '0000-00-00';
update issue set iss_closed = '1971-01-01' where iss_closed = '0000-00-00';
update licenses set lic_date = '1971-01-01' where lic_date = '0000-00-00';
update models set mod_eopur = '1971-01-01' where mod_eopur = '0000-00-00';
update models set mod_eoship = '1971-01-01' where mod_eoship = '0000-00-00';
update models set mod_eol = '1971-01-01' where mod_eol = '0000-00-00';
update modified set mod_date = '1971-01-01' where mod_date = '0000-00-00';
update packages set pkg_update = '1971-01-01' where pkg_update = '0000-00-00';
update patching set patch_date = '1971-01-01' where patch_date = '0000-00-00';
update routing set route_update = '1971-01-01' where route_update = '0000-00-00';
update software set sw_eol = '1971-01-01' where sw_eol = '0000-00-00';
update software set sw_eos = '1971-01-01' where sw_eos = '0000-00-00';
update software set sw_update = '1971-01-01' where sw_update = '0000-00-00';
update sw_support set sw_eol = '1971-01-01' where sw_eol = '0000-00-00';
update sw_support set sw_eos = '1971-01-01' where sw_eos = '0000-00-00';
update sysgrp_members set mem_update = '1971-01-01' where mem_update = '0000-00-00';
update sysgrp set grp_update = '1971-01-01' where grp_update = '0000-00-00';
update syspwd set pwd_update = '1971-01-01' where pwd_update = '0000-00-00';
update users set usr_start = '1971-01-01' where usr_start = '0000-00-00';
update users set usr_end = '1971-01-01' where usr_end = '0000-00-00';
update users set usr_checkin = '1971-01-01' where usr_checkin = '0000-00-00';


# again, for the old inventory


update alarms set alarm_timestamp = '1971-01-01 00:00:00' where alarm_timestamp = '0000-00-00 00:00:00';
update bigfix set big_release = '1971-01-01' where big_release = '0000-00-00';
update bigfix set big_scheduled = '1971-01-01' where big_scheduled = '0000-00-00';
update events set evt_date = '1971-01-01' where evt_date = '0000-00-00';
update hardware set hw_eol = '1971-01-01' where hw_eol = '0000-00-00';
update intvuln set iv_date = '1971-01-01' where iv_date = '0000-00-00';
update inventory set inv_centrify = '1971-01-01' where inv_centrify = '0000-00-00';
update maint set man_start = '1971-01-01' where man_start = '0000-00-00';
update maint set man_end = '1971-01-01' where man_end = '0000-00-00';
update outage set out_closed = '1971-01-01' where out_closed = '0000-00-00';
update policy set pol_date = '1971-01-01' where pol_date = '0000-00-00';
update polls set poll_expires = '1971-01-01' where poll_expires = '0000-00-00';
update psaps_arch set psap_updated = '1971-01-01' where psap_updated = '0000-00-00';
update psaps set psap_updated = '1971-01-01' where psap_updated = '0000-00-00';
update report set rep_date = '1971-01-01' where rep_date = '0000-00-00';
update retire set ret_date = '1971-01-01' where ret_date = '0000-00-00';
update rsdp_backups set bu_start = '1971-01-01' where bu_start = '0000-00-00';
update rsdp_server set rsdp_completion = '1971-01-01' where rsdp_completion = '0000-00-00';
update sudoers set sudo_expire = '1971-01-01' where sudo_expire = '0000-00-00';
update vulnerabilities set vuln_date = '1971-01-01' where vuln_date = '0000-00-00';
update vulnerabilities set vuln_deldate = '1971-01-01' where vuln_deldate = '0000-00-00';



### Table Cleanup

where we drop columns that aren't used any more. This will continue as the system is upgraded

a_groups:

alter table a_groups drop column grp_snow;
alter table a_groups drop column grp_magic;
alter table a_groups drop column grp_category;
alter table a_groups drop column grp_changelog;
alter table a_groups drop column grp_clfile;
alter table a_groups drop column grp_clserver;
alter table a_groups drop column grp_report;
alter table a_groups drop column grp_clscript;


hardware table: all are in the models table

alter table hardware drop column hw_speed;
alter table hardware drop column hw_size;
alter table hardware drop column hw_eol;

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


### Table Purge

these tables should be empty upon new installation. Just making sure of a clean system installation.

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


And some conversion work now.

In the tags table, the default is 0 right now but all should be set to 1 for 'Server Tag'.

update tags set tag_type = 1;


### Database Verification

done checking databases for columns that aren't used or should be cleared:

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


### New table

The text columns can't be null

alter table backups change bu_notes bu_notes text not null default "";

I want to modify backups anyway to have a sub table of when to run backups.

create table backupdays (
  bud_id int(10) not null auto_increment,
  bud_backupid int(10) not null default 0,    # pointer to the main backup record.
  bud_type int(10) not null default 0,        # incremental or full
  bud_time char(10) not null default "00:00", # when to perform backups
  bud_user int(10) not null default 0,        # who created this entry and when
  bud_timestamp timestamp not null default current_timestamp,
  bud_description char(50) not null default '',  # description for more information if necessary.
  primary key (bud_id)
);


### New Section 

This should be a chain; org, bu, group, user so adding the next level up.

alter table business_unit add column bus_org int(10) not null default 0 after bus_id;


### Timestamp 2021-09-29 

alter table tags change tag_name tag_name char(255) not null default '';

Change project to Software in tag_types table

update tag_types set type_name = 'Software' where type_id = 4;

Add Hardware to tag_types table

insert into tag_types set type_id = null, type_name = 'Hardware';


### Timestamp 2021-10-04

alter table products drop column prod_oldcode;
alter table products drop column prod_remedy;
alter table products drop column prod_type;
alter table products drop column prod_citype;
alter table products drop column prod_tier1;
alter table products drop column prod_tier2;
alter table products drop column prod_tier3;
alter table products drop column prod_group;


### Timestamp 2021-10-06

Removing grp_role and adding grp_business. It should be a chain where org is at top, then business, then group.

alter table a_groups drop column grp_role;
alter table a_groups add column grp_department int(10) not null default 0 after grp_organization;
rename table business_unit to business;


### Timestamp 2021-10-16

Basically changing to be more org structure like. Org at top, then bus, then dept, then groups, then users.

alter table organizations add column org_manager int(10) not null default 0;
alter table business change bus_org bus_organization int(10) not null default 0 after bus_name;
alter table business add column bus_manager int(10) not null default 0;
alter table business drop column bus_unit;
alter table department change dep_dept dep_business int(10) not null default 0 after dep_name;
alter table department drop column dep_unit;
alter table department add column dep_manager int(10) not null default 0;
alter table a_groups drop column grp_organization;

dropped:
bus_org (changed to bus_organization)
bus_unit
dep_dept (changed to dep_business)
dep_unit
grp_organization


### Timestamp 2021-10-19

Search for the following interface columns as they can be replaced by the new IPAM.

alter table interface drop column int_monstatus;
alter table interface drop column int_monservice;
alter table interface drop column int_mondate;
alter table interface drop column int_xpoint;
alter table interface drop column int_ypoint;
alter table interface drop column int_zpoint;

int_server
int_domain
int_ip6 - ipaddress.ip_ipv6
int_addr - ipaddress.ip_ipv4
int_vaddr - verification; delete
int_veth - verification; delete
int_network - it's zero for all? is it used?
int_mask - ipaddress.ip_network -> network.net_mask
int_gate - ipaddress.ip_type == gateway
int_vgate - verification; delete
int_vlan - ipaddress.ip_network -> network.net_vlan
int_zone - ipaddress.ip_network -> network.net_zone -> net_zones.zone_zone (zone_acronym).


Part of monitoring now
int_openview - mon_system.ms_name
int_nagios - mon_system.ms_name
int_ping - mon_type.mt_name
int_ssh - mon_type.mt_name
int_http - mon_type.mt_name
int_ftp - mon_type.mt_name
int_smtp - mon_type.mt_name
int_snmp - mon_type.mt_name
int_load - mon_type.mt_name
int_uptime - mon_type.mt_name
int_cpu - mon_type.mt_name
int_swap - mon_type.mt_name
int_memory - mon_type.mt_name
int_cfg2html
int_notify - monitoring.mon_notify
int_hours - monitoring.mon_hours


### Timestamp 2021-10-29

Updated lnmt1cuomtool11 and inventory with the above table changes.


### Timestamp 2021-11-26

Updated mysql on RHEL8 with the above table changes.

Error:

select tag name,tag_companyid from tags where tag_group = 1 and tag_type = 1 group by tag_name order by tag_name
Expression #2 of SEELCT list is not in GROUP BY clause and contains nonaggregated column 'inventory.tags.tag_companyid' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by

modify mysql:

SET GLOBAL sql_mode(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));


### Timestamp 2021-11-29

Remove the following from the users table:

alter table users drop column usr_altemail;
alter table users drop column usr_clientid;
alter table users drop column usr_page;
alter table users drop column usr_pagemail;
alter table users drop column usr_deptname;
alter table users drop column usr_magic;
alter table users drop column usr_report;
alter table users drop column usr_confirm;
alter table users drop column usr_maint;
alter table users drop column usr_disposition;
alter table users drop column usr_headers;
alter table users drop column usr_start;
alter table users drop column usr_end;
alter table users drop column usr_maillist;

### Timestamp 2021-11-30

Updated nikodemus tables

### Timestamp 2021-12-06

Fixed missing default setting for a blank drop down menu.

### Timestamp 2021-12-13

For the converted systems, get the first server id in the inventory and delete all software on servers that don't exist.
For a new site, this isn't necessary.

create table svr_software (
  svr_id int(10) not null auto_increment,
  svr_companyid int(10) not null default 0,
  svr_softwareid int(10) not null default 0,
  svr_groupid int(10) not null default 0,
  svr_certid int(10) not null default 0,
  svr_facing int(10) not null default 0,
  svr_primary int(10) not null default 0,
  svr_locked int(10) not null default 0,
  svr_userid int(10) not null default 0,
  svr_verified int(10) not null default 0,
  svr_update date not null default '1971-01-01',
  primary key (svr_id)
);

create table sw_types (
  typ_id int(10) not null auto_increment,
  typ_name char(255) not null default '',
  primary key (typ_id)
);

for existing sites or converted ones, the order is correct for the rest of the updates that follow.

insert into sw_types set typ_id = null,typ_name = 'Application';
insert into sw_types set typ_id = null,typ_name = 'Authentication';
insert into sw_types set typ_id = null,typ_name = 'Backups';
insert into sw_types set typ_id = null,typ_name = 'Cluster';
insert into sw_types set typ_id = null,typ_name = 'Application Server';
insert into sw_types set typ_id = null,typ_name = 'Cell Broadcast';
insert into sw_types set typ_id = null,typ_name = 'Centrify';
insert into sw_types set typ_id = null,typ_name = 'OS';
insert into sw_types set typ_id = null,typ_name = 'Open Source';
insert into sw_types set typ_id = null,typ_name = 'CIL';
insert into sw_types set typ_id = null,typ_name = 'Commercial';
insert into sw_types set typ_id = null,typ_name = 'Custom';
insert into sw_types set typ_id = null,typ_name = 'Database';
insert into sw_types set typ_id = null,typ_name = 'Database Client';
insert into sw_types set typ_id = null,typ_name = 'Database Manager/Interpreter';
insert into sw_types set typ_id = null,typ_name = 'E911 Control Plane PDE';
insert into sw_types set typ_id = null,typ_name = 'ESXi';
insert into sw_types set typ_id = null,typ_name = 'Firmware';
insert into sw_types set typ_id = null,typ_name = 'Hawaii';
insert into sw_types set typ_id = null,typ_name = 'Hypervisor';
insert into sw_types set typ_id = null,typ_name = 'IEN Voice';
insert into sw_types set typ_id = null,typ_name = 'Instance';
insert into sw_types set typ_id = null,typ_name = 'Management';
insert into sw_types set typ_id = null,typ_name = 'Middleware';
insert into sw_types set typ_id = null,typ_name = 'Monitoring';
insert into sw_types set typ_id = null,typ_name = 'National';
insert into sw_types set typ_id = null,typ_name = 'Perl Library';
insert into sw_types set typ_id = null,typ_name = 'Position Determination';
insert into sw_types set typ_id = null,typ_name = 'Qwest';
insert into sw_types set typ_id = null,typ_name = 'RDB';
insert into sw_types set typ_id = null,typ_name = 'Schema';
insert into sw_types set typ_id = null,typ_name = 'Script';
insert into sw_types set typ_id = null,typ_name = 'SS7 Stack';
insert into sw_types set typ_id = null,typ_name = 'Storage Array';
insert into sw_types set typ_id = null,typ_name = 'TCP/IP Commercial UP PDE';
insert into sw_types set typ_id = null,typ_name = 'Vendor';
insert into sw_types set typ_id = null,typ_name = 'Web Server';
insert into sw_types set typ_id = null,typ_name = 'Wiki';

Then run the inventory/software/migrate.php script to change ownerships to the proper table

alter table software drop column sw_companyid;
alter table software drop column sw_notes;
alter table software drop column sw_group;
alter table software drop column sw_facing;
alter table software drop column sw_primary;
alter table software drop column sw_locked;
alter table software drop column sw_eolticket;
alter table software drop column sw_eosticket;
alter table software drop column sw_cert;
alter table software drop column sw_verified;
alter table software drop column sw_update;
alter table software drop column sw_notification;


update software set sw_type = 0 where sw_type = '';
update software set sw_type = 1 where sw_type = 'Application' or sw_type = 'App' or sw_type = 'Applicatiion' or sw_type = 'Applicatoin';
update software set sw_type = 2 where sw_type = 'Authentication';
update software set sw_type = 3 where sw_type = 'Backups';
update software set sw_type = 4 where sw_type = 'Cluster';
update software set sw_type = 5 where sw_type = 'Application Server';
update software set sw_type = 6 where sw_type = 'Cell Broadcast';
update software set sw_type = 7 where sw_type = 'centrify';
update software set sw_type = 8 where sw_type = 'OS';
update software set sw_type = 9 where sw_type = 'Open Source';
update software set sw_type = 10 where sw_type = 'CIL';
update software set sw_type = 11 where sw_type = 'Commercial' or sw_type = 'Commerical' or sw_type = 'Commericial';
update software set sw_type = 12 where sw_type = 'Custom';
update software set sw_type = 13 where sw_type = 'Database';
update software set sw_type = 14 where sw_type = 'Database Client';
update software set sw_type = 15 where sw_type = 'Database Manager/Interpreter';
update software set sw_type = 16 where sw_type = 'E911 Control Plane PDE';
update software set sw_type = 17 where sw_type = 'ESXi';
update software set sw_type = 18 where sw_type = 'Firmware';
update software set sw_type = 19 where sw_type = 'Hawaii';
update software set sw_type = 20 where sw_type = 'Hypervisor';
update software set sw_type = 21 where sw_type = 'IEN Voice';
update software set sw_type = 22 where sw_type = 'Instance';
update software set sw_type = 23 where sw_type = 'Management';
update software set sw_type = 24 where sw_type = 'Middleware';
update software set sw_type = 25 where sw_type = 'Monitoring';
update software set sw_type = 26 where sw_type = 'National';
update software set sw_type = 27 where sw_type = 'Perl Library';
update software set sw_type = 28 where sw_type = 'Position Determination';
update software set sw_type = 29 where sw_type = 'Qwest';
update software set sw_type = 30 where sw_type = 'RDB';
update software set sw_type = 31 where sw_type = 'Schema';
update software set sw_type = 32 where sw_type = 'Script';
update software set sw_type = 33 where sw_type = 'SS7 Stack';
update software set sw_type = 34 where sw_type = 'Storage Array';
update software set sw_type = 35 where sw_type = 'TCP/IP Commercial UP PDE';
update software set sw_type = 36 where sw_type = 'Vendor';
update software set sw_type = 37 where sw_type = 'Web Server' or sw_type = 'Webserver';
update software set sw_type = 38 where sw_type = 'Wiki';

update software set sw_vendor = 0 where sw_vendor = '';
update software set sw_vendor = 1 where sw_vendor = 'AMD';
update software set sw_vendor = 2 where sw_vendor = 'BlackBox';
update software set sw_vendor = 3 where sw_vendor = 'Cisco';
update software set sw_vendor = 4 where sw_vendor = 'Compaq';
update software set sw_vendor = 5 where sw_vendor = 'Compellent';
update software set sw_vendor = 6 where sw_vendor = 'Dell';
update software set sw_vendor = 7 where sw_vendor = 'Dell Compellent';
update software set sw_vendor = 8 where sw_vendor = 'Digi';
update software set sw_vendor = 9 where sw_vendor = 'Epson';
update software set sw_vendor = 10 where sw_vendor = 'Extreme Networks';
update software set sw_vendor = 11 where sw_vendor = 'F5 Networks';
update software set sw_vendor = 12 where sw_vendor = 'Force10 Networks, Inc.';
update software set sw_vendor = 13 where sw_vendor = 'Foundry Networks';
update software set sw_vendor = 14 where sw_vendor = 'Fujitsu';
update software set sw_vendor = 15 where sw_vendor = 'Hitachi';
update software set sw_vendor = 16 where sw_vendor = 'HP' or sw_vendor = 'Unknown: OSF1 v5.1';
update software set sw_vendor = 17 where sw_vendor = 'Intel';
update software set sw_vendor = 18 where sw_vendor = 'Juniper';
update software set sw_vendor = 19 where sw_vendor = 'Kontron';
update software set sw_vendor = 20 where sw_vendor = 'Maxtor';
update software set sw_vendor = 21 where sw_vendor = 'Micron Electronic, Inc.';
update software set sw_vendor = 22 where sw_vendor = 'Microsoft';
update software set sw_vendor = 23 where sw_vendor = 'MRV';
update software set sw_vendor = 24 where sw_vendor = 'NEC';
update software set sw_vendor = 25 where sw_vendor = 'NetScreen Technologies';
update software set sw_vendor = 26 where sw_vendor = 'Nexsan';
update software set sw_vendor = 27 where sw_vendor = 'Oracle' or sw_vendor = 'Unknown: Oracle Red Hat Enterprise Linux ES release 4 (Nahant Update 7)';
update software set sw_vendor = 28 where sw_vendor = 'Pure Storage';
update software set sw_vendor = 29 where sw_vendor = 'Quantum';
update software set sw_vendor = 30 where sw_vendor = 'Radware';
update software set sw_vendor = 31 where sw_vendor = 'Red Hat' or sw_vendor = 'RedHat';
update software set sw_vendor = 32 where sw_vendor = 'Ricoh';
update software set sw_vendor = 33 where sw_vendor = 'Seagate';
update software set sw_vendor = 34 where sw_vendor = 'Sonus Networks, Inc.';
update software set sw_vendor = 35 where sw_vendor = 'Stratus';
update software set sw_vendor = 36 where sw_vendor = 'Sun Microsystems';
update software set sw_vendor = 37 where sw_vendor = 'Toshiba';
update software set sw_vendor = 38 where sw_vendor = 'Veritas';
update software set sw_vendor = 39 where sw_vendor = 'VMware' or sw_vendor = 'VMware/Dell';
update software set sw_vendor = 43 where sw_vendor = 'Unknown';
update software set sw_vendor = 44 where sw_vendor = 'Acronis';
update software set sw_vendor = 45 where sw_vendor = 'Andrews';
update software set sw_vendor = 46 where sw_vendor = 'Apache' or sw_vendor = 'Apache Foundation';
update software set sw_vendor = 47 where sw_vendor = 'Axway';
update software set sw_vendor = 48 where sw_vendor = 'Carl Schelin';
update software set sw_vendor = 49 where sw_vendor = 'CentOS';
update software set sw_vendor = 50 where sw_vendor = 'Centrify';
update software set sw_vendor = 51 where sw_vendor = 'CollabNet';
update software set sw_vendor = 52 where sw_vendor = 'Customer Internal';
update software set sw_vendor = 53 where sw_vendor = 'Debian';
update software set sw_vendor = 54 where sw_vendor = 'Elk';
update software set sw_vendor = 55 where sw_vendor = 'Empirix';
update software set sw_vendor = 56 where sw_vendor = 'Entrust' or sw_vendor = 'Enrust';
update software set sw_vendor = 57 where sw_vendor = 'ESRI';
update software set sw_vendor = 58 where sw_vendor = 'ESX';
update software set sw_vendor = 59 where sw_vendor = 'Generic';
update software set sw_vendor = 60 where sw_vendor = 'GSI';
update software set sw_vendor = 61 where sw_vendor = 'HP / Stratavia';
update software set sw_vendor = 62 where sw_vendor = 'IBM';
update software set sw_vendor = 63 where sw_vendor = 'Informix';
update software set sw_vendor = 64 where sw_vendor = 'Intrado' or sw_vendor = 'Intado';
update software set sw_vendor = 65 where sw_vendor = 'Internal';
update software set sw_vendor = 66 where sw_vendor = 'Internal Custom';
update software set sw_vendor = 67 where sw_vendor = 'JBoss';
update software set sw_vendor = 68 where sw_vendor = 'Linux';
update software set sw_vendor = 69 where sw_vendor = 'Mango Springs';
update software set sw_vendor = 70 where sw_vendor = 'MediaWiki';
update software set sw_vendor = 71 where sw_vendor = 'Mobile Arts';
update software set sw_vendor = 72 where sw_vendor = 'Mongo';
update software set sw_vendor = 73 where sw_vendor = 'Nagios';
update software set sw_vendor = 74 where sw_vendor = 'NewRelic';
update software set sw_vendor = 75 where sw_vendor = 'Novell';
update software set sw_vendor = 76 where sw_vendor = 'One2Many';
update software set sw_vendor = 77 where sw_vendor = 'Open Code';
update software set sw_vendor = 78 where sw_vendor = 'Open Source';
update software set sw_vendor = 79 where sw_vendor = 'PostGres' or sw_vendor = 'PostGreSQL';
update software set sw_vendor = 80 where sw_vendor = 'Radisys';
update software set sw_vendor = 81 where sw_vendor = 'Riverbed';
update software set sw_vendor = 82 where sw_vendor = 'Runner Technologies';
update software set sw_vendor = 83 where sw_vendor = 'Sansay';
update software set sw_vendor = 84 where sw_vendor = 'Secure Computing';
update software set sw_vendor = 85 where sw_vendor = 'Spark';
update software set sw_vendor = 86 where sw_vendor = 'Splunk';
update software set sw_vendor = 87 where sw_vendor = 'Sterling Commerce';
update software set sw_vendor = 88 where sw_vendor = 'Sudo';
update software set sw_vendor = 89 where sw_vendor = 'Symantec';
update software set sw_vendor = 90 where sw_vendor = 'Ubuntu';
update software set sw_vendor = 91 where sw_vendor = 'Ulticom';
update software set sw_vendor = 93 where sw_vendor = 'Verint';
update software set sw_vendor = 94 where sw_vendor = 'Vordel';
update software set sw_vendor = 95 where sw_vendor = 'West' or sw_vendor = 'West Corp';
update software set sw_vendor = 96 where sw_vendor = 'Zones';
update software set sw_vendor = 97 where sw_vendor = 'Unknown: FreeBSD 4.6-RELEASE';
update software set sw_vendor = 98 where sw_vendor = 'MicroStrategy';
update software set sw_vendor = 99 where sw_vendor = 'CoreOS';
update software set sw_vendor = 100 where sw_vendor = 'Unknown: OpenBSD 6.8';
update software set sw_vendor = 43 where sw_vendor = 'Unknown: Unknown';
update software set sw_vendor = 97 where sw_vendor = 'FreeBSD';

Check the table to see if there are any unassigned vendors.

select sw_vendor from software;

If not, continue.

create table vendors (
  ven_id int(10) not null auto_increment,
  ven_name char(100) not null default '',
  primary key (ven_id)
);



insert into vendors set ven_id = null, ven_name = 'AMD';
insert into vendors set ven_id = null, ven_name = 'BlackBox';
insert into vendors set ven_id = null, ven_name = 'Cisco Systems';
insert into vendors set ven_id = null, ven_name = 'Compaq';
insert into vendors set ven_id = null, ven_name = 'Compellent';
insert into vendors set ven_id = null, ven_name = 'Dell';
insert into vendors set ven_id = null, ven_name = 'Dell Compellent';
insert into vendors set ven_id = null, ven_name = 'Digi';
insert into vendors set ven_id = null, ven_name = 'Epson';
insert into vendors set ven_id = null, ven_name = 'Extreme Networks';
insert into vendors set ven_id = null, ven_name = 'F5 Networks';
insert into vendors set ven_id = null, ven_name = 'Force10 Networks, Inc.';
insert into vendors set ven_id = null, ven_name = 'Foundry Networks';
insert into vendors set ven_id = null, ven_name = 'Fujitsu';
insert into vendors set ven_id = null, ven_name = 'Hitachi';
insert into vendors set ven_id = null, ven_name = 'HP';
insert into vendors set ven_id = null, ven_name = 'Intel';
insert into vendors set ven_id = null, ven_name = 'Juniper';
insert into vendors set ven_id = null, ven_name = 'Kontron';
insert into vendors set ven_id = null, ven_name = 'Maxtor';
insert into vendors set ven_id = null, ven_name = 'Micron Electronic, Inc.';
insert into vendors set ven_id = null, ven_name = 'Microsoft';
insert into vendors set ven_id = null, ven_name = 'MRV';
insert into vendors set ven_id = null, ven_name = 'NEC';
insert into vendors set ven_id = null, ven_name = 'NetScreen Technologies';
insert into vendors set ven_id = null, ven_name = 'Nexsan';
insert into vendors set ven_id = null, ven_name = 'Oracle';
insert into vendors set ven_id = null, ven_name = 'Pure Storage';
insert into vendors set ven_id = null, ven_name = 'Quantum';
insert into vendors set ven_id = null, ven_name = 'Radware';
insert into vendors set ven_id = null, ven_name = 'Red Hat';
insert into vendors set ven_id = null, ven_name = 'Richoh';
insert into vendors set ven_id = null, ven_name = 'Seagate';
insert into vendors set ven_id = null, ven_name = 'Sonus Networks, Inc.';
insert into vendors set ven_id = null, ven_name = 'Stratus';
insert into vendors set ven_id = null, ven_name = 'Sun Microsystems';
insert into vendors set ven_id = null, ven_name = 'Toshiba';
insert into vendors set ven_id = null, ven_name = 'Veritas';
insert into vendors set ven_id = null, ven_name = 'VMware';
insert into vendors set ven_id = null, ven_name = 'Watchguard';
insert into vendors set ven_id = null, ven_name = 'Western Digital';
insert into vendors set ven_id = null, ven_name = 'Data Foundry';
insert into vendors set ven_id = null, ven_name = 'Unknown';
insert into vendors set ven_id = null, ven_name = 'Acronis';
insert into vendors set ven_id = null, ven_name = 'Andrews';
insert into vendors set ven_id = null, ven_name = 'Apache Foundation';
insert into vendors set ven_id = null, ven_name = 'Axway';
insert into vendors set ven_id = null, ven_name = 'Carl Schelin';
insert into vendors set ven_id = null, ven_name = 'CentOS';
insert into vendors set ven_id = null, ven_name = 'Centrify';
insert into vendors set ven_id = null, ven_name = 'CollabNet';
insert into vendors set ven_id = null, ven_name = 'Customer Internal';
insert into vendors set ven_id = null, ven_name = 'Debian';
insert into vendors set ven_id = null, ven_name = 'Elk';
insert into vendors set ven_id = null, ven_name = 'Empirix';
insert into vendors set ven_id = null, ven_name = 'Entrust';
insert into vendors set ven_id = null, ven_name = 'ESRI';
insert into vendors set ven_id = null, ven_name = 'ESX';
insert into vendors set ven_id = null, ven_name = 'Generic';
insert into vendors set ven_id = null, ven_name = 'GSI';
insert into vendors set ven_id = null, ven_name = 'HP / Stratavia';
insert into vendors set ven_id = null, ven_name = 'IBM';
insert into vendors set ven_id = null, ven_name = 'Informix';
insert into vendors set ven_id = null, ven_name = 'Intrado';
insert into vendors set ven_id = null, ven_name = 'Internal';
insert into vendors set ven_id = null, ven_name = 'Internal Custom';
insert into vendors set ven_id = null, ven_name = 'JBoss';
insert into vendors set ven_id = null, ven_name = 'Linux';
insert into vendors set ven_id = null, ven_name = 'Mango Springs';
insert into vendors set ven_id = null, ven_name = 'Mediawiki';
insert into vendors set ven_id = null, ven_name = 'Mobile Arts';
insert into vendors set ven_id = null, ven_name = 'Mongo';
insert into vendors set ven_id = null, ven_name = 'Nagios';
insert into vendors set ven_id = null, ven_name = 'NewRelic';
insert into vendors set ven_id = null, ven_name = 'Novell';
insert into vendors set ven_id = null, ven_name = 'One2Many';
insert into vendors set ven_id = null, ven_name = 'Open Code';
insert into vendors set ven_id = null, ven_name = 'Open Source';
insert into vendors set ven_id = null, ven_name = 'PostGreSQL';
insert into vendors set ven_id = null, ven_name = 'Radisys';
insert into vendors set ven_id = null, ven_name = 'Riverbed';
insert into vendors set ven_id = null, ven_name = 'Runner Technologies';
insert into vendors set ven_id = null, ven_name = 'Sansay';
insert into vendors set ven_id = null, ven_name = 'Secure Computing';
insert into vendors set ven_id = null, ven_name = 'Spark';
insert into vendors set ven_id = null, ven_name = 'Splunk';
insert into vendors set ven_id = null, ven_name = 'Sterling Commerce';
insert into vendors set ven_id = null, ven_name = 'Sudo';
insert into vendors set ven_id = null, ven_name = 'Symantec';
insert into vendors set ven_id = null, ven_name = 'Ubuntu';
insert into vendors set ven_id = null, ven_name = 'Ulticom';
insert into vendors set ven_id = null, ven_name = 'Unknown';
insert into vendors set ven_id = null, ven_name = 'Verint';
insert into vendors set ven_id = null, ven_name = 'Vordel';
insert into vendors set ven_id = null, ven_name = 'West Corp';
insert into vendors set ven_id = null, ven_name = 'Zones';
insert into vendors set ven_id = null, ven_name = 'FreeBSD';
insert into vendors set ven_id = null, ven_name = 'MicroStrategy';
insert into vendors set ven_id = null, ven_name = 'CoreOS';
insert into vendors set ven_id = null, ven_name = 'OpenBSD';
insert into vendors set ven_id = null, ven_name = 'HPE';


alter table software change sw_vendor sw_vendor int(10) not null default 0;
alter table software change sw_type sw_type int(10) not null default 0;

Going through all files with the various software changes.


update models set mod_vendor = 1 where mod_vendor = 'AMD';
update models set mod_vendor = 2 where mod_vendor = 'BlackBox';
update models set mod_vendor = 3 where mod_vendor = 'Cisco Systems';
update models set mod_vendor = 4 where mod_vendor = 'Compaq';
update models set mod_vendor = 5 where mod_vendor = 'Compellent';
update models set mod_vendor = 42 where mod_vendor = 'Data Foundry';
update models set mod_vendor = 6 where mod_vendor = 'Dell';
update models set mod_vendor = 7 where mod_vendor = 'Dell Compellent';
update models set mod_vendor = 8 where mod_vendor = 'Digi';
update models set mod_vendor = 9 where mod_vendor = 'Epson';
update models set mod_vendor = 10 where mod_vendor = 'Extreme Networks';
update models set mod_vendor = 11 where mod_vendor = 'F5';
update models set mod_vendor = 11 where mod_vendor = 'F5 Networks';
update models set mod_vendor = 12 where mod_vendor = 'Force10 Networks, Inc.';
update models set mod_vendor = 13 where mod_vendor = 'Foundry Networks';
update models set mod_vendor = 14 where mod_vendor = 'Fujitsu';
update models set mod_vendor = 41 where mod_vendor = 'HGST';
update models set mod_vendor = 15 where mod_vendor = 'Hitachi';
update models set mod_vendor = 16 where mod_vendor = 'HP';
update models set mod_vendor = 17 where mod_vendor = 'Intel';
update models set mod_vendor = 18 where mod_vendor = 'Juniper';
update models set mod_vendor = 19 where mod_vendor = 'Kontron';
update models set mod_vendor = 20 where mod_vendor = 'Maxtor';
update models set mod_vendor = 21 where mod_vendor = 'Micron Electronic, Inc';
update models set mod_vendor = 22 where mod_vendor = 'Microsoft';
update models set mod_vendor = 23 where mod_vendor = 'MRV';
update models set mod_vendor = 43 where mod_vendor = 'N/A';
update models set mod_vendor = 24 where mod_vendor = 'NEC';
update models set mod_vendor = 25 where mod_vendor = 'NetScreen Technologies';
update models set mod_vendor = 26 where mod_vendor = 'Nexsan';
update models set mod_vendor = 27 where mod_vendor = 'Oracle';
update models set mod_vendor = 28 where mod_vendor = 'Pure Storage';
update models set mod_vendor = 29 where mod_vendor = 'Quantum';
update models set mod_vendor = 30 where mod_vendor = 'Radware';
update models set mod_vendor = 31 where mod_vendor = 'Red Hat';
update models set mod_vendor = 32 where mod_vendor = 'Ricoh';
update models set mod_vendor = 33 where mod_vendor = 'Seagate';
update models set mod_vendor = 34 where mod_vendor = 'Sonus';
update models set mod_vendor = 35 where mod_vendor = 'Stratus';
update models set mod_vendor = 36 where mod_vendor = 'Sun';
update models set mod_vendor = 37 where mod_vendor = 'TOSHIBA';
update models set mod_vendor = 43 where mod_vendor = 'Unknown';
update models set mod_vendor = 43 where mod_vendor = 'Varies';
update models set mod_vendor = 43 where mod_vendor = 'Vendor';
update models set mod_vendor = 38 where mod_vendor = 'Veritas';
update models set mod_vendor = 39 where mod_vendor = 'VMWare';
update models set mod_vendor = 40 where mod_vendor = 'Watchguard';
update models set mod_vendor = 41 where mod_vendor = 'Western Digital' or mod_vendor = 'Western Digitial';
update models set mod_vendor = 101 where mod_vendor = 'HPE';


alter table models change mod_vendor mod_vendor int(10) not null default 0;

### Timestamp 2021-12-23

Updated lnmt1cuomtool11 and inventory to this point.

### Timestamp 2022-01-21

Updated int_redundancy. This is just the conversion part. New systems won't need to do this.

alter table int_redundancy add column red_default int not null default 0;
insert into int_redundancy set red_id = null,red_text = 'Unassigned', red_default = 1;
update int_redundancy set red_default = 1 where red_id = 13;

For existing entries;

update interface set int_redundancy = 13 where int_redundancy = 0;

Note that lnmt1cuomtool11 and bldr0cuomdev1 are already updated.


### Timestamp 2022-04-14

Now the rest of the interface information. 

alter table int_media add column med_default int(10) not null default 0;
alter table int_speed add column spd_default int(10) not null default 0 ;
alter table int_duplex add column dup_default int(10) not null default 0;

insert into int_media set med_id = null,med_text = "Unassigned", med_default = 1;
insert into int_speed set spd_id = null,spd_text = "Unassigned", spd_default = 1;
insert into int_duplex set dup_id = null,dup_text = "Unassigned", dup_default = 1;

### Timestamp 2022-04-15

Updated lnmt1cuomtool11

### Timestamp 2022-04-26

Updated remote inventory

Removed all the license information. Only needed when transfering the database vs a brand new installation.

delete from licenses;

### Timestamp 2022-05-19

Added table:

create table assets (
  ast_id int(10) not null auto_increment,
  ast_parentid int(10) not null default 0,
  ast_modelid int(10) not null default 0,
  ast_serial char(100) not null default '',
  ast_asset char(100) not null default '',
  primary key (ast_id)
);

Updated lnmt1cuomtool11 and remote inventory


