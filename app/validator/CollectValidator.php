<?php

namespace logic\user\validator;

/**
 * Description of CollectValidator
 *
 * @author Dongasai
 */
class CollectValidator extends \Phalcon\Validation\Validator
{

    /**
     * 执行验证
     *
     * @param \Phalcon\Validation $validator
     * @param string $attribute
     * @return boolean
     */
    public function validate(\Phalcon\Validation $validation, $attribute)
    {

        $type = $validation->getValue('type');
        $numerical = $validation->getValue('numerical');
        $user_id = $validation->getValue('user_id');
        #先验证内容是否存在
        if ($type == 'article') {
            $model = new \logic\Article\model\article();
        } elseif ($type == 'bbs') {
            $model = new \logic\Bbs\model\forum();
        }
        $data35 = $model->findFirstById($numerical);

        if (!$data35) {
            $validation->appendMessage(
                new \Phalcon\Validation\Message("不存在的信息!", $attribute, $attribute)
            );
            return false;
        }
        # 检查信息重复
        $re = \logic\user\model\user_collect::query()
            ->where("type = '$type'")
            ->addWhere('numerical = ' . $numerical)
            ->andWhere('user_id = ' . $user_id)
            ->execute();
        if (!empty($re->toArray())) {

            $validation->appendMessage(
                new \Phalcon\Validation\Message(' 重复信息!', $attribute, $attribute)
            );
            return false;
        }

        return true;
    }

}
