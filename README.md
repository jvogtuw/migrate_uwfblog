UWF Blog Migration
==================

This module assists in the migration of configuration and content from the
Drupal 7 UWF Blog to Drupal 9.

Basic use
---------

1) Install a Drupal 9 site using the facilities_boundless profile.

2) Copy example.settings.local.php to the new site's directory and rename it to
   settings.local.php. Make sure the settings.php file knows about
   settings.local.php.

3) Add the following lines to settings.local.php:

    /** Tell the media_migration module to convert embed tokens to media_embed, preferably. */
    $settings['media_migration_embed_token_transform_destination_filter_plugin'] =
    'media_embed';
    $settings['media_migration_embed_media_reference_method'] = 'uuid';

    /** Increase the php memory limit for executing migrations via CLI **/
    if (PHP_SAPI === 'cli') {
      ini_set('memory_limit', '1.5G');
    }

4) Define two database connections:
   - ['migrate']['default']
   - ['d7_blog']['default']
   This is dumb and due to a bug. Make the definitions the same. It won't error
   if they're different, but it just gets confusing. The migrations in this
   module are looking for the d7_blog key. This should point to the Drupal 7
   version of the Blog that you want to migrate.

5) Make sure that the following modules are disabled:
   - transliterate_filenames (It messes with migrating file paths. You should
     reinstall it once you're done with migrations.)
   - web_profiler (It has conflicts with a lot of modules and will likely throw
     errors)

Continue to the caveats for additional info before executing the migrations.


Caveats
-------

1) Pathauto pattern migrations don't play well when the bundle name is changing
   as we're doing in this migration by renaming 'fsblog' to 'post'. Instead of
   writing a custom source plugin to override
   Drupal\pathauto\Plugin\migrate\source\PathautoPattern, just create the 'post'
   pattern manually.

2) Rolling back the paragraph item content migrations doesn't always delete all
   of the destination paragraph items. Running cron will delete some/all of
   them. If there are any left over, use the delete orphans tool
   (admin/config/system/delete-orphans), views bulk operations or custom code to
   delete them.

3) Running migrate update on the paragraph item migrations is not recommended
   because it (sometimes?) throws errors when changes are found. Something
   about trying to change the revision ID.

4) If a lede paragraph changes in the source, you'll need to rollback and rerun
   both blog_d7_paragraph_fsblog_section and blog_d7_node_complete_fsblog, in
   that order, to update the destination's field_post_abstract.


After migration
---------------

1) After the migrations are complete, reenable transliterate_filenames.
2) Delete or at least hide the field_post_lede on Blog post nodes. It was only
   created to make it easier to copy the contents of its body field to the new
   field_post_abstract.

