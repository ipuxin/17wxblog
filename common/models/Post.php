<?php

namespace common\models;

use Yii;
use yii\helpers\Html;

/**
 * This is the model class for table "post".
 *
 * @property integer $id
 * @property string $title
 * @property string $content
 * @property string $tags
 * @property integer $status
 * @property integer $create_time
 * @property integer $update_time
 * @property integer $author_id
 *
 * @property Comment[] $comments
 * @property Adminuser $author
 * @property Poststatus $status0
 */
class Post extends \yii\db\ActiveRecord
{
    private $_oldTags;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'post';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'content', 'status', 'author_id'], 'required'],
            [['content', 'tags'], 'string'],
            [['status', 'create_time', 'update_time', 'author_id'], 'integer'],
            [['title'], 'string', 'max' => 128],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => Adminuser::className(), 'targetAttribute' => ['author_id' => 'id']],
            [['status'], 'exist', 'skipOnError' => true, 'targetClass' => Poststatus::className(), 'targetAttribute' => ['status' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '标题',
            'content' => '内容',
            'tags' => '标签',
            'status' => '状态',
            'create_time' => '创建时间',
            'update_time' => '修改时间',
            'author_id' => '作者',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comment::className(), ['post_id' => 'id']);
    }

    public function getActiveComments()
    {
    	return $this->hasMany(Comment::className(), ['post_id' => 'id'])
    	->where('status=:status',[':status'=>2])->orderBy('id DESC');
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(Adminuser::className(), ['id' => 'author_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatus0()
    {
        return $this->hasOne(Poststatus::className(), ['id' => 'status']);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                //如果是新增就更新两条数据
                $this->create_time = time();
                $this->update_time = time();
            } else {
                $this->update_time = time();
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * 文章读出来以后,会执行此方法
     * 获取修改前的标签
     */
    public function afterFind()
    {
        parent::afterFind();
        $this->_oldTags = $this->tags;
    }

    /**
     * 在文章修保存前,调用修改标签的方法
     * 新增时:老标签为空.
     * 修改时:老标签存储的是老标签.
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        Tag::updateFrequency($this->_oldTags, $this->tags);
    }

    /**
     * 在文章删除后,调用修改标签的方法:
     * 新标签置空,则不更新标签
     */
    public function afterDelete()
    {
        parent::afterDelete();
        Tag::updateFrequency($this->tags, '');
    }

    public function getUrl()
    {
        return Yii::$app->urlManager->createUrl(
            ['post/detail', 'id' => $this->id, 'title' => $this->title]);
    }

    public function getBeginning($length = 288)
    {
        $tmpStr = strip_tags($this->content);
        $tmpLen = mb_strlen($tmpStr);

        $tmpStr = mb_substr($tmpStr, 0, $length, 'utf-8');
        return $tmpStr . ($tmpLen > $length ? '...' : '');
    }

    public function getTagLinks()
    {
        $links = array();
        foreach (Tag::string2array($this->tags) as $tag) {
            $links[] = Html::a(Html::encode($tag), array('post/index', 'PostSearch[tags]' => $tag));
        }
        return $links;
    }

    public function getCommentCount()
    {
        return Comment::find()->where(['post_id' => $this->id, 'status' => 2])->count();
    }
}
