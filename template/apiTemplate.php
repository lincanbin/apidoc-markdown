<?php
/**
 * @var ApiDocGenerator $this
 * @var ApiDocCommentObject $apidoc
 */

if ($apidoc->type === 'api'):
    // Title
?>
# <?php
    echo (!is_null($apidoc->apiDeprecated) ? '~~' : '') . (!is_null($apidoc->api['title']) ? $apidoc->api['title'] : $apidoc->apiName['name']) . (!is_null($apidoc->apiDeprecated) ? '~~' : '');
    ?> ([<?php echo $apidoc->apiGroup['name']; ?>](../../README.md#<?php echo $apidoc->apiGroup['name']; ?>))
<?php
elseif ($apidoc->type === 'define'):
    // apiDefine
?>
# <?php echo (!is_null($apidoc->apiDeprecated) ? '~~' : '') . $apidoc->apiDefine['name'] . (!is_null($apidoc->apiDeprecated) ? '~~' : ''); ?> ([<?php echo $this->config->name ?>](../../README.md#define))
<?php
endif;
?>
<?php
// apiDescription
if (!is_null($apidoc->apiDescription['text'])) :
    echo $apidoc->apiDescription['text'] . "\n";
    echo !is_null($apidoc->apiDeprecated['text']) ? " ### Deprecated \n\n> " . str_replace("\n", "\n> ", $apidoc->apiDeprecated['text']) . "\n\n" : '';
endif;
if ($apidoc->type === 'api'):
?>

`<?php echo strtoupper(substr($apidoc->api['method'], 1, -1)); ?>`

```
<?php echo $this->config->url . $apidoc->api['path']; ?>

```
<?php
endif;

// apiSampleRequest
if ($apidoc->type === 'api' && (!is_null($apidoc->apiSampleRequest) || $this->config->sampleUrl !== '')):
    ?>
##### Sample Request

```
<?php
    if (!is_null($apidoc->apiSampleRequest)) {
        echo $apidoc->apiSampleRequest['url'];
    } else {
        echo $this->config->sampleUrl . $apidoc->api['path'];
    }
?>

```

<?php
endif;

// apiExample
if (!is_null($apidoc->apiExample)):
    ?>
##### Example for usage of an API method
<?php echo $apidoc->apiExample['title'] ?: ''; ?>

```<?php echo isset($apidoc->apiExample['type']) ? substr($apidoc->apiExample['type'], 1, -1) : ''; ?>
<?php echo $apidoc->apiExample['example']; ?>
```
<?php
endif;

// apiParam
if (!is_null($apidoc->apiParam)):
?>
###  Parameters
Key|Value Type|Required|Default Value|Description
---|---|---|---|---
<?php
foreach ($apidoc->apiParam as $param):
    $parsedParam = $apidoc->parseField($param);
?>
<?php
    echo $parsedParam['key'];
?>|<?php
    echo $parsedParam['type'];
?>|<?php
    echo $parsedParam['required'];
?>|<?php
    echo $parsedParam['default'];
?>|<?php
    echo $parsedParam['description'];
?>

<?php
endforeach;?>

<?php
endif;
?>
<?php
// apiParamExample
if (!is_null($apidoc->apiParamExample)):
?>
### Parameter Request Example
<?php echo $apidoc->apiParamExample['title'] ?: ''; ?>

```<?php echo isset($apidoc->apiParamExample['type']) ? substr($apidoc->apiParamExample['type'], 1, -1) : ''; ?>
<?php echo $apidoc->apiParamExample['example']; ?>
```
<?php
endif;
// apiSuccess
if (!is_null($apidoc->apiSuccess)):
    ?>

###  Success return Parameter
Key|Value Type|Description
---|---|---
    <?php
    foreach ($apidoc->apiSuccess as $param):
        $parsedParam = $apidoc->parseField($param);
        ?>
<?php
echo $parsedParam['key'];
?>|<?php
echo $parsedParam['type'];
?>|<?php
echo $parsedParam['description'];
?>

    <?php
    endforeach;?>

<?php
endif;
?>
<?php
// apiSuccessExample
if (!is_null($apidoc->apiSuccessExample)):
    ?>
### Example of a success return message
<?php echo $apidoc->apiSuccessExample['title'] ?: ''; ?>

```<?php echo isset($apidoc->apiSuccessExample['type']) ? substr($apidoc->apiSuccessExample['type'], 1, -1) : ''; ?>
<?php echo $apidoc->apiSuccessExample['example']; ?>
```
<?php
endif;
?>