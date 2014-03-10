<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();


    if(!CModule::IncludeModule("iblock"))
    	return;
    
    if (WIZARD_INSTALL_DEMO_DATA)
    { 
      $iblockXMLFile = WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID."/news.xml"; 
    }
    else
    {
        $iblockXMLFile = WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID."/news_nodata.xml"; 
    }
     
    $iblockCode = "furniture_news_".WIZARD_SITE_ID; 
    $iblockType = "news"; 
    
    $rsIBlock = CIBlock::GetList(array(), array("CODE" => $iblockCode, "TYPE" => $iblockType));
    $iblockID = false; 
    if ($arIBlock = $rsIBlock->Fetch())
    {
    	$iblockID = $arIBlock["ID"]; 
    	if (WIZARD_REINSTALL_DATA)
    	{
    		CIBlock::Delete($arIBlock["ID"]); 
    		$iblockID = false; 
    	}
    }
    
   
    if($iblockID == false)
    {
    	$iblockID = WizardServices::ImportIBlockFromXML(
    		$iblockXMLFile,
    		"furniture_news",
    		$iblockType,
    		WIZARD_SITE_ID,
    		$permissions = Array(
    			"1" => "X",
    			"2" => "R"
    		)
    	);
    
    	if ($iblockID < 1)
    		return;
    	
    	//WizardServices::SetIBlockFormSettings($iblockID, Array ( 'tabs' => GetMessage("W_IB_GROUP_PHOTOG_TAB1").$REAL_PICTURE_PROPERTY_ID.GetMessage("W_IB_GROUP_PHOTOG_TAB2").$rating_PROPERTY_ID.GetMessage("W_IB_GROUP_PHOTOG_TAB3").$vote_count_PROPERTY_ID.GetMessage("W_IB_GROUP_PHOTOG_TAB4").$vote_sum_PROPERTY_ID.GetMessage("W_IB_GROUP_PHOTOG_TAB5").$APPROVE_ELEMENT_PROPERTY_ID.GetMessage("W_IB_GROUP_PHOTOG_TAB6").$PUBLIC_ELEMENT_PROPERTY_ID.GetMessage("W_IB_GROUP_PHOTOG_TAB7"), ));
    	
    	//IBlock fields
    	$iblock = new CIBlock;
    	$arFields = Array(
    		"ACTIVE" => "Y",
    		"FIELDS" => array ( 'IBLOCK_SECTION' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'ACTIVE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'Y', ), 'ACTIVE_FROM' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '=today', ), 'ACTIVE_TO' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'SORT' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'NAME' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => '', ), 'PREVIEW_PICTURE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'FROM_DETAIL' => 'N', 'SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', ), ), 'PREVIEW_TEXT_TYPE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'text', ), 'PREVIEW_TEXT' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'DETAIL_PICTURE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', ), ), 'DETAIL_TEXT_TYPE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'text', ), 'DETAIL_TEXT' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'XML_ID' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'CODE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'TAGS' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), ), 
    		"CODE" => $iblockCode, 
    		"XML_ID" => $iblockCode,
    		"NAME" => $iblock->GetArrayByID($iblockID, "NAME"),
    		//"NAME" => "[".WIZARD_SITE_ID."] ".$iblock->GetArrayByID($iblockID, "NAME")
    	);
    	
    	$iblock->Update($iblockID, $arFields);
    }
    else
    {
    	$arSites = array(); 
    	$db_res = CIBlock::GetSite($iblockID);
    	while ($res = $db_res->Fetch())
    		$arSites[] = $res["LID"]; 
    	if (!in_array(WIZARD_SITE_ID, $arSites))
    	{
    		$arSites[] = WIZARD_SITE_ID;
    		$iblock = new CIBlock;
    		$iblock->Update($iblockID, array("LID" => $arSites));
    	}
    }
    
    CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/news.php", array("NEWS_IBLOCK_ID" => $iblockID));
    CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/news/index.php", array("NEWS_IBLOCK_ID" => $iblockID));
    CWizardUtil::ReplaceMacros($_SERVER["DOCUMENT_ROOT"]."/bitrix/templates/testtemplate_/footer.php", Array("NEWS_IBLOCK_ID" => $iblockID));
    CWizardUtil::ReplaceMacros($_SERVER["DOCUMENT_ROOT"]."/bitrix/templates/testtemplate/footer.php", Array("NEWS_IBLOCK_ID" => $iblockID));

    if(CModule::IncludeModule("main"))
        CUserOptions::SetOption('form', 'form_element_'.$iblockID, array('tabs' => GetMessage("FORM_CUSTOM_AJUST")));
?>
