<?php

/**
 * Description of United
 *
 * @author b.le
 */
class united {

    const keyword = 'unit';

    private $class_name;
    private $method_name;
    private static $assertions = array(
        'equals', 'differs', 
        'instance',
        'same',
        'has',
        
    );
    /**
     * 
     * @param string $class_name
     */
    public function testClass($class_name) {
        $this->class_name = $class_name;
        $reader = new \Phalcon\Annotations\Adapter\Memory();
        
        $reflector = $reader->get($this->class_name);
        //var_export($reflector->getReflectionData());
        foreach ($reflector->getMethodsAnnotations() as $method => $annotations) {
            $this->testMethod($method, $annotations);
        }
    }

    /**
     * 
     * @param string $method_name
     * @param Phalcon\Annotations\Collection $annotations
     */
    public function testMethod($method_name, Phalcon\Annotations\Collection $annotations) {
        $this->method_name = $method_name;
        foreach ($annotations as $annotation) {
            if ($annotation->getName() == self::keyword) {
                $this->testCase($annotation);
            }
        }
    }

    public function testCase(Phalcon\Annotations\Annotation $annotation) {
        $arguments = $annotation->getArguments();
        echo ($this->buildTest($arguments) ? 'I' : 'O'), ' ',
        $this->class_name, '::',
        $this->method_name, ' ',
        json_encode(self::param($arguments)), "\n"
        ;
    }

    public function buildTest($arguments) {
        $object = self::instanciate($this->class_name, $arguments);

        if (($throws = self::getProp($arguments, 'throws', false))) {
            try {
                $arguments_nothrows = $arguments;
                unset($arguments_nothrows['throws']);
                $this->buildTest($arguments_nothrows);
            } catch (Exception $ex) {
                return ($ex->getMessage() == $throws);
            }
        } else {
            $params = array_map(
                array(__CLASS__, 'param'),
                self::getProp($arguments, 'params', array())
            );
            
            if (!$params) {
                $rank = 0;
                while(isset($arguments[$rank])) {
                    $params[] = $arguments[$rank];
                    unset($arguments[$rank]);
                    $rank++;
                }
            }
            unset($arguments['params']);
            $actual = call_user_func_array(array($object, $this->method_name), $params);
            
            $has_assert = false;
            foreach($arguments as $assertion => $expected) {
                if (method_exists(__CLASS__, $method = "assert$assertion")) {
                    $has_assert = true;
                    if (! $this->$method($actual, $expected)) {
                        return false;
                    }
                }
            }
            
            return $has_assert;
        }
    }

    public function assertEquals($actual, $expected) {
        return $actual == $expected;
    }
    
    public function assertDiffers($actual, $expected) {
        return $actual != $expected;
    }
    
    public function assertInstance($actual, $expected) {
        return $actual instanceof $expected;
    }
    
    public static function param($param) {
        if (! $param instanceof \Phalcon\Annotations\Annotation) {
            return $param;
        } else {
            $name = $param->getName();
            if ($name == 'class') {
                return self::instanciate($param->getArgument(0), self::param($param->getArguments()));
            }
        }
    }

    public static function instanciate($class_name, $arguments) {
        $construct = array_map(
            array(__CLASS__, 'param'),
            (array) self::getProp($arguments, '__construct', array())
        );
        
        switch (count($construct)) {
            case 0:
                $instance = new $class_name;
                break;
            case 1:
                $instance = new $class_name($construct[0]);
                break;
            case 2:
                $instance = new $class_name($construct[0], $construct[1]);
                break;
            case 3:
                $instance = new $class_name($construct[0], $construct[1], $construct[2]);
                break;
            default:
                throw new Exception("too many arguments");
        }
        
        foreach(self::getProp($arguments, 'methods', array()) as $method => $params) {
            call_user_func_array(array($instance, $method), $params);
        }
        
        return $instance;
    }

    private static function getProp($array, $prop, $default = null) {
        return isset($array[$prop]) ? $array[$prop] : $default;
    }

    public function testFunction($func_name) {
        $reader = new \Phalcon\Annotations\Adapter\Memory();
        $reflector = $reader->get($func_name);
        //var_dump($reflector);
        echo 'ooo';
    }
}
