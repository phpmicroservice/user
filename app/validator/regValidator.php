<?php

namespace app\validator;

/**
 * Description of regValidator
 *
 * @author Dongasai
 */
class regValidator extends \Phalcon\Validation\Validator
{

    /**
     * 执行验证
     * @param \Phalcon\Validation $validator
     * @param string $attribute
     * @return boolean
     */
    public function validate(\Phalcon\Validation $validator, $attribute)
    {
        $value = $validator->getValue($attribute);
        if ($value) {
            $message = $this->getOption("message");
            if (!$message) {
                $message = "The " . __CLASS__ . " is not valid";
            }
            $validator->appendMessage(
                new \Phalcon\Validation\Message($message, $attribute, "Ip")
            );
            return false;
        }
        return true;
    }

}
