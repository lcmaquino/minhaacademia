<?php

namespace App\Models;

use Hash;
use App\CPFFormatter;
use Lcmaquino\GoogleOAuth2\GoogleUser;
use MyYouTubeChannel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'role', 'access_token', 'refresh_token', 'expires_in',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'access_token', 'refresh_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'expires_in',
    ];

    /**
     * Show CPF with mask.
     *
     * @return string
     */
    public function cpfShow()
    {
        $cpf = new CPFFormatter($this->cpf);
        return $cpf->show();
    }

    /**
     * Check if user is an admin.
     *
     * @return boolean
     */
    public function isAdmin(){
        return ($this->role() && ($this->role()->name === 'admin')) ? true : false;
    }

    /**
     * Check if user is a teacher.
     *
     * @return boolean
     */
    public function isTeacher(){
        return ($this->role() && ($this->role()->name === 'teacher')) ? true : false;
    }

    /**
     * Check if user is a student.
     *
     * @return boolean
     */
    public function isStudent(){
        return ($this->role() && ($this->role()->name === 'student')) ? true : false;
    }

    /**
     * Get user's role.
     *
     * @return Model
     */
    public function role(){
        return $this->belongsTo('App\Models\Role', 'role', 'id')->first();
    }

    /**
     * Get courses that user is the teacher.
     *
     * @return Model
     */
    public function courses(){
        return $this->hasMany('App\Models\Course', 'teacher', 'id');
    }

    /**
     * Get user's certify.
     *
     * @return Model
     */
    public function certify(Course $course){
        return Certify::where(['cpf' => $this->cpf, 'course' => $course->id])->first();
    }

    /**
     * Refresh user's access token if it has expired.
     * Returns true if token was refreshed.
     * @return boolean
     */
    public function refreshTokenIfNeeded(){
        $updated = false;
        if (now()->greaterThanOrEqualTo($this->expires_in) && !empty($this->refresh_token)) {
            $token = MyYouTubeChannel::refreshUserToken($this->refresh_token);
            if($token) {
                $this->access_token = $token;
                $this->expires_in = now()->addSeconds(3599);
                $this->save();
                $updated = true;
            }
        }
        return $updated;
    }

    /**
     * Get the name of user's role.
     *
     * @return Model
     */
    public function roleName(){
        return Role::roleName($this->role);
    }

    /**
     * Get the first user stored as administrator.
     *
     * @return User
     */
    static public function firstAdmin(){
        return User::where(['role' => Role::roleId('admin')])->first();
    }

    /**
     * Store a new user.
     *
     * @param GoogleUser $user
     * @return void
     */
    static public function store(GoogleUser $user)
    {
        return User::create([
            'name' => null,
            'email' => $user->email,
            'password' => Hash::make($user->token), //the password must not be empty.
            'access_token' => $user->token,
            'refresh_token' => isset($user->refreshToken) ? $user->refreshToken : null,
            'expires_in' => now()->addSeconds($user->expiresIn),
            'role' => Role::roleId('student'),
            'cpf' => null,
        ]);
    }

    /**
     * Update "access token", "refresh token" and "expires in" user's attributes.
     *
     * The $oauth2 array should be formatted as:
     *   [
     *     'token' => string|null,
     *     'refreshToken' => string|null,
     *     'expiresIn' => int
     *   ]
     * @param array $oauth2
     * @return $this
     */
    public function updateToken($oauth2 = [])
    {
        $this->access_token = $oauth2['token'];
        $this->refresh_token = empty($oauth2['refreshToken']) ? $this->refresh_token : $oauth2['refreshToken'];
        $this->expires_in = now()->addSeconds($oauth2['expiresIn']);
        $this->save();

        return $this;
    }

    /**
     * Find an user by their email.
     *
     * @param string $email
     * @return User|null
     */
    static public function findByEmail($email = ''){
        return User::where(['email' => $email])->first();
    }
}