<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class StoreAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:update-admin {user*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update email and password for admin user';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $userArg = $this->argument('user');
        $firstAdmin = User::firstAdmin();
        if ($firstAdmin) {
            $firstAdmin->email = $userArg[0];
            $firstAdmin->password = Hash::make($userArg[1]);
            $firstAdmin->save();
            $this->info($firstAdmin->email . ' atualizado com sucesso.');
            return 0;
        }else{
            $this->error('Ops! Nenhum usuário administrador foi encontrado.');
            return 1;
        }
    }
}