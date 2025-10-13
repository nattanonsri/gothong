<?= $this->include('front/header') ?>
<?= $this->include('front/_css') ?>
<?php

$uri = service('uri');
$requestURI = $uri->getSegment(2);
$requestURI2 = $uri->getSegment(3);




if($requestURI2 != 'login' && $requestURI2 != 'couponPrint3' && $requestURI != 'screen' && $requestURI2 != 'receiptCheck'){

    echo $this->include('front/sidebar');
    echo $this->include('front/navbar');
}
?>
<?= $this->renderSection('content') ?>


<?= $this->include('front/footer') ?>
<?= $this->include('front/_js') ?>