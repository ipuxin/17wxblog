<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tag".
 *
 * @property integer $id
 * @property string $name
 * @property integer $frequency
 */
class Tag extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tag';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['frequency'], 'integer'],
            [['name'], 'string', 'max' => 128],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'frequency' => 'Frequency',
        ];
    }

    public static function string2array($tags)
    {
        return preg_split('/\s*,\s*/', trim($tags), -1, PREG_SPLIT_NO_EMPTY);
    }

    public static function array2string($tags)
    {
        return implode(', ', $tags);
    }

    public static function addTags($tags)
    {
        if (empty($tags)) return;

        /**
         * 循环查找:
         * 已存在:则只将频率+1.
         * 不存在:新增名称,将频率置为1.
         */
        foreach ($tags as $name) {
            $aTagCount = Tag::find()->where(['name' => $name])->count();

            if (!$aTagCount) {
                //不存在:新增,频率置为1
                $tag = new Tag;
                $tag->name = $name;
                $tag->frequency = 1;
                $tag->save();
            } else {
                //存在:找到这个对象,只将频率+1
                $aTag = Tag::find()->where(['name' => $name])->one();
                $aTag->frequency += 1;
                $aTag->save();
            }
        }
    }

    public static function removeTags($tags)
    {
        if (empty($tags)) return;

        foreach ($tags as $name) {
            $aTag = Tag::find()->where(['name' => $name])->one();
            $aTagCount = Tag::find()->where(['name' => $name])->count();

            if ($aTagCount) {
                if ($aTagCount && $aTag->frequency <= 1) {
                    $aTag->delete();
                } else {
                    //频率数大于
                    $aTag->frequency -= 1;
                    $aTag->save();
                }
            }
        }
    }

    public static function updateFrequency($oldTags, $newTags)
    {
        if (!empty($oldTags) || !empty($newTags)) {
            $oldTagsArray = self::string2array($oldTags);
            $newTagsArray = self::string2array($newTags);

            /**
             * array_diff():返回第一个数组有,第二个数组中没有的元素.
             * 新增:$oldTags为空,$newTags为全部新标签.
             *  addTags:中有全部要添加的标签.
             *  removeTags:中为空,此方法执行后将立即返回.
             * 删除:$oldTags为要删除的标签,$newTags为空.(规律同上)
             * 修改:既有新增,也有删除.
             */
            self::addTags(array_values(array_diff($newTagsArray, $oldTagsArray)));
            self::removeTags(array_values(array_diff($oldTagsArray, $newTagsArray)));
        }
    }

    public static function findTagWeights($limit = 20)
    {
        $tag_size_level = 5;

        $models = Tag::find()->orderBy('frequency desc')->limit($limit)->all();
        $total = Tag::find()->limit($limit)->count();

        //向上取整，12/5=2.4 为3，每个等级三个元素。
        $stepper = ceil($total / $tag_size_level);

        $tags = array();
        $counter = 1;

        if ($total > 0) {
            //如果标签数量大于0，循环
            foreach ($models as $model) {
                //1/3=0.3   为1，$weight=2
                //2/3=0.6   为1，$weight=2
                //3/3=1     为1，$weight=2

                //4/3---5/3---6/3
                $weight = ceil($counter / $stepper) + 1;
                $tags[$model->name] = $weight;
                $counter++;
            }
            ksort($tags);
        }
        return $tags;
    }
}
