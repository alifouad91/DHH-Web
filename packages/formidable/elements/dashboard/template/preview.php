<?php  

  defined('C5_EXECUTE') or die(_("Access Denied."));    
  $curl = Loader::helper('concrete/urls');    
?>

<iframe src="<?php  echo $curl->getToolsURL('dashboard/templates/preview/?templateID='.$template->templateID, 'formidable') ?>" width="100%" height="500px;" frameborder="0" marginheight="0" marginwidth="0"></iframe>