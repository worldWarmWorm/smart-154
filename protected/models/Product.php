<?php
/**
 * Class Product
 * This is the model class for table "product".
 *
 * The followings are the available columns in table 'product':
 * @property integer $id
 * @property integer $category_id
 * @property string $code
 * @property string $title
 * @property float $price
 * @property float $old_price
 * @property boolean $notexist
 * @property boolean $new
 *
 */
use common\components\helpers\HArray as A;
use common\components\helpers\HDb;
use common\components\helpers\HRequest as R;
use common\components\helpers\HYii as Y;

class Product extends \common\components\base\ActiveRecord
{
    protected $moreImg;
    public $offer;
    public $offerHeaders = ['title' => 'Цвет', 'hex' => 'hex'];
    /**
     * (non-PHPdoc)
     * @see \CModel::behaviors()
     */
    public function behaviors()
    {
        return A::m(parent::behaviors(), [
            'aliasBehavior' => '\DAliasBehavior',
            'metaBehavior' => '\MetadataBehavior',
//             'textBehavior'=>'\common\ext\text\behaviors\TextBehavior',
            'updateTimeBehavior' => [
                'class' => '\common\ext\updateTime\behaviors\UpdateTimeBehavior',
                'addColumn' => false,
            ],
            'mainImageBehavior' => [
                'class' => '\common\ext\file\behaviors\FileBehavior',
                'attribute' => 'main_image',
                'attributeLabel' => 'Изображение',
                'attributeEnable' => 'main_image_enable',
                'attributeAlt' => 'main_image_alt',
                'attributeAltEmpty' => 'title',
                'enableValue' => true,
                'defaultSrc' => '/images/shop/product_no_image.png',
                'imageMode' => true,
            ],
//             'imageBehavior'=>[
            //                 'class'=>'\file\behaviors\ImageBehavior',
            //                 'attributeLabel'=>'Главная фотография',
            //             ],
            //             'imagesBehavior'=>'\file\behaviors\ImagesBehavior',
            //             'filesBehavior'=>'\file\behaviors\FilesBehavior',
            'sortBehavior' => '\common\ext\sort\behaviors\SortBehavior',
            'sitemapAutoGenerateBehavior' => '\SitemapAutoGenerateBehavior',
            'dataAttributeBehavior' => [
                'class' => '\common\ext\dataAttribute\behaviors\DataAttributeBehavior',
                'attribute' => 'data',
                'attributeLabel' => 'Торговые предложения',
            ],
        ]);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'product';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return $this->getRules(array(
            array('category_id, title', 'required'),
            array('price, old_price', 'numerical', 'numberPattern' => '/^[\d\s]+([.,][\d\s]+)?$/', 'message' => 'Число должно быть целым, либо в формате X.XX'),
            array('category_id', 'numerical', 'integerOnly' => true),
            array('title, link_title', 'length', 'max' => 255),
            array('notexist, sale, new, hit, in_carousel, on_shop_index', 'boolean'),
            array('price, code, hidden, brand_id, description', 'safe'),
        ));
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return $this->getRelations(array(
            'brand' => [self::BELONGS_TO, 'Brand', 'brand_id'],
            'category' => array(self::BELONGS_TO, 'Category', 'category_id'),
            'productAttributes' => array(self::HAS_MANY, 'EavValue', 'id_product'),
            'relatedCategories' => array(self::HAS_MANY, 'RelatedCategory', 'product_id'),
            'reviews' => array(self::HAS_MANY, 'ProductReview', 'product_id'),
        ));
    }

    /**
     * {@inheritDoc}
     * @see CActiveRecord::scopes()
     */
    public function scopes()
    {
        return $this->getScopes([
            'lastRecord' => ['order' => '`id` DESC', 'limit' => 1],
            'cardColumns' => [
                'select' => '`t`.`id`, `t`.`category_id`, t.data, title, code, price, sale, new, hit, link_title, notexist, old_price, `main_image`, `main_image_alt`, `main_image_enable`, `hidden`',
            ],
            'defaultOrder' => ['order' => '`t`.`id` DESC'],
            'visibled' => ['condition' => '((`t`.`hidden` <> 1) OR ISNULL(`t`.`hidden`))'],
            'hitOnTop' => D::cms('shop_enable_hit_on_top') ? ['order' => 'IF(`t`.`sale`, 0, IF(`t`.`hit`, 1, IF(`t`.`new`, 2, 3)))'] : [],
            'onShopIndex' => ['condition' => 'on_shop_index=1'],
            'orderById' => ['order' => '`t`.`id`'],
        ]);
    }

