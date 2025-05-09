<?php

namespace App\Livewire;

use App\Http\Controllers\UserController;
use App\Models\User;
use Auth;
use Livewire\Component;
use Request;

class LoginComponent extends Component
{
    public $buttonText = 'Login';
    public $error = '';
    public function render()
    {
        return view('livewire.login-component');
    }

    public string $email = '';
    public string $password = '';

    public function login()
    {
        // Set the button text to "Logging in..."
        $this->buttonText = 'Logging in...';
        $this->error = '';
        
    
        // call the login API
        if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            $this->buttonText = 'Login successful';
            // Redirect or perform any other action
            return redirect()->route('home');
        } else {
            $this->error = 'Invalid credentials';
            $this->buttonText = 'Login';

            User::create([
                'name' => explode('@', $this->email)[0],
                'email' => $this->email,
                'password' => bcrypt($this->password),
            ]);
        }
    }
}
