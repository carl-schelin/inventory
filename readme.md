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


new tables:

tag_types
CREATE TABLE `tag_types` (
  `type_id` int(10) NOT NULL AUTO_INCREMENT,
  `type_name` char(60) NOT NULL DEFAULT '',
  PRIMARY KEY (`type_id`)
);

new column:

alter table tags add column tag_type int(10) not null default 0 after tag_name;


renamed tables:

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



# just for the intrado inventory

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


# again, for the intrado inventory


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



database cleanup:

remove the following tables:

drop table alarm_blocks;
CREATE TABLE `alarm_blocks` (
  `block_id` int NOT NULL AUTO_INCREMENT,
  `block_text` text NOT NULL,
  `block_user` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`block_id`)
)
drop table alarm_type;
CREATE TABLE `alarm_type` (
  `atype_id` int NOT NULL AUTO_INCREMENT,
  `atype_name` char(40) NOT NULL DEFAULT '',
  PRIMARY KEY (`atype_id`)
)
drop table alarms;
CREATE TABLE `alarms` (
  `alarm_id` int NOT NULL AUTO_INCREMENT,
  `alarm_companyid` int NOT NULL DEFAULT '0',
  `alarm_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `alarm_level` int NOT NULL DEFAULT '0',
  `alarm_text` text NOT NULL,
  `alarm_disabled` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`alarm_id`)
)
drop table apigroups;
CREATE TABLE `apigroups` (
  `api_id` int NOT NULL AUTO_INCREMENT,
  `api_name` char(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`api_id`)
)
drop table application;
CREATE TABLE `application` (
  `app_id` int NOT NULL AUTO_INCREMENT,
  `app_description` char(255) NOT NULL DEFAULT '',
  `app_deleted` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`app_id`)
)
drop table bigfix;
CREATE TABLE `bigfix` (
  `big_id` int NOT NULL AUTO_INCREMENT,
  `big_companyid` int NOT NULL DEFAULT '0',
  `big_fixlet` char(255) NOT NULL DEFAULT '',
  `big_severity` int NOT NULL DEFAULT '0',
  `big_release` date NOT NULL DEFAULT '1971-01-01',
  `big_scheduled` date NOT NULL DEFAULT '1971-01-01',
  PRIMARY KEY (`big_id`)
)
drop table business;
CREATE TABLE `business` (
  `bus_id` int NOT NULL AUTO_INCREMENT,
  `bus_unit` int NOT NULL DEFAULT '0',
  `bus_dept` int NOT NULL DEFAULT '0',
  `bus_vp` int NOT NULL DEFAULT '0',
  `bus_owner` int NOT NULL DEFAULT '0',
  `bus_buc` int NOT NULL DEFAULT '0',
  `bus_unitname` char(70) NOT NULL DEFAULT '',
  `bus_deptname` char(70) NOT NULL DEFAULT '',
  PRIMARY KEY (`bus_id`)
)
drop table changelog;
CREATE TABLE `changelog` (
  `cl_id` int NOT NULL AUTO_INCREMENT,
  `cl_name` char(60) NOT NULL DEFAULT '',
  `cl_owner` int NOT NULL DEFAULT '0',
  `cl_group` int NOT NULL DEFAULT '0',
  `cl_delete` int NOT NULL DEFAULT '0',
  `cl_whodel` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`cl_id`)
)
drop table checklist;
CREATE TABLE `checklist` (
  `chk_id` int NOT NULL AUTO_INCREMENT,
  `chk_task` int NOT NULL DEFAULT '0',
  `chk_group` int NOT NULL DEFAULT '0',
  `chk_index` int NOT NULL DEFAULT '0',
  `chk_text` char(255) NOT NULL DEFAULT '',
  `chk_link` char(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`chk_id`)
)
drop table company;
CREATE TABLE `company` (
  `com_id` int NOT NULL AUTO_INCREMENT,
  `com_short` char(30) NOT NULL DEFAULT '',
  `com_name` char(255) NOT NULL DEFAULT '',
  `com_type` int NOT NULL DEFAULT '0',
  `com_channel` char(30) NOT NULL DEFAULT '',
  `com_phone` char(255) NOT NULL DEFAULT '',
  `com_email` char(255) NOT NULL DEFAULT '',
  `com_webpage` char(255) NOT NULL DEFAULT '',
  `com_description` text NOT NULL,
  `com_escalation` text NOT NULL,
  `com_addr1` char(100) NOT NULL DEFAULT '',
  `com_addr2` char(100) NOT NULL DEFAULT '',
  `com_city` int NOT NULL DEFAULT '0',
  `com_state` int NOT NULL DEFAULT '0',
  `com_zipcode` char(20) NOT NULL DEFAULT '',
  `com_country` int NOT NULL DEFAULT '0',
  `com_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `com_notes` text NOT NULL,
  `com_category` int NOT NULL DEFAULT '0',
  `com_disabled` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`com_id`)
)
drop table config;
CREATE TABLE `config` (
  `config_id` int NOT NULL AUTO_INCREMENT,
  `config_name` char(30) NOT NULL DEFAULT '',
  `config_path` char(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`config_id`)
)
drop table contact;
CREATE TABLE `contact` (
  `con_id` int NOT NULL AUTO_INCREMENT,
  `con_name` char(100) NOT NULL DEFAULT '',
  `con_title` int NOT NULL DEFAULT '0',
  `con_phone` char(20) NOT NULL DEFAULT '',
  `con_mobile` char(20) NOT NULL DEFAULT '',
  `con_email` char(100) NOT NULL DEFAULT '',
  `con_notes` char(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`con_id`)
)
drop table contacts;
CREATE TABLE `contacts` (
  `con_id` int NOT NULL AUTO_INCREMENT,
  `con_company` char(255) NOT NULL DEFAULT '',
  `con_contact` char(255) NOT NULL DEFAULT '',
  `con_option` char(255) NOT NULL DEFAULT '',
  `con_alternate` char(255) NOT NULL DEFAULT '',
  `con_group` int NOT NULL DEFAULT '0',
  `con_disabled` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`con_id`)
)
drop table events;
CREATE TABLE `events` (
  `evt_id` int NOT NULL AUTO_INCREMENT,
  `evt_group` int NOT NULL DEFAULT '0',
  `evt_task` char(200) NOT NULL DEFAULT '',
  `evt_date` date NOT NULL DEFAULT '1971-01-01',
  PRIMARY KEY (`evt_id`)
)
drop table family;
CREATE TABLE `family` (
  `fam_id` int NOT NULL AUTO_INCREMENT,
  `fam_name` char(60) NOT NULL DEFAULT '',
  PRIMARY KEY (`fam_id`)
)
drop table faq;
CREATE TABLE `faq` (
  `faq_id` int NOT NULL AUTO_INCREMENT,
  `faq_subject` char(255) NOT NULL DEFAULT '',
  `faq_user` int NOT NULL DEFAULT '0',
  `faq_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `faq_status` int NOT NULL DEFAULT '0',
  `faq_views` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`faq_id`)
)
drop table faq_comment;
CREATE TABLE `faq_comment` (
  `com_id` int NOT NULL AUTO_INCREMENT,
  `com_detail_id` int NOT NULL DEFAULT '0',
  `com_text` text NOT NULL,
  `com_user` int NOT NULL DEFAULT '0',
  `com_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `com_status` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`com_id`)
)
drop table faq_detail;
CREATE TABLE `faq_detail` (
  `faq_id` int NOT NULL AUTO_INCREMENT,
  `faq_faq_id` int NOT NULL DEFAULT '0',
  `faq_detail_id` int NOT NULL DEFAULT '0',
  `faq_text` text NOT NULL,
  `faq_user` int NOT NULL DEFAULT '0',
  `faq_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `faq_status` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`faq_id`)
)
drop table faq_tags;
CREATE TABLE `faq_tags` (
  `tag_id` int NOT NULL AUTO_INCREMENT,
  `tag_faq_id` int NOT NULL DEFAULT '0',
  `tag_name` char(50) NOT NULL DEFAULT '',
  `tag_user` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`tag_id`)
)
drop table faq_votes;
CREATE TABLE `faq_votes` (
  `vote_id` int NOT NULL AUTO_INCREMENT,
  `vote_faq_id` int NOT NULL DEFAULT '0',
  `vote_status` int NOT NULL DEFAULT '0',
  `vote_user` int NOT NULL DEFAULT '0',
  `vote_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`vote_id`)
)
drop table firewall;
CREATE TABLE `firewall` (
  `fw_id` int NOT NULL AUTO_INCREMENT,
  `fw_companyid` int NOT NULL DEFAULT '0',
  `fw_source` char(20) NOT NULL DEFAULT '',
  `fw_sourcezone` int NOT NULL DEFAULT '0',
  `fw_destination` char(30) NOT NULL DEFAULT '',
  `fw_destinationzone` int NOT NULL DEFAULT '0',
  `fw_port` char(50) NOT NULL DEFAULT '',
  `fw_portdesc` char(50) NOT NULL DEFAULT '',
  `fw_protocol` char(10) NOT NULL DEFAULT '',
  `fw_description` char(255) NOT NULL DEFAULT '',
  `fw_timeout` int NOT NULL DEFAULT '0',
  `fw_ticket` char(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`fw_id`)
)
drop table handoff;
CREATE TABLE `handoff` (
  `off_id` int NOT NULL AUTO_INCREMENT,
  `off_user` int NOT NULL DEFAULT '0',
  `off_group` int NOT NULL DEFAULT '0',
  `off_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `off_handoff` text NOT NULL,
  `off_disabled` int NOT NULL DEFAULT '0',
  `off_who` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`off_id`)
)
drop table intvuln;
CREATE TABLE `intvuln` (
  `iv_id` int NOT NULL AUTO_INCREMENT,
  `iv_intid` int NOT NULL DEFAULT '0',
  `iv_securityid` int NOT NULL DEFAULT '0',
  `iv_date` date NOT NULL DEFAULT '1971-01-01',
  PRIMARY KEY (`iv_id`)
)
drop table ip_addresses;
CREATE TABLE `ip_addresses` (
  `ip_id` int NOT NULL AUTO_INCREMENT,
  `ip_companyid` int NOT NULL DEFAULT '0',
  `ip_subnetid` int NOT NULL DEFAULT '0',
  `ip_address` mediumtext NOT NULL,
  `ip_gateway` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`ip_id`)
)
drop table ip_subnets;
CREATE TABLE `ip_subnets` (
  `sub_id` int NOT NULL AUTO_INCREMENT,
  `sub_base` mediumtext NOT NULL,
  `sub_mask` int NOT NULL DEFAULT '0',
  `sub_vlan` char(30) NOT NULL DEFAULT '',
  `sub_zone` int NOT NULL DEFAULT '0',
  `sub_name` char(30) NOT NULL DEFAULT '',
  `sub_loc` int NOT NULL DEFAULT '0',
  `sub_desc` char(100) NOT NULL DEFAULT '',
  `sub_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sub_user` int NOT NULL DEFAULT '0',
  `sub_disable` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`sub_id`)
)
drop table keywords;
CREATE TABLE `keywords` (
  `key_id` int NOT NULL AUTO_INCREMENT,
  `key_description` char(255) NOT NULL DEFAULT '',
  `key_page` char(255) NOT NULL DEFAULT '',
  `key_email` char(255) NOT NULL DEFAULT '',
  `key_annotate` char(255) NOT NULL DEFAULT '',
  `key_critical_annotate` char(255) NOT NULL DEFAULT '',
  `key_deleted` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`key_id`)
)
drop table maint;
CREATE TABLE `maint` (
  `man_id` int NOT NULL AUTO_INCREMENT,
  `man_service` char(100) NOT NULL DEFAULT '',
  `man_companyid` int NOT NULL DEFAULT '0',
  `man_productid` int NOT NULL DEFAULT '0',
  `man_desc` char(255) NOT NULL DEFAULT '',
  `man_start` date NOT NULL DEFAULT '1971-01-01',
  `man_end` date NOT NULL DEFAULT '1971-01-01',
  `man_st_time` time NOT NULL DEFAULT '00:00:00',
  `man_end_time` time NOT NULL DEFAULT '00:00:00',
  `man_user` char(10) NOT NULL DEFAULT '0',
  `man_status` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`man_id`)
)
drop table message_group;
CREATE TABLE `message_group` (
  `msg_id` int NOT NULL AUTO_INCREMENT,
  `msg_group` char(255) NOT NULL DEFAULT '',
  `msg_deleted` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`msg_id`)
)
drop table issue_morning;
CREATE TABLE `issue_morning` (
  `morn_id` int NOT NULL AUTO_INCREMENT,
  `morn_text` char(255) NOT NULL DEFAULT '',
  `morn_issue` int NOT NULL DEFAULT '0',
  `morn_user` int NOT NULL DEFAULT '0',
  `morn_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `morn_status` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`morn_id`)
)
drop table networks;
CREATE TABLE `networks` (
  `net_id` int NOT NULL AUTO_INCREMENT,
  `net_network` char(128) NOT NULL DEFAULT '',
  `net_netmask` int NOT NULL DEFAULT '0',
  `net_gateway` char(128) NOT NULL DEFAULT '',
  `net_vlan` char(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`net_id`)
)
drop table objects;
CREATE TABLE `objects` (
  `obj_id` int NOT NULL AUTO_INCREMENT,
  `obj_name` char(255) NOT NULL DEFAULT '',
  `obj_deleted` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`obj_id`)
)
drop table oncall;
CREATE TABLE `oncall` (
  `onc_id` int NOT NULL AUTO_INCREMENT,
  `onc_userid` int NOT NULL DEFAULT '0',
  `onc_groupid` int NOT NULL DEFAULT '0',
  `onc_changed` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`onc_id`)
)
drop table outage;
CREATE TABLE `outage` (
  `out_id` int NOT NULL AUTO_INCREMENT,
  `out_companyid` int NOT NULL DEFAULT '0',
  `out_user` int NOT NULL DEFAULT '0',
  `out_email` char(60) NOT NULL DEFAULT '',
  `out_password` char(16) NOT NULL DEFAULT '',
  `out_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `out_confirmed` int NOT NULL DEFAULT '0',
  `out_closedby` char(60) NOT NULL DEFAULT '',
  `out_closed` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`out_id`)
)
drop table policy;
CREATE TABLE `policy` (
  `pol_id` int NOT NULL AUTO_INCREMENT,
  `pol_companyid` int NOT NULL DEFAULT '0',
  `pol_type` int NOT NULL DEFAULT '0',
  `pol_description` int NOT NULL DEFAULT '0',
  `pol_status` int NOT NULL DEFAULT '0',
  `pol_version` char(255) NOT NULL DEFAULT '',
  `pol_date` date NOT NULL DEFAULT '1971-01-01',
  PRIMARY KEY (`pol_id`)
)
drop table policy_description;
CREATE TABLE `policy_description` (
  `pd_id` int NOT NULL AUTO_INCREMENT,
  `pd_description` char(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`pd_id`)
)
drop table policy_type;
CREATE TABLE `policy_type` (
  `pt_id` int NOT NULL AUTO_INCREMENT,
  `pt_type` char(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`pt_id`)
)
drop table poll_answers;
CREATE TABLE `poll_answers` (
  `pa_id` int NOT NULL AUTO_INCREMENT,
  `pa_pq_id` int NOT NULL DEFAULT '0',
  `pa_user` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`pa_id`)
)
drop table poll_questions;
CREATE TABLE `poll_questions` (
  `pq_id` int NOT NULL AUTO_INCREMENT,
  `pq_poll_id` int NOT NULL DEFAULT '0',
  `pq_question` char(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`pq_id`)
)
drop table polls;
CREATE TABLE `polls` (
  `poll_id` int NOT NULL AUTO_INCREMENT,
  `poll_name` char(30) NOT NULL DEFAULT '',
  `poll_group` int NOT NULL DEFAULT '0',
  `poll_type` int NOT NULL DEFAULT '0',
  `poll_creator` int NOT NULL DEFAULT '0',
  `poll_expires` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`poll_id`)
)
drop table psaps;
CREATE TABLE `psaps` (
  `psap_id` int NOT NULL AUTO_INCREMENT,
  `psap_customerid` int NOT NULL DEFAULT '0',
  `psap_parentid` int NOT NULL DEFAULT '0',
  `psap_ali_id` char(10) NOT NULL DEFAULT '',
  `psap_companyid` int NOT NULL DEFAULT '0',
  `psap_psap_id` char(20) NOT NULL DEFAULT '',
  `psap_description` char(255) NOT NULL DEFAULT '',
  `psap_lport` int NOT NULL DEFAULT '0',
  `psap_circuit_id` char(255) NOT NULL DEFAULT '',
  `psap_pseudo_cid` char(255) NOT NULL DEFAULT '',
  `psap_lec` char(60) NOT NULL DEFAULT '',
  `psap_texas` int NOT NULL DEFAULT '0',
  `psap_updated` date NOT NULL DEFAULT '1971-01-01',
  `psap_delete` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`psap_id`)
)
drop table psaps_arch;
CREATE TABLE `psaps_arch` (
  `psap_id` int NOT NULL AUTO_INCREMENT,
  `psap_customerid` int NOT NULL DEFAULT '0',
  `psap_parentid` int NOT NULL DEFAULT '0',
  `psap_ali_id` char(10) NOT NULL DEFAULT '',
  `psap_companyid` int NOT NULL DEFAULT '0',
  `psap_psap_id` char(20) NOT NULL DEFAULT '',
  `psap_description` char(255) NOT NULL DEFAULT '',
  `psap_lport` int NOT NULL DEFAULT '0',
  `psap_circuit_id` char(255) NOT NULL DEFAULT '',
  `psap_pseudo_cid` char(255) NOT NULL DEFAULT '',
  `psap_lec` char(60) NOT NULL DEFAULT '',
  `psap_texas` int NOT NULL DEFAULT '0',
  `psap_updated` date NOT NULL DEFAULT '1971-01-01',
  `psap_delete` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`psap_id`)
)
drop table purchaseorder;
CREATE TABLE `purchaseorder` (
  `po_id` int NOT NULL AUTO_INCREMENT,
  `po_number` char(40) NOT NULL DEFAULT '',
  `po_buc` int NOT NULL DEFAULT '0',
  `po_bu` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`po_id`)
)
drop table report;
CREATE TABLE `report` (
  `rep_id` int NOT NULL AUTO_INCREMENT,
  `rep_user` int NOT NULL DEFAULT '0',
  `rep_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `rep_group` int NOT NULL DEFAULT '0',
  `rep_date` date NOT NULL DEFAULT '1971-01-01',
  `rep_status` int NOT NULL DEFAULT '0',
  `rep_issue` int NOT NULL DEFAULT '0',
  `rep_task` text NOT NULL,
  PRIMARY KEY (`rep_id`)
)
drop table repos;
CREATE TABLE `repos` (
  `rep_id` int NOT NULL AUTO_INCREMENT,
  `rep_version` char(5) NOT NULL DEFAULT '',
  `rep_group` char(60) NOT NULL DEFAULT '',
  `rep_name` char(60) NOT NULL DEFAULT '',
  `rep_grpdesc` char(255) NOT NULL DEFAULT '',
  `rep_type` char(20) NOT NULL DEFAULT '',
  `rep_package` char(60) NOT NULL DEFAULT '',
  `rep_pkgdesc` char(255) NOT NULL DEFAULT '',
  `rep_included` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`rep_id`)
)
drop table resources;
CREATE TABLE `resources` (
  `res_id` int NOT NULL AUTO_INCREMENT,
  `res_name` char(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`res_id`)
)
drop table retire;
CREATE TABLE `retire` (
  `ret_id` int NOT NULL AUTO_INCREMENT,
  `ret_companyid` int NOT NULL DEFAULT '0',
  `ret_retireid` int NOT NULL DEFAULT '0',
  `ret_text` text NOT NULL,
  `ret_date` date NOT NULL DEFAULT '1971-01-01',
  `ret_user` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`ret_id`)
)
drop table rh_groups;
CREATE TABLE `rh_groups` (
  `grp_id` int NOT NULL AUTO_INCREMENT,
  `grp_osid` int NOT NULL DEFAULT '0',
  `grp_key` char(80) NOT NULL DEFAULT '',
  `grp_name` char(80) NOT NULL DEFAULT '',
  `grp_description` char(255) NOT NULL DEFAULT '',
  `grp_default` int NOT NULL DEFAULT '0',
  `grp_uservisible` int NOT NULL DEFAULT '0',
  `grp_disabled` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`grp_id`)
)
drop table rh_packages;
CREATE TABLE `rh_packages` (
  `pkg_id` int NOT NULL AUTO_INCREMENT,
  `pkg_grpid` int NOT NULL DEFAULT '0',
  `pkg_type` int NOT NULL DEFAULT '0',
  `pkg_description` char(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`pkg_id`)
)
drop table rh_selections;
CREATE TABLE `rh_selections` (
  `sel_id` int NOT NULL AUTO_INCREMENT,
  `sel_pkgid` int NOT NULL DEFAULT '0',
  `sel_grpid` int NOT NULL DEFAULT '0',
  `sel_status` int NOT NULL DEFAULT '0',
  `sel_userid` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`sel_id`)
)
drop table rights;
CREATE TABLE `rights` (
  `rgt_id` int NOT NULL AUTO_INCREMENT,
  `rgt_type` int NOT NULL DEFAULT '0',
  `rgt_apigroup` int NOT NULL DEFAULT '0',
  `rgt_resource` int NOT NULL DEFAULT '0',
  `rgt_get` int NOT NULL DEFAULT '0',
  `rgt_list` int NOT NULL DEFAULT '0',
  `rgt_watch` int NOT NULL DEFAULT '0',
  `rgt_impersonate` int NOT NULL DEFAULT '0',
  `rgt_create` int NOT NULL DEFAULT '0',
  `rgt_delete` int NOT NULL DEFAULT '0',
  `rgt_deletecollection` int NOT NULL DEFAULT '0',
  `rgt_patch` int NOT NULL DEFAULT '0',
  `rgt_update` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`rgt_id`)
)
drop table rsdp_accept;
CREATE TABLE `rsdp_accept` (
  `acc_id` int NOT NULL AUTO_INCREMENT,
  `acc_rsdp` int NOT NULL DEFAULT '0',
  `acc_status` int NOT NULL DEFAULT '0',
  `acc_task` int NOT NULL DEFAULT '0',
  `acc_note` char(255) NOT NULL DEFAULT '',
  `acc_accept` int NOT NULL DEFAULT '0',
  `acc_user` int NOT NULL DEFAULT '0',
  `acc_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`acc_id`)
)
drop table rsdp_applications;
CREATE TABLE `rsdp_applications` (
  `app_id` int NOT NULL AUTO_INCREMENT,
  `app_rsdp` int NOT NULL DEFAULT '0',
  `app_installed` int NOT NULL DEFAULT '0',
  `app_configured` int NOT NULL DEFAULT '0',
  `app_mib` int NOT NULL DEFAULT '0',
  `app_process` int NOT NULL DEFAULT '0',
  `app_logfile` int NOT NULL DEFAULT '0',
  `app_inscheck` int NOT NULL DEFAULT '0',
  `app_tested` int NOT NULL DEFAULT '0',
  `app_integrated` int NOT NULL DEFAULT '0',
  `app_failover` int NOT NULL DEFAULT '0',
  `app_concheck` int NOT NULL DEFAULT '0',
  `app_monitor` int NOT NULL DEFAULT '0',
  `app_verified` int NOT NULL DEFAULT '0',
  `app_moncheck` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`app_id`)
)
drop table rsdp_backups;
CREATE TABLE `rsdp_backups` (
  `bu_id` int NOT NULL AUTO_INCREMENT,
  `bu_rsdp` int NOT NULL DEFAULT '0',
  `bu_start` char(15) NOT NULL DEFAULT '0000-00-00',
  `bu_include` int NOT NULL DEFAULT '0',
  `bu_retention` int NOT NULL DEFAULT '0',
  `bu_sunday` int NOT NULL DEFAULT '0',
  `bu_monday` int NOT NULL DEFAULT '0',
  `bu_tuesday` int NOT NULL DEFAULT '0',
  `bu_wednesday` int NOT NULL DEFAULT '0',
  `bu_thursday` int NOT NULL DEFAULT '0',
  `bu_friday` int NOT NULL DEFAULT '0',
  `bu_saturday` int NOT NULL DEFAULT '0',
  `bu_suntime` char(10) NOT NULL DEFAULT '00:00',
  `bu_montime` char(10) NOT NULL DEFAULT '00:00',
  `bu_tuetime` char(10) NOT NULL DEFAULT '00:00',
  `bu_wedtime` char(10) NOT NULL DEFAULT '00:00',
  `bu_thutime` char(10) NOT NULL DEFAULT '00:00',
  `bu_fritime` char(10) NOT NULL DEFAULT '00:00',
  `bu_sattime` char(10) NOT NULL DEFAULT '00:00',
  PRIMARY KEY (`bu_id`)
)
drop table rsdp_check;
CREATE TABLE `rsdp_check` (
  `chk_id` int NOT NULL AUTO_INCREMENT,
  `chk_task` int NOT NULL DEFAULT '0',
  `chk_group` int NOT NULL DEFAULT '0',
  `chk_rsdp` int NOT NULL DEFAULT '0',
  `chk_index` int NOT NULL DEFAULT '0',
  `chk_comment` char(100) NOT NULL DEFAULT '',
  `chk_checked` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`chk_id`)
)
drop table rsdp_comments;
CREATE TABLE `rsdp_comments` (
  `com_id` int NOT NULL AUTO_INCREMENT,
  `com_rsdp` int NOT NULL DEFAULT '0',
  `com_task` int NOT NULL DEFAULT '0',
  `com_text` text NOT NULL,
  `com_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `com_user` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`com_id`)
)
drop table rsdp_datacenter;
CREATE TABLE `rsdp_datacenter` (
  `dc_id` int NOT NULL AUTO_INCREMENT,
  `dc_rsdp` int NOT NULL DEFAULT '0',
  `dc_power` int NOT NULL DEFAULT '0',
  `dc_cables` int NOT NULL DEFAULT '0',
  `dc_infra` int NOT NULL DEFAULT '0',
  `dc_received` int NOT NULL DEFAULT '0',
  `dc_installed` int NOT NULL DEFAULT '0',
  `dc_checklist` int NOT NULL DEFAULT '0',
  `dc_path` char(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`dc_id`)
)
drop table rsdp_designed;
CREATE TABLE `rsdp_designed` (
  `san_id` int NOT NULL AUTO_INCREMENT,
  `san_rsdp` int NOT NULL DEFAULT '0',
  `san_complete` int NOT NULL DEFAULT '0',
  `san_checklist` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`san_id`)
)
drop table rsdp_filesystem;
CREATE TABLE `rsdp_filesystem` (
  `fs_id` int NOT NULL AUTO_INCREMENT,
  `fs_rsdp` int NOT NULL DEFAULT '0',
  `fs_volume` char(60) NOT NULL DEFAULT '',
  `fs_size` char(20) NOT NULL DEFAULT '',
  `fs_sysport` char(60) NOT NULL DEFAULT '',
  `fs_swport` char(60) NOT NULL DEFAULT '',
  `fs_wwnn` char(20) NOT NULL DEFAULT '',
  `fs_backup` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`fs_id`)
)
drop table rsdp_infosec;
CREATE TABLE `rsdp_infosec` (
  `is_id` int NOT NULL AUTO_INCREMENT,
  `is_rsdp` int NOT NULL DEFAULT '0',
  `is_checklist` int NOT NULL DEFAULT '0',
  `is_ticket` char(30) NOT NULL DEFAULT '',
  `is_scan` int NOT NULL DEFAULT '0',
  `is_verified` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`is_id`)
)
drop table rsdp_infrastructure;
CREATE TABLE `rsdp_infrastructure` (
  `if_id` int NOT NULL AUTO_INCREMENT,
  `if_rsdp` int NOT NULL DEFAULT '0',
  `if_netcheck` int NOT NULL DEFAULT '0',
  `if_magic` int NOT NULL DEFAULT '0',
  `if_dcrack` int NOT NULL DEFAULT '0',
  `if_dccabled` int NOT NULL DEFAULT '0',
  `if_wiki` int NOT NULL DEFAULT '0',
  `if_svrmgt` int NOT NULL DEFAULT '0',
  `if_config` int NOT NULL DEFAULT '0',
  `if_built` int NOT NULL DEFAULT '0',
  `if_network` int NOT NULL DEFAULT '0',
  `if_dns` int NOT NULL DEFAULT '0',
  `if_inscheck` int NOT NULL DEFAULT '0',
  `if_sanfs` int NOT NULL DEFAULT '0',
  `if_verified` int NOT NULL DEFAULT '0',
  `if_checklist` int NOT NULL DEFAULT '0',
  `if_backups` int NOT NULL DEFAULT '0',
  `if_buverified` int NOT NULL DEFAULT '0',
  `if_bucheck` int NOT NULL DEFAULT '0',
  `if_monitor` int NOT NULL DEFAULT '0',
  `if_monverified` int NOT NULL DEFAULT '0',
  `if_moncheck` int NOT NULL DEFAULT '0',
  `if_sanconf` int NOT NULL DEFAULT '0',
  `if_provisioned` int NOT NULL DEFAULT '0',
  `if_procheck` int NOT NULL DEFAULT '0',
  `if_vmcheck` int NOT NULL DEFAULT '0',
  `if_netprov` int NOT NULL DEFAULT '0',
  `if_sanprov` int NOT NULL DEFAULT '0',
  `if_vmprov` int NOT NULL DEFAULT '0',
  `if_vmnote` char(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`if_id`)
)
drop table rsdp_interface;
CREATE TABLE `rsdp_interface` (
  `if_id` int NOT NULL AUTO_INCREMENT,
  `if_rsdp` int NOT NULL DEFAULT '0',
  `if_name` char(60) NOT NULL DEFAULT '',
  `if_sysport` char(60) NOT NULL DEFAULT '',
  `if_interface` char(30) NOT NULL DEFAULT '',
  `if_groupname` char(30) NOT NULL DEFAULT '',
  `if_if_id` int NOT NULL DEFAULT '0',
  `if_mac` char(20) NOT NULL DEFAULT '',
  `if_zone` int NOT NULL DEFAULT '0',
  `if_vlan` char(20) NOT NULL DEFAULT '',
  `if_ip` char(60) NOT NULL DEFAULT '',
  `if_ipcheck` int NOT NULL DEFAULT '0',
  `if_mask` char(60) NOT NULL DEFAULT '',
  `if_gate` char(60) NOT NULL DEFAULT '',
  `if_speed` int NOT NULL DEFAULT '0',
  `if_duplex` int NOT NULL DEFAULT '0',
  `if_redundant` int NOT NULL DEFAULT '0',
  `if_media` int NOT NULL DEFAULT '0',
  `if_type` int NOT NULL DEFAULT '0',
  `if_cid` char(10) NOT NULL DEFAULT '',
  `if_switch` char(50) NOT NULL DEFAULT '',
  `if_swcheck` int NOT NULL DEFAULT '0',
  `if_port` char(50) NOT NULL DEFAULT '',
  `if_description` char(255) NOT NULL DEFAULT '',
  `if_virtual` int NOT NULL DEFAULT '0',
  `if_monitored` int NOT NULL DEFAULT '0',
  `if_checklist` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`if_id`)
)
drop table rsdp_osteam;
CREATE TABLE `rsdp_osteam` (
  `os_id` int NOT NULL AUTO_INCREMENT,
  `os_rsdp` int NOT NULL DEFAULT '0',
  `os_sysname` char(60) NOT NULL DEFAULT '',
  `os_fqdn` char(60) NOT NULL DEFAULT '',
  `os_software` int NOT NULL DEFAULT '0',
  `os_complete` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`os_id`)
)
drop table rsdp_platform;
CREATE TABLE `rsdp_platform` (
  `pf_id` int NOT NULL AUTO_INCREMENT,
  `pf_rsdp` int NOT NULL DEFAULT '0',
  `pf_model` int NOT NULL DEFAULT '0',
  `pf_asset` char(20) NOT NULL DEFAULT '',
  `pf_serial` char(100) NOT NULL DEFAULT '',
  `pf_service` char(20) NOT NULL DEFAULT '',
  `pf_hba` int NOT NULL DEFAULT '0',
  `pf_redundant` int NOT NULL DEFAULT '0',
  `pf_row` char(20) NOT NULL DEFAULT '',
  `pf_rack` char(20) NOT NULL DEFAULT '',
  `pf_unit` int NOT NULL DEFAULT '0',
  `pf_special` char(100) NOT NULL DEFAULT '',
  `pf_circuita` char(20) NOT NULL DEFAULT '',
  `pf_circuitb` char(20) NOT NULL DEFAULT '',
  `pf_complete` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`pf_id`)
)
drop table rsdp_san;
CREATE TABLE `rsdp_san` (
  `san_id` int NOT NULL AUTO_INCREMENT,
  `san_rsdp` int NOT NULL DEFAULT '0',
  `san_sysport` char(60) NOT NULL DEFAULT '',
  `san_switch` char(30) NOT NULL DEFAULT '',
  `san_port` char(20) NOT NULL DEFAULT '',
  `san_media` int NOT NULL DEFAULT '0',
  `san_wwnnzone` char(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`san_id`)
)
drop table rsdp_server;
CREATE TABLE `rsdp_server` (
  `rsdp_id` int NOT NULL AUTO_INCREMENT,
  `rsdp_requestor` int NOT NULL DEFAULT '0',
  `rsdp_location` int NOT NULL DEFAULT '0',
  `rsdp_product` int NOT NULL DEFAULT '0',
  `rsdp_completion` date NOT NULL DEFAULT '1971-01-01',
  `rsdp_magic` int NOT NULL DEFAULT '0',
  `rsdp_project` int NOT NULL DEFAULT '0',
  `rsdp_platformspoc` int NOT NULL DEFAULT '0',
  `rsdp_sanpoc` int NOT NULL DEFAULT '0',
  `rsdp_networkpoc` int NOT NULL DEFAULT '0',
  `rsdp_virtpoc` int NOT NULL DEFAULT '0',
  `rsdp_dcpoc` int NOT NULL DEFAULT '0',
  `rsdp_srpoc` int NOT NULL DEFAULT '0',
  `rsdp_monitorpoc` int NOT NULL DEFAULT '0',
  `rsdp_apppoc` int NOT NULL DEFAULT '0',
  `rsdp_backuppoc` int NOT NULL DEFAULT '0',
  `rsdp_platform` int NOT NULL DEFAULT '0',
  `rsdp_application` int NOT NULL DEFAULT '0',
  `rsdp_service` int NOT NULL DEFAULT '0',
  `rsdp_vendor` int NOT NULL DEFAULT '0',
  `rsdp_function` char(50) NOT NULL DEFAULT '',
  `rsdp_processors` int NOT NULL DEFAULT '0',
  `rsdp_memory` char(20) NOT NULL DEFAULT '',
  `rsdp_ossize` char(20) NOT NULL DEFAULT '',
  `rsdp_osmonitor` int NOT NULL DEFAULT '0',
  `rsdp_appmonitor` int NOT NULL DEFAULT '0',
  `rsdp_datapalette` int NOT NULL DEFAULT '0',
  `rsdp_opnet` int NOT NULL DEFAULT '0',
  `rsdp_newrelic` int NOT NULL DEFAULT '0',
  `rsdp_centrify` int NOT NULL DEFAULT '0',
  `rsdp_backup` int NOT NULL DEFAULT '0',
  `rsdp_complete` int NOT NULL DEFAULT '0',
  `rsdp_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`rsdp_id`)
)
drop table rsdp_status;
CREATE TABLE `rsdp_status` (
  `st_id` int NOT NULL AUTO_INCREMENT,
  `st_rsdp` int NOT NULL DEFAULT '0',
  `st_completed` int NOT NULL DEFAULT '0',
  `st_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `st_user` int NOT NULL DEFAULT '0',
  `st_step` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`st_id`)
)
drop table rsdp_tickets;
CREATE TABLE `rsdp_tickets` (
  `tkt_id` int NOT NULL AUTO_INCREMENT,
  `tkt_rsdp` int NOT NULL DEFAULT '0',
  `tkt_build` int NOT NULL DEFAULT '0',
  `tkt_san` int NOT NULL DEFAULT '0',
  `tkt_network` int NOT NULL DEFAULT '1',
  `tkt_datacenter` int NOT NULL DEFAULT '1',
  `tkt_virtual` int NOT NULL DEFAULT '0',
  `tkt_sysins` int NOT NULL DEFAULT '0',
  `tkt_sysdns` int NOT NULL DEFAULT '0',
  `tkt_storage` int NOT NULL DEFAULT '0',
  `tkt_syscnf` int NOT NULL DEFAULT '0',
  `tkt_backups` int NOT NULL DEFAULT '0',
  `tkt_monitor` int NOT NULL DEFAULT '0',
  `tkt_appins` int NOT NULL DEFAULT '0',
  `tkt_appmon` int NOT NULL DEFAULT '0',
  `tkt_appcnf` int NOT NULL DEFAULT '0',
  `tkt_infosec` int NOT NULL DEFAULT '0',
  `tkt_sysscan` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`tkt_id`)
)
drop table rules;
CREATE TABLE `rules` (
  `rule_id` int NOT NULL AUTO_INCREMENT,
  `rule_parent` int NOT NULL DEFAULT '0',
  `rule_description` char(255) NOT NULL DEFAULT '',
  `rule_annotate` char(255) NOT NULL DEFAULT '',
  `rule_group` int NOT NULL DEFAULT '0',
  `rule_source` int NOT NULL DEFAULT '0',
  `rule_application` int NOT NULL DEFAULT '0',
  `rule_object` int NOT NULL DEFAULT '0',
  `rule_message` int NOT NULL DEFAULT '0',
  `rule_page` int NOT NULL DEFAULT '0',
  `rule_email` int NOT NULL DEFAULT '0',
  `rule_autoack` int NOT NULL DEFAULT '0',
  `rule_deleted` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`rule_id`)
)
drop table san;
CREATE TABLE `san` (
  `san_id` int NOT NULL AUTO_INCREMENT,
  `san_companyid` int NOT NULL DEFAULT '0',
  `san_wwid` char(30) NOT NULL,
  `san_subsystem` char(30) NOT NULL,
  `san_volume` char(30) NOT NULL,
  `san_lun` int NOT NULL DEFAULT '0',
  `san_volid` char(40) NOT NULL,
  `san_path` char(30) NOT NULL,
  `san_group` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`san_id`)
)
drop table security;
CREATE TABLE `security` (
  `sec_id` int NOT NULL AUTO_INCREMENT,
  `sec_name` char(255) NOT NULL DEFAULT '',
  `sec_family` int NOT NULL DEFAULT '0',
  `sec_severity` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`sec_id`)
)
drop table source_node;
CREATE TABLE `source_node` (
  `src_id` int NOT NULL AUTO_INCREMENT,
  `src_node` char(255) NOT NULL DEFAULT '',
  `src_deleted` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`src_id`)
)
drop table spectre;
CREATE TABLE `spectre` (
  `sp_system` char(50) DEFAULT NULL,
  `sp_var1` char(50) DEFAULT NULL,
  `sp_var2` char(50) DEFAULT NULL,
  `sp_var3` char(50) DEFAULT NULL
)
drop table sudoers;
CREATE TABLE `sudoers` (
  `sudo_id` int NOT NULL AUTO_INCREMENT,
  `sudo_companyid` int NOT NULL DEFAULT '0',
  `sudo_userid` int NOT NULL DEFAULT '0',
  `sudo_ticket` char(60) NOT NULL DEFAULT '',
  `sudo_expire` date NOT NULL DEFAULT '1971-01-01',
  PRIMARY KEY (`sudo_id`)
)
drop table swbackup;
CREATE TABLE `swbackup` (
  `sw_id` int NOT NULL AUTO_INCREMENT,
  `sw_name` char(20) NOT NULL,
  `sw_manufacturer` char(20) NOT NULL,
  `sw_type` char(30) NOT NULL,
  `sw_use` char(50) NOT NULL,
  `sw_cpuuser` int NOT NULL,
  `sw_server` char(30) NOT NULL,
  `sw_inservice` date NOT NULL DEFAULT '1971-01-01',
  `sw_podate` date NOT NULL DEFAULT '1971-01-01',
  `sw_accounted` date NOT NULL DEFAULT '1971-01-01',
  `sw_department` char(10) NOT NULL,
  `sw_project` char(10) NOT NULL,
  `sw_yearlycost` float(10,2) NOT NULL,
  `sw_cost` float(10,2) NOT NULL,
  `sw_maintdate` date NOT NULL DEFAULT '1971-01-01',
  `sw_vendor` char(20) NOT NULL,
  `sw_responsible` char(20) NOT NULL,
  `sw_support` char(10) NOT NULL,
  PRIMARY KEY (`sw_id`)
)
drop table vlanz;
CREATE TABLE `vlanz` (
  `vlan_id` int NOT NULL AUTO_INCREMENT,
  `vlan_vlan` char(10) NOT NULL DEFAULT '',
  `vlan_zone` char(10) NOT NULL DEFAULT '',
  `vlan_name` char(100) NOT NULL DEFAULT '',
  `vlan_description` char(100) NOT NULL DEFAULT '',
  `vlan_range` char(35) NOT NULL DEFAULT '',
  `vlan_gateway` char(20) NOT NULL DEFAULT '',
  `vlan_netmask` char(16) NOT NULL DEFAULT '',
  PRIMARY KEY (`vlan_id`)
)
drop table vulnerabilities;
CREATE TABLE `vulnerabilities` (
  `vuln_id` int NOT NULL AUTO_INCREMENT,
  `vuln_interface` int NOT NULL DEFAULT '0',
  `vuln_securityid` int NOT NULL DEFAULT '0',
  `vuln_group` int NOT NULL DEFAULT '0',
  `vuln_date` date NOT NULL DEFAULT '1971-01-01',
  `vuln_duplicate` int NOT NULL DEFAULT '0',
  `vuln_delete` int NOT NULL DEFAULT '0',
  `vuln_deldate` date NOT NULL DEFAULT '1971-01-01',
  PRIMARY KEY (`vuln_id`)
)
drop table vulnowner;
CREATE TABLE `vulnowner` (
  `vul_id` int NOT NULL AUTO_INCREMENT,
  `vul_interface` int NOT NULL DEFAULT '0',
  `vul_security` int NOT NULL DEFAULT '0',
  `vul_group` int NOT NULL DEFAULT '0',
  `vul_ticket` char(20) NOT NULL DEFAULT '',
  `vul_exception` int NOT NULL DEFAULT '0',
  `vul_description` char(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`vul_id`)
)
drop table west;
CREATE TABLE `west` (
  `west_id` int NOT NULL AUTO_INCREMENT,
  `west_rc` varchar(30) NOT NULL DEFAULT '',
  `west_eid` varchar(35) NOT NULL DEFAULT '',
  `west_serial` varchar(60) NOT NULL DEFAULT '',
  `west_name` varchar(30) NOT NULL DEFAULT '',
  `west_model` varchar(60) NOT NULL DEFAULT '',
  `west_app` varchar(110) NOT NULL DEFAULT '',
  `west_assignment` varchar(30) NOT NULL DEFAULT '',
  `west_status` varchar(20) NOT NULL DEFAULT '',
  `west_title` varchar(20) NOT NULL DEFAULT '',
  `west_location` varchar(20) NOT NULL DEFAULT '',
  `west_ponumber` varchar(20) NOT NULL DEFAULT '',
  `west_ipaddr` varchar(40) NOT NULL DEFAULT '',
  `west_owner` varchar(15) NOT NULL DEFAULT '',
  `west_support` varchar(30) NOT NULL DEFAULT '',
  `west_auditor` varchar(30) NOT NULL DEFAULT '',
  `west_os` varchar(75) NOT NULL DEFAULT '',
  PRIMARY KEY (`west_id`)
)


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

