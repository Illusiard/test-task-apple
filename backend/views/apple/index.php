<?php

use backend\assets\AppleListAsset;
use kartik\icons\Icon;

use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 */

$this->title = 'Apple list';
AppleListAsset::register($this);

?>

<div class="site-index">
    <h1 class="display-4">Список яблок</h1>
    <br />
    <div id="app">
        <div class="row g-3 align-items-center">
            <div class="col-1">
                <label for="appleListAdd" class="col-form-label">Count</label>
            </div>
            <div class="col-1">
                <input v-model="count" id="appleListAdd" class="form-control" aria-labelledby="countHelpInline">
            </div>
            <div class="col-auto">
                <button v-on:click="create()" class="btn btn-success"><?= Icon::show('plus', ['class'=>'fa-sm']) ?></button>
            </div>
            <div class="col-auto">
                <span id="countHelpInline" class="form-text">
                  Добавить яблок
                </span>
            </div>
        </div>
        <div class="row g-3 align-items-center">
            <div class="col-1">
                <label for="appleBite" class="col-form-label">Percent</label>
            </div>
            <div class="col-1">
                <input v-model="bitePercent" id="appleBite" class="form-control" aria-labelledby="biteHelpInline">
            </div>
            <div class="col-auto">
                <span id="biteHelpInline" class="form-text">
                  Выберете процент, который хотите откусить, и нажмите на укус в списке яблок
                </span>
            </div>
        </div>
        <div v-for="apple in apples" v-if="apple.status !== 3" class="apple row" :style="{background:apple.color}">
            <div class="col-2">{{ apple.created_at }}</div>
            <div class="col-1">{{ apple.owner }}</div>
            <div v-if="apple.status === 0" class="col-2">
                Висит
            </div>
            <div v-if="apple.status === 1" class="col-2">
                Лежит на земле
            </div>
            <div v-if="apple.status === 2" class="col-2">
                Испортилось
            </div>
            <div class="col-2">{{ apple.dropped_at }}</div>
            <div class="col-4">{{ apple.percent }}%</div>
            <div v-if="apple.status === 0" class="col-1">
                <button v-on:click="drop(apple.id)" class="btn btn-warning"><?=  Icon::show('bullseye', ['class'=>'fa-xs']) ?></button>
            </div>
            <div v-if="apple.status === 1" class="col-1">
                <button v-on:click="bite(apple.id)" class="btn btn-success"><?=  Icon::show('minus', ['class'=>'fa-xm']) ?></button>
            </div>
            <div v-if="apple.status === 2" class="col-1">
                <button v-on:click="hide(apple.id)" class="btn btn-danger"><?=  Icon::show('trash', ['class'=>'fa-xm']) ?></button>
            </div>
        </div>
    </div>
</div>
<style>
    .row {
        margin:3px;
    }
    .apple.row:hover {
        margin: 2px;
        border: 1px solid black ;
    }
</style>
