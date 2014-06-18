<?php
/*
Plugin Name: WordPress MU Synchronize Taxonomies
Plugin URI: https://github.com/adde/wordpress-mu-sync-taxonomies
Description: Synchronizes taxonomies from the main site to all sites in the network.
Version: 0.0.1
Author: Andreas Jönsson
Author URI: http://consid.se/
*/



/**
 * Add page to tools menu in admin panel
 *
 */
function st_add_pages() {
  if ( is_super_admin() ) {
    add_submenu_page('tools.php', 'Sync Taxonomies', 'Sync Taxonomies', 'manage_options', 'synctaxonomies', 'st_manager' );
  }
}
add_action('admin_menu', 'st_add_pages');



/**
 * Add page to settings menu in network admin panel
 *
 */
function st_network_add_pages() {
  add_submenu_page( 'settings.php', 'Sync Taxonomies', 'Sync Taxonomies', 'manage_options', 'synctaxonomies', 'st_manager' );
}
add_action('network_admin_menu', 'st_network_add_pages');



/**
 * Render the form. Tacke care of postback.
 *
 */
function st_manager() {

  $done = null;
  $taxonomies = get_taxonomies();

  // We dont want the main site in the list, only sub sites
  $sites = wp_get_sites(array('offset' => 1));

  if(isset($_POST['sync'])) {

    // Get all terms from source taxonomy
    $terms_to_sync = get_terms(array($_POST['taxonomy_source']), array('hide_empty' => false));
    $taxonomy_target = $_POST['taxonomy_target'];

    // Loop through sites and update the terms
    if( count( $_POST['sites'] ) ) {
      $count = 0;
      foreach ($_POST['sites'] as $site) {
        switch_to_blog($site);
        foreach ($terms_to_sync as $term) {
          wp_insert_term(
            $term->name,
            $taxonomy_target,
            array(
              'description'=> $term->description,
              'slug' => $term->slug
            )
          );
        }
        restore_current_blog();
      }
      $done = true;
    }
  }
  ?>

  <div class="wrap">

    <?php
    if( $done )
      echo '<div id="message" class="updated fade"><p><strong>' . __( 'Sync was completed successfully.', 'wpmu-sync-taxonomies' ) . '</strong></p></div>';

    echo '<h2>' . __( 'Synchronize Taxonomies', 'wpmu-sync-taxonomies' ) . '</h2>'; ?>

    <form name="sync_taxonomies" action="" method="post">
      <input type="hidden" name="action" value="synctaxonomies" />

      <?php wp_nonce_field('synctaxonomies'); ?>

      <table class="form-table">

        <tr valign="top">
          <th scope="row"><?php _e('Source Taxonomy','wpmu-sync-taxonomies') ?></th>
          <td>
            <select name="taxonomy_source" id="taxonomy_source" style="width:30%;">
              <?php foreach($taxonomies as $tax) : ?>
                <option><?php echo $tax; ?></option>
              <?php endforeach; ?>
            </select>
            <br />
            <?php _e('The taxonomy of which to synchronize from.','wpmu-sync-taxonomies') ?>
          </td>
        </tr>

        <tr valign="top">
          <th scope="row"><?php _e('Target Taxonomy','wpmu-sync-taxonomies') ?></th>
          <td>
            <select name="taxonomy_target" id="taxonomy_target" style="width:30%;">
              <?php foreach($taxonomies as $tax) : ?>
                <option><?php echo $tax; ?></option>
              <?php endforeach; ?>
            </select>
            <br />
            <?php _e('The taxonomy of which to synchronize to.','wpmu-sync-taxonomies') ?>
          </td>
        </tr>

        <tr valign="top">
          <th scope="row"><?php _e('Sites','wpmu-sync-taxonomies') ?></th>
          <td>
            <select name="sites[]" id="sites" multiple size="<?php echo count($sites); ?>" style="width:30%;">
              <?php foreach($sites as $site) : ?>
                <option value="<?php echo $site['blog_id']; ?>"><?php echo get_blog_details($site['blog_id'])->blogname; ?></option>
              <?php endforeach; ?>
            </select>
            <br />
            <?php _e('Which blogs the taxonomies should be synced to.','wpmu-sync-taxonomies') ?>
          </td>
        </tr>

      </table>

      <div class='submit'><input class='button-primary' type='submit' name='sync' value='<?php _e( 'Synchronize', 'wpmu-sync-taxonomies' ) ?>' /></div>
    </form>
  </div> <!-- end .wrap -->
  <?php
}
