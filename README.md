# Super Admin Performance Boost

> Also, take a look at [Super Admin All Sites Menu](https://github.com/soderlind/super-admin-all-sites-menu#super-admin-all-sites-menu)

On a WordPress Multisite, tries to avoid using [`switch_to_blog()` and `restore_current_blog()`](assets/switch-to-blog.png) when possible.

## Description

1. Short-circuit the `get_blogs_of_user()` function. We don't have to check if a site is own by the user, we want all sites.
2. Extend `WP_MS_Sites_List_Table` and use bespoke `Super_Admin_Performance_Boost::get_admin_url()` and `Super_Admin_Performance_Boost::get_home_url()` funtions.
3. Extend `WP_MS_Users_List_Table`
   - For the Super Admin, hide their sites in the `Sites` column. No point in [listing the sites](assets/all-sites.png), the super admins own 'em all.
   - For rest of the users, use bespoke `Super_Admin_Performance_Boost::get_home_url()` funtions.

## Side note

I wish I did't have to write this plugin, but it feels like WordPress Multisite is not ready for the Super Admin role.

I have a multisite with 100+ sites, and the following issues are a pain:

- The [My Sites menu doesn't work for more thqn 23 sites](https://core.trac.wordpress.org/ticket/15317), hence my [Super Admin All Sites Menu](https://github.com/soderlind/super-admin-all-sites-menu#super-admin-all-sites-menu) plugin.
- `switch_to_blog()`, there's no need for switching to a blog to check if the super admin has access, they do.
- There's no point in listing all sites in the `Sites` column for the Super Admin, they own them all.

## Installation

You know the drill.

## Copyright and License

Super Admin Performance Boost is copyright 2023+ Per Soderlind

Super Admin Performance Boost is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 2 of the License, or (at your option) any later version.

Super Admin Performance Boost is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU Lesser General Public License along with the Extension. If not, see http://www.gnu.org/licenses/.
