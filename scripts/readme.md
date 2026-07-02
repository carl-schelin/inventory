### Listing of scripts

The following scripts are available to manage the environment.



accounts.php

# need 2 command line options.
# p|g - Password file or Group file
# path and file name
#
# accounts.php p /usr/local/admin/servers/[servername]/passwd
# accounts.php g /usr/local/admin/servers/[servername]/group
# 
# ultimately the user name needs to be matched with the group as well
# check the third field for group name and associate that.




ansible.php
ansible.yaml.php

The ansible script created a ini type hosts file which is fine for some uses but fails with others.

The ansible.yaml script creates a more appropriate inventory file which is used by ansible.


bugsnfeatures.php

Sends out an email with the list of requested bugs and features to the email listed in the settings.php site dev line

cert.notify.php

# get today's date
# loop through the certs in the database.
# build the localtime value (seconds from epoch)
# read the cert_group value to get the list of users who want to be notified
# loop through the users 
# build the warning date
# compare it with the expiration date


chkserver.import.php

Imports the output of chkserver for each system and assigns the approprate level based on the output.



chkserver.input.php

jj:



core.exclude.php
create.routes.php
endoflife.php
filesystems.php
fingerprint.php
function.php
import.php
initialize.php
inventory.php
inventory.review.php
ipaddress.import.php
lockuser.php
manage.servers
nagios.copy
nagios.lab.php
nagios.loms.php
nagios.prod.php
nagios.sqa.php
nagios.switch.php
package.import.php
patch.count.php
server.exclude.php
servers.php
serverupdate
service.class.php
settings.php
show.inventory.submit.php
show.inventory.userlist.php
software.eol.php
support.email.php
update.ansible.php
update.monitoring.php
upload.email.php
upload.family.php
upload.security.php
upload.vulnerability.php
users.exclude.php
valid.email.php
vmimport.php
