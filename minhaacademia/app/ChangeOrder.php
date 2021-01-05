<?php

namespace App;

class ChangeOrder
{
    protected $models, $model, $oldOrder, $newOrder;

    /**
     * Constructor for ChangeOrder class.
     *
     * @param array(Model) $models
     * @param Model $model
     * @param integer $newOrder
     */
    public function __construct($models, $model, $newOrder){
        $this->models = $models;
        $this->model = $model;
        $this->oldOrder = $model->order;
        $this->newOrder = $newOrder;
    }

    /**
     * Save the models with a new order.
     *
     * @return void
     */
    public function save() {
        $maxOrder = $this->models->count() - 1;

        if ($maxOrder > 0 && $this->oldOrder != $this->newOrder) {
            $this->model->order = $this->newOrder;
            $this->model->save();
            $models = $this->models;
            if ($this->newOrder > $this->oldOrder) {
                for($i = $this->oldOrder + 1; $i <= $this->newOrder; $i++) {
                    $model = $models->get($i);
                    $model->order--;
                    $model->save();
                }
            }else{
                for($i = $this->newOrder; $i <= $this->oldOrder - 1; $i++) {
                    $model = $models->get($i);
                    $model->order++;
                    $model->save();
                }
            }
        }
    }
}