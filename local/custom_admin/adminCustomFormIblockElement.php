<?php

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule('iblock');

$IBLOCK_ID = $_GET['IBLOCK_ID'];

function cookieCreate($key, $val) {
  if( !empty($val) ) {
    setcookie($key, serialize($val));
  }
}
function isSelected ($a, $get) {
  if(is_array($get) && in_array($a, $get)) {
     return 'selected checked';
  } elseif($a == $get) {
    return 'selected checked';
  }

  return false;
}

$arInputProps = array("CIEF_PROP", "CIEF_TYPEWORK", "CIEF_SHOWHIDE" , "CIEF_ENUMPROP");

foreach ($arInputProps as  $val) {
    /*Записываем куку*/
    cookieCreate($val, $_GET[$val]);
}

foreach ($arInputProps as $val) {
  /*Считываем куку*/
  if(!empty($_COOKIE[$val]) && empty($_GET[$val]))
    $_GET[$val] = unserialize($_COOKIE[$val]);
}

/*Добываем возможные свойства инфоблока*/
$properties = CIBlockProperty::GetList(Array("name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$IBLOCK_ID, "PROPERTY_TYPE"=>"L"));
while ($prop_fields = $properties->GetNext())
{
  $arProp[$prop_fields['ID']] = $prop_fields;
}

if(empty($arProp))
  exit;
/*Добываем параметры определенного свойства*/
$getPropCode = $arProp[$_GET['CIEF_PROP']]['CODE'];
if(!empty($getPropCode)) {
    $property_enums = CIBlockPropertyEnum::GetList(Array("id"=>"asc"), Array("IBLOCK_ID"=>$IBLOCK_ID, "CODE"=>$getPropCode));
    while($enum_fields = $property_enums->GetNext())
    {
      $arEnumProp[] = $enum_fields;
    }

}

?>
<div id="customIblockElementForm" class='<?=$_GET['CIEF_SHOWHIDE']?>'>
<h2>Массовое управление свойствами <span id="showhideform"></span></h2>
  <form metod="get">
     <div style="display: none">
       <input
          id="CIEF_SHOW"
          name="CIEF_SHOWHIDE"
          type="radio"
          value="show"
          <?=isSelected("show", $_GET['CIEF_SHOWHIDE'])?>
        />
        <input
          id="CIEF_HIDE"
          type="radio"
          name="CIEF_SHOWHIDE"
          value="hide"
          <?=isSelected("hide", $_GET['CIEF_SHOWHIDE'])?>
        />
     </div>
     <select id="CIEF_PROP" name="CIEF_PROP">
      <option value="">Выберите свойство</option>
      <?foreach($arProp as $prop) {?>
        <option <?=isSelected($prop['ID'], $_GET['CIEF_PROP'])?> value="<?=$prop['ID']?>">
          <?=$prop['NAME']?>  [<?=$prop['CODE']?>]
        </option>
      <?}?>
    </select>

    <br><br>

    <?if(!empty($arEnumProp)) {?>
    <select id="CIEF_ENUMPROP" name="CIEF_ENUMPROP[]" size=5 multiple>
      <option value="">(Пусто)</option>
      <?foreach($arEnumProp as $enum) {?>
        <option value="<?=$enum['ID']?>" <?=isSelected($enum['ID'], $_GET['CIEF_ENUMPROP'])?>>
          <?=$enum['VALUE']?>
        </option>
       <?}?>
    </select>
    <br><br>
    <?}?>

    <label>
      <input name="CIEF_TYPEWORK" type="radio" value="update" <?=isSelected('update', $_GET['CIEF_TYPEWORK'])?>>
      Дополнить
    </label>
     <label>
      <input name="CIEF_TYPEWORK" type="radio" value="add" <?=isSelected('add', $_GET['CIEF_TYPEWORK'])?>>
      Перезаписать
    </label>
     <label>
      <input name="CIEF_TYPEWORK" type="radio" value="remove" <?=isSelected('remove', $_GET['CIEF_TYPEWORK'])?>>
      Очистить
    </label>
    <br><br>
    <input id="submitCIE" type="submit" value="Отправить">
  </form>
  <div class="clear"></div>
  <style>
     #customIblockElementForm {
      display: block;
      padding: 0 ;
     }
     .clear {clear: both;}
     #showhideform {
       background: url(/bitrix/panel/main/images/bx-admin-sprite-small-2.png) no-repeat 2px -4221px;
       width: 20px;
       height: 20px;
       cursor: pointer;
       display: inline-block;
    }
    #customIblockElementForm form {
      display: none;
    }
    #customIblockElementForm.show form {
     display: block;
    }
  </style>
</div>

