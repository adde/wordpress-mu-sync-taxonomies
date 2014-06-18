WordPress MU Synchronize Taxonomies
===================================

Synchronizes terms from one taxonomy on the main site to another taxonomy on all sites in the network.


Installation
------------
Download as zip. Unzip folder into `yoursite/wp-content/plugins`. Activate the plugin in WordPress admin.


Usage
-----
Access the synchronisation tool via Tools menu in the admin panel. Select which taxonomy you want to sync from and to. Finally choose which sites you want the terms to be updated.


License
-------
This software is free and carries a WTFPL license.


Changelog
---------
0.0.2 (2014-06-18)
* Replaced deprecated function `is_site_admin()` with `is_super_admin()` to prevent notices.

0.0.1 (2014-06-17)
* First release.