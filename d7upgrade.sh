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
# we already have done the code upgrade at /var/www/fend.  We need to run the db upgrade before upgrading to D7
drush @fend updb

# disabling CiviCRM makes sense because otherwise the D7 site will complain about not being able to find CiviCRM
drush @fend dis civicrm

# --> empty fend d7 database'
echo 'MM says: empty fend d7 database'; mysql -e "drop database fend7_drupal;create database fend7_drupal"

# --> delete and recreate fend7 site' -> unecessary now because we are linking to the repocd 0
#echo 'MM says: delete fend7 site'; rm -r /var/www/fend7; mkdir /var/www/fend7

# --> run the site upgrade
echo 'MM says: run drush site upgrade'; cd /var/www/fend; drush sup @fend7 2

cd /var/www/fend7

drush en bartik seven toolbar civicrm
drush dis admin_menu
drush vset theme_default bartik #TODO change to fend7
drush vset admin_theme seven
drush cc all
drush civicrm-upgrade-db
# now I want to check the output of the upgrade script...

# check if the images are working OK
# 
# 
# The following contrib modules were enabled in your Drupal site, but are now standard in core: filefield, imagefield.  These modules may need to be reconfigured after the upgrade is complete.                                                                       [ok]
# You are using the project cck, which requires data migration or other special processing.  Please see http://drupal.org/project/cck and http://drupal.org/node/895314 for more information on how to do this.                                                        [warning]
# You are using the project filefield, which requires data migration or other special processing.  Data migration for this module will be provided by the Content Migrate submodule of cck.  Enable content_migrate after upgrading; see http://drupal.org/node/781088.[warning]
# You are using the project imagefield, which requires data migration or other special processing.  Data migration for this module will be provided by the Content Migrate submodule of cck.  Enable content_migrate after upgrading; see                              [warning]
# http://drupal.org/node/781088.
# You are using the project token, which requires data migration or other special processing.  In Drupal 7, the contrib token module handles UI, as well as field and profile tokens; all other functionality has been migrated to core.                               [warning]
# 
# 
# 
# Disabling admin_menu, content, filefield, imagefield, civicrm, civicrm_invoice, civicrmtheme, image, img_assist, image_attach, smtp, i18nblocks, i18n, i18nmenu, i18nstrings, i18ntaxonomy, pathauto, token, wysiwyg, views, views_ui, Fend-basic                    [ok]
# The following extensions will be disabled: admin_menu, content, filefield, imagefield, civicrm, civicrm_invoice, civicrmtheme, image, img_assist, image_attach, smtp, i18nblocks, i18n, i18nmenu, i18nstrings, i18ntaxonomy, pathauto, token, wysiwyg, views, views_ui, Fend-basic
# 
# Some user time zones have been emptied and need to be set to the correct values. Use the new time zone options to choose whether to remind users at login to set the correct time zone.                                                                              [warning]
# 
# The content type page had uploads disabled but contained uploaded file data. Uploads have been re-enabled to migrate the existing data. You may delete the "File attachments" field in the page type if this data is not necessary.                                  [status]
# The content type article had uploads disabled but contained uploaded file data. Uploads have been re-enabled to migrate the existing data. You may delete the "File attachments" field in the article type if this data is not necessary.                            [status]
# 

# TODO
# - work out how we will do the block re-arranging
# - check images are working OK
# - enable google analytics
# - 
