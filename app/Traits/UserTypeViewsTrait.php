<?php

namespace App\Traits;

use Auth;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

/**
 * Trait to handle different view paths for different user types.
 */
trait UserTypeViewsTrait
{
    /**
     * If the user is an admin, it will prepend 'backend.' to the view name, and if the user is a vendor,
     * it will prepend 'frontend.' to the view name. If the user is neither an admin nor a vendor,
     * the original view name will be returned.
     *
     * @param $view
     * @return mixed|string
     */
    public function getViewPath($view)
    {
        if( Auth::user()->user_type == 'admin' ) {
            return 'backend.' . $view;
        } else if( loginType() == 'vendor' ) {
            return 'frontend.' . $view;
        } else {
            // Handle other user types here
            return $view;
        }
    }

    /**
     * @param $view
     * @param array $data
     * @return Application|Factory|View
     */
    public function loadView($view, array $data = [])
    {
        $viewPath = $this->getViewPath($view);
        return view($viewPath, $data);
    }
}
