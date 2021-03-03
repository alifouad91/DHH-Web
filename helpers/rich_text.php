<?php
defined('C5_EXECUTE') or die("Access Denied.");

class RichTextHelper extends Concrete5_Helper_File
{

    public function getRichTextEditor($fieldName, $value, $additionalClass = null)
    {

        Loader::element('editor_config', array('editor_mode' => 'rich_text_basic', 'editor_selector' => 'ccm-advanced-editor-' . $fieldName));
        Loader::element('editor_controls', array('mode' => 'full'));

        print Loader::helper('form')->textarea($fieldName, $value, array('class' => $additionalClass . ' ccm-advanced-editor-' . $fieldName));

    }


}