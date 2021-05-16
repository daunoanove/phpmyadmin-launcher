<?php

namespace App;

class Launcher {
    protected $instances = [];
    protected $checked = false;
    protected $path;

    function __construct()
    {
        $this->path = dirname(__DIR__);
    }

    public function getInstances(): array
    {
        return $this->instances;
    }

    public function getInstance($k = null): array
    {
        if ( array_key_exists($k, $this->instances) )
        {
            return $this->instances[$k];
        }

        throw new \App\Exceptions\Launcher\InstanceNotFoundException('Instance not found');
    }

    public function preparePhpMyAdminConfig(): void
    {
        $path = sprintf('%s/public/phpmyadmin', $this->path);
        if ( !is_dir($path) )
        {
            throw new \App\Exceptions\Launcher\PhpMyAdmin\PhpMyAdminFolderNotFound(sprintf('Folder "%s" not found', $path));
        }

        $content = file_get_contents(sprintf('%s/data/config.txt', $this->path));

        $content = str_replace(
            [
                '{blowfishSecret}',
                '{signonUrl}',
            ],
            [
                \Illuminate\Support\Str::random(32),
                url('/logout'),
            ],
            $content
        );

        file_put_contents(sprintf('%s/config.inc.php', $path), $content);
    }

    public function checkInstanceConnection($k = null): bool
    {
        $instance = $this->getInstance($k);

        $mysqli = mysqli_init();
        $mysqli->options(MYSQLI_OPT_CONNECT_TIMEOUT, 10);
        $mysqli->real_connect($instance['host'], $instance['user'], $instance['password'], null, $instance['port']);

        return true;
    }

    public function launch($k = null): array
    {
        $instance = $this->getInstance($k);

        ini_set('session.use_cookies', 'true');
        $secure_cookie = false;
        session_set_cookie_params(0, '/', '', $secure_cookie, true);
        $session_name = 'SignonSession';
        session_name($session_name);
        @session_start();

        $_SESSION['PMA_single_signon_user'] = $instance['user'];
        $_SESSION['PMA_single_signon_password'] = $instance['password'];
        $_SESSION['PMA_single_signon_host'] = $instance['host'];
        $_SESSION['PMA_single_signon_port'] = $instance['port'];

        $_SESSION['PMA_single_signon_cfgupdate'] = ['verbose' => sprintf('%s@%s', $instance['user'], $instance['host'])];
        $_SESSION['PMA_single_signon_HMAC_secret'] = hash('sha1', uniqid(strval(rand()), true));

        $id = session_id();
        @session_write_close();

        return [
            'redirect' => sprintf('%s/phpmyadmin/', url('/')),
        ];
    }

    public function logout(): void
    {
        $params = session_get_cookie_params();
        setcookie('SignonSession', '', time() - 86400, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        //session_destroy();
    }

    public function check(): void
    {
        if ( false === $this->checked )
        {
            $this->checked = true;

            $instances = config('app.instances');

            if ( is_null($instances) )
            {
                throw new \App\Exceptions\Launcher\InstancesConfigFileException('Instances config file not found');
            }

            foreach ( $instances as $k => &$instance )
            {
                $instance['k'] = $k;

                $validator = \Validator::make($instance, $this->rules());
                if ( $validator->fails() )
                {
                    throw new \App\Exceptions\Launcher\ErrorWithInstanceConfigException('Error with an instance config');
                }
    
                $this->instances[] = $validator->validated();
            }
        }
    }

    protected function rules(): array
    {
        return [
            'k' => 'required|int',
            'host' => 'required',
            'port' => 'required|int',
            'user' => 'required',
            'password' => 'required',
        ];
    }
}