<?php
/**
 * Super Admin Performance Boost. Modify the list of sites in the network admin.
 *
 * @package   Super_Admin_Performance_Boost
 */

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}
if ( ! class_exists( 'WP_MS_Sites_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-ms-sites-list-table.php';
}

/**
 * For the Super Admin, display a list of all sites on the network using
 * bespoke Super_Admin_Performance_Boost::get_admin_url() and Super_Admin_Performance_Boost::get_home_url().
 *
 * @since 1.0.0
 *
 * @see WP_MS_Sites_List_Table
 */
class Super_Admin_Sites_List_Table extends WP_MS_Sites_List_Table {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() { // phpcs:ignore
		parent::__construct();
	}

	/**
	 * Generates and displays row action links. (Copied from WP_MS_Sites_List_Table).
	 * Modified to use Super_Admin_Performance_Boost::get_admin_url() and Super_Admin_Performance_Boost::get_home_url().
	 *
	 * @param array  $item        Site being acted upon.
	 * @param string $column_name Current column name.
	 * @param string $primary     Primary column name.
	 * @return string Row actions output for sites in Multisite, or an empty string
	 *                if the current column is not the primary column.
	 */
	protected function handle_row_actions( $item, $column_name, $primary ) {
		if ( $primary !== $column_name ) {
			return '';
		}

		// Restores the more descriptive, specific name for use within this method.
		$blog     = $item;
		$blogname = untrailingslashit( $blog['domain'] . $blog['path'] );

		// Preordered.
		$actions = [
			'edit'       => '',
			'backend'    => '',
			'activate'   => '',
			'deactivate' => '',
			'archive'    => '',
			'unarchive'  => '',
			'spam'       => '',
			'unspam'     => '',
			'delete'     => '',
			'visit'      => '',
		];

		$actions['edit']    = '<a href="' . esc_url( network_admin_url( 'site-info.php?id=' . $blog['blog_id'] ) ) . '">' . __( 'Edit' ) . '</a>';
		$actions['backend'] = "<a href='" . esc_url( Super_Admin_Performance_Boost::get_admin_url( $blog['blog_id'] ) ) . "' class='edit'>" . __( 'Dashboard' ) . '</a>';
		if ( get_network()->site_id !== $blog['blog_id'] ) {
			if ( '1' === $blog['deleted'] ) {
				$actions['activate'] = '<a href="' . esc_url( wp_nonce_url( network_admin_url( 'sites.php?action=confirm&amp;action2=activateblog&amp;id=' . $blog['blog_id'] ), 'activateblog_' . $blog['blog_id'] ) ) . '">' . __( 'Activate' ) . '</a>';
			} else {
				$actions['deactivate'] = '<a href="' . esc_url( wp_nonce_url( network_admin_url( 'sites.php?action=confirm&amp;action2=deactivateblog&amp;id=' . $blog['blog_id'] ), 'deactivateblog_' . $blog['blog_id'] ) ) . '">' . __( 'Deactivate' ) . '</a>';
			}

			if ( '1' === $blog['archived'] ) {
				$actions['unarchive'] = '<a href="' . esc_url( wp_nonce_url( network_admin_url( 'sites.php?action=confirm&amp;action2=unarchiveblog&amp;id=' . $blog['blog_id'] ), 'unarchiveblog_' . $blog['blog_id'] ) ) . '">' . __( 'Unarchive' ) . '</a>';
			} else {
				$actions['archive'] = '<a href="' . esc_url( wp_nonce_url( network_admin_url( 'sites.php?action=confirm&amp;action2=archiveblog&amp;id=' . $blog['blog_id'] ), 'archiveblog_' . $blog['blog_id'] ) ) . '">' . _x( 'Archive', 'verb; site' ) . '</a>';
			}

			if ( '1' === $blog['spam'] ) {
				$actions['unspam'] = '<a href="' . esc_url( wp_nonce_url( network_admin_url( 'sites.php?action=confirm&amp;action2=unspamblog&amp;id=' . $blog['blog_id'] ), 'unspamblog_' . $blog['blog_id'] ) ) . '">' . _x( 'Not Spam', 'site' ) . '</a>';
			} else {
				$actions['spam'] = '<a href="' . esc_url( wp_nonce_url( network_admin_url( 'sites.php?action=confirm&amp;action2=spamblog&amp;id=' . $blog['blog_id'] ), 'spamblog_' . $blog['blog_id'] ) ) . '">' . _x( 'Spam', 'site' ) . '</a>';
			}

			if ( current_user_can( 'delete_site', $blog['blog_id'] ) ) {
				$actions['delete'] = '<a href="' . esc_url( wp_nonce_url( network_admin_url( 'sites.php?action=confirm&amp;action2=deleteblog&amp;id=' . $blog['blog_id'] ), 'deleteblog_' . $blog['blog_id'] ) ) . '">' . __( 'Delete' ) . '</a>';
			}
		}

		$actions['visit'] = "<a href='" . esc_url( Super_Admin_Performance_Boost::get_home_url( $blog['blog_id'], '/' ) ) . "' rel='bookmark'>" . __( 'Visit' ) . '</a>';

		/**
		 * Filters the action links displayed for each site in the Sites list table.
		 *
		 * The 'Edit', 'Dashboard', 'Delete', and 'Visit' links are displayed by
		 * default for each site. The site's status determines whether to show the
		 * 'Activate' or 'Deactivate' link, 'Unarchive' or 'Archive' links, and
		 * 'Not Spam' or 'Spam' link for each site.
		 *
		 * @since 3.1.0
		 *
		 * @param string[] $actions  An array of action links to be displayed.
		 * @param int      $blog_id  The site ID.
		 * @param string   $blogname Site path, formatted depending on whether it is a sub-domain
		 *                           or subdirectory multisite installation.
		 */
		$actions = apply_filters( 'manage_sites_action_links', array_filter( $actions ), $blog['blog_id'], $blogname );

		return $this->row_actions( $actions );
	}
}
