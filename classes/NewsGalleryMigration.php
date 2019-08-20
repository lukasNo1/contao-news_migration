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
 * Class NewsGalleryMigration
 *
 * The goal of this script is to migrate the contents of the NewsGallery
 * into contao 3 news content elements.
 *
 * newsgallery migration
 * @package Contao
 * @copyright  Nothing interactive 2013  <https://www.nothing.ch/>
 * @author     Patrick Fiaux <nodz@nothing.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */
class NewsGalleryMigration extends \Controller {

    /**
     * Initialize the object
     */
    public function __construct() {
        parent::__construct();

        // Fix potential Exception on line 0 because of __destruct method (see http://dev.contao.org/issues/2236)
        $this->import((TL_MODE == 'BE' ? 'BackendUser' : 'FrontendUser'), 'User');
        $this->import('Database');
    }


    /**
     * Run updates
     */
    public function run() {
        $this->log('Running NewsMigration contao 2 to 3 scripts', __CLASS__ . ' run()', TL_CONFIGURATION);

        // Migrate the news gallery module data
        $this->migrateNewsGallery();
        // after this runs the extra fields from tl_news can be safely deleted from the database
    }

    /**
     * Migrates the contao 2 newsgallery data into
     * contao 3 ce_gallery inside of news articles
     * https://contao.org/en/extension-list/view/newsgallery.10000069.en.html
     */
    private function migrateNewsGallery() {
        // If the tl_news addGallery field doesn't exist skip this import
        if (!$this->Database->fieldExists('addGallery', 'tl_news'))
        {
            return;
        }

        $selectQuery = "SELECT * FROM `tl_news` WHERE `addGallery`=?";

        $insertQuery = "INSERT INTO `tl_content` " . "%s";

        $dropQuery = "ALTER TABLE `tl_news` DROP `addGallery`";

        $objResult = $this->Database->prepare($selectQuery)->execute(1);

        // Only import if any news actually have galleries
        if ($objResult->numRows > 0)
        {
            $migrated      = 0;
            $filesNotFound = 0;
            ;
            while ($objResult->next())
            {
                // Set the insert data
                $gallery = array(
                    'pid'         => $objResult->id,
                    'ptable'      => 'tl_news',
                    'type'        => 'gallery',
                    'tstamp'      => time(),
                    // This has to be done manually below
                    'multiSRC'    => '',
                    'perRow'      => $objResult->perRow,
                    // There's no meta sort in c3 so use custom
                    'sortBy'      => ($objResult->sortBy == 'meta') ? 'custom' : $objResult->sortBy,
                    'size'        => $objResult->gal_size,
                    'imagemargin' => $objResult->gal_imagemargin,
                    'fullsize'    => $objResult->gal_fullsize,
                    'headline'    => $objResult->gal_headline,
                );

                // Update the multi SRC to contao 3 files model
                $files = unserialize($objResult->multiSRC);

                $files = $files ?: [];

                $multiSRC = array();
                // Lookup each file path in the contao file model and get id
                foreach ($files as $file)
                {
                    $find = \FilesModel::findMultipleByPaths(array($file));

                    //var_dump($find->getResult()->count());

                    if ($find > 0)
                    {
                        $multiSRC[] = $find->first()->uuid;
                    }
                    else
                    {
                        $this->log(
                            'Image/folder not found: ' . $file . ' skipping it.',
                            __CLASS__ . ' migrateNewsGallery()',
                            TL_ERROR
                        );
                        $filesNotFound++;
                    }
                }

                $gallery['multiSRC'] = serialize($multiSRC);

                // Build an insert query
                $this->Database->prepare($insertQuery)->set($gallery)->execute();

                $migrated++;
            }

            $this->log(
                'In total ' . $filesNotFound . ' files could not be located and were removed from galleries.',
                __CLASS__ . ' migrateNewsGallery()',
                TL_ERROR
            );

            $this->log(
                'Successfully migrated ' . $migrated . '/' . $objResult->numRows . ' galleries into Contao3 News articles',
                __CLASS__ . ' migrateNewsGallery()',
                TL_CRON
            );

            // Drop add gallery to avoid double import
            $this->Database->prepare($dropQuery)->execute();
            // Note the other fields still have to be added manually
        }
    }
}