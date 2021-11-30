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



new tables:

tag_types
CREATE TABLE `tag_types` (
  `type_id` int(10) NOT NULL AUTO_INCREMENT,
  `type_name` char(60) NOT NULL DEFAULT '',
  PRIMARY KEY (`type_id`)
);

add the following data and index. There is code that uses this data as listed:
1: Servers
2: Locations
3: Products
4: Software
5: Hardware

new column in tags table for tag_types data:

alter table tags add column tag_type int(10) not null default 0 after tag_name;

And for existing servers:

update tags set tag_type = 1;


renamed tables due to changes in database reserved words:

groups was renamed to a_groups.
window was renamed to maint_window


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




table cleanup; where we drop columns that aren't used any more.

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


And some conversion work now.

In the tags table, the default is 0 right now but all should be set to 1 for 'Server Tag'.

update tags set tag_type = 1;



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





Networking Section: Where modifications are made


=== New Section ===

This should be a chain; org, bu, group, user so adding the next level up.

alter table business_unit add column bus_org int(10) not null default 0 after bus_id;


=== 20210929 ===

alter table tags change tag_name tag_name char(255) not null default '';

Change project to Software in tag_types table
Add Hardware to tag_types table

==== 20211004 ===

alter table products drop column prod_oldcode;
alter table products drop column prod_remedy;
alter table products drop column prod_type;
alter table products drop column prod_citype;
alter table products drop column prod_tier1;
alter table products drop column prod_tier2;
alter table products drop column prod_tier3;
alter table products drop column prod_group;

==== 20211006 ===

Removing grp_role and adding grp_business. It should be a chain where org is at top, then business, then group.

alter table a_groups drop column grp_role;
alter table a_groups add column grp_department int(10) not null default 0 after grp_organization;
rename table business_unit to business

==== 20211016 =====

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


==== 20211019 ====

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


==== 20211029 ====

Updated lnmt1cuomtool11 and inventory with the above table changes.


==== 20211126 ====

Updated Inteliquent with the above table changes.

Error:

select tag name,tag_companyid from tags where tag_group = 1 and tag_type = 1 group by tag_name order by tag_name
Expression #2 of SEELCT list is not in GROUP BY clause and contains nonaggregated column 'inventory.tags.tag_companyid' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by

modify mysql:

SET GLOBAL sql_mode(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));


==== 20211129 ====

Remove the following from the users table:

alter table users drop column usr_altemail;
alter table users drop column usr_clientid;
alter table users drop column usr_page;
alter table users drop column usr_pagemail;
alter table users drop column usr_deptname;
alter table users drop column usr_magic
alter table users drop column usr_report;
alter table users drop column usr_confirm;
alter table users drop column usr_maint;
alter table users drop column usr_disposition;
alter table users drop column usr_headers;
alter table users drop column usr_start;
alter table users drop column usr_end;
alter table users drop column usr_maillist;








