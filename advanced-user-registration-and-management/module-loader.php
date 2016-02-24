<?php
// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

class LR_Modules_Loader {

    /**
     * Constractor call on class call
     */
    function __construct() {
        $this->include_submodule();
    }

    /**
     * Including modules files
     */
    function include_submodule() {
        $dirs = glob( LR_ROOT_DIR . '*', GLOB_ONLYDIR );
        $loadModules = array();
        foreach ($dirs as $dir) {
            $loadModules[] = json_decode( $this->get_config_file( $dir ) );
        }

        usort( $loadModules, array( $this, 'custom_sort' ) );

        foreach ( $loadModules as $loadModule ) {
            if( ! empty( $loadModule ) ){
                include_once LR_ROOT_DIR . $loadModule->name . '/' . $loadModule->load;
            }
        }
    }

    /**
     * Sort module by weight
     * 
     * @param type $a
     * @param type $b
     * @return type
     */
    function custom_sort($a, $b) {
        return $a->weight < $b->weight ? -1 : 1; //Compare the scores
    }

    /**
     * Get module.config file content
     * @param type $dirName
     * @return type
     */
    function get_config_file($dirName) {
        $config = file_get_contents($dirName . '/module.config');
        if($config != false){
            return $config;
        }
        return '';
    }
}
