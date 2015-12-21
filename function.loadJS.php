<?php
/**
 * Smarty loadJS plugin
 *
 * @package    Smarty
 * @subpackage PluginsFunction
 */

/**
 * Smarty {loadJS} function plugin
 * Type:     function<br>
 * Name:     loadJS<br>
 * Purpose:  include JS files using script tags (merge option available)
 *
 * @author Ole Koeckemann <ole.k@web.de>
 * @link   
 *
 * 
 * @param array                    $params   parameters
 * @param Smarty_Internal_Template $template template object
 *
 * @return string|null
 */
 class LoadJS {
     protected static $_instance = null;
     
     public static $mergedFileFormat = "merged-%s.js";
     public static $mergedFile = null;
     
     private $location;
     private $outputDir;
     private $attributes;
     private $concatenate = false;
     
     private $scriptTag;
     
     public static function getInstance(){
         if(self::$_instance === null)
            self::$_instance = new self;
         return self::$_instance;
     }
     
     public static function IsMerged($outputDir){
         $found = glob($outputDir . sprintf(self::$mergedFileFormat, '*') );
         if(count($found) > 0)
         {
             self::$mergedFile = $found[0];
             return true;
         }
         return false;
     }
    /**
     * disable cloning the object
     */
    protected function __clone() {}
    /**
     * disable direct constructor call
     */
    protected function __construct() {}
    
    public function SetLocation($l) {
        $this->location = (isset($l)) ? $l : null;
    }
    
    public function SetOutputDir($d) {
        $this->outputDir = (isset($d)) ? $d : null;
    }
    
    public function SetConcatenate($b) {
        $this->concatenate = $b;
    }
    
    public function SetAttributes($a){
         $this->attributes = (isset($a)) ? $a : '';
    }
    
    private function cleanMergedFiles(){
        // remove all old merged files
        foreach(glob($this->outputDir . 'merged-*.js') as $f) {
            
            if(!empty(self::$mergedFile) && $f == self::$mergedFile) continue;
            unlink($f);
        }
    }
    
    public function Load(){
        if(empty($this->location)) throw new Exception('Missing location. Please call SetLocation([...]) first');
        if(empty($this->location)) throw new Exception('Missing OutputDir. Please call SetOutput([...]) first');
        
        $this->scriptTag = '';
        
        if(is_array($this->location))
            $files = $this->location;
        else
            $files = glob($this->location);
        
        $filesChanged = '';
        
        foreach($files as $f) {
            if($this->outputDir && $this->concatenate)
                $filesChanged .= (string)filemtime($f);
            else
                $this->scriptTag .= "\n\t\t<script src=\"{$f}\" type=\"text/javascript\" {$this->attributes}></script>";
        }
        
        if($this->outputDir && $this->concatenate) {
            $shash = sha1($filesChanged);
            self::$mergedFile = $this->outputDir . sprintf(self::$mergedFileFormat, $shash);
            
            if(!file_exists(self::$mergedFile)) {
                $fp = fopen(self::$mergedFile, 'w');
                foreach($files as $f) {
                    fwrite($fp, file_get_contents($f));
                }
                fclose($fp);
            }
            $this->scriptTag = "\n\t\t<script src=\"".self::$mergedFile."\" type=\"text/javascript\" {$this->attributes}></script>";
        }
        
        $this->cleanMergedFiles();
        
        return $this->scriptTag;
    }
 }
 
function smarty_function_loadJS($params, $template)
{
    $loadJS = LoadJS::getInstance();
    
    $l = (isset($params['location'])) ? $params['location'] : null;
    $o = (isset($params['output'])) ? $params['output'] : null;
    $a = (isset($params['attr'])) ? $params['attr'] : null;
    $c = (isset($params['concatenate'])) ? $params['concatenate'] : null;
    
    $loadJS->SetLocation($l);
    $loadJS->SetOutputDir($o);
    $loadJS->SetAttributes($a);
    $loadJS->SetConcatenate($c);
    
    return $loadJS->Load();
}
