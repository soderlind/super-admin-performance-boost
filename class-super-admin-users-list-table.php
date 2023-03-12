<?php
/**
 * Super Admin Performance Boost. Modify the list of users in the network admin.
 *
 * @package   Super_Admin_Performance_Boost
 */

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}
if ( ! class_exists( 'WP_MS_Users_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-ms-users-list-table.php';
}

/**
 * For the Super Admin, If Super Admin, link to the sites.php page. Otherwise display a list to sites on the network using
 * bespoke  Super_Admin_Performance_Boost::get_home_url().
 *
 * @since 1.1.0
 *
 * @see WP_MS_Users_List_Table
 */
class Super_Admin_Users_List_Table extends WP_MS_Users_List_Table {


	/**
	 * If Super Admin, link to the sites.php page.
	 *
	 * @since 1.1.0
	 *
	 * @param WP_User $user
	 * @param string  $classes
	 * @param string  $data
	 * @param string  $primary
	 */
	protected function _column_blogs( $user, $classes, $data, $primary ) {
		echo '<td class="', $classes, ' has-row-actions" ', $data, '>';
		if ( is_super_admin( $user->ID ) ) {
			echo '<a href="' . esc_url( network_admin_url( 'sites.php' ) ) . '">' . __( 'Sites' ) . '</a>';
		} else {
			echo $this->column_blogs( $user );
			echo $this->handle_row_actions( $user, 'blogs', $primary );
		}
		echo '</td>';
	}

	/**
	 * Handles the sites column output. (Copied from WP_MS_Users_List_Table).
	 * Modified to use Super_Admin_Performance_Boost::get_home_url().
	 *
	 * @since 1.1.0
	 *
	 * @param WP_User $user The current WP_User object.
	 */
	public function column_blogs( $user ) {
		$blogs = get_blogs_of_user( $user->ID, true );
		if ( ! is_array( $blogs ) ) {
			return;
		}

		foreach ( $blogs as $site ) {
			if ( ! can_edit_network( $site->site_id ) ) {
				continue;
			}

			$path         = ( '/' === $site->path ) ? '' : $site->path;
			$site_classes = [ 'site - ' . $site->site_id ];
			/**
			 * Filters the span class for a site listing on the mulisite user list table.
			 *
			 * @since WP 5.2.0
			 *
			 * @param string[] $site_classes Array of class names used within the span tag. Default "site-#" with the site's network ID .
			 * @param int      $site_id      Site ID .
			 * @param int      $network_id   Network ID .
			 * @param WP_User  $user         WP_User object .
			 */
			$site_classes = apply_filters( 'ms_user_list_site_class', $site_classes, $site->userblog_id, $site->site_id, $user );
			if ( is_array( $site_classes ) && ! empty( $site_classes ) ) {
				$site_classes = array_map( 'sanitize_html_class', array_unique( $site_classes ) );
				echo '<span class="' . esc_attr( implode( ' ', $site_classes ) ) . '">';
			} else {
				echo '<span>';
			}
			echo '<a href="' . esc_url( network_admin_url( 'site-info.php?id=' . $site->userblog_id ) ) . '">' . str_replace( '.' . get_network()->domain, '', $site->domain . $path ) . '</a>';
			echo ' <small class="row-actions">';
			$actions         = [];
			$actions['edit'] = '<a href="' . esc_url( network_admin_url( 'site-info.php?id=' . $site->userblog_id ) ) . '">' . __( 'Edit' ) . '</a>';

			$class = '';
			if ( 1 === (int) $site->spam ) {
				$class .= 'site-spammed ';
			}
			if ( 1 === (int) $site->mature ) {
				$class .= 'site-mature ';
			}
			if ( 1 === (int) $site->deleted ) {
				$class .= 'site-deleted ';
			}
			if ( 1 === (int) $site->archived ) {
				$class .= 'site-archived ';
			}

			$actions['view'] = '<a class="' . $class . '" href="' . esc_url( Super_Admin_Performance_Boost::get_home_url( $site->userblog_id ) ) . '">' . __( 'View' ) . '</a>';

			/**
			 * Filters the action links displayed next the sites a user belongs to
			 * in the Network Admin Users list table.
			 *
			 * @since WP 3.1.0
			 *
			 * @param string[] $actions     An array of action links to be displayed. Default 'Edit', 'View'.
			 * @param int      $userblog_id The site ID.
			 */
			$actions = apply_filters( 'ms_user_list_site_actions', $actions, $site->userblog_id );

			$action_count = count( $actions );

			$i = 0;

			foreach ( $actions as $action => $link ) {
				++$i;

				$separator = ( $i < $action_count ) ? ' | ' : '';

				echo "<span class='$action'>{$link}{$separator}</span>";
			}

			echo '</small></span><br />';
		}
	}

}
