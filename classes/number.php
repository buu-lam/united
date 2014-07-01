<?php
/**
 * Description of app
 *
 * @author b.le
 */
class number {
    
    public $value;
    
    public function __construct($value = 0) {
        $this->value = $value;
    }
    /**
     * @unit(1, equals: 1)
     * @unit(params: [@class(number, __construct: [2])], equals: 2)
     * @unit(
     *      __construct: [5], 
     *      1, 
     *      equals: 6
     * )
     * @unit(
     *      params: [@class(number, __construct: [2])],
     *      __construct: 5,  
     *      equals: 7
     * )
     * @return string
     */
    public function add($a) {
        return $this->value + (($a instanceof number) ? $a->value : $a);
    }
    
    /**
     * @unit(6, 3, equals: 2)
     * @unit(0, 1, equals: 0)
     * @unit(1, 0, throws: "can't divide by 0")
     */
    public function divide($a, $b) {
        if ($b == 0) {
            throw new Exception("can't divide by 0");
        }
        return $a / $b;
    }
    
    
    public function getOffset() {
        return $this->value;
    }
    
    /**
     * @param string $name
     * @param mixed $value
     */
    public function setProp($name, $value) {
        $this->$name = $value;
    }
}
