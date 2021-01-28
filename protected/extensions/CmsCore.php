<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Alexok
 * Date: 02.11.11
 * Time: 15:32
 * To change this template use File | Settings | File Templates.
 */ 
class CmsCore
{
    public static function prepareTreeMenu($items)
    {
        $result = array();
        $level  = 1;

        foreach($items as $item) {
            if ($item->level > $level)
                continue;

            $menu_item = array(
                'label'=>$item->title,
                'url'=>array('shop/category', 'id'=>$item->id)
            );
            if (!$item->isLeaf()) {
                $menu_item['items'] = self::prepareTreeSubMenu($items, $item, $item->level+1);
            }
            $result[] = $menu_item;
        }

        return $result;
    }

    public static function prepareTreeSubMenu($items, $parent, $level)
    {
        $result = array();

        foreach($items as $item) {
            if ($item->level == $level && $item->isDescendantOf($parent)) {
                $menu_item = array(
                    'label'=>$item->title,
                    'url'=>array('shop/category', 'id'=>$item->id)
                );
                if (!$item->isLeaf()) {
                    $menu_item['items'] = self::prepareTreeSubMenu($items, $item, $item->level+1);
                }
                $result[] = $menu_item;
            }
        }

        return $result;
    }

    public static function prepareTreeSelect($items)
    {
        $result = array();

        foreach($items as $item) {
            if ($item->level >1) {
                $prefix = str_repeat('-', $item->level-1);
                $item->title = $prefix.' '.$item->title;
            }

            $result[] = $item;
        }

        return $result;
    }
}
