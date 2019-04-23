<?php

namespace DockerElephant;

use GuzzleHttp\Client;

require_once __DIR__ . '/../vendor/autoload.php';

defined('FLICKR_ENDPOINT') ||
    define('FLICKR_ENDPOINT', 'https://api.flickr.com');

defined('FLICKR_KEY') ||
    define('FLICKR_KEY', getenv('FLICKR_KEY') ?? '');

defined('FLICKR_SEC') ||
    define('FLICKR_SEC', getenv('FLICKR_SEC') ?? '');

defined('PHOTOS_PER_PAGE') ||
    define('PHOTOS_PER_PAGE', 20);

$client = new Client([
    'base_uri' => FLICKR_ENDPOINT,
    'headers' => [
        'User-Agent' => 'DockerElePHPant/1.0',
        'Accept'     => 'application/json, text/xml',
    ]
]);

$page = 1;
if ([] !== $_GET && array_key_exists('page', $_GET)) {
    $page = (int) $_GET['page'];
}

$response = $client->request('GET', '/services/rest/', [
    'query' => [
        'method' => 'flickr.photos.search',
        'tags' => 'elephpant',
        'api_key' => FLICKR_KEY,
        'page' => $page,
        'per_page' => PHOTOS_PER_PAGE,
    ],
]);
$responseData = (string) $response->getBody();
$xmlDoc = simplexml_load_string($responseData);
$photoList = $xmlDoc->photos;
$totalPages = (int) $photoList['pages'];
$currentPage = (int) $photoList['page'];
$minRange = 0;
$displayPages = 5;
if ($currentPage > ($minRange + $displayPages)) {
    $minRange = $currentPage - $displayPages;
}
?>
<html>
    <head>
        <title>Dockerised ElePHPant Display</title>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
        <!-- Optional theme -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap-theme.min.css" integrity="sha384-6pzBo3FDv/PJ8r2KRkGHifhEocL+1X2rVCTTkUfGk7/0pbek5mMa1upzvWbrUbOZ" crossorigin="anonymous">
    </head>
    <body>
        <div class="container">
            <h1 class="h1">Dockerised ElePHPant Display</h1>

            <div class="row">
                <div class="col-md-12">
                    <?php foreach ($photoList->photo as $photo): ?>
                        <?php $photoUrl = sprintf(
                            'https://www.flickr.com/photos/%s/%d/',
                            (string) $photo['owner'],
                            (int) $photo['id']
                        ) ?>
                        <div class="elephpant-box col-md-3">
                            <a href="<?php echo $photoUrl ?>" class="thumbnail">
                                <img src="<?php echo sprintf(
                                    'https://farm%d.staticflickr.com/%d/%d_%s_%s.jpg',
                                    (int) $photo['farm'],
                                    (int) $photo['server'],
                                    (int) $photo['id'],
                                    $photo['secret'],
                                    'm'
                                ) ?>" alt="<?php echo htmlentities($photo['title'], ENT_QUOTES, 'utf-8') ?>">
                            </a>
                            <br>
                            <p>
                                <a href="<?php echo $photoUrl ?>">
                                    <?php echo htmlentities($photo['title'], ENT_QUOTES, 'utf-8') ?>
                                </a>
                            </p>
                        </div>
                    <?php endforeach ?>
                </div><!-- /.col-md-12 -->
            </div><!-- /.row -->

            <div class="row">
                <div class="col-md-12">
                    <nav aria-label="Page navigation">
                        <ul class="pagination">
                            <?php if ($currentPage > 1): ?>
                                <li><a href="<?php echo sprintf('/?page=%d', ($page - 1)) ?>" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>
                            <?php else: ?>
                                <li class="disabled"><a aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>
                            <?php endif ?>
                            <?php for($i = $minRange; $i < ($minRange + $displayPages); $i++): ?>
                                <?php if ($currentPage === ($i + 1)): ?>
                                    <li class="active"><a><?php echo ($i + 1) ?> <span class="sr-only">(current)</span></a></li>
                                <?php else: ?>
                                    <li><a href="<?php echo sprintf('/?page=%d', ($i + 1)) ?>"><?php echo ($i + 1) ?></a></li>
                                <?php endif ?>
                            <?php endfor ?>
                            <?php if ($totalPages > $currentPage): ?>
                                <li><a href="<?php echo sprintf('/?page=%d', ($page + 1)) ?>" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>
                            <?php else: ?>
                                <li class="disabled"><a href="#" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>
                            <?php endif ?>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha384-nvAa0+6Qg9clwYCGGPpDQLVpLNn0fRaROjHqs13t4Ggj3Ez50XnGQqc/r8MhnRDZ" crossorigin="anonymous"></script>
        <!-- Latest compiled and minified JavaScript -->
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js" integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous"></script>
    </body>
</html>
