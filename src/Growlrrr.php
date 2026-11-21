<?php
namespace AppZz\CLI;
use AppZz\Helpers\Arr;

class Growlrrr {

    private $_bin = '/opt/homebrew/bin/grrr';

    /**
     * Allowed methods
     * @var array
     */
    private $_methods = [
        'title',
        'message',
        'sound',
        'bundle',
        'open',
        'execute',
        'image',
        'thread'
    ];

    private $_params;

    public function __construct ($bin = '')
    {
        if ($bin) {
            $this->_bin = $bin;
        }
    }

    public static function factory ($bin = '') : Object
    {
        return new Notifier ();
    }

    /**
     * Set params avoid setters
     * @param  array  $params
     * @return $this
     */
    public function params (array $params = []) : Object
    {
        $this->_params = $params;
        return $this;
    }

    public function register ($icon, $bundle) : bool
    {
        $cmd = "%s apps add --appId %s --appIcon %s";
        system (sprintf ($this->_bin, $bundle, $icon), $retval);
        return ($retval === 0);
    }

    public function unregister ($bundle) : bool
    {
        $cmd = "%s apps remove --appId %s --force";
        system (sprintf ($this->_bin, $bundle, $icon), $retval);
        return ($retval === 0);
    }

    public function __call ($method, $params)
    {
        if ( ! in_array ($method, $this->_methods)) {
            throw new \Exception ('Wrong method: ' . $method);
        }

        $value = Arr::get($params, 0);

        switch ($method) {
            case 'thread':
                $method = 'threadId';
            break;
        }

        $this->_params[$method] = $value;

        return $this;
    }

    public function send () : bool
    {
        $cmd = $this->_bin;

        $message = Arr::get ($this->_params, 'message', '');
        unset ($this->_params['message']);

        foreach ($this->_params as $key => $value) {
            $cmd .= sprintf (' --%s %s', $key, (! empty ($value) ? escapeshellarg($value) : ''));
            $cmd = rtrim ($cmd);
        }

        $cmd .= ' ' . escapeshellarg ($message);

        $result = system ($cmd, $retval);
        return ($retval === 0);
    }
}
