<?php
/**
 * Виджет поля формы "Редактор TinyMce" (облегченный режим).
 *
 */
namespace common\widgets\form;

class TinyMceLiteField extends TinyMceField
{
    /**
     * 
     * {@inheritDoc}
     * @see \common\widgets\form\TinyMceField::$full
     */
    public $full=false;
    
    /**
     *
     * {@inheritDoc}
     * @see \common\widgets\form\TinyMceField::$uploadImages
     */
    public $uploadImages=false;
    
    /**
     *
     * {@inheritDoc}
     * @see \common\widgets\form\TinyMceField::$uploadFiles
     */
    public $uploadFiles=false;
    
     /**
     *
     * {@inheritDoc}
     * @see \common\widgets\form\TinyMceField::$showAccordion
     */
    public $showAccordion=false;
}