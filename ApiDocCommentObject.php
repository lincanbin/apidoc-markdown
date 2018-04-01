<?php
/**
 * Created by PhpStorm.
 * User: lincanbin
 * Date: 2018/3/24
 * Time: 10:30
 */

/**
 * Class ApiDocCommentObject
 * @property array api
 * @property array apiDefine
 * @property array apiDeprecated
 * @property array apiDescription
 * @property array apiError
 * @property array apiErrorExample
 * @property array apiExample
 * @property array apiGroup
 * @property array apiHeader
 * @property array apiHeaderExample
 * @property array apiIgnore
 * @property array apiName
 * @property array apiParam
 * @property array apiParamExample
 * @property array apiPermission
 * @property array apiPrivate
 * @property array apiSampleRequest
 * @property array apiSuccess
 * @property array apiSuccessExample
 * @property array apiUse
 * @property array apiVersion
 *
 */
class ApiDocCommentObject
{
    const REGEX_VAR = '([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)';
    const REGEX_HTTP_METHOD = '{(get|head|post|put|delete|trace|options|connnect|patch)}';
    const REGEX_TYPE = '{' . self::REGEX_VAR . '(\[\])?}';
    const REGEX_GROUP = '\(' . self::REGEX_VAR . '\)';
    const REGEX_ALL = '(.*)+';
    const REGEX_ALL_WITH_LINE_BREAK = '(.*|\n)+';
    const REGEX_FIELD_WITH_DEFAULT = '(\[)?' . self::REGEX_VAR . '({(\d+)?(\.\.|-)(\d+)?})?(=' . self::REGEX_VAR . ')?(\])?';
    const parseExampleRule = array(
        'type'    => self::REGEX_TYPE,
        'title'   => self::REGEX_ALL,
        'example' => self::REGEX_ALL_WITH_LINE_BREAK,
    );
    const multipleParams = array('apiError', 'apiHeader', 'apiParam', 'apiSuccess');
    const parseRules = array(
        'api'               => array(
            //https://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol#Request_methods
            'method' => self::REGEX_HTTP_METHOD,
            'path'   => self::REGEX_ALL,
            'title'  => self::REGEX_ALL,
        ),
        'apiDefine'         => array(
            'name'        => self::REGEX_VAR,
            'title'       => self::REGEX_ALL,//TODO: To be repaired
            'description' => self::REGEX_ALL_WITH_LINE_BREAK,
        ),
        'apiDeprecated'     => array(
            'text' => self::REGEX_ALL_WITH_LINE_BREAK,
        ),
        'apiDescription'    => array(
            'text' => self::REGEX_ALL_WITH_LINE_BREAK,
        ),
        'apiError'          => array(
            'group'       => self::REGEX_VAR,
            'type'        => self::REGEX_TYPE,
            'field'       => self::REGEX_VAR,
            'description' => self::REGEX_ALL_WITH_LINE_BREAK,
        ),
        'apiErrorExample'   => self::parseExampleRule,
        'apiExample'        => self::parseExampleRule,
        'apiGroup'          => array(
            'name' => self::REGEX_VAR,
        ),
        'apiHeader'         => array(
            'group'       => self::REGEX_GROUP,
            'type'        => self::REGEX_TYPE,
            'field'       => self::REGEX_FIELD_WITH_DEFAULT,
            'description' => self::REGEX_ALL,
        ),
        'apiHeaderExample'  => self::parseExampleRule,
        'apiIgnore'         => array(
            'hint' => self::REGEX_ALL_WITH_LINE_BREAK,
        ),
        'apiName'           => array(
            'name' => self::REGEX_ALL,
        ),
        'apiParam'          => array(
            'group'       => self::REGEX_GROUP,
            'type'        => self::REGEX_TYPE,
            'field'       => self::REGEX_FIELD_WITH_DEFAULT,
            'description' => self::REGEX_ALL_WITH_LINE_BREAK,
        ),
        'apiParamExample'   => self::parseExampleRule,
        'apiPermission'     => array(
            'name' => self::REGEX_ALL,
        ),
        'apiPrivate'        => array(),
        'apiSampleRequest ' => array(
            'url' => self::REGEX_ALL,
        ),
        'apiSuccess'        => array(
            'group'       => self::REGEX_GROUP,
            'type'        => self::REGEX_TYPE,
            'field'       => self::REGEX_FIELD_WITH_DEFAULT,
            'description' => self::REGEX_ALL_WITH_LINE_BREAK,
        ),
        'apiSuccessExample' => self::parseExampleRule,
        'apiUse'            => array(
            'name' => self::REGEX_ALL,
        ),
        'apiVersion'        => array(
            'version' => self::REGEX_ALL,
        ),
    );

