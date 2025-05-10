<?php namespace Artistro08\UserImport\Console;

use Illuminate\Console\Command;
use RainLab\User\Models\User;

/**
 * ConvertGuests Command
 *
 * @link https://docs.octobercms.com/3.x/extend/console-commands.html
 */
class ConvertGuests extends Command
{
    /**
     * @var string signature for the console command.
     */
    protected $signature = 'userimport:convert_guests';

    /**
     * @var string description is the console command description
     */
    protected $description = 'No description provided yet...';

    /**
     * handle executes the console command.
     */
    public function handle()
    {
        $users = User::where('is_guest', true)->get();
        
        foreach ($users as $user) {
            $user->convertToRegistered();
            $this->output->writeln("Converted guest user: {$user->email}. Email sent.");
        }

    }
}
