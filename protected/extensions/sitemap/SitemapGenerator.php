<?php

/**
* Класс генерации карты сайта.
*/
namespace ext\sitemap;

class SitemapGenerator extends Sitemap //Наследуемся от карты сайта.
{
    //Точка функция которая генерирует отдельно страницы, категории, продукты
	public function generateSitemap() 
    {
        $this->disableNormalizeDate();
        
        $baseUrl=rtrim(\Yii::app()->createAbsoluteUrl('/'), '/');
        
        $this->setXsltUrl($baseUrl.'/data/sitemap-xsl/sitemap.xsl');
		$this->setPath(\Yii::getPathOfAlias('webroot.sitemaps').'/');
		
        $this->resetSitemap();
		$this->generatePageXml($prefix.'sitemap-page');
        
        $this->resetSitemap();
		$this->generateCategoryXml($prefix.'sitemap-category');
        
        $this->resetSitemap();
		$this->generateEventXml($prefix.'sitemap-events');
        
        $this->setFilename($prefix.'sitemap');
		$this->createSitemapIndex($baseUrl.'/', date('c'));
	}

	//Добавление meta данных.
	private function addMetaItem($url, $priority=false, $changefreq=false, $lastmod=false) 
    {
		$this->addItem(
            $url, 
            $this->normalizePriority($priority), 
            $this->normalizeChangeFreq($changefreq),
            $this->normalizeLastMod($lastmod?:date('Y-m-d H:i:s'))
        );
	}

    private function generateEventXml($xmlName) 
    {
		if($models=\Event::model()->findAll(['select'=>'id,update_time'])) {
            $this->setFilename($xmlName);
            $this->addMetaItem(\Yii::app()->createUrl('site/events'));
            foreach($models as $model) {
                $this->addMetaItem(
                    \Yii::app()->createUrl('site/event', ['id'=>$model->id]),
                    false,
                    false,
                    $model->update_time
                );
            }
		}
	}

	private function generatePageXml($xmlName) 
    {
		if($models=\Page::model()->findAll(['select'=>'id,update_time,alias'])) {
            $this->setFilename($xmlName);
            foreach($models as $model) {
                if($model->alias=='index') {
                    $url='/';
                }
                else {
                    $url=\Yii::app()->createUrl('site/page', ['id'=>$model->id]);
                }
                $this->addMetaItem(
                    $url,
                    $model->meta->priority,
                    $model->meta->changefreq,
                    $model->update_time
                );
            }
		}
	}

	private function generateCategoryXml($xmlName) 
    {
        $this->setFilename($xmlName);
        $this->addMetaItem('/shop');
        if($categories=\Category::model()->findAll(['select'=>'id,lft,rgt,level,root,update_time', 'order'=>'root,lft'])) {
            $priorities=[];
            foreach($categories as $model) {
                $priority=false;
                if($model->meta) {
                    $priority=$model->meta->priority;
                    if(empty($priority)){
                        if($model->level>1) $priority=sprintf('%0.1f',1.5/$model->level);
                        else $priority=1;
                    }
                }
                
                $priorities[$model->id]=$priority;
                
                $this->addMetaItem(
                    \Yii::app()->createUrl('shop/category', ['id'=>$model->id]),
                    $priority,
                    $model->meta->changefreq,
                    $model->update_time
                );
                
            }
            
            foreach($categories as $model) {
                $this->resetSitemap();
                $this->generateProductXml($model->id, $xmlName.'-'.$model->id.'-product', $priorities[$model->id]);
            }
        }
	}
    
    //Универсальная функция генерации по модели, если у модели есть мета бехивер.
	private function generateProductXml($categoryId, $xmlName, $priority) 
    {
        $_GET['id']=$categoryId;
        $criteria=\Product::model()->visibled()->with('productAttributes')->search(false, true);
        $criteria->select='`t`.`id`, `t`.`update_time`';
		if($models=\Product::model()->visibled()->findAll($criteria)) {
            $this->setFilename($xmlName);
            foreach($models as $model) {
                $this->addMetaItem(
                    \Yii::app()->createUrl('shop/product', ['id'=>$model->id]),
                    $model->meta->priority,
                    $model->meta->changefreq,
                    $model->update_time
                );
            }
        }
	}

	//Универсальная функция генерации по модели, если у модели есть мета бехивер.
	private function generateXml($url, $xmlName, $model) 
    {
		if($models=$model::model()->findAll()) {
            $this->setFilename($xmlName);
            foreach($models as $model) {
                $this->addMetaItem(
                    \Yii::app()->createUrl($url, ['id'=>$model->id]),
                    $model->meta->priority,
                    $model->meta->changefreq,
                    $model->update_time
                );
            }
        }
	}
    
    private function normalizeLastMod($time)
    {
        if(!$time) {
            return date('c');
        }
        return date_create_from_format('Y-m-d H:i:s', $time)->format('c');
    }
    
    private function normalizePriority($priority, $default=false)
    {
        if($default === false) {
            $default=\D::cms('sitemap_priority');
        }
        
        if(!is_numeric($priority) || ((float)$priority < 0) || ((float)$priority > 1)) {
            $priority=$default;
        }
        
        if((!(float)$priority || ((float)$priority > 0)) && ((float)$priority <= 1)) {
            return sprintf('%0.2f', (float)$priority);
        }
        
        return 1;
    }
    
    private function normalizeChangeFreq($changefreq, $default=false)
    {
        $values=['always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never'];
        
        if(in_array($changefreq, $values)) {
            return $changefreq;
        }
        
        if($default === false) {
            $default=\D::cms('sitemap_changefreq');
        }
        
        return in_array($default, $values) ? $default : 'always';
    }
}
