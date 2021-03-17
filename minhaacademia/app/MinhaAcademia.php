<?php

namespace App;

use Illuminate\Support\Facades\Http;
use Illuminate\Filesystem\Filesystem;
use GuzzleHttp\Client;
use ZipArchive;
use Exception;

class MinhaAcademia
{
    /**
     * The Minha Academia version.
     *
     * @var string
     */
    protected const VERSION = '1.2.6';

    /**
     * Number of steps for updating process.
     * @var int
     */
    protected const STEPS = 4;

    /**
     * Updating process queue.
     *
     * @var array
     */
    protected $queue = ['latest', 'extract', 'copy', 'clear'];

    /**
     * The directories to be updated.
     *
     * @var array
     */
    protected $updateDirs = [
        '/app',
        '/database',
        '/public',
        '/routes'
    ];

    /**
     * The updating process messages.
     *
     * @var array
     */
    protected $messages = [
        'Baixando arquivo… (Pode demorar alguns minutos.)',
        'Extraindo arquivo…',
        'Movendo arquivo…',
        'Limpando arquivos temporários.'
    ];

    /**
     * The Minha Academia latest version.
     *
     * @var string
     */
    protected $latestVersion = null;

    /**
     * The zip filename.
     *
     * @var string
     */
    protected $zipFile = null;

    /**
     * The zip directory.
     *
     * @var string
     */
    protected $zipDir = null;

    /**
     * The current updating process step.
     *
     * @var int
     */
    protected $step = 0;

    /**
     * Get the Minha Academia version number.
     *
     * @return string
     */
    static public function version(){
        return static::VERSION;
    }

    /**
     * Check if there are steps to run.
     *
     * @return bool
     */
    protected function updating(){
        return ($this->step < self::STEPS);
    }

    /**
     * Start the updating process.
     *
     * @return bool
     */
    public function update(){
        $updated = true;
        $hasUpdate = $this->hasUpdate();

        if($hasUpdate === null) {
            $this->flushMessage('Não foi possível verificar novas atualizações. Tente novamente mais tarde.');
            return false;
        }

        if($hasUpdate === false) {
            $this->flushMessage('A sua aplicação já está atualizada com a versão mais recente.');
            return false;
        }

        while($this->updating()) {
            $this->flushMessage($this->messages[$this->step]);

            if (!$this->run()) {
                $updated = false;
                break;
            }
        }

        if ($updated) {
            $this->flushMessage('Atualização completa. Nova versão: ' . $this->getLatestVersion());
        }else{
            $this->flushMessage('Não foi possível concluir a atualização.');
            $this->restore();
        }
        
        return $updated;
    }

    protected function flushMessage($msg = '') {
        // Echo the message.
        echo '<p>' . $msg . '</p>';
        // This is for the buffer achieve the minimum size in order to flush data.
        echo str_repeat(' ',1024*64);
        // Send output to browser immediately.
        flush();
    }

    /**
     * Run each step of updating process.
     *
     * @return bool
     */
    protected function run(){
        $method = $this->queue[$this->step];
        $exec = call_user_func(MinhaAcademia::class . '::'. $method);

        if($exec){
            $this->step++;
        }
        
        return $exec;
    }

    /**
     * Get the Minha Academia latest version number.
     *
     * @return string
     */
    static public function getLatestVersion(){
        try {
            $response = Http::retry(3, 100)->get('https://api.github.com/repos/lcmaquino/minhaacademia/tags', [
                'per_page' => 1,
                'page' => 1
            ]);
            $tags = $response->json();
        }catch(Exception $e){
            $tags = null;
        }

        return (empty($tags) || empty($tags[0])) ? null : $tags[0]['name'];
    }

    /**
     * Check if there is an available update.
     * 
     * Returns null if it could not get the application latest version.
     * 
     * @return bool|null
     */
    static public function hasUpdate(){
        $currentVersion = 'v' . static::VERSION;
        $latestVersion = static::getLatestVersion();

        return empty($latestVersion) ? null : (strcmp($latestVersion, $currentVersion) > 0);
    }

    /**
     * It downloads from GitHub the Minha Academia latest version.
     *
     * @return bool
     */
    protected function latest(){
        $downloaded = false;
        $repoUri = 'https://github.com/lcmaquino/minhaacademia/';
        $this->latestVersion = $this->getLatestVersion();
        $fs = new Filesystem();
        if (!empty($this->latestVersion)) {
            $this->zipFile = $this->latestVersion . '.zip';
            $this->zipDir = storage_path('app/update/' . $this->latestVersion);
            try {
                $fs->makeDirectory($this->zipDir);
                $resource = fopen($this->zipDir . '/' . $this->zipFile, 'w');
                $client = new Client(['base_uri' => $repoUri]);
                $response = $client->request('GET', 'archive/'. $this->zipFile, [
                    'sink' => $resource,
                ]);
                $downloaded = true;
            }catch(Exception $e){
                $downloaded = null;
            }
        }

        return $downloaded;
    }

    /**
     * Unzip the file downloaded from GitHub repository.
     *
     * @return bool
     */
    protected function extract(){
        $extracted = null;
        $zip = new ZipArchive;
        if ($zip->open($this->zipDir . '/' . $this->zipFile) === true) {
            $zip->extractTo($this->zipDir);
            $zip->close();
            $extracted = true;
        } else {
            $extracted = false;
        }
        return $extracted;
    }

    /**
     * Copy the new directories.
     *
     * @return bool
     */
    protected function copy(){
        $copied = true;
        $version = substr($this->latestVersion, 1);
        $extractedDir = $this->zipDir . '/minhaacademia-' . $version;
        $baseDir = base_path();
        $fs = new Filesystem();

        try{
            if (!$this->backup())
                return false;

            foreach ($this->updateDirs as $d){
                if (!$fs->copyDirectory($extractedDir . '/minhaacademia' . $d, $baseDir . $d)) {
                    return false;
                }
            }
        }catch(Exception $e){
            $copied = false;
        }
        
        return $copied;
    }

    /**
     * Backup current directories.
     *
     * @return bool
     */
    protected function backup(){
        $baseDir = base_path();
        $fs = new  Filesystem();

        foreach($this->updateDirs as $d) {
            if (!$fs->copyDirectory($baseDir . $d, $this->zipDir . '/backup' . $d)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Restore the backup.
     *
     * @return bool
     */
    protected function restore(){
        $baseDir = base_path();
        $fs = new  Filesystem();

        foreach($this->updateDirs as $d) {
            if (!$fs->copyDirectory($this->zipDir . '/backup' . $d, $baseDir . $d)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Delete the directory created for updating process.
     *
     * @return bool
     */
    protected function clear(){
        $fs = new Filesystem();
        return $fs->deleteDirectory($this->zipDir);
    }
}