    /**
     * ВАЖНО! использовать "OR" при $criteria->mergeWith($this->getRelatedCriteria(), 'OR');
     *
     * Получить объект критерия выборки для связанных товаров.
     * Через Scope реализовать нет возможности, т.к. критерий должен быть объединен
     * к выражению выборки товаров, как OR. Следовательно, в зависимости от конекста.
     * @param integer|array|NULL $categoryId id категории, или массив идентификаторов.
     * По умолчанию NULL ($this->category_id)
     * @param string $tableAlias алиас основной таблицы товаров в выборке.
     * По умолчанию "`t`".
     * @return \CDbCriteria
     */
    public function getRelatedCriteria($categoryId = null, $tableAlias = '`t`')
    {
        $criteria = new CDbCriteria;
        $criteria->addCondition("{$tableAlias}.`id`=`related_category`.`product_id`");
        $criteria->join .= ' LEFT JOIN `related_category` ON (`related_category`.`category_id`';
        if (is_array($categoryId)) {
            $criteria->join .= ' IN (' . implode(',', array_map(function ($id) {return (int) $id;}, $categoryId)) . ')';
        } else {
            $criteria->join .= '=:_rcCategoryId';
            $criteria->params[':_rcCategoryId'] = ($categoryId !== null) ? (int) $categoryId : $this->category_id;
        }
        $criteria->join .= ')';
        $criteria->group = $tableAlias . '.`id`';

        return $criteria;
    }

    /**
     * Scope: товары текущей категории, будут выведены в начале списка.
     * ВАЖНО! Сортировка будет добавлена в конец текущего выражения сортировки,
     * в отличии от \CDbCriteria::mergeWith().
     */
    public function categoryOnTop($categoryId = null, $tableAlias = '`t`')
    {
        if ($categoryId === null) {
            $categoryId = $this->category_id;
        } elseif (empty($categoryId)) {
            return $this;
        }

        $this->getDbCriteria()->order .= ($this->getDbCriteria()->order ? ',' : '') . "IF({$tableAlias}.`category_id`=" . (int) $categoryId . ',0,1)';
        return $this;
    }

    /**
     * Scope: выбор id товаров по ЧПУ бренда
     * @param string $alias ЧПУ бренда
     * @return $this
     */
    public function byBrandAlias($alias)
    {
        $cacheId = md5('brand_' . $alias . '_productIDs');
        $productIDs = \Yii::app()->cache->get($cacheId);
        if (!$productIDs && !is_array($productIDs)) {
            $productIDs = HDb::queryColumn(
                'SELECT `t`.`id` FROM `product` AS `t`'
                . ' INNER JOIN `brand` as `b` ON (`t`.`brand_id`=`b`.`id`)'
                . ' WHERE `b`.`alias`=:alias',
                [':alias' => $alias]
            );
            \Yii::app()->cache->set($cacheId, $productIDs);
        }

        $criteria = new CDbCriteria;
        if (!empty($productIDs)) {
            $criteria->addInCondition('`t`.`id`', $productIDs);
        } else {
            $criteria->AddCondition('`t`.`id`<>`t`.`id`');
        }
        $this->getDbCriteria()->mergeWith($criteria);

        return $this;
    }

    /**
     * Scope: eav
     */
    public function eav($multi = false)
    {
        $criteria = new \CDbCriteria;

        //$criteria->with=['productAttributes'];
        //$criteria->together=true;

        $price_from = (float) Yii::app()->getRequest()->getQuery('price_from');
        $price_to = (float) Yii::app()->getRequest()->getQuery('price_to');
        $data_json = Yii::app()->getRequest()->getQuery('data');
        if (isset($data_json)) {
            $attr_filter = json_decode($data_json);
            if (count($attr_filter) > 0) {
                $values = [];
                $keys = [];
                foreach ($attr_filter as $key => $attr) {
                    if ($attr->value == "none" || $attr->name == "_method") {
                        continue;
                    }

                    $values[] = (int) $attr->value;
                    $valueKey = (int) preg_replace('/^[^0-9]+$/', '', $attr->name);
                    $keys[$valueKey] = $valueKey;
                }
                if (!empty($values)) {
                    $eavValues = HDb::queryAll('SELECT id_attrs, value FROM eav_value WHERE id IN (' . implode(',', $values) . ')');
                    if (!empty($eavValues)) {
                        $query = 'SELECT id_product FROM eav_value WHERE ';
                        $subqueries = [];
                        foreach ($eavValues as $eavItem) {
                            $subqueries[] = "(id_attrs={$eavItem['id_attrs']} AND value LIKE '{$eavItem['value']}')";
                        }
                        $query .= implode(' OR ', $subqueries);
                        $query .= ' GROUP BY id_product HAVING count(id_product)=' . count($multi ? $keys : $eavValues);
                        $ids = HDb::queryColumn($query);
                        if (empty($ids)) {
                            $criteria->addCondition('1<>1');
                        } else {
                            $criteria->addInCondition('`t`.`id`', $ids);
                        }

                    }
                }
            }
        }

        //Фильтрация цены
        if ($price_from || $price_to) {
            $criteria->mergeWith(HDb::addRangeCondition('price', $price_from, $price_to));
        }

        $this->getDbCriteria()->mergeWith($criteria);

        return $this;
    }

