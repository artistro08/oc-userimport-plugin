<?php namespace EgerStudios\UserImport;

use Backend;
use Event;
use System\Classes\PluginBase;

/**
 * Plugin Information File
 *
 * @link https://docs.octobercms.com/3.x/extend/system/plugins.html
 */
class Plugin extends PluginBase
{
    public $require = ['RainLab.User'];
    
    
    /**
     * pluginDetails about this plugin.
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'User Import',
            'description' => 'Import users from CSV with column mapping',
            'author'      => 'EgerStudios',
            'icon'        => 'icon-users'
        ];
    }


    /**
     * register method, called when the plugin is first registered.
     */
    public function register()
    {
        //
    }

    /**
     * boot method, called right before the request route.
     */
    public function boot()
    {
        Event::listen('backend.menu.extendItems', function($manager)
        {
            $manager->addSideMenuItems('RainLab.User', 'user', [
                'users' => [
                    'label'       => 'rainlab.user::lang.users.menu_label',
                    'url'         => Backend::url('rainlab/user/users'),
                    'icon'        => 'icon-user',
                    'permissions' => ['rainlab.users.*'],
                    'order'       => 100,
                ],
                'import' => [
                    'label'       => 'egerstudios.userimport::lang.label.import',
                    'url'         => Backend::url('egerstudios/userimport/userimport/import'),
                    'icon'        => 'icon-sign-in',
                    'permissions' => ['egerstudios.userimport.import'],
                    'order'       => 200,
                ]
            ]);
        });
    }

    /**
     * registerComponents used by the frontend.
     */
    public function registerComponents()
    {
        return []; // Remove this line to activate

        return [
            'EgerStudios\UserImport\Components\MyComponent' => 'myComponent',
        ];
    }

    /**
     * registerPermissions used by the backend.
     */
    public function registerPermissions()
    {
        return [
            'egerstudios.userimport.some_permission' => [
                'tab' => 'egerstudios.userimport::lang.label.tab',
                'label' => 'egerstudios.userimport::lang.label.permission',
            ],
        ];
    }

    /**
     * registerNavigation used by the backend.
     */
    public function registerNavigation()
    {
        return [];
    }
}
