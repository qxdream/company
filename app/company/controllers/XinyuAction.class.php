<?php
$action = load_company_routing();
eval('class XinyuAction extends Company' . $action . ' {}');
?>