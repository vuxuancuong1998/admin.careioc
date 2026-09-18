<?php

class router {
 /*
 * @the registry
 */
 private $registry;

 /*
 * @the controller path
 */
 private $path;

 private $args = array();

 public $file;

 public $controller;

 public $action; 

 function __construct($registry) {
        $this->registry = $registry;
 }

 /**
 *
 * @set controller directory path
 *
 * @param string $path
 *
 * @return void
 *
 */
 function setPath($path) {

	/*** check if path i sa directory ***/
	if (is_dir($path) == false)
	{
		throw new Exception ('Invalid controller path: `' . $path . '`');
	}
	/*** set the path ***/
 	$this->path = $path;
}


 /**
 *
 * @load the controller
 *
 * @access public
 *
 * @return void
 *
 */
 public function loader()
 {
	/*** check the route ***/
	$this->getController();

	/*** if the file is not there diaf ***/
	if (is_readable($this->file) == false)
	{
		$this->file = $this->path.'/error404.php';
                $this->controller = 'error404';
	}

	/*** include the controller ***/
	include $this->file;

	/*** a new controller class instance ***/
	$class = $this->controller . 'Controller';
	$controller = new $class($this->registry);

	/*** check if the action is callable ***/
	if (is_callable(array($controller, $this->action)) == false)
	{
		$action = 'index';
	}
	else
	{
		$action = $this->action;
	}
	/*** run the action ***/
	$controller->$action($this->args);
	/*
	if(!empty($this->args))
		$controller->$action($this->args);
	else
	{
		$this->args = array();
		$controller->$action();
	}
	*/
 }


 /**
 *
 * @get the controller
 *
 * @access private
 *
 * @return void 
 *
 */
private function getController() {

	/*** get the route from the url ***/
	$route = (empty($_GET['rt'])) ? '' : $_GET['rt'];

	if (empty($route))
	{
		$route = 'index';
	}
	else
	{
		/*** get the parts of the route ***/
		$parts = explode('/', $route);
		$indexActions = array('login', 'logout', 'accounts', 'outpatient', 'inpatient', 'departments', 'beds', 'staff', 'infrastructure', 'security');
		if(in_array($parts[0], $indexActions, true))
		{
			$this->controller = "index";
			$this->action = $parts[0];
			if (isset($parts[1]) && $parts[1] !== '') {
				$count_args = count($parts);
				$k = 1;
				$args = array();
				for ($i = 1; $i < $count_args; $i++) {
					$args[$k++] = $parts[$i];
				}
				$this->args = $args;
			} else {
				$this->args = array();
			}
		}
		//Router page
		elseif($parts[0] == "gioi-thieu.html")
		{
			// echo $parts[0];
			$this->controller = "page";
			$this->action = "introduce";
			if(isset( $parts[1]))
			{
				$count_args = count($parts);
				$k = 1;
				$args = array();
				for($i = 1; $i < $count_args; $i++)
					$args[$k++] = $parts[$i]; 
				$this->args = $args;
			}
		}
		elseif($parts[0] == "dang-ky-lich-kham.html")
		{
			// echo $parts[0];
			$this->controller = "page";
			$this->action = "booking";
			if(isset( $parts[1]))
			{
				$count_args = count($parts);
				$k = 1;
				$args = array();
				for($i = 1; $i < $count_args; $i++)
					$args[$k++] = $parts[$i]; 
				$this->args = $args;
			}
		}
		elseif($parts[0] == "nha-thuoc.html")
		{
			// echo $parts[0];
			$this->controller = "page";
			$this->action = "products";
			if(isset( $parts[1]))
			{
				$count_args = count($parts);
				$k = 1;
				$args = array();
				for($i = 1; $i < $count_args; $i++)
					$args[$k++] = $parts[$i]; 
				$this->args = $args;
			}
		}elseif($parts[0] == "nha-thuoc")
		{
			// echo $parts[0];
			$this->controller = "page";
			$this->action = "products";
			if(isset( $parts[1]))
			{
				$count_args = count($parts);
				$k = 1;
				$args = array();
				for($i = 1; $i < $count_args; $i++)
					$args[$k++] = $parts[$i]; 
				$this->args = $args;
			}
		}elseif($parts[0] == "tin-tuc-su-kien.html")
		{
			// echo $parts[0];
			$this->controller = "page";
			$this->action = "events";
			if(isset( $parts[1]))
			{
				$count_args = count($parts);
				$k = 1;
				$args = array();
				for($i = 1; $i < $count_args; $i++)
					$args[$k++] = $parts[$i]; 
				$this->args = $args;
			}
		}elseif($parts[0] == "lich-cong-tac.html")
		{
			// echo $parts[0];
			$this->controller = "page";
			$this->action = "lichcongtac";
			if(isset( $parts[1]))
			{
				$count_args = count($parts);
				$k = 1;
				$args = array();
				for($i = 1; $i < $count_args; $i++)
					$args[$k++] = $parts[$i]; 
				$this->args = $args;
			}
		}elseif($parts[0] == "doi-ngu-bac-si.html")
		{
			// echo $parts[0];
			$this->controller = "page";
			$this->action = "doctors";
			if(isset( $parts[1]))
			{
				$count_args = count($parts);
				$k = 1;
				$args = array();
				for($i = 1; $i < $count_args; $i++)
					$args[$k++] = $parts[$i]; 
				$this->args = $args;
			}
		}elseif($parts[0] == "chuyen-khoa.html")
		{
			// echo $parts[0];
			$this->controller = "page";
			$this->action = "chuyenkhoa";
			if(isset( $parts[1]))
			{
				$count_args = count($parts);
				$k = 1;
				$args = array();
				for($i = 1; $i < $count_args; $i++)
					$args[$k++] = $parts[$i]; 
				$this->args = $args;
			}
		}elseif($parts[0] == "gioi-thieu")
		{
			// echo $parts[0];
			$this->controller = "page";
			$this->action = "introduce";
			if(isset( $parts[1]))
			{
				$count_args = count($parts);
				$k = 1;
				$args = array();
				for($i = 1; $i < $count_args; $i++)
					$args[$k++] = $parts[$i]; 
				$this->args = $args;
			}
		}
		else
		{
			$this->controller = $parts[0];
			if(isset($parts[1]))
			{
				$this->action = $parts[1];
			}
			if(isset( $parts[2]) && $parts[2] != "")
			{
				$count_args = count($parts);
				$k = 1;
				$args = array();
				for($i = 2; $i < $count_args; $i++)
					$args[$k++] = $parts[$i]; 
				$this->args = $args;
			}
			else
			{
				$this->args = array();
			}
		} 
	}

	if (empty($this->controller))
	{
		$this->controller = 'index';
	}

	/*** Get action ***/
	if (empty($this->action))
	{
		$this->action = 'index';
	}

	/*** set the file path ***/
	$this->file = $this->path .'/'. $this->controller . 'Controller.php';
}


}

?>
