<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ListView;
use frontend\components\TagsCloudWidget;
use frontend\components\RctReplyWidget;
use common\models\Post;
use yii\caching\DbDependency;
use yii\caching\yii\caching;

/* @var $this yii\web\View */
/* @var $searchModel common\models\PostSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
//
//$this->title = 'Posts';
//$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container">

    <div class="row">

        <div class="col-md-9">

            <ol class="breadcrumb">
                <li><a href="<?= Yii::$app->homeUrl; ?>">首页</a></li>
                <li>文章列表</li>

            </ol>

            <?= ListView::widget([
                'id' => 'postList',
                'dataProvider' => $dataProvider,
                'itemView' => '_listitem',//子视图,显示一篇文章的标题等内容.
                'layout' => '{items} {pager}',
                'pager' => [
                    'maxButtonCount' => 10,
                    'nextPageLabel' => Yii::t('app', '下一页'),
                    'prevPageLabel' => Yii::t('app', '上一页'),
                ],
            ]) ?>

        </div>


        <div class="col-md-3">
            <div class="searchbox">
                <ul class="list-group">
                    <li class="list-group-item">
                        <span class="glyphicon glyphicon-search" aria-hidden="true"></span> 查找文章（
                        <?php
                        //数据缓存示例代码

                        //                        $data = Yii::$app->cache->get('postCount');
                        //                        $dependency = new DbDependency(['sql' => 'select count(id) from post']);
                        //
                        //                        if ($data === false) {
                        //                            $data = Post::find()->count();
                        //                            sleep(5);
                        //                            Yii::$app->cache->set('postCount', $data, 600, $dependency); //设置缓存60秒后过期
                        //                        }
                        //
                        //                        echo $data;
                        ?>
                        <?php //= Post::find()->count(); ?>）
                    </li>
                    <li class="list-group-item">
                        <form class="form-inline" action="<?= Yii::$app->urlManager->createUrl(['post/index']); ?>"
                              id="w0" method="get">
                            <div class="form-group">
                                <input type="text" class="form-control" name="PostSearch[title]" id="w0input"
                                       placeholder="按标题">
                            </div>
                            <button type="submit" class="btn btn-default">搜索</button>
                        </form>

                    </li>
                </ul>
            </div>

            <div class="tagcloudbox">
                <ul class="list-group">
                    <li class="list-group-item">
                        <span class="glyphicon glyphicon-tags" aria-hidden="true"></span> 标签云
                    </li>
                    <li class="list-group-item">
                        <?php
                        //片段缓存示例代码
                        $dependency = new DbDependency(['sql' => 'select count(id) from post']);

                        if ($this->beginCache('cache', ['duration' => 600], ['dependency' => $dependency])) {
                            echo TagsCloudWidget::widget(['tags' => $tags]);
                            $this->endCache();
                        }

                        ?>
                        <?php

                        /**
                         * array (size=16)
                         * 'ActiveRecord' => float 2
                         * 'Composer' => float 4
                         * 'DAO' => float 3
                         * 'DetailView' => float 4
                         * 'Gii' => float 3
                         * 'GridView' => float 2
                         * 'ListView' => float 3
                         * 'RESTful Web服务' => float 3
                         * 'Yii' => float 2
                         * 'Yii2' => float 2
                         * 'widget' => float 5
                         * '安装' => float 4
                         * '小部件' => float 5
                         * '教程' => float 5
                         * '查询构建器' => float 4
                         * '视频教程' => float 5
                         */
                        ?>
                        <?= TagsCloudWidget::widget(['tags' => $tags]); ?>
                    </li>
                </ul>
            </div>

            <div class="commentbox">
                <ul class="list-group">
                    <li class="list-group-item">
                        <span class="glyphicon glyphicon-comment" aria-hidden="true"></span> 最新回复
                    </li>
                    <li class="list-group-item">
                        <?= RctReplyWidget::widget(['recentComments' => $recentComments]) ?>

                    </li>
                </ul>
            </div>


        </div>


    </div>

</div>
