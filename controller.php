<?php
/*
 * FontAwesome Social Media "Vist" Icons by Thomas Dausner
 * based on: SVG Social Media Icons by Karl Dilkington (aka MrKDilkington)
 *
 * This software is licensed under the terms described in the concrete5.org marketplace.
 * Please find the add-on there for the latest license copy.
 */
namespace Concrete\Package\TdsSocialMedia;

use Concrete\Core\Package\Package;
use Concrete\Core\Block\BlockType\BlockType;

class Controller extends Package
{
    protected $pkgHandle = 'tds_social_media';
    protected $appVersionRequired = '8.1';
    protected $pkgVersion = '0.0.1';

    public function getPackageName()
    {
        return t('TDS Social Media Icons EU-GDPR compliant');
    }

    public function getPackageDescription()
    {
        return t('Add EU-GDPR compliant social media icons on your pages.');
    }

    public function install()
    {
        $pkg = parent::install();

        $blk = BlockType::getByHandle($this->pkgHandle);
        if (!is_object($blk)) {
            BlockType::installBlockType($this->pkgHandle, $pkg);
        }
    }
	
 	public function uninstall()
	{
		$pkg = parent::uninstall();
 	}

}
