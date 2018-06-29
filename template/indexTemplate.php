<?php
/**
 * Created by PhpStorm.
 * User: lincanbin
 * Date: 2018/3/29
 * Time: 17:55
 */
/**
 * @var ApiDocGenerator $this
 * @var ApiDocCommentObject $apiDoc
 */
?>
# <?php echo $this->config->title ?>

<?php echo $this->config->description ?>

<?php
if ($this->config->header['title']) {
    echo "## " . $this->config->header['title'] . "\n";
}
if (is_file($this->input . $this->config->header['filename'])) {
    include $this->input . $this->config->header['filename'];
}
?>
<?php
foreach ($this->apiList as $groupName => $group):
if (!empty($group)):
?>

## <?php echo $groupName ?>

Name|Method|Path
---|---|---
<?php
    foreach ($group as $apiDoc):
        if (!empty($apiDoc->api['method']) && !empty($apiDoc->api['path']) && !empty($apiDoc->apiGroup['name']) && !empty($apiDoc->apiName['name'])):
?>
[<?php
echo (!is_null($apiDoc->apiDeprecated) ? '~~' : '') . !empty($apiDoc->api['title']) ? $apiDoc->api['title'] : $apiDoc->apiName['name'] . (!is_null($apiDoc->apiDeprecated) ? '~~' : '');
?>](./apidoc/<?php echo $apiDoc->apiGroup['name'] . '/' . $apiDoc->apiName['name'] . '.md'; ?>)|<?php
echo strtoupper(substr($apiDoc->api['method'], 1, -1));
?>|<?php
echo $apiDoc->api['path'];
?>

<?php
        endif;
    endforeach;
endif;
endforeach;
?>

<?php
if ($this->config->footer['title']) {
    echo "## " . $this->config->footer['title'] . "\n";
}
if (is_file($this->input . $this->config->footer['filename'])) {
    include $this->input . $this->config->footer['filename'];
}
?>


Powered By © 2018 [apidoc-markdown](https://github.com/lincanbin/apidoc-markdown)