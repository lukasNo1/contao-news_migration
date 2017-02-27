# [UNMAINTAINED]
This project is not maintained anymore.

# CONTAO EXTENSION: new_migration - Imports newsgallery into contao 3
This is a migration script for contao 2 sites migrated to contao 3 that used the [newsgallery](https://contao.org/en/extension-list/view/newsgallery.10000069.en.html) extension. The extension is unlikely to be updated for contao 3 as the functionality can be achieved with the core. This extension will imports the newsgallery data into gallery content element of contao 3 news articles.

## SETUP AND USAGE
### Prerequisites
 * Contao 3.1.x

### Installation
1. Place the config and classes folders in system/modules/news_migration

### How to use
_Prerequisite:_ Run the contao 3.0 and 3.1 Installation scripts

1. (Recommended) Backup your database
2. Update the database (e.g. with the _Extension manager_) this will execute the runonce
3. Check the _System Log_ for the status of the news migration
4. (Optional) Update the database and remove the now imported tl_news fields (Only 'addGallery' is automatically removed to prevent double import)
5. (Optional) Remove the news_migration folder entirely


### Known Issues / To Dos

* None at the moment!

## VERSION HISTORY

### 0.1.0 (2013-08-13)
* Initial release

## LICENSE
* Author:		Nothing Interactive, Switzerland
* Website: 		[https://www.nothing.ch/](https://www.nothing.ch/)
* Version: 		0.1.0
* Date: 		2013-08-13
* License: 		[GNU Lesser General Public License (LGPL)](http://www.gnu.org/licenses/lgpl.html)
