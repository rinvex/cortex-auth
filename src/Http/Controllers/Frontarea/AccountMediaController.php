<?php

declare(strict_types=1);

namespace Cortex\Auth\Http\Controllers\Frontarea;

use Cortex\Auth\Models\Member;
use Cortex\Foundation\Models\Media;
use Cortex\Foundation\Http\Controllers\AuthenticatedController;

class AccountMediaController extends AuthenticatedController
{
    /**
     * Destroy given member media.
     *
     * @param \Cortex\Auth\Models\Member      $member
     * @param \Cortex\Foundation\Models\Media $media
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function destroy(Member $member, Media $media)
    {
        $member->media()->where($media->getKeyName(), $media->getKey())->first()->delete();

        return intend([
            'url' => route('frontarea.cortex.auth.account.settings'),
            'with' => ['warning' => trans('cortex/foundation::messages.resource_deleted', ['resource' => trans('cortex/foundation::common.media'), 'identifier' => $media->getRouteKey()])],
        ]);
    }
}
