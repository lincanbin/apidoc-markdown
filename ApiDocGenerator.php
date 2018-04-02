<?php
require_once __DIR__ . '/ApiDocCommentObject.php';
require_once __DIR__ . '/ApiDocConfig.php';

/**
 *
 */
class ApiDocGenerator
{
    private $input;
    private $output;
    private $template;
    protected $config;

    private $apiList;
    /**
     * @var RecursiveIteratorIterator $fileIteratorfileIterator
     */
    private $fileIterator;

    public function __construct($input, $output, $template)
    {
        try {
            $this->input = $input;
            $this->output = $output;
            $this->template = $template;
            $this->fileIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->input));
            $this->config = new ApiDocConfig();
            $this->config->load($this->input);

            foreach ($this->fileIterator as $file) {
                /**
                 * @var SplFileInfo $file
                 */
                if ($file->isDir() || $file->getExtension() !== 'php') {
                    continue;
                }
                $this->parseFile($file);
            }
        } catch (Exception $exception) {
            echo $exception->getMessage();
        }
        var_dump($this->config);
    }

    private function parseFile(SplFileInfo $splFileInfo)
    {
        $fileSize = $splFileInfo->getSize();
        $fileName = str_replace('\\', '/', mb_substr($splFileInfo->getPathname(), mb_strlen($this->input) - 1));
        if ($fileSize === 0 || $fileSize === false) {
            return false;
        }
        echo $splFileInfo->getPathname() . "\n";
        $splFileObject = $splFileInfo->openFile('r');
        $docComments = array_filter(token_get_all($splFileObject->fread($fileSize)), function ($entry) {
            return is_array($entry) && $entry[0] == T_DOC_COMMENT;
        });
        foreach ($docComments as $docComment) {
            $this->parseComment($docComment, $fileName);
        }
        return true;
    }

    private function parseComment($comment, $fileName)
    {
        // CRLF to LF
        $comment[1] = str_replace("\r\n", "\n", mb_substr($comment[1], 2, mb_strlen($comment[1]) - 2));
        echo $fileName . "\n";
        //var_dump($comment);
        $apiDocCommentObject = new ApiDocCommentObject($comment[1], $fileName, $comment[2]);

        $this->saveDoc($apiDocCommentObject);
    }

    private function saveDoc(ApiDocCommentObject $apidoc)
    {
        //var_dump($apiDocCommentObject);
        var_dump($apidoc->parsedParams);
        if ($apidoc->type === 'api') {
            $fileName = 'apidoc/' . $apidoc->apiGroup['name'] . '/' . $apidoc->apiName['name'] . '.md';
        } elseif ($apidoc->type === 'define') {
            $fileName = 'apidoc/define/' . $apidoc->apiDefine['name'] . '.md';
        } else {
            return false;
        }
        var_dump($fileName);
        ob_start();
        try{
            include $this->template . 'apiTemplate.php';
            $content = ob_get_contents();
        } catch (Exception $ex){
            $content = '';
        }
        ob_end_clean();
        $this->saveFile($fileName, $content);
        return true;
    }

    private function saveFile($fileName, $content)
    {
        if (empty($content)) {
            return false;
        }
        $fileName = $this->output . $fileName;
        $path = dirname($fileName);
        if (!is_dir($path)) {
            mkdir($path, 0775, true);
        }
        $handle = fopen($fileName, 'w');
        $writeResult = fwrite($handle, $content);
        fclose($handle);
        return $writeResult;
    }
}