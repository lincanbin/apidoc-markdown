<?php
require_once __DIR__ . '/ApiDocCommentObject.php';
require_once __DIR__ . '/ApiDocConfig.php';

/**
 *
 */
class ApiDocGenerator
{
    private static $supportExtension = ['php', 'js', 'java', 'go'];
    private static $ignoreList = [
        'node_modules'
    ];
    private $input;
    private $output;
    private $template;
    protected $config;
    private $apiDefineList = array();
    private $apiList = array();
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
            foreach ($this->config->order as $item) {
                $this->apiList[$item] = array();
            }
            foreach ($this->fileIterator as $file) {
                /**
                 * @var SplFileInfo $file
                 */
                if (
                    0 < count(array_intersect(array_map('strtolower', preg_split("/(\|\/)/", $file->getPathname())), self::$ignoreList))
                    || $file->isDir()
                    || !in_array($file->getExtension(), self::$supportExtension)
                ) {
                    continue;
                }
                $this->parseFile($file);
            }
            // Save api define

            foreach ($this->apiDefineList as $apiDoc) {
                /**
                 * @var ApiDocCommentObject $apiDoc
                 */
                $this->saveDoc($apiDoc);
            }
            // Merge apiDefine into apiDoc
            foreach ($this->apiList as $group) {
                foreach ($group as $apiDoc) {
                    /**
                     * @var ApiDocCommentObject $apiDoc
                     */
                    $apiDoc->mergeApiDefine($this->apiDefineList);
                }
            }
            // Save api Doc
            foreach ($this->apiList as $group) {
                foreach ($group as $apiDoc) {
                    /**
                     * @var ApiDocCommentObject $apiDoc
                     */
                    echo $apiDoc->fileName . "\n";
                    $this->saveDoc($apiDoc);
                }
            }
            $this->saveIndex();
        } catch (Exception $exception) {
            echo $exception->getMessage();
        }
        // var_dump($this->config);
    }

    private function parseFile(SplFileInfo $splFileInfo)
    {
        $fileSize = $splFileInfo->getSize();
        $fileName = str_replace('\\', '/', mb_substr($splFileInfo->getPathname(), mb_strlen($this->input) - 1));
        if ($fileSize === 0 || $fileSize === false) {
            return false;
        }
        // echo $splFileInfo->getPathname() . "\n";
        $splFileObject = $splFileInfo->openFile('r');
        $splFileContent = $splFileObject->fread($fileSize);
        switch ($splFileInfo->getExtension()) {
            case 'php':
                $docComments = array_filter(token_get_all($splFileContent), function ($entry) {
                    return is_array($entry) && $entry[0] == T_DOC_COMMENT;
                });
                break;
            default:
                preg_match_all("#\/\*.*?\*\/#s", $splFileContent, $temp);
                $docComments = empty($temp) ? [] : $temp[0];
        }
        foreach ($docComments as $docComment) {
            if (is_array($docComment)) {
                $this->parseComment($fileName, $docComment[1], $docComment[2]);
            } else if (is_string($docComment)) {
                $this->parseComment($fileName, $docComment);
            }
        }
        return true;
    }

    private function parseComment($fileName, $comment, $lineNumber = 1)
    {
        // CRLF to LF
        $comment = str_replace("\r\n", "\n", mb_substr($comment, 2, mb_strlen($comment) - 2));
        // echo $fileName . "\n";
        //var_dump($comment);
        $apiDocCommentObject = new ApiDocCommentObject($comment, $fileName, $lineNumber);
        if ($apiDocCommentObject->type === 'api') {
            if (!isset($this->apiList[$apiDocCommentObject->apiGroup['name']])) {
                $this->apiList[$apiDocCommentObject->apiGroup['name']] = array();
            }
            $this->apiList[$apiDocCommentObject->apiGroup['name']][$apiDocCommentObject->apiName['name']] = $apiDocCommentObject;
        } elseif ($apiDocCommentObject->type === 'define') {
            $this->apiDefineList[$apiDocCommentObject->apiDefine['name']] = $apiDocCommentObject;
        } else {
            return false;
        }
        //$this->saveDoc($apiDocCommentObject);
        return true;
    }

    private function saveDoc(ApiDocCommentObject $apiDoc)
    {
        //var_dump($apiDocCommentObject);
        // var_dump($apiDoc->parsedParams);
        if ($apiDoc->type === 'api') {
            if (empty($apiDoc->apiGroup['name']) || empty($apiDoc->apiName['name'])) {
                return false;
            }
            $fileName = 'apidoc/' . $apiDoc->apiGroup['name'] . '/' . $apiDoc->apiName['name'] . '.md';
        } elseif ($apiDoc->type === 'define') {
            $fileName = 'apidoc/define/' . $apiDoc->apiDefine['name'] . '.md';
        } else {
            return false;
        }
        echo $fileName . "\n";
        // var_dump($fileName);
        ob_start();
        try {
            include $this->template . 'apiTemplate.php';
            $content = ob_get_contents();
        } catch (Exception $ex) {
            $content = '';
        }
        ob_end_clean();
        $this->saveFile($fileName, $content);
        return true;
    }

    private function saveIndex()
    {
        $fileName = 'README.md';
        ob_start();
        try {
            include $this->template . 'indexTemplate.php';
            $content = ob_get_contents();
        } catch (Exception $ex) {
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