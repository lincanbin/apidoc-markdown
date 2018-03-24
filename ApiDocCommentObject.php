<?php
/**
 * Created by PhpStorm.
 * User: lincanbin
 * Date: 2018/3/24
 * Time: 10:30
 */

class ApiDocCommentObject
{
    const REGEX_VAR = '([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)';
    const REGEX_ALL = '(.*)+';
    const parseRule = array(
        'api' => array(
            //https://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol#Request_methods
            'method' => '{(get|head|post|put|delete|trace|options|connnect|patch)}',
            'path'   => self::REGEX_ALL,
            'title'  => self::REGEX_ALL,
        ),
    );
    public $fileName;
    public $lineNumber;
    public $comment = '';
    public $description = '';
    public $params = array();

    public $parsedParams = array();


    public function __construct($comment, $fileName, $lineNumber)
    {
        $this->comment = $comment;
        $this->parseBlock();
    }

    /**
     * An alias to __call();
     * allows a better DSL
     *
     * @param string $paramName
     * @return mixed
     */
    public function __get($paramName)
    {
        return $this->$paramName();
    }

    /**
     * Checks if the param exists
     *
     * @param string $paramName
     * @return mixed
     */
    public function __call($paramName, $values = null)
    {
        if ($paramName == "description") {
            return $this->description;
        } else if (isset($this->params[$paramName])) {
            $params = $this->params[$paramName];

            if (count($params) == 1) {
                return $params[0];
            } else {
                return $params;
            }
        }

        return null;
    }

    /**
     * Parse each line in the docblock
     * and store the params in `$this->all_params`
     * and the rest in `$this->description`
     */
    private function parseBlock()
    {
        $currentParam = null;
        // split at each line
        foreach (preg_split("/(\r?\n)/", $this->comment) as $line) {

            // if starts with an asterisk
            if (preg_match('/^(?=\s+?\*[^\/])(.+)/', $line, $matches)) {

                $info = $matches[1];

                // remove wrapping whitespace
                $info = trim($info);

                // remove leading asterisk
                $info = preg_replace('/^(\*\s+?)/', '', $info);

                // if it doesn't start with an "@" symbol
                // then add to the description
                if ($info[0] !== "@") {
                    if (is_null($currentParam)) {
                        $this->description .= "\n\n$info";
                    } else {
                        $description = array_pop($this->params[$currentParam]) . "\n\n$info";
                        $this->params[$currentParam][] = $description;
                    }
                    continue;
                } else {
                    // get the name of the param
                    preg_match('/@(\w+)/', $info, $matches);
                    $paramName = $matches[1];
                    $currentParam = $paramName;
                    // remove the param from the string
                    $value = str_replace("@$paramName ", '', $info);

                    // if the param hasn't been added yet, create a key for it
                    if (!isset($this->params[$paramName])) {
                        $this->params[$paramName] = array();
                    }

                    // push the param value into place
                    $this->params[$paramName][] = $value;

                    continue;
                }
            }
        }
    }

    private function parseParams()
    {
        
    }
}