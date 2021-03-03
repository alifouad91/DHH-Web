<?php defined('C5_EXECUTE') or die("Access Denied.");

class FeaturedPropertiesBlockController extends BlockController
{

    protected $btName        = 'Feature Property';
    protected $btDescription = '';
    protected $btTable       = 'btDCFeaturedProperties';

    protected $btInterfaceWidth  = "700";
    protected $btInterfaceHeight = "450";

    protected $btCacheBlockRecord                   = true;
    protected $btCacheBlockOutput                   = true;
    protected $btCacheBlockOutputOnPost             = true;
    protected $btCacheBlockOutputForRegisteredUsers = false;
    protected $btCacheBlockOutputLifetime           = CACHE_LIFETIME;

    public function getSearchableContent()
    {
        $content   = array();
        $content[] = $this->field_1_textbox_text;
        $content[] = $this->field_2_textbox_text;
        $content[] = $this->field_5_textbox_text;
        return implode(' - ', $content);
    }

    public function add()
    {
        $filterList = new HomePageFiltersList();
        $filters    = $filterList->get(0);
        $filters    = $filters ?: [];
        $filters    = array_merge(['0' => '--Choose One--'], $filters);
        $this->set('field_3_select_options', $filters);
    }

    public function edit()
    {
        $nFilters[] = '--Choose One--';
        $filterList = new HomePageFiltersList();
        $filters    = $filterList->get(0);
        $filters    = $filters ?: [];

        /**
         * @var HomePageFilters $filter
         */
        foreach ($filters as $filter) {
            $nFilters[$filter->getID()] = $filter->getName();
        }

        $this->set('field_3_select_options', $nFilters);
    }


    public function view()
    {
        $hpf          = $this->field_3_select_value;
        $keywords     = $this->field_2_textbox_text;
        $count        = $this->field_4_select_value;
        $propertyList = new PropertyList();

        $propertyList->populateAverageAndTotalRatings();
        if ($hpf) {
            $propertyList->filterByHomePageFilters($hpf);
        }
        if ($keywords) {
            $propertyList->filterByKeywords($keywords);
        }
        $properties = $propertyList->get($count);
        $this->set('properties', $properties);
    }


}
