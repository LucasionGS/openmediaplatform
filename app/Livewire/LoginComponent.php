<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Sign In - OpenMediaPlatform')]
class LoginComponent extends Component
{
    public $email = '';
    public $password = '';
    public $buttonText = 'Sign In';
    public $error = '';
    public $remember = false;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required',
    ];

    protected $messages = [
        'email.required' => 'Email is required.',
        'email.email' => 'Please enter a valid email address.',
        'password.required' => 'Password is required.',
    ];

    public function login()
    {
        $this->buttonText = 'Signing In...';
        $this->error = '';

        try {
            $this->validate();

            // Attempt to log the user in
            if (Auth::attempt([
                'email' => $this->email, 
                'password' => $this->password
            ], $this->remember)) {
                $this->buttonText = 'Sign In Successful';
                
                // Regenerate session to prevent session fixation
                session()->regenerate();
                
                // Redirect to intended page or home
                return redirect()->intended(route('home'));
            } else {
                $this->error = 'Invalid email or password. Please try again.';
                $this->buttonText = 'Sign In';
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->buttonText = 'Sign In';
            $this->error = collect($e->errors())->flatten()->first();
        } catch (\Exception $e) {
            $this->buttonText = 'Sign In';
            $this->error = 'An error occurred while signing in. Please try again.';
        }
    }

    public function render()
    {
        return view('livewire.login-component')
            ->layout('components.layouts.app');
    }
}
