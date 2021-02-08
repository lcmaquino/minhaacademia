<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Certify;
use App\Rules\UniqueEmail;
use App\Rules\UniqueCPF;
use MyYouTubeChannel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;

class UserController extends Controller
{
    /**
     * Class UserController Constructor.
    */
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.users');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.createUser');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateCreateUser($request);

        $cpf = $request->cpf;
        $cpf = empty($cpf) ? null : substr($cpf, 0, 3) . substr($cpf, 4, 3) . substr($cpf, 8, 3) . substr($cpf, -2);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->cpf = $cpf;
        $user->role = Role::roleId($request->role);
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('users');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return $this->edit($user);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $disabled = Auth::user()->isAdmin() ? '' : 'disabled';
        return view('site.user', [
            'user' => $user,
            'disabled' => $disabled,
        ]);
    }

    /**
     * Show the form for editing user's password.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function editPassword(User $user)
    {
        $this->authorize('create', $user);
        $auth_user = Auth::user();
        if ($auth_user->isAdmin() && $auth_user->id != $user->id) {
            return view('admin.editUserPassword', [
                'user' => $user,
            ]);
        }else{
            return view('site.editUserPassword', [
                'user' => $user,
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $this->validateUpdateUser($request, $user);

        if(Auth::user()->isAdmin()) {
            return $this->updateByAdmin($request, $user);
        }else{
            return $this->updateByNormalUser($request, $user);
        }
    }

    /**
     * Update the specified resource in storage (admin version).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    private function updateByAdmin(Request $request, User $user) {
        $old_name = $user->name;
        $user->name = $request->name;
        $old_email = $user->email;
        $user->email = $request->email;
        $old_cpf = $user->cpf;
        $cpf = $request->cpf;
        $cpf = substr($cpf, 0, 3) . substr($cpf, 4, 3) . substr($cpf, 8, 3) . substr($cpf, -2);
        $user->cpf = $cpf;
      
        if (Auth::user()->id !== $user->id) {
            $user->role = Role::roleId($request->role);
        }

        $user->save();

        if ($old_name != $request->name || $old_cpf != $cpf) {
            $certifies = Certify::where(['cpf' => $old_cpf])->cursor();
            foreach ($certifies as $certify) {
                $certify->name = $user->name;
                $certify->cpf = $user->cpf;
                $certify->codeGenerator();
                $certify->save();
            }
        }

        return redirect()->route('userShow', ['user' => $user]);
    }

    /**
     * Update the specified resource in storage (student or teacher version).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    private function updateByNormalUser(Request $request, User $user) {
        $cpf = $request->cpf;
        $cpf = empty($cpf) ? null : substr($cpf, 0, 3) . substr($cpf, 4, 3) . substr($cpf, 8, 3) . substr($cpf, -2);

        if (empty($user->name) && !empty($request->name)){
            $user->name = $request->name;
        }

        if (empty($user->cpf) && !empty($cpf)){
            $user->cpf = $cpf;
        }

        $user->save();
        return redirect()->route('userShow', ['user' => $user]);
    }

    /**
     * Update user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request, User $user)
    {
        $this->authorize('create', $user);
        $this->validateUpdateUserPassword($request);

        $user->password = Hash::make($request->new_password);
        $user->save();
        return redirect()->route('userEdit', [
            'user' => $user,
        ]);
    }

    /**
     * Validate form for create user.
     *
     * @param Request $request
     * @return void
     */
    public function validateCreateUser(Request $request){
        $rules = [
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:128', 'unique:users'],
            'cpf' => ['nullable', 'regex:/[0-9]{3}\.[0-9]{3}\.[0-9]{3}-[0-9]{2}/i', 'confirmed', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'max:255'],
            'role' => ['required', 'in:admin,teacher,student'],
        ];

        $error_messages = [
            'name.max' => 'O :attribute deve ter no máximo 255 caracteres',
            'email.required' => 'É obrigatório informar um :attribute',
            'email.email' => 'Formato do :attribute é inválido',
            'email.max' => 'O :attribute deve ter no máximo 128 caracteres',
            'email.unique' => 'Este :attribute já está cadastrado',
            'cpf.regex' => 'O formato do :attribute deve ser 000.000.000-00',
            'cpf.confirmed' => 'O :attribute não está igual ao campo de confirmação',
            'cpf.unique' => 'Este :attribute já está cadastrado',
            'password.required' => 'É obrigatório informar uma :attribute',
            'password.min' => 'A :attribute deve ter no mínimo 6 caracteres',
            'password.max' => 'A :attribute deve ter no máximo 255 caracteres',
            'role.required' => 'É obrigatório informar um :attribute',
            'role.in' => 'O :attribute deve ser Administrador, Professor ou Estudante',
        ];

        $attributes = [
            'name' => 'nome',
            'email' => 'e-mail',
            'cpf' => 'CPF',
            'password' => 'senha',
            'role' => 'papel',
        ];

        $request->validate($rules, $error_messages, $attributes);
    }

    /**
     * Validate form for update user.
     *
     * @param Request $request
     * @return void
     */
    public function validateUpdateUser(Request $request, User $user){
        $rules = [
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:128', new UniqueEmail($user)],
            'cpf' => ['nullable', 'regex:/[0-9]{3}\.[0-9]{3}\.[0-9]{3}-[0-9]{2}/i', 'confirmed', new UniqueCPF($user)],
            'role' => ['required','in:admin,teacher,student'],
        ];

        $error_messages = [
            'name.max' => 'O :attribute deve ter no máximo 255 caracteres',
            'email.required' => 'É obrigatório informar um :attribute',
            'email.email' => 'Formato do :attribute é inválido',
            'email.max' => 'O :attribute deve ter no máximo 128 caracteres',
            'cpf.regex' => 'O formato do :attribute deve ser 000.000.000-00',
            'cpf.confirmed' => 'O :attribute não está igual ao campo de confirmação',
            'role.required' => 'É obrigatório informar um :attribute',
            'role.in' => 'O :attribute deve ser Administrador, Professor ou Estudante',
        ];

        $attributes = [
            'name' => 'nome',
            'email' => 'e-mail',
            'cpf' => 'CPF',
            'role' => 'papel',
        ];

        $request->validate($rules, $error_messages, $attributes);
    }

    /**
     * Validate form for create user.
     *
     * @param Request $request
     * @return void
     */
    public function validateUpdateUserPassword(Request $request){

        $rules = [
            'new_password' => ['required', 'confirmed', 'string', 'min:6'],
            'new_password_confirmation' => ['required'],
        ];

        $error_messages = [
            'new_password.required' => 'É obrigatório informar a :attribute',
            'new_password.confirmed' => 'As senhas digitadas não são iguais',
            'new_password.string' => ':attribute deve ser formada por caracteres alfanuméricos',
            'new_password.min' => ':attribute deve ter no mínimo 6 caracteres',
            'new_password_confirmation.required' => 'É obrigatório informar a :attribute',
        ];

        $attributes = [
            'new_password' => 'nova senha',
            'new_password_confirmation' => 'confirmação da senha',
        ];

        if(!Auth::user()->isAdmin()) {
            $rules['current_password'] = ['required', 'password'];
            $error_messages['current_password.required'] = 'É obrigatório informar a :attribute';
            $error_messages['current_password.password'] = 'A senha digitada não confere com a :attribute';
            $attributes['current_password'] = 'senha atual';
        }

        $request->validate($rules, $error_messages, $attributes);
    }

    /**
     * Remove the specified resource from storage.
     * 
     * Only admin users can run this method. See App\Polices\UserPolicy.
     * 
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroyConfirmation()
    {
        $user = Auth::user();
        return view('site.userDestroyConfirmation', ['user' => $user]);
    }

    /**
     * Remove the specified resource from storage.
     * 
     * Only admin users can run this method. See App\Polices\UserPolicy.
     * 
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $errors = new MessageBag();

        if (!$user->isStudent()) {
            $admin = User::where([
                ['id', '<>', $user->id],
                'role' => Role::roleId('admin'),
            ])->first();

            if(empty($admin)){
                $errors->add('destroy', 'Não foi possível remover sua conta, pois o site precisa ter pelo menos um administrador.');
            }else{
                foreach ($user->courses as $course) {
                    $course->teacher = $admin->id;
                    $course->save();
                }
            }
        }

        if ($errors->count() > 0) {
            return redirect()->route('userDestroyConfirmation')->withErrors($errors);
        }else{
            MyYouTubeChannel::revokeToken($user->refresh_token);
            $user->delete();
            if (Auth::id() == $user->id) {
                return redirect()->route('logout');
            }else{
                return redirect()->route('users');
            }
        }
    }
}