    public $fileName;
    public $lineNumber;
    public $comment = '';
    public $description = '';
    public $params = array();

    public $type = null;
    public $parsedParams = array();

    /**
     * ApiDocCommentObject constructor.
     * @param $comment
     * @param $fileName
     * @param $lineNumber
     */
    public function __construct($comment, $fileName, $lineNumber)
    {
        $this->comment = $comment;
        $this->parseBlock();
        $this->parseParams();
        if ($this->api !== null && $this->apiGroup !== null && $this->apiName !== null) {
            $this->type = 'api';
        }
        if ($this->apiDefine !== null && isset($this->apiDefine['name']) && $this->apiDefine['name'] !== '') {
            $this->type = 'define';
        }
        if ($this->apiIgnore !== null) {//$this->apiPrivate === null || $this->apiIgnore !==null
            $this->type = null;
        }
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
     * @param $paramName
     * @param null $values
     * @return mixed|null|string
     */
    public function __call($paramName, $values = null)
    {
        if ($paramName == "description") {
            return $this->description;
        } else if (isset($this->parsedParams[$paramName])) {
            $params = $this->parsedParams[$paramName];

            if (!in_array($paramName, self::multipleParams) && count($params) > 0) {
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

    private function explode($paramName, $paramString)
    {
        if (self::parseRules[$paramName] === self::parseExampleRule || $paramName === 'apiDefine') {
            $tempLines = explode("\n", $paramString, 2);
            $result = array_filter(explode(' ', $tempLines[0]), function ($item) {
                return $item !== '';
            });
            if (isset($tempLines[1])) {
                $result[] = $tempLines[1] . "\n";
            }
        } elseif ($paramName === 'apiDescription') {
            $result = array($paramString);
        } else {
            $result = array_filter(explode(' ', $paramString), function ($item) {
                return $item !== '';
            });
        }
        return $result;
    }

    private function parseParams()
    {
        foreach ($this->params as $paramName => $params) {
            if (!isset(self::parseRules[$paramName])) {
                continue;
            }
            // if the param hasn't been added yet, create a key for it
            if (!isset($this->parsedParams[$paramName])) {
                $this->parsedParams[$paramName] = array();
            }

            foreach ($params as $paramString) {
                if ($paramString === "") {
                    continue;
                }
                $parseRulesKeys = array_keys($this::parseRules[$paramName]);
                $parseRuleKey = null;
                $parseRuleKey = array_shift($parseRulesKeys) ?: $parseRuleKey;
                $result = array();
                $options = $this->explode($paramName, $paramString);
                foreach ($options as $option) {
                    if ($parseRuleKey === null) {
                        break;
                    }
                    $continue = true;
                    do {
                        if (preg_match('@^' . self::parseRules[$paramName][$parseRuleKey] . '$@i', $option) > 0) {
                            $result[$parseRuleKey] = isset($result[$parseRuleKey])
                                ? $result[$parseRuleKey] . ' ' . $option
                                : $option;
                            $continue = false;
                        }
                        $parseRuleKey = array_shift($parseRulesKeys) ?: $parseRuleKey;
                        if (count($parseRulesKeys) === 0) {
                            $continue = false;
                        }
                    } while ($continue);
                }
                $this->parsedParams[$paramName][] = $result;
            }
        }
    }

    public function parseField($param)
    {
        $result = array(
            'key'         => '',
            'type'        => '',
            'required'    => '√',
            'default'     => '',
            'description' => ''
        );
        if (substr($param['field'], 0, 1) === '[' && substr($param['field'], -1, 1) === ']')
        {
            $result['required'] = '×';
            $param['field'] = substr($param['field'], 1, -1);
        }
        $temp = explode('=', $param['field'], 2);
        $result['key'] = $temp[0];
        if (isset($temp[1])) {
            $result['default'] = $temp[1];
        }
        if (isset($param['description'])) {
            $result['description'] = $param['description'];
        }
        if (isset($param['type'])) {
            $result['type'] = substr($param['type'], 1, -1);
        }
        return $result;
    }
}