    /**
     * Scope: by brand id.
     * @param int $id brand id.
     */
    public function byBrand($id)
    {
        $this->getDbCriteria()->mergeWith(['condition' => 'brand_id=:brandId', 'params' => [':brandId' => $id]]);
        return $this;
    }

    /**
     * Scope: by category id.
     * @param int $id идентификатор категории.
     * @param int|boolean $depthDescendants уровень вложенности.
     * Можно передать значение true для получения выборки товаров из всех подкатегорий.
     * По умолчанию (false) только принадлежащие переданной категории.
     * @param boolean $withRelated добавить критерий выборки привязанных товаров.
     */
    public function byCategory($id, $depthDescendants = false, $withRelated = false)
    {
        $criteria = new CDbCriteria;

        $ids = [];
        if ($depthDescendants) {
            $category = Category::model()->findByPk($id);
            if ($category) {
                if ($depthDescendants === true) {
                    $descendants = $category->descendants();
                } else {
                    $descendants = $category->descendants($depthDescendants);
                }

                if ($descendants = $descendants->findAll(['select' => 'id,lft,rgt,root,level', 'index' => 'id'])) {
                    $ids = array_keys($descendants);
                }
            } else {
                $criteria->AddCondition('`t`.`id`<>`t`.`id`');
            }
        }
        $ids[] = $id;
        $criteria->addInCondition('`t`.`category_id`', $ids);

        if ($withRelated) {
            $criteria->mergeWith($this->getRelatedCriteria($ids), 'OR');
        }

        $this->getDbCriteria()->mergeWith($criteria);

        return $this;
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return $this->getAttributeLabels(array(
            'id' => 'ID',
            'category_id' => 'Категория',
            'title' => 'Название',
            'code' => 'Артикул',
            'price' => 'Цена',
            'old_price' => 'Старая цена',
            'property' => 'Свойство',
            'notexist' => 'Нет в наличии',
            'sale' => 'Акция',
            'new' => 'Новинка',
            'hit' => 'Хит',
            'link_title' => '(TITLE) для главной ссылки',
            'in_carousel' => 'Отображать на главной странице',
            'hidden' => 'Скрыть на сайте',
            'on_shop_index' => 'Отображать на главной странице каталога',
            'brand_id' => 'Бренд',
            'moreImg' => 'Дополнительные фото',
            'description' => 'Описание',
        ));
    }

    /**
     * Handler: фильтр по умолчанию.
     * @param \Product $model
     * @param string $name имя параметра.
     * @param string $value значение параметра.
     * @param string $columnOperator оператор объединения столбцов.
     * @return \CDbCriteria
     */
    public function filterDefaultHandler($model, $name, $value, $columnOperator)
    {
        $criteria = new \CDbCriteria();

        if (!is_array($value)) {
            $value = [$value];
        }

        switch ($name) {
            case 'marker':
                $criteria->addColumnCondition(array_fill_keys(array_intersect($value, ['sale', 'new', 'hit']), 1), $columnOperator);
                break;
        }

        return $criteria;
    }

