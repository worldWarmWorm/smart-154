<?php
class SitemapAutoGenerateBehavior extends \CBehavior
{
	public function events()
	{
		return array(
			'onAfterSave'=>'afterSave',
			'onAfterDelete'=>'afterDelete'
		);
	}

	/**
	 * (non-PHPdoc)
	 * @see \CActiveRecord::afterSave()
	 */
	public function afterSave()
	{
		$this->generateSiteMap();
		return true;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CActiveRecord::afterDelete()
	 */
	public function afterDelete()
	{
		$this->generateSiteMap();
		return true;
	}
	
	/**
	 * Генерация карты сайта
	 */
	protected function generateSiteMap()
	{
        if(D::cms('sitemap_auto_generate')) {
            $smap = new \ext\sitemap\SitemapGenerator();
            $smap->generateSitemap();
        }
	}
}
