<?php

/**
 *
 * @param sfForm $form
 * @param string $filedName
 * @return string
 */
function ajaxFieldValidator(sfForm $form, $filedName)
{
    $validator = $form->getValidator($filedName);
    
    $json = json_encode(array('validatorClassName' => get_class($validator),
                              'options' => json_encode($validator->getOptions()),
                              'value' => '%value%',
                              'translationCatalogue' => $form->getWidgetSchema()->getFormFormatter()->getTranslationCatalogue()));

    $id = $form->getName().'_'.$filedName;

    $json = str_replace('"%value%"', '$("#'.$id.'").val()', $json);

    $result = '<script type="text/javascript">/* <![CDATA[ */'."\n";
    $result .= '$(document).ready(function () {'."\n";
    $result .= '$("#'.$id.'").change(function(){'."\n";
    $result .= '    $.each($(".error_list"),function(){'."\n";
    $result .= '        if($(this).next().attr("id") == "'.$id.'"){'."\n";
    $result .= '            $(this).remove();'."\n";
    $result .= '        }'."\n";
    $result .= '    });'."\n";
    $result .= '    $.getJSON("'.url_for("@jnAjaxFormValidator_validateJSON", true).'", '.$json.', function(data){'."\n";
    $result .= '        if(!data.isValid){'."\n";
    $result .= '            var mes = "";'."\n";
    $result .= '            for(i = 0; i < data.messages.length; i++){'."\n";
    $result .= '               mes += "<li>"+data.messages[i]+"</li>";'."\n";
    $result .= '            }'."\n";
    $result .= '            '."\n";
    $result .= '            $("#'.$id.'").before("<ul class=\"error_list\">"+mes+"</ul>");'."\n";
    $result .= '        }'."\n";
    $result .= '    });'."\n";
    $result .= '});'."\n";
    $result .= '});'."\n";
    $result .= '/* ]]> */</script>'."\n";
    return $result;
}

/**
 *
 * @param sfForm $form
 * @return string
 */
function ajaxAllFieldsValidators(sfForm $form)
{
    $result = "";
    foreach($form as $fieldName => $field)
    {
        $result .= ajaxFieldValidator($form, $fieldName);
    }
    return $result;
}