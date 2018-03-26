<?php
require_once __DIR__ . '/ApiDocCommentObject.php';

/**
 *
 */
class ApiDocGenerator
{
    private $input;
    private $output;
    private $template;
    /**
     * @var RecursiveIteratorIterator $fileIteratorfileIterator
     */
    private $fileIterator;

    public function __construct($input, $output, $template)
    {
        $this->input = $input;
        $this->output = $output;
        $this->template = $template;
        $this->fileIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->input));

        foreach ($this->fileIterator as $file) {
            /**
             * @var SplFileInfo $file
             */
            if ($file->isDir() || $file->getExtension() !== 'php') {
                continue;
            }
            $this->parseFile($file);
        }
    }

    private function parseFile(SplFileInfo $splFileInfo)
    {
        $fileSize = $splFileInfo->getSize();
        $fileName = mb_substr($splFileInfo->getPathname(), mb_strlen($this->input) - 1);
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
        //var_dump($apiDocCommentObject);
        var_dump($apiDocCommentObject->parsedParams);
    }
}