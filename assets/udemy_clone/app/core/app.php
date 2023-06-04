<?php



class App
{
    protected $controller = '_404';
    protected $method = 'index';
    public static $page = '_404';

    public function __construct()
    {
        $arr = $this->getURL();

        $filename = "../app/controllers/" . ucfirst($arr[0]) . ".php";
        if (file_exists($filename)) {
            require $filename;
            $controllerName = '\\App\\Controllers\\' . ucfirst($arr[0]);
            $this->controller = new $controllerName;
            self::$page = $arr[0];
            unset($arr[0]);
        } else {
            require "../app/controllers/" . $this->controller . ".php";
        }

        $mymethod = $arr[1] ?? $this->method;
        $mymethod = str_replace("-", "_", $mymethod);

        if (!empty($arr[1])) {
            if (method_exists($this->controller, strtolower($mymethod))) {
                $this->method = strtolower($mymethod);
                unset($arr[1]);
            }
        }

        $arr = array_values($arr);
        call_user_func_array([$this->controller, $this->method], $arr);
    }

    private function getURL()
    {
        $url = $_GET['url'] ?? 'home';
        $url = filter_var($url, FILTER_SANITIZE_URL);
        $arr = explode("/", $url);
        return $arr;
    }
}
