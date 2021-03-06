<?php

namespace kato\modules\tag\widgets;

use yii\helpers\Html;
use kato\modules\tag\models\Tag;

/**
 * Class Tags
 * Usage:
 * \kato\widgets\Tags::widget([
 *  'model' => $model,
 *  ]);
 *
 * @package kato\widgets
 */
class Tags extends \yii\bootstrap\Widget
{
    public $model;
    public $options = [];
    public $containerOptions = ['class' => 'tags-list'];

    /**
     * @return string
     */
    public function run()
    {
        if ($this->getAllTags()->count() < 1) {
            return false;
        }

        return Html::tag('ul', $this->getTags(), $this->containerOptions);
    }

    /**
     * Look into Tag model by tag type
     * @return mixed
     */
    private function getAllTags()
    {
        return Tag::find()
            ->where(['tag_type' => $this->model->className()])
            ->limit(20);
    }

    /**
     * Returns list of tags from model
     * @return string
     */
    private function getTags()
    {
        $allTags = $this->getAllTags()->all();
        $tags = '';

        $total=0;
        foreach ($allTags as $tag) {
            $total += $tag->frequency;
        }

        foreach($allTags as $tag) {
            $weight = 8 + (int)(16*$tag->frequency/($total+10));
            $this->options['style'] = "font-size:{$weight}pt";

            if (isset($_GET[$this->get_real_class($this->model)]['tags']) && $_GET[$this->get_real_class($this->model)]['tags'] === $tag->name) {
                $this->options['class'] = 'tag active';
            } else {
                $this->options['class'] = 'tag';
            }
            $tags .= Html::tag('li', Html::a($tag->name, ['/blog/index', $this->get_real_class($this->model) . '[tags]' => $tag->name]), $this->options);
        }

        $tags .= Html::tag('li', '', ['class' => 'clearfix']);

        return $tags;
    }

    /**
     * Obtains an object class name without namespaces
     * @param $obj
     * @return string
     */
    private function get_real_class($obj) {
        $classname = get_class($obj);

        if (preg_match('@\\\\([\w]+)$@', $classname, $matches)) {
            $classname = $matches[1];
        }

        return $classname;
    }

}