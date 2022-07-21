<?php

declare(strict_types=1);

namespace Cortex\Auth\DataTables\Adminarea;

use Cortex\Auth\Models\Guardian;
use Cortex\Auth\Transformers\GuardianTransformer;
use Cortex\Foundation\DataTables\AbstractDataTable;

class GuardiansDataTable extends AbstractDataTable
{
    /**
     * {@inheritdoc}
     */
    protected $model = Guardian::class;

    /**
     * {@inheritdoc}
     */
    protected $transformer = GuardianTransformer::class;

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns(): array
    {
        $link = config('cortex.foundation.route.locale_prefix')
            ? '"<a href=\""+routes.route(\'adminarea.cortex.auth.guardians.edit\', {guardian: full.id, locale: \''.$this->request()->segment(1).'\'})+"\">"+data+"</a>"'
            : '"<a href=\""+routes.route(\'adminarea.cortex.auth.guardians.edit\', {guardian: full.id})+"\">"+data+"</a>"';

        return [
            'id' => ['checkboxes' => json_decode('{"selectRow": true}'), 'exportable' => false, 'printable' => false],
            'username' => ['title' => trans('cortex/auth::common.username'), 'render' => $link.'+(full.is_active ? " <i class=\"text-success fa fa-check\"></i>" : " <i class=\"text-danger fa fa-close\"></i>")', 'responsivePriority' => 0],
            'email' => ['title' => trans('cortex/auth::common.email')],
            'created_at' => ['title' => trans('cortex/auth::common.created_at'), 'render' => "moment(data).format('YYYY-MM-DD, hh:mm:ss A')"],
            'updated_at' => ['title' => trans('cortex/auth::common.updated_at'), 'render' => "moment(data).format('YYYY-MM-DD, hh:mm:ss A')"],
        ];
    }
}
