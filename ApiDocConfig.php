<?php
/**
 * Created by PhpStorm.
 * User: lincanbin
 * Date: 2018/3/29
 * Time: 15:48
 */

class ApiDocConfig
{
    public $name;
    public $version;
    public $description;
    public $title;
    public $url;
    public $sampleUrl;
    public $header = array(
        'title'    => '',
        'filename' => ''
    );
    public $footer = array(
        'title'    => '',
        'filename' => ''
    );
    public $order = array();

    /**
     * @param $inputPath
     * @throws Exception
     */
    public function load($inputPath)
    {
        $configFile = new SplFileInfo($inputPath . 'apidoc.json');
        var_dump($inputPath . 'apidoc.json');
        if ($configFile->isFile()) {
            $config = json_decode($configFile->openFile('r')->fread($configFile->getSize()), true);
        } else {
            throw new Exception('apidoc.json not found. ');
        }
        if ($config === false) {
            throw new Exception('Invalid apidoc.json. ');
        }
        foreach ($config as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}