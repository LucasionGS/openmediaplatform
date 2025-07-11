<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Register - OpenMediaPlatform')]
class RegisterComponent extends Component
{
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $registration_code = '';
    public $buttonText = 'Create Account';
    public $error = '';
    public $success = '';

    protected $rules = [
        'name' => 'required|string|min:2|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
    ];

    protected $messages = [
        'name.required' => 'Name is required.',
        'name.min' => 'Name must be at least 2 characters.',
        'email.required' => 'Email is required.',
        'email.email' => 'Please enter a valid email address.',
        'email.unique' => 'This email is already registered.',
        'password.required' => 'Password is required.',
        'password.min' => 'Password must be at least 8 characters.',
        'password.confirmed' => 'Password confirmation does not match.',
        'registration_code.required' => 'Registration code is required.',
        'registration_code.correct' => 'Invalid registration code.',
    ];

    public function mount()
    {
        // Add registration code validation rule if environment variable is set
        if ($this->isRegistrationCodeRequired()) {
            $this->rules['registration_code'] = 'required|string';
        }
    }

    public function isRegistrationCodeRequired()
    {
        return !empty(env('OMP_REGISTRATION_CODE'));
    }

    public function getRequiredRegistrationCode()
    {
        return env('OMP_REGISTRATION_CODE');
    }

    public function register()
    {
        $this->buttonText = 'Creating Account...';
        $this->error = '';
        $this->success = '';

        try {
            // Add registration code validation rule if required
            if ($this->isRegistrationCodeRequired()) {
                $this->rules['registration_code'] = 'required|string';
            }

            $this->validate();

            // Validate registration code if required
            if ($this->isRegistrationCodeRequired()) {
                if ($this->registration_code !== $this->getRequiredRegistrationCode()) {
                    $this->buttonText = 'Create Account';
                    $this->error = 'Invalid registration code.';
                    return;
                }
            }

            $isFirst = false;
            if (!User::first(["id"])) {
                $isFirst = true;
            }

            // Create the user
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'channel_name' => $this->name, // Use name as default channel name
                'email_verified_at' => now(), // Auto-verify for simplicity
            ]);

            if ($isFirst) {
                $user->role = User::ROLE_ADMIN; // First user is admin
                $user->save();
            }


            // Log the user in
            Auth::login($user);

            $this->success = 'Account created successfully!';
            $this->buttonText = 'Account Created';

            // Redirect to home page
            return redirect()->route('home');

        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->buttonText = 'Create Account';
            $this->error = collect($e->errors())->flatten()->first();
        } catch (\Exception $e) {
            $this->buttonText = 'Create Account';
            $this->error = 'An error occurred while creating your account. Please try again.';
        }
    }

    public function render()
    {
        return view('livewire.register-component')
            ->layout('components.layouts.app');
    }
}
