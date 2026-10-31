<?php

namespace App\Livewire\Superadmin\User;

use App\Models\User;
use Livewire\Component; 
use Illuminate\Support\Facades\Hash;

class Index extends Component
{
    public $name,$nip,$email,$role,$password,$password_confirmation,$user_id;
    public function render()
    {
        
        $data=array(
            'title'  => 'Data User',
            'user' => User::orderBy('role','asc')->get(),
        );

        return view('livewire.superadmin.user.index',
        $data);
    }

    public function create(){
        $this->resetValidation();
        $this->reset([
            'name',
            'nip',
            'email',
            'role',
            'password',
            'password_confirmation',
        ]);
    }
    public function store()
    {
        $this->validate([
            'name'                  => 'required',
            'nip'                   => 'nullable|unique:users,nip',
            'email'                 => 'required|email',
            'role'                  => 'required',
            'password'              => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
        ],[
            'name.required'                     => 'name Tidak Boleh Kosong',
            'nip.nullabel'                      => 'NIP Tidak Boleh Kosong / boleh diisi strip ( -  )',
            'nip.unique'                        => 'NIP Sudah Digunakan',   
            'email.required'                    => 'Email Tidak Boleh Kosong',
            'email.email'                       => 'Format Email Salah',
            'role.required'                     => 'Role Tidak Boleh Kosong',
            'password.required'                 => 'Password Tidak Boleh Kosong',
            'password.min'                      => 'Password Minimal 8 Karakter',
            'password.confirmed'                => 'Password Tidak Sama',
            'password_confirmation.required'    => 'Password Tidak Boleh Kosong',
        ]);

        $user               = new User();
        $user->name         = $this->name;
        $user->nip          = $this->nip ?:null;
        $user->email        = $this->email;
        $user->role         = $this->role;
        $user->password     = Hash::make($this->password);
        $user->save();
        $this->dispatch('closeModal'); 
    }

    public function edit($id){
        $this->resetValidation();
        $user = User::findOrFail($id);
        $this->name                     = $user->name;
        $this->email                    = $user->email;
        $this->role                     = $user->role;
        $this->password                 = '';
        $this->password_confirmation    = '';
        $this->user_id                  = $user->id;
    }

    public function update($id)
    {
        $user               = User::findOrFail($id);
        $this->validate([
            'name'                  => 'required',
            'email'                 => 'required|email|unique:users,email,'.$id,
            'role'                  => 'required',
            'password'              => 'nullable|min:8|confirmed',
        ],[
            'name.required'                     => 'name Tidak Boleh Kosong',
            'email.required'                    => 'Email Tidak Boleh Kosong',
            'email.email'                       => 'Format Email Salah',
            'email.unique'                      => 'Email Sudah Digunakan',
            'role.required'                     => 'Role Tidak Boleh Kosong',
            'password.min'                      => 'Password Minimal 8 Karakter',
            'password.confirmed'                => 'Password Tidak Sama',

        ]);

        $user->name         = $this->name;
        $user->email        = $this->email;
        $user->role         = $this->role;

        if(filled($this->password)){
            $user->password     = Hash::make($this->password);
        }
        $user->save();

        $this->dispatch('closeEditModal'); 
    }   

    public function confrim($id){
        $user = User::findOrFail($id);
        $this->name         = $user->name;
        $this->email        = $user->email;
        $this->role         = $user->role;
        $this->user_id      = $user->id;
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        $this->dispatch('closeDeleteModal'); 
    }
}
