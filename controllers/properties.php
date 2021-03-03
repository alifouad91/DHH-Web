<?php
defined('C5_EXECUTE') or die("Access Denied.");

Class PropertiesController Extends Controller
{

    const ITEMS_TO_LOAD = 10;

    public function view($path = null)
    {
        if (!$path)
        {
            $this->set('task', 'listing');
        }
        else{
            $property = Property::getByPath($path);
            if (!$property)
            {
                $property = Property::getByPath($path, Property::PATH_OLD);
                $property ? $this->redirect('/properties/' . $property->getPath()) : $this->redirect('page_not_found');
            }
            $this->set('task', 'detail');
            $this->set('property', $property);
        }
    }

}
