<?php namespace Artistro08\UserImport\Controllers;

use Backend\Classes\Controller;
use ApplicationException;
use RainLab\User\Models\User;
use October\Rain\Support\Str;
use BackendMenu;

class UserImport extends Controller
{
    public $implement = [
        'Backend.Behaviors.ImportExportController'
    ];

    public $importExportConfig = 'config_import_export.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('RainLab.User', 'user', 'import');
    }

    public function beforeImport()
    {
        $importOptions = post('ImportOptions', []);
        $this->importOptions = [
            'send_welcome_email' => array_get($importOptions, 'send_welcome_email', false),
            'auto_activate' => array_get($importOptions, 'auto_activate', false),
            'username_format' => array_get($importOptions, 'username_format', 'firstname.lastname')
        ];
    }

    protected function generateUsername($firstname, $lastname)
    {
        $format = $this->importOptions['username_format'] ?? 'firstname.lastname';
        
        switch ($format) {
            case 'firstname.lastname':
                $username = strtolower($firstname . '.' . $lastname);
                break;
            case 'firstinitial.lastname':
                $username = strtolower(substr($firstname, 0, 1) . '.' . $lastname);
                break;
            default:
                $username = strtolower($firstname . '.' . $lastname);
        }

        $username = Str::slug($username);
        
        $baseUsername = $username;
        $counter = 1;
        while (User::whereUsername($username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        return $username;
    }
}
