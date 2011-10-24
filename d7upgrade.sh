#!/bin/bash

#!/bin/bash

# --> backup and recreate current fend *Drupal* database
# mysqldump fend_drupal > /clients/fend/dumps/fend_drupal.sql; mysql -e "drop database fend_drupal;create database fend_drupal"; mysql fend_drupal < /clients/fend/mtl-orig/fend_drupal_db.sql

# --> backup and recreate current fend *CiviCRM* database
# mysql -e "drop database fend_civicrm;create database fend_civicrm"; mysql fend_civicrm < /clients/fend/mtl-orig/fend_civicrm_db.sql
mysql -e "drop database fend7_civicrm;create database fend7_civicrm"
mysqldump fend_civicrm | mysql fend7_civicrm
# --> clear cache for good measure.
drush @fend cc all

# --> run prelim database upgrade
# in case any modules have been updated for D6, we need to ensure they are updated
drush @fend upc
drush @fend updb

# disabling CiviCRM makes sense because otherwise the D7 site will complain about not being able to find CiviCRM
drush @fend dis civicrm

# --> empty fend d7 database'
echo 'MM says: empty fend d7 database'; mysql -e "drop database fend7_drupal;create database fend7_drupal"

# --> delete and recreate fend7 site' -> unecessary now because we are linking to the repocd 0
#echo 'MM says: delete fend7 site'; rm -r /var/www/fend7; mkdir /var/www/fend7

# --> run the site upgrade
echo 'MM says: run drush site upgrade'; cd /var/www/fend; drush sup @fend7

cd /var/www/fend7

drush en fender sevencivi toolbar civicrm
drush dis admin_menu
drush vset theme_default fender
drush vset admin_theme sevencivi
drush cc all
drush civicrm-upgrade-db

# TODO
# - work out how we will do the block re-arranging
# - check images are working OK
# - enable google analytics
# - 
