<?php

/**
 * 
 *
 * @author Tomas Naibrt / javli.net
 */
class jnAjaxFormValidatorActions extends sfActions
{
    public function executeValidateJSON()
    {
        $this->forward404If(is_null($validatorClassName = $this->getRequestParameter('validatorClassName')));

        $validator = new $validatorClassName;
        $validator->setOptions($this->object_to_array(json_decode($this->getRequestParameter('options'))));

        $value = $this->getRequestParameter('value');

        $data['messages'] = array();
        try
        {
            $validator->clean($value);
            $data['isValid'] = true;
        }
        catch(sfValidatorError $validatorError)
        {
            $data['isValid'] = false;

            sfLoader::loadHelpers('I18N');
            $data['messages'][] = str_replace('%value%', $value, __($validatorError->getMessage(), array(), $this->getRequestParameter('translationCatalogue')));
        }

        $json = json_encode($data);

        if(!$this->getRequest()->isXmlHttpRequest())
        {
            var_dump($json);
            die();
        }
        else
        {
            $this->getResponse()->setHttpHeader('Content-type', 'application/json');
        }

        return $this->renderText($json);
    }

    /**
     *
     * @param object $object
     * @return array
     */
    public function object_to_array($object)
    {
        if(is_array($object) || is_object($object))
        {
            $array = array();
            foreach($object as $key => $value)
            {
                $array[$key] = $this->object_to_array($value);
            }
            return $array;
        }
        return $object;
    }
}
?>
