<?php

namespace Icrewsystems\Rtm;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Icrewsystems\Rtm\Skeleton\SkeletonClass
 */
class RtmFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'rtm';
    }
}