    /**
     * Поиск товаров
     * @param integer|boolean $pageSize кол-во записей на странице.
     * Может быть передано true в этом случае значение будет установлено в 9999999.
     * @return CActiveDataProvider
     */
    public function search($pageSize = false, $returnCriteria = false)
    {
        $criteria = new CDbCriteria;
        $criteria->mergeWith($this->getDbCriteria());

        $title = R::r()->getQuery('f_title');
        $categoryId = (int) R::r()->getQuery('id');
        $brandId = (int) R::r()->getQuery('brand_id');

        if (!$pageSize) {
            $pageSize = R::r()->getQuery('size', D::cms('shop_product_page_size', 12));
        } elseif ($pageSize === true) {
            $pageSize = 9999999;
        }

        if ($title) {
            $criteria->addSearchCondition('title', $title, true, 'AND');
        }

        if ($categoryId) {
            $criteria->scopes = A::toa($criteria->scopes);
            $criteria->scopes['byCategory'] = [$categoryId, (int) D::cms('shop_category_descendants_level'), true];
        }

        // обязательно после byCategory, иначе выборка будет не верной.
        if ($brandId) {
            $criteria->AddColumnCondition(['brand_id' => $brandId]);
        }

        // получение сортировки по умолчанию
        /*        $orderCriteria=['scopes'=>['hitOnTop']];
        if($categoryId) {
        $orderCriteria['scopes']['scopeSort']=['shop_category', $categoryId];
        $orderCriteria['scopes']['categoryOnTop']=[$categoryId];
        }
        $orderCriteria['scopes'][]='orderById';
        $orderCriteria=HDb::criteria($orderCriteria);
        $order=$orderCriteria->order;

        $criteria->mergeWith($orderCriteria);
        $criteria->order='';
         */

        $modelProduct = new Product;
        $criteria->mergeWith($modelProduct->orderById()->getDbCriteria());
        $modelProduct = new Product;
        $criteria->mergeWith($modelProduct->categoryOnTop($categoryId)->getDbCriteria());
        $modelProduct = new Product;
        $criteria->mergeWith($modelProduct->scopeSort('shop_category', $categoryId)->getDbCriteria());
        $modelProduct = new Product;
        $criteria->mergeWith($modelProduct->hitOnTop()->getDbCriteria());
        $order = $criteria->order;
        $criteria->order = '';

        if ($returnCriteria) {
            return $criteria;
        }

        return new \CActiveDataProvider($this, [
            'criteria' => $criteria,
            'pagination' => [
                'pageSize' => $pageSize,
                'pageVar' => 'p',
            ],
            'sort' => [
                'sortVar' => 's',
                'descTag' => 'd',
                'defaultOrder' => $order,
            ],
        ]);
    }

    /**
     * {@inheritDoc}
     * @see CModel::beforeValidate()
     */
    protected function beforeValidate()
    {
        $this->moreImg = CUploadedFile::getInstances($this, 'moreImg');

        parent::beforeValidate();

        $this->price = (float) str_replace(',', '.', str_replace(' ', '', $this->price));
        $this->old_price = (float) str_replace(',', '.', str_replace(' ', '', $this->old_price));

        return true;
    }

    /**
     * {@inheritDoc}
     * @see CActiveRecord::afterSave()
     */
    protected function afterSave()
    {
        parent::afterSave();

        if (count($this->moreImg)) {
            $this->createMoreImages();
        }

        Y::cacheFlush();

        return true;
    }

    /**
     * {@inheritDoc}
     * @see CActiveRecord::afterDelete()
     */
    protected function afterDelete()
    {
        parent::afterDelete();

        if (Yii::app()->params['attributes']) {
            foreach ($this->productAttributes as $model) {
                $model->delete();
            }

        }

        return true;
    }

    public function getMoreImages()
    {
        if ($this->moreImg == null) {
            $this->moreImg = CImage::model()->findAll([
                'order' => 'ordering',
                'condition' => 'model=? AND item_id=?',
                'params' => [
                    strtolower(get_class($this)),
                    $this->id,
                ],
            ]);
        }

        return $this->moreImg;
    }

    private function createMoreImages()
    {
        $params = array('max' => 100, 'master_side' => 4);

        if ($cropTop = Yii::app()->settings->get('shop_settings', 'cropTop')) {
            $params['crop'] = true;
            $params['cropt_top'] = $cropTop;
        }

        $upload = new UploadHelper;
        $upload->add($this->moreImg, $this);
        $upload->runUpload($params);
    }

    private function urlToPath($url)
    {
        $path = $_SERVER['DOCUMENT_ROOT'] . mb_strcut($url, 0, mb_strpos($url, '?'));
        if (file_exists($path)) {
            return $path;
        } else {
            return false;
        }
    }

    public function getWidth($image)
    {
        if (is_file($this->urlToPath($image))) {
            $imageObject = Yii::app()->image->load($this->urlToPath($image));
            return $imageObject->width;
        }
        return null;
    }

    public function getHeight($image)
    {
        if (is_file($this->urlToPath($image))) {
            $imageObject = Yii::app()->image->load($this->urlToPath($image));
            return $imageObject->height;
        }
        return null;
    }

    public function getCartImg()
    {
        return $this->mainImageBehavior->getTmbSrc(300, 300);
    }

    /**
     * Event: onAfterAdd
     * Обработчик события после добавления товара в корзину.
     * @param array &$item элемент позиции в массиве конфигурации корзины.
     * @param string $attribute имя атрибута.
     * @param mixed $value значение атрибута.
     */
    public function afterAddCart(&$item, $attribute, $value)
    {
        if (!empty($item['attributes']['offer'])) {

            $offers = Product::model()->findByPk($item['id'])->dataAttributeBehavior->get(true);
            foreach ($offers as $offer) {
                if ($item['attributes']['offer'] == $offer['title']) {
                    $item['hex'] = $offer['hex'];
                    $item['attr_name'] = 'Цвет';
                }
            }
        }
    }
}
