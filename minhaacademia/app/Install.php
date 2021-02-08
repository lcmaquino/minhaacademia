<?php

class Install {
    /**
     * Number of steps for installing process.
     * @var int
     */
    protected const STEPS = 5;

    /**
     * The current installing process step.
     *
     * @var int
     */
    protected $step = 0;

    /**
     * Installing process queue.
     *
     * @var array
     */
    protected $queue = [
        'makeSymbolicLink',
        'keyGenerate',
        'migrateFresh',
        'dbSeed',
        'updateAdmin'
    ];

    /**
     * The installing process messages.
     *
     * @var array
     */
    protected $messages = [
        'Criando link simbólico…',
        'Gerando a chave de criptografia…',
        'Criando as tabelas no banco de dados…',
        'Preenchendo as tabelas no banco de dados…',
        'Atualizando usuário administrador…',
    ];

    /**
     * The installing process failure messages.
     *
     * @var array
     */
    protected $failureMessages = [
        'Não foi possível criar o link simbólico. Verifique se você tem permissão de escrita na pasta minhaacademia/storage/app/public e htdocs/site.',
        'Não foi possível gerar a chave de criptografia.',
        'Não foi possível criar o banco de dados. Verifique suas configurações.',
        'Não foi possível preencher o banco de dados. Verifique suas configurações.',
        'A senha do administrador não foi atualizada.',
    ];

    /**
     * The form token.
     *
     * @var string
     */
    protected $token = null;

    /**
     * Check if there are steps to run.
     *
     * @return bool
     */
    protected function installing(){
        return ($this->step < self::STEPS);
    }

    /**
     * Start the updating process.
     *
     * @return bool
     */
    public function run(){
        $installed = true;
        $content = '';
        
        // If Laravel is already installed, then redirect to home.
        if ($this->existsDatabase() && !$this->emptyDatabase()) {
            header('Location: ' . $this->home());
            exit();
        }

        if ($this->requirements()) {
            if(session_status() !== PHP_SESSION_ACTIVE){
                session_start();
            }

            if (empty($_POST)){
                $this->token = $this->tokenGenerate(8);
                $_SESSION['token'] = $this->token;
                $content .= $this->installForm();
                $installed = false;
            }else{
                $errors = $this->validate($_POST);
                if (empty($errors)) {
                    header('Content-type: text/plain');
                    $email = trim($_POST['email']);
                    $password = trim($_POST['password']);
                    $this->flushMessage('## Processo de instalação iniciado.');

                    while($this->installing()) {
                        $this->flushMessage($this->messages[$this->step]);
            
                        if (!$this->runStep()) {
                            $installed = false;
                            $this->flushMessage('(ERRO ' . $this->step . ') '. $this->failureMessages[$this->step]);
                            break;
                        }
                    }

                    if($installed) {
                        $this->flushMessage('## Processo finalizado. Acesse: ' . $this->home());
                    }else{
                        $this->flushMessage('## Não foi possível concluir a instalação.');
                        $this->clear();
                    }

                    exit();
                }else{
                    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
                    $this->token = isset($_SESSION['token']) ? $_SESSION['token'] : null;
                    $content .= $this->errors($errors);
                    $content .= $this->installForm($email);
                }
            }
        }else{
            $content .= $this->requirementsMessage();
        }

        $this->render($content);
    }

    /**
     * Run each step of installing process.
     *
     * @return bool
     */
    protected function runStep(){
        $method = $this->queue[$this->step];
        $exec = call_user_func(Install::class . '::'. $method);

        if($exec){
            $this->step++;
        }
        
        return $exec;
    }

    /**
     * Check the installation requirements.
     *
     * @return bool
     */
    public function requirements(){
        return $this->validatePhpVersion() &&
            $this->exists('php') &&
            $this->validateEnv();
    }
    
    /**
     * Check if an command exists.
     *
     * @return bool
     */
    protected function exists($command){
        $OS = strpos(PHP_OS, 'WIN') === 0;
        $exec = $OS ? 'where' : 'command -v';
        
        return strlen(trim(shell_exec("$exec $command"))) > 0;
    }

    /**
     * Flush a message to user browser.
     *
     * @param string $msg
     * @return void
     */
    protected function flushMessage($msg = '') {
        echo $msg;
        echo str_repeat(' ', 1024*64);
        flush();
    }

