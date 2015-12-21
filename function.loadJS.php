<?php
/**
 * Smarty {loadJS} function plugin
 * 
 * Type:     function<br>
 * Name:     loadJS<br>
 * Purpose:  include JS files using script tags (merge option available)
 *
 * @author     Ole Koeckemann <ole.k@web.de>
 * @link       https://github.com/ole1986/smarty-plugin-loadJS
 * @package    Smarty
 * @subpackage PluginsFunction
 *
 * Smarty loadJS plugin
 * Copyright (C) 2015  Ole Koeckemann
 *
 * This program is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation, either version 3 of the License, or (at your option)
 * any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of  MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Singleton class 'LoadJS' used in Smarty template function 'loadJS'
 */
 class LoadJS {
     /**
      * instance of the current class
      */
     protected static $_instance = null;
     
     /**
      * 
      */
     public static $mergedFileFormat = "merged-%s.js";
     public static $mergedFile = null;
     
     /**
      * stores the location(s) of the js script files 
      */
     private $location;
     /**
      * contains the output folder (esspacially for thed merged file)
      */
     private $outputDir;
     /**
      * additional attributes being added to the <script> - tag
      */
     private $attributes;
     /**
      * enables concatenate
      */
     private $concatenate = false;
     
     private $scriptTag;
     
     /**
      * Constructor replacement used for singleton classes
      */
     public static function getInstance(){
         if(self::$_instance === null)
            self::$_instance = new self;
         return self::$_instance;
     }
     
     /**
      * check if the merged js file is available in the given folder
      */
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
    
    /**
     * set the javascript files
     * @params string|array
     */
    public function SetLocation($l) {
        $this->location = (isset($l)) ? $l : null;
    }
    
    /**
     * set the output path for merged files
     * @params string $d 
     */
    public function SetOutputDir($d) {
        $this->outputDir = (isset($d)) ? $d : null;
    }
    /**
     * enable or disable concatenate for the files defined in $this->location
     * @params bool $b
     */
    public function SetConcatenate($b) {
        $this->concatenate = $b;
    }
    /**
     * set additional attributes added into <script> - tag
     */
    public function SetAttributes($a){
         $this->attributes = (isset($a)) ? $a : '';
    }
    
    /**
     * remove the old merged-*.js docuement 
     */
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

/**
 * smarty {loadJS} function call using the following parameters
 *
 * location   : string or array value to defined javascript files
 * output     : the output path being used to store the merged file
 * attr       : additinal HTMLÖ attributes for every <script> - tag (example: "async='true'")
 * concatenate: enable of disable merged js
 */
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
