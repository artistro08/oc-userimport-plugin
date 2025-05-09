<?php namespace EgerStudios\UserImport\Models;

use Backend\Models\ImportModel;
use RainLab\User\Models\User;
use RainLab\User\Models\UserGroup;
use TokenRepositoryInterface;
use Mail;
use Schema;
use Log;
use Exception;
use Str;

class UserImportModel extends ImportModel
{
    public $rules = [
        'email' => 'required|email'
    ];

    protected $isVersion2 = null;

    protected function isVersion2()
    {
        if ($this->isVersion2 === null) {
            $userModel = new User;
            // If first_name exists, it's v3, otherwise it's v2
            $this->isVersion2 = !Schema::hasColumn($userModel->getTable(), 'first_name');
        }

        return $this->isVersion2;
    }

    public function importData($results, $sessionKey = null)
    {
        foreach ($results as $row => $data) {
            try {
                // Get import options
                $sendWelcomeEmail = post('ImportOptions.send_welcome_email', false);
                $autoActivate = post('ImportOptions.auto_activate', false);
                $usernameFormat = post('ImportOptions.username_format', 'firstname.lastname');
                $usersGroups = post('ImportOptions.user_groups', []);
              

                // Generate username if needed
                if (empty($data['username'])) {
                    $firstname = $data['firstname'];
                    $lastname = $data['lastname'];

                    if (!empty($firstname) && !empty($lastname)) {
                        $data['username'] = $this->generateUsername(
                            $firstname,
                            $lastname,
                            $usernameFormat
                        );
                    } else {
                        $data['username'] = $data['email'];
                    }
                }

                // Generate secure random password if not provided
                if (empty($data['password'])) {
                    $password = Str::random(12) . mt_rand(); // 12 characters for better security + a random number
                    $data['password'] = $password;
                    $data['password_confirmation'] = $password;
                }

                // Convert fields for v2 if needed
                if ($this->isVersion2()) {
                    $data['name'] = $data['firstname'];
                    $data['surname'] = $data['lastname'];
                    unset($data['firstname'], $data['lastname']);
                }
                
                
                // Create user
                $user = User::updateOrCreate(
                    ['email' => $data['email'],
                    'first_name' => $data['firstname'],
                    'last_name'=> $data['lastname'],
                    'password' => $data['password'],
                    'is_guest' => true,
                ]);

                $user->convertToRegistered();
                
                
                
                
                /**
                 * Handles the user group assignment process.
                 *
                 * @param array $usersGroups An array of user group IDs to be assigned to the user.
                 * @param \RainLab\User\Models\User $user The user model instance.
                 *
                 * @return void
                 */
                if(!empty($usersGroups)) {
                    $existingGroupIds = $user->groups()->pluck('id')->toArray();

                    // Calculate new groups being added
                    $newGroups = array_diff($usersGroups, $existingGroupIds);
                    if (!empty($newGroups)) {}

                    $allGroupIds = array_unique(array_merge($existingGroupIds, $usersGroups));
                    $user->groups()->sync($allGroupIds);
                }
                
                
                if($autoActivate) {
                    if($this->isVersion2()) {
                        $user->attemptActivation($user->activation_code);
                    } else {
                        $user->markEmailAsVerified();
                    }
                }
                
                if ($sendWelcomeEmail) {
                    Mail::queue('rainlab.user::mail.welcome', $user->getNotificationVars(), function($message) use ($user) {
                        $message->to($user->email, $user->full_name);
                    });
                }


                $this->logCreated();

            }
            catch (Exception $ex) {
                $this->logError($row, $ex->getMessage());
            }
        }
    }

    /**
     * Returns available user groups for the dropdown
     */
    public function getUserGroupOptions()
    {
        return UserGroup::lists('name', 'id');
    }

    protected function generateUsername($firstname, $lastname, $format)
    {
        if (empty($firstname) || empty($lastname)) {
            return null;
        }

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

        $username = str_slug($username);
        
        if (empty($username)) {
            return null;
        }

        $baseUsername = $username;
        $counter = 1;
        while (User::whereUsername($username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        return $username;
    }
}
