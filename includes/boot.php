<?php
class boot {
	private $modules;
	public function __construct(){
		$this->listModules();
		$this->pageRouter();
	}
	
	public function listModules(){
		$this->modules = new stdClass;
		$directories = scandir(PROJECT_ROOT .'/modules');
		foreach($directories as $module){
			$path = PROJECT_ROOT .'/modules/'. $module .'/indexAction.php';
			if(strlen($module) > 3 AND file_exists($path)){
				require_once($path);
				if(class_exists($module)){
					$this->modules->$module = new $module();
				}
			}
		}
	}
	
	public function loadMenus(){
		$menus = new stdClass;
		if(!is_object($this->modules))
			$this->listModules();
		foreach($this->modules as $name => $module){
			try{
				if(method_exists($module, 'menu'))
					$menus->$name = $module->menu();
			}
			catch(Exception $e){
				print $e->getMessage();
				exit();
			}
		}
		return $menus;
	}
	
	public function pageRouter(){
		$menus = $this->loadMenus();
        require_once(PROJECT_ROOT .'/includes/interface.php');
		if(isset($_GET['q']) AND !empty($_GET['q'])){
			$content = '404 Page not found!';
			$request = trim($_GET['q']);
			$args = explode('/', $request);
			$paths = array();
			foreach($args as $arg){
				$paths[] = (count($paths)) ? implode('/', $paths) .'/'. $arg : $arg;
			}
			$found = false;
			foreach($menus as $module => $items){
				foreach($items as $path => $item){
					if(in_array($path, $paths)){
						if(isset($item['callback'])){
							try{
								$callback = $item['callback'];
								$wrapper = $this->modules->$module;
								$arguments = array();
								if(isset($item['arguments']) AND count($item['arguments'])){
									foreach($item['arguments'] as $index){
										if(isset($args[$index]))
											$arguments[] = $args[$index];
									}
								}
								if(method_exists($wrapper, $callback))
									$content = call_user_func_array(array($wrapper, $callback), $arguments);
								
								if($path == $request)
									$found = true;
							}
							catch (Exception $e){
								print $e->getMessage();
								exit();
							}
						}
					}
					if($found)
						break;
				}
				if($found)
					break;
			}

			if(!$found){
                $paths = array_reverse($paths);
                $paths[] = reset($paths) .'/index';
                foreach ($paths as $path){
                    $action = end(explode('/', $path)) .'Action';
                    $actionPath = $path .'Action.php';
                    if(file_exists('modules/'. $actionPath)){
                        require_once(PROJECT_ROOT .'/modules/'. $actionPath);
                        if(class_exists($action)){
                            try {
                                $actionObject = new $action();
                                $arguments = array_diff($args, explode('/', $path));
                                $content = $this->handleRequest($actionObject, $arguments);
                                if (method_exists($actionObject, 'content'))
                                    $content .= '<br>'. call_user_func_array(array($actionObject, 'content'), $arguments);
                            }
                            catch (Exception $e){
                                print $e->getMessage();
                                exit();
                            }
                        }
                    }
                }
            }
			print $content;
		}
		else
			print 'Loaded content of front page.';
	}

	public function handleRequest($actionObject, $arguments){
        $response = '';
        $method = strtolower(getenv('REQUEST_METHOD'));
        $arguments['parameters'] = $this->parseIncomingParams();
        if (method_exists($actionObject, $method))
            $response = call_user_func_array(array($actionObject, $method), $arguments);
        return $response;
    }

    public function parseIncomingParams() {
        $parameters = array();

        // first of all, pull the GET vars
        if (isset($_SERVER['QUERY_STRING'])) {
            parse_str($_SERVER['QUERY_STRING'], $parameters);
        }

        // now how about PUT/POST bodies? These override what we got from GET
        $data = file_get_contents("php://input");
        $content_type = false;
        if(isset($_SERVER['CONTENT_TYPE'])) {
            $content_type = $_SERVER['CONTENT_TYPE'];
        }
        switch($content_type) {
            case "application/json":
                $body_params = json_decode($data);
                if($body_params) {
                    foreach($body_params as $param_name => $param_value) {
                        $parameters[$param_name] = $param_value;
                    }
                }
                $format = "json";
                break;
            case "application/x-www-form-urlencoded":
                parse_str($data, $postvars);
                foreach($postvars as $field => $value) {
                    $parameters[$field] = $value;
                }
                $format = "html";
                break;
        }
        return $parameters;
    }
}