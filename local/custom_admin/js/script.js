
function inArray(needle, haystack) {
    var length = haystack.length;
    for(var i = 0; i < length; i++) {
        if(haystack[i] == needle) return true;
    }
    return false;
}

$(document).ready(function(){
  if(location.pathname == "/bitrix/admin/iblock_element_admin.php")
  {
    var parent = $('.adm-filter-main-table').parent();
    var url = "/local/admin/adminCustomFormIblockElement.php"
    var datapage = location.search.replace( '?', '' );

    $.ajax({
      url: url,
      data: datapage,
      success: function(data){
        parent.append(data);
      }
    });


      $(document).on('change', '#customIblockElementForm select', function(){
          $.ajax({
            url: url ,
            data: datapage+'&'+$(this).parents('form').serialize() ,
            success: function(data){
              $('#customIblockElementForm').remove();
              parent.append(data);
            }
          });
      });
      $(document).on('click', '#customIblockElementForm #showhideform', function(){
        $('#customIblockElementForm').toggleClass('show');

        if($('#customIblockElementForm').hasClass('show')) {
          $('#CIEF_SHOW').prop('checked', true);
        } else {
          $('#CIEF_HIDE').prop('checked', true);
        }
        $.ajax({
            url: url ,
            data: datapage+'&'+$('#customIblockElementForm form').serialize() ,
            success: function(data){
              $('#customIblockElementForm').remove();
              parent.append(data);
            }
          });
      })

      $(document).on('click', '#customIblockElementForm #submitCIE', function(){
        var id_prop = $('#CIEF_PROP').val();
        var value = $('#CIEF_ENUMPROP').val();
        var typework = $('[name="CIEF_TYPEWORK"]:checked').val();
        var select_prop_from_table = $('select[name *= PROPERTY_' + id_prop + ']');

        if(id_prop  == ''){
          alert('Выбериете свойство');
          return false;
        }
        if(value  == null){
          alert('Установите параметр свойства');
          return false;
        }
        if(select_prop_from_table.length == 0){
          alert('Для того чтобы воспользоваться данной формой, установите хотя бы 1 элемент в режим редактирования и убедитесь что отбираемое свойство присутствует в таблице');
          return false;
        }
        if(typework  == null){
          alert('Установите тип записи');
          return false;
        }


        $('option', select_prop_from_table).each(function(){
          switch(typework) {
            case 'update':
             if(inArray($(this).attr('value'), value)){
                $(this).prop('selected', true);
              }
            break;
            case 'add':
              $(this).prop('selected', false);
             if(inArray($(this).attr('value'), value)){
                $(this).prop('selected', true);
              }
            break;
            case 'remove':
              $(this).prop('selected', false);
            break;
          }
        })


        return false;
      });


  }
})
