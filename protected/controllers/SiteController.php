<?php

class SiteController extends Controller
{
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			'downloadFile'=>[
				'class'=>'\common\ext\file\actions\DownloadFileAction',
				'allowDirs'=>['files']
			]
		);
	}

    public function actionSitemap() {
        $title = 'Карта сайта';
        
        $this->prepareSeo($title);
        $this->breadcrumbs->add($title);
        
        $this->render('sitemap');
    }
    
    public function actionPrivacyPolicy()
    {
    	$this->layout = 'other';
    	
    	if(D::cms('privacy_policy')) {
	        $page = Page::model()->findByPk(D::cms('privacy_policy'));
    	}
    	else {
    		$page=Page::model()->wcolumns(['alias'=>'privacy-policy'])->find();
    	}
        if (!$page) {
            throw new CHttpException('404', 'Страница не найдена');
        }

        $this->prepareSeo($page->title);
        $this->seoTags($page);
        ContentDecorator::decorate($page);

        if($page->blog_id) {
        	$this->breadcrumbs->addByCmsMenu($page->blog);
        	$this->breadcrumbs->add($page->title);
        } else {
        	$this->breadcrumbs->addByCmsMenu($page, array(), true);
        }

        $view=$page->view_template ?: 'page';
        
        $this->render($view, compact('page'));
    }

	public function actionIndex()
	{
        $this->layout = 'index';

        $menuItem = CmsMenu::getInstance()->getDefault();

        if (!$menuItem)
            throw new CHttpException('404', 'Не найдена страница по умолчанию');

        if ($menuItem->options['model'] == 'page') {
            $page = Page::model()->findByPk($menuItem->options['id']);

            if (!$page)
                throw new CHttpException('404', 'Не найдена главная страница');

            $this->prepareSeo();
            $this->seoTags($page);
            ContentDecorator::decorate($page);

            $view=$page->view_template ?: 'page';
            $this->render($view, compact('page'));
        } elseif ($menuItem->options['model']=='shop') {
            $this->forward('shop/index');
        } elseif ($menuItem->options['model']=='event') {
            if (isset($menuItem->options['id'])) {
                $_GET['id'] = $menuItem->options['id'];
                $this->forward('site/event');
            } else
                $this->forward('site/events');
        } elseif ($menuItem->options['model']=='blog') {
            $_GET['id'] = $menuItem->options['id'];
            $this->forward('site/blog');
        } else {
            throw new CHttpException(404, 'Страница не определена');
        }
	}

    public function actionPage($id)
    {
        $this->layout = 'other';

        $page = Page::model()->findByPk($id);

        if (!$page) {
            throw new CHttpException('404', 'Страница не найдена');
        }

        $this->prepareSeo($page->title);
        $this->seoTags($page);
        ContentDecorator::decorate($page);

        if($page->blog_id) {
        	$this->breadcrumbs->addByCmsMenu($page->blog);
        	$this->breadcrumbs->add($page->title);
        } else {
        	$this->breadcrumbs->addByCmsMenu($page, array(), true);
        }

        $view=$page->view_template ?: 'page';
        
        $this->render($view, compact('page'));
    }

    public function actionEvent($id)
    {
        $this->layout = 'other';

        $event = Event::model()->findByPk($id);

        if (!$event) {
            throw new CHttpException('404', Yii::t('events','event_not_found'));
        }

        $this->prepareSeo($event->title);
		$this->seoTags($event);
        ContentDecorator::decorate($event);
        
        $this->breadcrumbs->add($this->getEventHomeTitle(), '/news');
        $this->breadcrumbs->add($event->title);
        
        $this->render('event', compact('event'));
    }

    public function actionEvents()
    {
        $this->layout = 'other';

        $criteria = new CDbCriteria();
        $criteria->condition = 'publish = 1';
        $criteria->order     = 'created DESC';

        $count = Event::model()->count($criteria);

        $pages = new CPagination($count);
        $pages->pageSize = Yii::app()->params['news_limit'] ? Yii::app()->params['news_limit'] : 7;
        $pages->applyLimit($criteria);

        $events = Event::model()->findAll($criteria);

        $this->prepareSeo($this->getEventHomeTitle());

        foreach($events as $e) {
            ContentDecorator::decorate($e);
        }
        
        $this->breadcrumbs->add($this->getEventHomeTitle());

        $this->render('events', compact('events', 'pages'));
    }

    public function actionBlog($id)
    {
        $this->layout = 'other';

        $blog = Blog::model()->findByPk($id);

        if (!$blog) {
            throw new CHttpException('404', Yii::t('blog','blog_not_found'));
        }

        $criteria = new CDbCriteria();
        $criteria->condition = 'blog_id = ?';
        $criteria->order     = 'created DESC';
        $criteria->params[]  = $id;

        $count = Page::model()->count($criteria);

        $pages = new CPagination($count);
        $pages->pageSize = Yii::app()->params['posts_limit'] ? Yii::app()->params['posts_limit'] : 7;
        $pages->applyLimit($criteria);

        $posts = Page::model()->findAll($criteria);

        $this->prepareSeo($blog->title);
        
        $this->breadcrumbs->addByCmsMenu($blog, array(), true);
        
        $this->render('blog', compact('blog', 'posts', 'pages'));
    }
    
    public function getEventHomeTitle()
    {
    	return D::cms('events_title', Yii::t('events','events_title'));
    }
}
