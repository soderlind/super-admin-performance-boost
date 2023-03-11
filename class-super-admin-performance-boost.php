<?php

declare( strict_types = 1 );

class Super_Admin_Performance_Boost {

	public function __construct() {
		add_filter( 'pre_get_blogs_of_user', [ $this, 'super_admin_get_blogs_of_user' ], 9, 3 );
		add_filter( 'wp_list_table_class_name', [ $this, 'super_admin_wp_list_table_class_name' ], 10, 2 );
		add_filter( 'wpmu_users_columns', [ $this, 'filter_wpmu_users_columns' ] );
	}

	/**
	 * Super Admin owns all site, no point in listing them, i,e,: Remove the 'blogs' column from the users list table.
	 *
	 * @param string[] $users_columns An array of user columns. Default 'cb', 'username', 'name', 'email', 'registered', 'blogs'.
	 * @return string[] An array of user columns. Default 'cb', 'username', 'name', 'email', 'registered', 'blogs'.
	 */
	public function filter_wpmu_users_columns( array $users_columns ) : array {
		if ( ! \is_super_admin() ) {
			return $users_columns;
		}
		$users_columns = [
			'cb'         => '<input type="checkbox" />',
			'username'   => __( 'Username' ),
			'name'       => __( 'Name' ),
			'email'      => __( 'Email' ),
			'registered' => _x( 'Registered', 'user' ),
		// 'blogs'      => __( 'Sites' ),
		];
		return $users_columns;
	}

	/**
	 * For the Super Admin, use a custom list table classes
	 *
	 * @param string $class_name The list table class to use.
	 * @param array  $args       An array containing _get_list_table() arguments.
	 * @return string The list table class to use.
	 */
	public function super_admin_wp_list_table_class_name( string $class_name, array $args ) : string {
		if ( ! \is_super_admin() ) {
			return $class_name;
		}
		if ( 'WP_MS_Sites_List_Table' === $class_name ) {
			$class_name = 'Super_Admin_Sites_List_Table';
		}

		return $class_name;
	}

		/**
		 * For the superadmin, speed up the loading of the get_blogs_of_user() function.
		 *
		 * Stores the blogname, siteurl, home, and post_count in the wp_blogmeta table.
		 *
		 * @param null|object[] $sites   An array of site objects of which the user is a member.
		 * @param int           $user_id User ID.
		 * @param bool          $all     Whether the returned array should contain all sites, including those marked 'deleted', 'archived', or 'spam'. Default false.
		 * @return null|object[] An array of site objects of which the user is a member.
		 */
	public function super_admin_get_blogs_of_user( ?array $sites, int $user_id, bool $all ) : ?array {

		if ( ! \is_super_admin() ) {
			return $sites;
		}
		$_sites = \get_sites(
			[
				'orderby'  => 'path',
				// 'number'   => $this->load_increments,
				// 'offset'   => $offset,
				'deleted'  => '0',
				'mature'   => '0',
				'archived' => '0',
				'spam'     => '0',
			]
		);

		if ( empty( $_sites ) ) {
			return $sites;
		}

		$sites = [];
		foreach ( $_sites as $site ) {

			$s          = (array) $site;
			$blogname   = get_site_meta( $s['blog_id'], 'blogname', true );
			$siteurl    = get_site_meta( $s['blog_id'], 'siteurl', true );
			$home       = get_site_meta( $s['blog_id'], 'home', true );
			$post_count = get_site_meta( $s['blog_id'], 'post_count', true );

			if ( false === $blogname || false === $siteurl || false === $home || false === $post_count ) {
				continue;
			}

			if ( '' === $blogname ) {
				$blogname = get_blog_details( $s['blog_id'] )->blogname;
				add_site_meta( $s['blog_id'], 'blogname', $blogname );
			}
			if ( '' === $siteurl ) {
				$siteurl = get_blog_details( $s['blog_id'] )->siteurl;
				add_site_meta( $s['blog_id'], 'siteurl', $siteurl );
			}
			if ( '' === $home ) {
				$home = get_blog_details( $s['blog_id'] )->home;
				add_site_meta( $s['blog_id'], 'home', $home );
			}
			if ( '' === $post_count ) {
				$post_count = get_blog_details( $s['blog_id'] )->post_count;
				add_site_meta( $s['blog_id'], 'post_count', $post_count );
			}

			$sites[ $s['blog_id'] ] = (object) array_merge(
				$s,
				[
					'userblog_id' => $s['blog_id'],
					'blogname'    => $blogname,
					'siteurl'     => $siteurl,
					'home'        => $home,
					'post_count'  => $post_count,
				]
			);
		}

		return $sites ?? null;
	}



	public static function get_admin_url( int $blog_id ) : string {
		$admin_url = get_site_meta( $blog_id, 'siteurl', true );
		if ( false === $admin_url ) {
			$admin_url = get_blog_details( $blog_id )->siteurl;
			add_site_meta( $blog_id, 'siteurl', $admin_url );
		}
		return $admin_url;
	}

	public static function get_home_url( int $blog_id ) : string {
		$home_url = get_site_meta( $blog_id, 'home', true );
		if ( false === $home_url ) {
			$home_url = get_blog_details( $blog_id )->home;
			add_site_meta( $blog_id, 'home', $home_url );
		}
		return $home_url;
	}

}