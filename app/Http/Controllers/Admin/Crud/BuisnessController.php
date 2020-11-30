<?php

namespace App\Http\Controllers\Admin\Crud;

use App\Models\Buisness;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Sanjab\Controllers\CrudController;
use Sanjab\Helpers\CrudProperties;
use Sanjab\Helpers\MaterialIcons;
use Sanjab\Widgets\CheckboxWidget;
use Sanjab\Widgets\IdWidget;
use Sanjab\Widgets\Relation\BelongsToPickerWidget;
use Sanjab\Widgets\TextAreaWidget;
use Sanjab\Widgets\TextWidget;

class BuisnessController extends CrudController
{
    protected static function properties(): CrudProperties
    {
        return CrudProperties::create('buisnesses')
                ->model(\App\Models\Buisness::class)
                ->title(__('Buisness'))
                ->titles(__('Buisnesses'))
                ->icon(MaterialIcons::BUSINESS)
                ->creatable(false)
                ->badge(function () {
                    return Buisness::where('approved', 0)->count();
                });
    }

    protected function init(string $type, Model $item = null): void
    {
        $this->widgets[] = IdWidget::create();

        $this->widgets[] = CheckboxWidget::create('approved', __('Approved'));

        $this->widgets[] = BelongsToPickerWidget::create('user', __('User'))
                            ->format('%id. %first_name %last_name')
                            ->required();

        $this->widgets[] = TextWidget::create('name', __('Name'))
                            ->required();

        $this->widgets[] = TextAreaWidget::create('description', __('Description'))
                            ->nullable();
    }

    protected function queryScope(Builder $query)
    {
        $query->withoutGlobalScope('approved');
    }
}
