# Super Admin Performance Boost

Try to aviod using switch_to_blog() and restore_current_blog() when possible.

Super Admin owns all site, so:

1. Short-circuit the `get_blogs_of_user()` function. We don't have to check if the site is own by the user, we want all sites.
2. Extend `WP_MS_Sites_List_Table` to and use bespoke `Super_Admin_Performance_Boost::get_admin_url()` and `Super_Admin_Performance_Boost::get_home_url()` funtions.
3. Hide the `Sites` column in `WP_MS_Users_List_Table`, no point in [listing the sites](assets/all-sites.png) the super admins owns (they own 'em all)

## Installation

You know the drill.

## Copyright and License

Super Admin Performance Boost is copyright 2023+ Per Soderlind

Super Admin Performance Boost is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 2 of the License, or (at your option) any later version.

Super Admin Performance Boost is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU Lesser General Public License along with the Extension. If not, see http://www.gnu.org/licenses/.
