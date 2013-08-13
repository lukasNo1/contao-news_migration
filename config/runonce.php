<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2013 Leo Feyer
 *
 * @package Comments
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Contao;

/**
 * Run migration Scripts in the order we want
 * @copyright  Nothing interactive 2013  <https://www.nothing.ch/>
 * @author     Patrick Fiaux <nodz@nothing.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */

// News Migration
$objNews = new NewsGalleryMigration();
$objNews->run();

//die('RunOnce in debug mode.'); //Useful for debug keeps contao from deleting the runonce while debugging