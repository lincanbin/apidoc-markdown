<?php
/**
 * @var ApiDocGenerator $this
 * @var ApiDocCommentObject $apiDoc
 */

if ($apiDoc->type === 'api'):
    // Title
?>
# <?php
    echo (!is_null($apiDoc->apiDeprecated) ? '~~' : '') . (!is_null($apiDoc->api['title']) ? $apiDoc->api['title'] : $apiDoc->apiName['name']) . (!is_null($apiDoc->apiDeprecated) ? '~~' : '');
    ?> ([<?php echo $apiDoc->apiGroup['name']; ?>](../../README.md#<?php echo urlencode(strtolower($apiDoc->apiGroup['name'])); ?>))
<?php
elseif ($apiDoc->type === 'define'):
    // apiDefine
?>
# <?php echo (!is_null($apiDoc->apiDeprecated) ? '~~' : '') . $apiDoc->apiDefine['name'] . (!is_null($apiDoc->apiDeprecated) ? '~~' : ''); ?> ([<?php echo $this->config->name ?>](../../README.md#define))
<?php
endif;
?>
<?php
// apiDescription
if (!is_null($apiDoc->apiDescription['text'])) :
    echo $apiDoc->apiDescription['text'] . "\n\n";
    echo !is_null($apiDoc->apiDeprecated['text']) ? " ### Deprecated \n\n> " . str_replace("\n", "\n> ", $apiDoc->apiDeprecated['text']) . "\n\n" : '';
endif;
if ($apiDoc->type === 'api'):
?>

`<?php echo strtoupper(substr($apiDoc->api['method'], 1, -1)); ?>`

```
<?php echo $this->config->url . $apiDoc->api['path']; ?>

```
<?php
endif;

// apiSampleRequest
if ($apiDoc->type === 'api' && (!is_null($apiDoc->apiSampleRequest) || $this->config->sampleUrl !== '')):
    ?>
##### Sample Request

```
<?php
    if (!is_null($apiDoc->apiSampleRequest)) {
        echo $apiDoc->apiSampleRequest['url'];
    } else {
        echo $this->config->sampleUrl . $apiDoc->api['path'];
    }
?>

```

<?php
endif;

// apiExample
if (!is_null($apiDoc->apiExample)):
    ?>
##### Example for usage of an API method
<?php echo $apiDoc->apiExample['title'] ?: ''; ?>

```<?php echo isset($apiDoc->apiExample['type']) ? substr($apiDoc->apiExample['type'], 1, -1) : ''; ?>
<?php echo $apiDoc->apiExample['example']; ?>
```
<?php
endif;

// apiParam
if (!is_null($apiDoc->apiParam)):
?>
###  Parameters
Key|Value Type|Required|Default Value|Description
---|---|---|---|---
<?php
foreach ($apiDoc->apiParam as $param):
    $parsedParam = $apiDoc->parseField($param);
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
if (!is_null($apiDoc->apiParamExample)):
?>
### Parameter Request Example
<?php echo $apiDoc->apiParamExample['title'] ?: ''; ?>

```<?php echo isset($apiDoc->apiParamExample['type']) ? substr($apiDoc->apiParamExample['type'], 1, -1) : ''; ?>
<?php echo $apiDoc->apiParamExample['example']; ?>
```
<?php
endif;

// apiHeader
if (!is_null($apiDoc->apiHeader)):
    ?>
### Parameters passed to you API-Header
Key|Value Type|Required|Default Value|Description
---|---|---|---|---
    <?php
    foreach ($apiDoc->apiHeader as $param):
        $parsedParam = $apiDoc->parseField($param);
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
// apiHeaderExample
if (!is_null($apiDoc->apiHeaderExample)):
    ?>
### Parameter request example
<?php echo $apiDoc->apiHeaderExample['title'] ?: ''; ?>

```<?php echo isset($apiDoc->apiHeaderExample['type']) ? substr($apiDoc->apiHeaderExample['type'], 1, -1) : ''; ?>
<?php echo $apiDoc->apiHeaderExample['example']; ?>
```
<?php
endif;


// apiSuccess
if (!is_null($apiDoc->apiSuccess)):
    ?>

###  Success return Parameter
Key|Value Type|Description
---|---|---
    <?php
    foreach ($apiDoc->apiSuccess as $param):
        $parsedParam = $apiDoc->parseField($param);
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
if (!is_null($apiDoc->apiSuccessExample)):
    ?>
### Example of a success return message
<?php echo !empty($apiDoc->apiSuccessExample['title']) ? $apiDoc->apiSuccessExample['title'] : ''; ?>

```<?php echo isset($apiDoc->apiSuccessExample['type']) ? substr($apiDoc->apiSuccessExample['type'], 1, -1) : ''; ?>
<?php echo !empty($apiDoc->apiSuccessExample['example']) ? $apiDoc->apiSuccessExample['example'] : "\n"; ?>
```
<?php
endif;
// apiError
if (!is_null($apiDoc->apiError)):
    ?>

###  Error return Parameter
Key|Value Type|Description
---|---|---
<?php
foreach ($apiDoc->apiError as $param):
    $parsedParam = $apiDoc->parseField($param);
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
// apiErrorExample
if (!is_null($apiDoc->apiErrorExample)):
    ?>
### Example of a success return message
<?php echo $apiDoc->apiErrorExample['title'] ?: ''; ?>

```<?php echo isset($apiDoc->apiErrorExample['type']) ? substr($apiDoc->apiErrorExample['type'], 1, -1) : ''; ?>
<?php echo $apiDoc->apiErrorExample['example']; ?>
```
<?php
endif;
?>