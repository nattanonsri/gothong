<!DOCTYPE html>
<html lang="th" class="notranslate" translate="no">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="google-signin-client_id" content="<?= getenv('GOOGLE_CLIENT_ID') ?? '' ?>">
    <?= csrf_meta() ?>
    <title><?php echo $title ?? TITLE ?></title>
    <link rel="icon" type="image/png" href="<?php echo asset_url(FAVICON) ?? ''; ?>">
    <meta property="og:title" content="<?php echo $title ?? TITLE ?>">
    <meta property="og:description" content="<?php echo $description ?? DESCRIPTION ?>">
    <meta property="fb:app_id" content="<?php echo getenv('FACEBOOK_APP_ID') ?>">
    <meta property="og:image" content="<?= $feature_image ?? asset_url(FEATURE_IMAGE); ?>" />
    <meta property="og:site_name" content="bigbonus">
    <meta property="og:type" content="website" />

    <meta name="google" content="notranslate">

    <?php
    helper('minify');
    $cssFiles = [
        'assets/fonts/prompt/stylesheet.css',
        'assets/bootstrap-5.3.7/css/bootstrap.min.css',
        'assets/fontawesome/css/all.min.css',
        'assets/select2/select2.min.css',
        'assets/select2/select2-bootstrap-5-theme.min.css',
        'assets/DataTables/datatables.min.css',
        'assets/daterangepicker/daterangepicker.css',
    ];


    $minifiedCssFile = 'assets/css/minified_backend.css';
    $inputJsFiles = [
        'assets/js/jquery.min.js',
        'assets/bootstrap-5.3.7/js/bootstrap.bundle.min.js',
        'assets/fontawesome/js/all.min.js',
        'assets/js/sweetalert2.all.min.js',
        'assets/select2/select2.full.min.js',
        'assets/DataTables/datatables.min.js',
        'assets/daterangepicker/moment.min.js',
        'assets/daterangepicker/daterangepicker.js',
        'assets/js/config.js',
        'assets/js/dashboards-analytics.js',
        'assets/js/chart.js',
        'assets/js/interact.min.js',

    ];
    $outputJsFile = 'assets/js/minified_backend.min.js';

    if (getenv('CI_ENVIRONMENT') != 'production') {
        minify_css($cssFiles, $minifiedCssFile);
        minify_js($inputJsFiles, $outputJsFile);
    }
    ?>
    <link rel="stylesheet" type="text/css" href="<?= asset_url($minifiedCssFile) ?>">
    <script src="<?= asset_url($outputJsFile); ?>"></script>

    <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/css/jquery-ui.min.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/css/ui.fancytree.min.css'); ?>">
    <script src="<?= asset_url('assets/js/jquery-ui.min.js'); ?>"></script>
    <script src="<?= asset_url('assets/js/jquery.fancytree-all.min.js'); ?>"></script>

    <script>
        let base_url = '<?php echo base_url(); ?>';
        let asset_url = '<?php echo asset_url(); ?>';
        const envStatus = '<?= getenv('CI_ENVIRONMENT') ?? "" ?>';

        function buttonLoading(elem) {
            $(elem).attr("data-original-text", $(elem).html());
            $(elem).prop("disabled", true);
            $(elem).html('<i class="fa fa-spinner fa-spin"></i> wait...');
        }

        function buttonReset(elem) {
            $(elem).prop("disabled", false);
            $(elem).html($(elem).attr("data-original-text"));
        }
    </script>
</head>

<body>