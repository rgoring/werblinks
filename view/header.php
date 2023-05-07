<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="https://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo $title ?></title>
<link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">
<? foreach($css as $css_link) { ?>
<link href="/css/<? echo $css_link; ?>" rel="stylesheet" type="text/css" />
<? } ?>
<? foreach($js as $js_link) { ?>
<script type="text/javascript" src="/js/<? echo $js_link; ?>"></script>
<? } ?>
</head>
