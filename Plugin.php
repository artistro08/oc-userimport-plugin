<?php
namespace Artistro08\UserImport;

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
            'author'      => 'Artistro08',
            'icon'        => 'icon-users'
        ];
    }


    /**
     * register method, called when the plugin is first registered.
     */
    public function register()
    {
        $this->registerConsoleCommand('artistro08.convert_guests', \Artistro08\UserImport\Console\ConvertGuests::class);
    }

    /**
     * boot method, called right before the request route.
     */
    public function boot()
    {
        Event::listen('backend.menu.extendItems', function ($manager) {
            $manager->addSideMenuItems('RainLab.User', 'user', [
                'import' => [
                    'label'       => 'artistro08.userimport::lang.label.import',
                    'url'         => Backend::url('artistro08/userimport/userimport/import'),
                    'icon'        => 'icon-sign-in',
                    'permissions' => ['artistro08.userimport.import'],
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
            'Artistro08\UserImport\Components\MyComponent' => 'myComponent',
        ];
    }

    /**
     * registerPermissions used by the backend.
     */
    public function registerPermissions()
    {
        return [
            'artistro08.userimport.some_permission' => [
                'tab'   => 'artistro08.userimport::lang.label.tab',
                'label' => 'artistro08.userimport::lang.label.permission',
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
