<?php


namespace App\Traits;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Spatie\DbDumper\Databases\MySql;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

trait UserTypeRoutesTrait
{
    /**
     * If the user is an admin, it will prepend 'admin.' to the route name, and if the user is a vendor,
     * it will prepend 'seller.' to the route name. If the user is neither an admin nor a vendor,
     * the original route name will be returned.
     *
     * @param $route
     * @return string
     */
    public function getRouteName($route): string
    {
        if (loginType() == 'vendor') {
            return 'seller.' . $route;
        } else {
            // Handle other user types here
            return $route;
        }
    }

    /**
     * Redirect to the appropriate route based on the user type.
     *
     * @param $route
     * @param array $parameters
     * @param int $status
     * @param array $headers
     * @return RedirectResponse
     */
    public function redirectToRoute($route, array $parameters = [], int $status = 302, array $headers = []): RedirectResponse
    {
        $routeName = $this->getRouteName($route);
        return redirect()->route($routeName, $parameters, $status, $headers);
    }
}

