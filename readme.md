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
the detail record of a server, and any icons or wait bars used by the Inventory to show scripts are loading data.

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