    /**
     * Get the home URL.
     *
     * @return string
     */
    protected function home(){
        $host = $_SERVER['HTTP_HOST'];
        $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        $protocol = isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) ? 'https://' : 'http://';

        return $protocol . $host . $uri;
    }

    /**
     * Generate a token.
     *
     * @param integer $length
     * @return string
     */
    protected function tokenGenerate($length = 4){
        return bin2hex(random_bytes($length));
    }

    /**
     * Check if the database existis.
     *
     * @return bool
     */
    protected function existsDatabase() {
        $exists = false;
        $env = $this->env();
        $dbHost = $env['DB_HOST'];
        $dbPort = $env['DB_PORT'];
        $dbDatabase = $env['DB_DATABASE'];
        $dbUsername = $env['DB_USERNAME'];
        $dbPassword = $env['DB_PASSWORD'];
        $link = mysqli_connect($dbHost, $dbUsername, $dbPassword, '', $dbPort);

        if ($link !== false) {
            $query = 'SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = \'' . $dbDatabase . '\'';
            $result = mysqli_query($link, $query);
            if ($result !== false) {
                $exists = (mysqli_num_rows($result) == 1);
                $result->close();
            }
        }

        return $exists;
    }

    /**
     * Check if the database is empty.
     *
     * @return bool
     */
    protected function emptyDatabase() {
        $empty = false;
        $env = $this->env();
        $dbHost = $env['DB_HOST'];
        $dbPort = $env['DB_PORT'];
        $dbDatabase = $env['DB_DATABASE'];
        $dbUsername = $env['DB_USERNAME'];
        $dbPassword = $env['DB_PASSWORD'];
        $link = mysqli_connect($dbHost, $dbUsername, $dbPassword, '', $dbPort);

        if($link !== false) {
            $query = 'SHOW TABLES FROM ' . $dbDatabase;
            $result = mysqli_query($link, $query);
            if ($result !== false) {
                $empty = (mysqli_num_rows($result) == 0);
                $result->close();
            }
        }

        return $empty;
    }

    /**
     * Make a symbolic link.
     *
     * @return bool
     */
    protected function makeSymbolicLink(){
        $OS = strpos(PHP_OS, 'WIN') === 0;
        $env = $this->env();

        if ($env === false) {
            return false;
        }

        $publicHtml = $env['PUBLIC_HTML'];

        if ($OS) {
            $target = $this->basePath() . '\\storage\\app\\public';
            $link = $this->basePath() . str_replace('/', '\\', $publicHtml) . '\\storage';
        }else{
            $target = $this->basePath() . '/storage/app/public';
            $link = $this->basePath() . $publicHtml . '/storage';
        }
        
        return (file_exists($target) && !file_exists($link)) ? symlink($target, $link) : false;
    }

    /**
     * Run an artisan command and return the resulted code.
     *
     * The last line of the execution will be saved on $lastLineOutput.
     * 
     * @param string $cmd
     * @param string $lastLineOutput
     * @return int|null
     */
    protected function artisan($cmd = '', &$lastLineOutput = null){
        $cmd = 'php artisan ' . $cmd;
        $fullOutput = null;
        $resultCode = null;
        $lastLineOutput = exec('cd ' . $this->basePath() . ' && '. $cmd, $fullOutput, $resultCode);
        return $resultCode;
    }

    /**
     * Generate and save the application key.
     *
     * @return bool
     */
    protected function keyGenerate(){
        $key = null;
        $result_code = $this->artisan('key:generate --show', $key);

        return (!$result_code && !empty($key) && $this->saveKey($key) && !$this->artisan('config:cache'));
    }

    /**
     * Migrate the database.
     *
     * @return bool
     */
    protected function migrateFresh(){
        return ($this->artisan('migrate:fresh') === 0);
    }

    /**
     * Seed the database.
     *
     * @return bool
     */
    protected function dbSeed(){
        return ($this->artisan('db:seed') === 0);
    }

    /**
     * Update admin password.
     *
     * @return bool
     */
    protected function updateAdmin(){
        $email = isset($_POST['email']) ? $_POST['email'] : null;
        $password = isset($_POST['password']) ? $_POST['password'] : null;

        return ($this->artisan('user:update-admin ' . $email . ' ' . $password) === 0);
    }

    /**
     * Clear the installation process.
     *
     * @return void
     */
    protected function clear() {
        $OS = strpos(PHP_OS, 'WIN') === 0;
        $env = $this->env();
        $publicHtml = ($env !== false) ? $env['PUBLIC_HTML'] : null;

        if($publicHtml !== null) {
            $storagePath = $publicHtml . '/storage';
            $linkPath = $this->basePath() . (
                $OS ? str_replace('/', '\\', $storagePath) : $storagePath
                
            );
            $cmd = $OS ? 'rd /s /q' : 'rm -rf';

            exec($cmd . ' ' . escapeshellarg($linkPath));
        }
    }

    /**
     * Get the application's base path.
     *
     * @return string
     */
    protected function basePath(){
        return strpos(PHP_OS, 'WIN') === 0 ? __DIR__ . '\\..' : __DIR__ . '/..';
    }

    /**
     * Get the .env path.
     *
     * @return string
     */
    protected function envPath(){
        return $this->basePath() . (strpos(PHP_OS, 'WIN') === 0 ? '\\.env' : '/.env');
    }

    /**
     * Validate the PHP version.
     *
     * @return bool
     */
    protected function validatePhpVersion(){
        return (strcmp(phpversion(), '7.3') >= 0) &&
            (strcmp(phpversion(), '8.0') < 0);
    }
    
    /**
     * Validate the .env file.
     *
     * @return bool
     */
    protected function validateEnv(){
        $valid = false;

        if(file_exists($this->envPath())) {
            $env = $this->env();
            $valid = $env['PUBLIC_HTML'] && $env['DB_HOST'] 
                && $env['DB_PORT'] && $env['DB_DATABASE'] 
                && $env['DB_USERNAME'] && $this->existsDatabase();
        }

        return $valid;
    }

    /**
     * Get the parameters on .env file as an associative array.
     *
     * Returns false on failure.
     * 
     * @param string $param
     * @return array|false
     */
    protected function env(){
        return parse_ini_file($this->envPath(), false, INI_SCANNER_RAW);
    }

    /**
     * Save the app key to .env file.
     *
     * Returns false on failure.
     * 
     * @param string $key
     * @return bool
     */
    protected function saveKey($key = '') {
        $saved = false;
        $envPath = $this->envPath();
        $env = $this->env();

        if (is_array($env)) {
            $env['APP_KEY'] = $key;
            $envStr = '';
            $newLineHere = [
                'APP_URL',
                'PUBLIC_HTML',
                'LOG_CHANNEL',
                'DB_PASSWORD',
                'SESSION_LIFETIME',
                'REDIS_PORT',
                'MAIL_TO_ADDRESS',
                'AWS_BUCKET',
                'PUSHER_APP_CLUSTER'
            ];

            foreach($env as $key => $value) {
                $hasSpace = (trim($value) && strpos($value, ' ') !== false);
                $envStr .= $hasSpace ? 
                    $this->eol($key . '="' . $value . '"') : 
                    $this->eol($key . '=' . $value);
                if(in_array($key, $newLineHere)) {
                    $envStr .= $this->eol();
                }
            }

            $saved = (file_put_contents($envPath, $envStr) !== false);
        }

        return $saved;
    }

    /**
     * Add an "end of line" character to the line.
     *
     * @param string $line
     * @return string
     */
    protected function eol($line = '') {
        return $line . PHP_EOL;
    }

    /**
     * Get the HTML code for the error messages in $errors.
     *
     * @param array $errors
     * @return string
     */
    protected function errors($errors = []) {
        $html = $this->eol('<div class="alert alert-danger">');
        $html .= $this->eol('<ul>');
        foreach($errors as $error) {
            $html .= $this->eol('<li>' . $error . '</li>');
        }
        $html .= $this->eol('</ul>');
        $html .= $this->eol('</div>');
        return $html;
    }

    /**
     * Validate the intallation form.
     *
     * @param array $post
     * @return array
     */
    protected function validate($post = []) {
        $errors = [];

        $token = isset($post['token']) ? $post['token'] : null;
        if(empty($token) || ($token !== $_SESSION['token'])){
            $errors[] = 'Sessão inválida.';
        }

        $email = isset($post['email']) && 
            filter_var($post['email'], FILTER_VALIDATE_EMAIL);
        if(!$email){
            $errors[] = 'E-mail inválido';
        }

        $password = isset($post['password']) ? trim($post['password']) : null;
        $password_confirmation = isset($post['password_confirmation']) ? trim($post['password_confirmation']) : null;
        if(empty($password) || empty($password_confirmation) || $password !== $password_confirmation){
            $errors[] = 'A senha é diferente da confirmação';
        }else{
            if (strlen($password) < 6) {
                $errors[] = 'A senha deve ter no mínimo 6 caracteres';
            }
        }

        return $errors;
    }

    /**
     * Get the HTML code for the requirements message.
     *
     * @return string
     */
    protected function requirementsMessage() {
        $html = $this->eol('<h2>Requisitos da aplicação</h2>');
        $html .= $this->errors(['Não é possível instalar!']);
        $html .= $this->eol('<div class="text-left">');
        $html .= $this->eol('<ul>');
        $phpVersion = $this->validatePhpVersion() ? 'OK' : 'Falhou';
        $html .= $this->eol('<li>(' . $phpVersion . ') PHP versão 7.3.x;</li>');
        $phpCmd = $this->exists('php') ? 'OK' : 'Falhou';
        $html .= $this->eol('<li>(' . $phpCmd . ') Comando <code>php</code> disponível no terminal;</li>');
        $validateEnv = $this->validateEnv() ? 'OK' : 'Falhou';
        $html .= $this->eol('<li>(' . $validateEnv . ') Arquivo <code>minhaacademia\.env</code> foi criado e contém as configurações de um banco de dados existente;</li>');
        $html .= $this->eol('</ul>');
        $html .= $this->eol('</div>');
        return $html;
    }

    /**
     * Get the HTML code for the installation form.
     *
     * @param string $email
     * @return string
     */
    protected function installForm($email = ''){
        $html = $this->eol('<form action="#" method="POST">');
        $html .= $this->eol('<div class="u-full-width">');
        $html .= $this->eol('<label for="email">E-mail do administrador:</label>');
        $html .= $this->eol('<input id="email" style="width:50%;" type="email" name="email" value="' . $email . '"><br>');
        $html .= $this->eol('</div>');
        $html .= $this->eol('<div class="u-full-width">');
        $html .= $this->eol('<label for="password">Senha:</label>');
        $html .= $this->eol('<input id="password" style="width:50%;"  type="password" name="password" value=""><br>');
        $html .= $this->eol('</div>');
        $html .= $this->eol('<div class="u-full-width">');
        $html .= $this->eol('<label for="password">Confirmação da senha:</label>');
        $html .= $this->eol('<input id="password_confirmation" style="width:50%;" type="password" name="password_confirmation" value=""><br>');
        $html .= $this->eol('</div>');
        $html .= $this->eol('<input type="hidden" name="token" value="' . $this->token . '">');
        $html .= $this->eol('<input type="submit" value="Instalar">');
        $html .= $this->eol('</form>');
        return $html;
    }

    /**
     * Get the HTML code for installation page.
     *
     * @param string $content
     * @return string
     */
    protected function theme($content = ''){
        $base = $this->eol('<!DOCTYPE html>');
        $base .= $this->eol('<!-- "Educai as crianças para que não seja necessário punir os adultos." (Pitágoras) -->' );
        $base .= $this->eol('<html lang="pt-br">');
        $base .= $this->eol('<head>');
        $base .= $this->eol('<meta charset="utf-8">');
        $base .= $this->eol('<title>Minha Academia - Instalação</title>');
        $base .= $this->eol('<meta name="viewport" content="width=device-width, initial-scale=1">');
        $base .= $this->eol('<link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:ital,wght@0,400;0,600;0,900;1,400;1,600;1,900&display=swap" rel="stylesheet">');
        $base .= $this->eol('<link rel="stylesheet" href="css/normalize.css">');
        $base .= $this->eol('<link rel="stylesheet" href="css/skeleton.css">');
        $base .= $this->eol('<link rel="stylesheet" href="css/custom.css">');
        $base .= $this->eol('<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>');
        $base .= $this->eol('<link rel="icon" type="image/png" href="img/favicon.png">');
        $base .= $this->eol('</head>');
        $base .= $this->eol('<body>');
        $base .= $this->eol('<div class="section">');
        $base .= $this->eol('<div class="container u-full-width">');
        $base .= $this->eol('<h1>Minha Academia - Instalação</h1>');
        $base .= $this->eol($content);
        $base .= $this->eol('</div>');
        $base .= $this->eol('</div>');
        $base .= $this->eol('</body>');
        $base .= $this->eol('</html>');
        return $base;
    }

    /**
     * Get the HTML code for the installation page with $content.
     *
     * @param string $content
     * @return string
     */
    public function render($content = ''){
        echo $this->theme($content);
    }
}
