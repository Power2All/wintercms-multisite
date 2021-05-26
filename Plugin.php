<?php namespace Power2All\Multisite;

use System\Classes\PluginBase;
use Power2All\Multisite\Models\Setting;
use BackendAuth;
use Backend;
use Config;
use Event;
use Cache;
use Request;
use App;
use Flash;

/**
 * Multisite Plugin Information File
 * Plugin icon is used with Creative Commons (CC BY 4.0) Licence
 * Icon author: http://pixelkit.com/
 */
class Plugin extends PluginBase
{

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'power2all.multisite::lang.details.title',
            'description' => 'power2all.multisite::lang.details.description',
            'author'      => 'Power2All (Original Author: Keios)',
            'icon'        => 'icon-cubes',
        ];
    }

    /**
     * @return array
     */
    public function registerPermissions()
    {
        return [
            'power2all.multisite.access_settings' => [
                'tab'   => 'power2all.multisite::lang.permissions.tab',
                'label' => 'power2all.multisite::lang.permissions.settings',
            ],
        ];
    }

    /**
     * @return array
     */
    public function registerSettings()
    {
        return [
            'multisite' => [
                'label'       => 'power2all.multisite::lang.details.title',
                'description' => 'power2all.multisite::lang.details.description',
                'category'    => 'system::lang.system.categories.cms',
                'icon'        => 'icon-cubes',
                'url'         => Backend::url('power2all/multisite/settings'),
                'permissions' => ['power2all.multisite.settings'],
                'order'       => 500,
                'keywords'    => 'multisite domains themes',
            ],
        ];
    }

    /**
     * Multisite boot method
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \UnexpectedValueException
     */
    public function boot()
    {
        $backendUri = Config::get('cms.backendUri');
        $requestUrl = Request::url();
        $currentHostUrl = Request::getHost();

        /*
         * Get domain to theme bindings from cache, if it's not there, load them from database,
         * save to cache and use for theme selection.
         */
        $binds = Cache::rememberForever(
            'power2all_multisite_settings',
            function () {
                try {
                    $cacheableRecords = Setting::generateCacheableRecords();
                } catch (\Illuminate\Database\QueryException $e) {
                    if (BackendAuth::check()) {
                        Flash::error(trans('power2all.multisite:lang.flash.db-error'));
                    }

                    return null;
                }

                return $cacheableRecords;

            });
        /*
         * Oooops something went wrong, abort.
         */
        if ($binds === null) {
            return null;
        }
        /*
         * Check if this request is in backend scope and is using domain,
         * that is protected from using backend
         */
        foreach ($binds as $domain => $bind) {
            if (preg_match('/\\'.$backendUri.'/', $requestUrl) && preg_match(
                    '/'.$currentHostUrl.'/i',
                    $domain) && $bind['is_protected']
            ) {
                return App::abort(401, 'Unauthorized.');
            }
        }

        /*
         * If current request is in backend scope, do not check cms themes
         * Allows for current theme changes in Winter Theme Selector
         */
        if (preg_match('/\\'.$backendUri.'/', $requestUrl)) {
            return null;
        }
        /*
         * Listen for CMS activeTheme event, change theme according to binds
         * If there's no match, let CMS set active theme
         */
        Event::listen(
            'cms.theme.getActiveTheme',
            function () use ($binds, $currentHostUrl) {
                foreach ($binds as $domain => $bind) {
                    if (preg_match('/'.$currentHostUrl.'/i', $domain)) {
                        Config::set('app.url', $domain);

                        return $bind['theme'];
                    }
                }
            });
    }

}
