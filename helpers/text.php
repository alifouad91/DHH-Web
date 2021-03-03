<?php
defined('C5_EXECUTE') or die('Access Denied.');
class TextHelper extends Concrete5_Helper_Text {
    public function getArrayFromHtml($html)
    {
        $html = str_replace('\r','',strip_tags($html));
        $html = preg_replace('/\r\n?/', "\n", $html);
        $html = explode(PHP_EOL,strip_tags($html));
        $html = array_filter($html);
        return $html;
    }
}
