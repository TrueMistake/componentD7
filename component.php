<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
\Bitrix\Main\Loader::IncludeModule("iblock");

$arFilter = Array("=IBLOCK_ID" => $arParams['IBLOCK_ID'], "=ACTIVE" => "Y");
$newsList = \Bitrix\Iblock\ElementTable::getList(
    array(
        'select' => ['*', 'DETAIL_PAGE_URL' => 'IBLOCK.DETAIL_PAGE_URL','SECTION','PHOTO','SECTION_NAME' => 'IBLOCK_ELEMENT_SECTION_NAME'],
        "filter" => $arFilter,
        'limit' => 5,
        "order" => [
            "RAND" => "asc"
        ],
        'runtime' => array(
            'SECTION' => array(
                'data_type' => '\Bitrix\Iblock\SectionTable',
                'reference' => array(
                    '=this.IBLOCK_SECTION_ID' => 'ref.ID'
                ),
                'join_type' => 'inner'
            ),
            'PHOTO' => array(
                'data_type' => '\Bitrix\Main\FileTable',
                'reference' => array(
                    '=this.DETAIL_PICTURE' => 'ref.ID'
                ),
                'join_type' => 'inner'
            ),
            new \Bitrix\Main\Entity\ExpressionField('RAND', 'RAND()')
        ),
        "cache"=>array("ttl"=>300, "cache_joins"=>true)
    )
);
while ($ob = $newsList->fetch()) {
    $arResult[] = [
        'viewImg' => (is_array($arParams['IBLOCK_ID']))?false:true,
        'id' => $ob['id'],
        'name' => $ob['NAME'],
        'section' => $ob['SECTION_NAME'],
        'date' => date('d.m', strtotime($ob['DATE_CREATE'])),
        'picture' => "/upload/{$ob['IBLOCK_ELEMENT_PHOTO_SUBDIR']}/{$ob['IBLOCK_ELEMENT_PHOTO_FILE_NAME']}",
        'url' => CIBlock::ReplaceDetailUrl($ob['DETAIL_PAGE_URL'], $ob, false, 'E')
    ];
}

$this->IncludeComponentTemplate();
?>