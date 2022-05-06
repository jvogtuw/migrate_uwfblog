UWF Blog Migration
==================

This module assists in the migration of configuration and content from the
Drupal 7 UWF Blog to Drupal 9.

Instructions
------------

1) Install a Drupal 9 site using the facilities_boundless profile (this will be replaced with the starter site). Make sure you have:
  - These text filters: full_html; standard_html
  - These roles (besides the default admin, anon, auth): content manager; content admin

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

4) Define a database connection:
   - ['migrate']['default']
  Technically, you can call it something other than 'migrate' but you'll have to define another connection called 'migrate'. The Drush migrate commands will error if it can't find a 'migrate' connection even if your migration is looking for something else.

5) Make sure that the following modules are disabled:
   - transliterate_filenames (It messes with migrating file paths and creates duplicate files. Reinstall it once you're done with migrations.)
   - web_profiler (It has some classes that cause namespace errors with multiple modules.)

Continue to the caveats for additional info before executing the migrations.


Caveats
-------

1) Pathauto pattern migrations don't play well when the bundle name is changing
   as we're doing in this migration by renaming 'fsblog' to 'post'. Instead of
   writing a custom source plugin to override
   Drupal\pathauto\Plugin\migrate\source\PathautoPattern, just create the 'post'
   pattern manually.

2) Running migrate update on the paragraph item migrations is not recommended
   because it (sometimes?) throws errors when changes are found. Something
   about trying to change the revision ID. [Not sure if this is still true]

3) If a lede paragraph changes in the source, you'll need to rollback and rerun
   both blog_d7_paragraph_fsblog_section and blog_d7_node_complete_fsblog, in
   that order, to update the destination's field_post_abstract.


After migration
---------------

1) After the migrations are complete, reenable transliterate_filenames. Only do this once you know you're done running migrations.

2) Post field organization:
   - Hide the field_post_lede on Blog post nodes. It was migrated to make it
     easier to copy the contents of its body field to the new field_post_abstract.
     You can delete the field entirely once you're absolutely sure you're done
     migrating.
   - Hide the 'Archived' field from edit form and display.
   - Update the 'Abstract (temp field - don't use)' field label to just
     'Abstract'.

3) Set permissions.

4) Build Workflows/Content moderation/Notifications.

5) Build main menu.

6) Create view(s) for the homepage and term pages.

7) Configure Solr search.

8) Set the allowed paragraph types for field_post_content.


Highlights
----------

Some of the things this migration does/has...

* Process plugin mapping text formats. Not entirely sure it's necessary but it's at least one way to do it.

* Source plugin to get the alias and publication date. There was an issue migrating the aliases with a separate migration. Something cause by renaming the node bundle I think, but it's been a while.

* Configuration changes:
  - Combined two paragraph types ("section" and "full_image") into a single paragraph type ("content_section").
  - Changed node bundle name: "fsblog" to "post"
  - Changed many field names

* Updated embedded media tags with media_wysiwyg_filter process plugin (see media_migration module).

* Pulled the value from a field within a paragraph to a node-level field. Lede to Abstract.

* Converted document/video/image entities to media entities with media_migration.
