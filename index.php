<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <meta name="description" content=""/>
    <meta name="author" content=""/>
    <title>SeoElk - ваш лось для продвижения в сети.</title>
    <!-- Favicon-->
    <link rel="icon" type="image/png" href="/logo.png"/>
    <!-- Bootstrap icons-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet"
          type="text/css"/>
    <!-- Google fonts-->
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic" rel="stylesheet"
          type="text/css"/>
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="css/styles.css" rel="stylesheet"/>
</head>
<body>
<!-- Navigation-->
<nav class="navbar navbar-light bg-light static-top">
    <div class="container">
        <a class="navbar-brand" href="/"><img style="height: 30px;" src="/logo.png">SeoElk - ваш лось для продвижении в сети</a>
        <a class="btn btn-primary" href="/blog">Блог</a>
    </div>
</nav>

<div id="spinner" style="display: none;
position: fixed; left: 0; top: 0; right: 0; bottom: 0;
background: rgba(255,255,255,0.8); z-index: 100">
<div style="position: absolute; left: 50%; top: 47%;">
    <div style="position: relative; left: -50%; border: solid #373f79 1px;
    white-space: nowrap; z-index: 1000; background-color: white; opacity: 1;">
        <span style="margin-left: 5px;">Проверяем текст</span>
        <img src="/assets/img/Settings.gif">
    </div>
</div>
</div>


<script type="application/javascript">


    function checkWebSites(links) {

        let linksCount = links.length;
        let processedLinks = 0;
        let uniqueTextInPercentsMin = 100.00;
        let results = [];

        console.log("linksCount:" + linksCount);

        for (let link of links) {
            let siteUrl = link.link;
            let title = link.title;

            const request = new XMLHttpRequest();
            const urlEncoded = encodeURI("/grabber.php?url=" + siteUrl + "&t=" + new Date().getTime());
            request.open('GET', urlEncoded);

            request.setRequestHeader('Content-Type', 'application/x-www-form-url');
            request.addEventListener("readystatechange", () => {

                if (request.readyState === 4) {

                    if (request.status === 200) {

                        let response = request.responseText;
                        let verifyResult = JSON.parse(response);

                        let uniqueness = verifyResult.uniqueness
                        let uniquenessFloat = (Math.round(uniqueness * 100) / 100).toFixed(2)

                        if (uniqueTextInPercentsMin > uniqueness)
                            uniqueTextInPercentsMin = uniquenessFloat;

                        if (uniqueness < 95) {

                            let count =  results.filter(function (el) {
                                return el.link === verifyResult.siteUrl;
                            });

                            console.log("#count.length:" + count.length)

                            if(count.length === 0) {
                                let item = {
                                    link: verifyResult.siteUrl,
                                    uniqueness: uniqueness,
                                    text: title + ' | уникальность: (' + (uniquenessFloat) + '%)'
                                };

                                console.log("#add item:" + item);

                                results.push(item);
                            }
                        }
                    }

                    processedLinks++;
                    if (processedLinks === linksCount) {

                        console.log("#results length:" + results.length);

                        results.sort((a, b) => (a.uniqueness > b.uniqueness) ? 1 : ((b.uniqueness > a.uniqueness) ? -1 : 0))

                        console.log("#results length2:" + results.length);

                        printResults(results);

                        if(uniqueTextInPercentsMin === 99)
                            alert('Сканирование завершено! Текст уникален на: ' + uniqueTextInPercentsMin + '%');
                    }

                }
            });
            request.send();
        }

        if (linksCount === 0) {
            alert('Плагиат в сети не найден! Для большей уверености, поменяйте системные настойки и повторите проверку.');
        }
    }

    function printResults(results) {

        let resultListId = document.getElementById('resultListId');

        let child = resultListId.lastElementChild;
        while (child) {
            resultListId.removeChild(child);
            child = resultListId.lastElementChild;
        }

        if (results.length === 0) {
            let divMsg = document.createElement('div');
            divMsg.innerText = "Текст полностью уникален! Поздравляем :)";
            divMsg.style.color = 'white';
            divMsg.style.fontWeight = 'bold';

            resultListId.appendChild(divMsg);

        } else {
            for (let item of results) {

                let copyLink = document.createElement('a');
                copyLink.innerText = item.text;
                copyLink.href = item.link;
                copyLink.target = '_blank';

                copyLink.style.color = 'white';
                copyLink.style.fontWeight = 'bold';

                resultListId.appendChild(copyLink);
                resultListId.appendChild(document.createElement('br'));
            }
        }

        document.getElementById('resultCaptionId').style.display = 'block';
        document.getElementById('resultListId').style.display = 'block';

        setSpinnerStatus(false);
    }

    function setSpinnerStatus(status) {
        document.getElementById('spinner').style.display = status ? 'block' : 'none';
    }

    function performSearch() {

        setSpinnerStatus(true);

        let text = document.getElementById('textToVerify').value;
        const request = new XMLHttpRequest();

        let body = 'text=' + encodeURIComponent(text) +
            '&searchType=' + encodeURIComponent(1);  // 1 - it's easy level of search

        const urlEncoded = encodeURI("/search.php");
        request.open('POST', urlEncoded, true);

        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        request.addEventListener("readystatechange", () => {

            if (request.readyState === 4) {

                if (request.status === 200) {
                    let response = request.responseText;
                    let links = JSON.parse(response);

                    if (links.length > 0) {
                        checkWebSites(links);
                    } else {
                        printResults([]);
                    }
                } else {
                    setSpinnerStatus(false);
                    alert('На сегодня исчерпан лимит бесплатных запросов!');
                }
            }
        });
        request.send(body);
    }

    function processSearch() {
        performSearch();
    }
</script>

<!-- Masthead-->
<header class="masthead">
    <div class="container position-relative">
        <div class="row justify-content-center">
            <div class="col-xl-6">
                <div class="text-center text-white">
                    <!-- Page heading-->
                    <h2 style="margin-top: .3em;">Проверить текст на уникальность</h2>

                    <div class="form-subscribe">
                        <!-- Email address input-->
                        <div class="row">
                            <div class="col">
                                <textarea class="form-control form-control-lg" id="textToVerify"></textarea>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 1.5em;">
                            <div class="col">
                                <button class="btn btn-primary btn-lg" id="submitButton" type="button"
                                        onclick="processSearch();">
                                    Проверить
                                </button>
                            </div>
                        </div>

                        <div class="row" id="resultCaptionId" style="display: none; margin-top: 1.7em;">
                            <div class="col">
                                <h2>Результат поиска:</h2>
                            </div>
                        </div>

                        <div style="display: none;" id="resultListId"></div>


                        <!-- Submit success message-->
                        <!---->
                        <!-- This is what your users will see when the form-->
                        <!-- has successfully submitted-->
                        <div class="d-none" id="submitSuccessMessage">
                            <div class="text-center mb-3">
                                <div class="fw-bolder">Form submission successful!</div>
                                <p>To activate this form, sign up at</p>
                                <a class="text-white" href="https://startbootstrap.com/solution/contact-forms">https://startbootstrap.com/solution/contact-forms</a>
                            </div>
                        </div>
                        <!-- Submit error message-->
                        <!---->
                        <!-- This is what your users will see when there is-->
                        <!-- an error submitting the form-->
                        <div class="d-none" id="submitErrorMessage">
                            <div class="text-center text-danger mb-3">Error sending message!</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- Icons Grid-->
<section class="features-icons bg-light text-center">
    <div class="container">
        <div class="row">
            <div class="col-lg-4">
                <div class="features-icons-item mx-auto mb-5 mb-lg-0 mb-lg-3">
                    <div class="features-icons-icon d-flex"><i class="bi-window m-auto text-primary"></i></div>
                    <h3>Легко находит копипаст</h3>
                    <p class="lead mb-0">В 95% случаев копипаст в сети будет найден</p>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="features-icons-item mx-auto mb-5 mb-lg-0 mb-lg-3">
                    <div class="features-icons-icon d-flex"><i class="bi-layers m-auto text-primary"></i></div>
                    <h3>Частично находит рерайт</h3>
                    <p class="lead mb-0">На данный момент еще в процессе улучшений алгоритм поиска рерайта</p>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="features-icons-item mx-auto mb-0 mb-lg-3">
                    <div class="features-icons-icon d-flex"><i class="bi-terminal m-auto text-primary"></i></div>
                    <h3>Практично в использовании</h3>
                    <p class="lead mb-0">Практичный сервис, для тех кто продвигается в сети с помощью статей</p>
                </div>
            </div>
        </div>
    </div>
</section>


<?php

const DB_SERVER_NAME = 'localhost';
const DB_USER_NAME = 'n55403_seotexts';
const DB_PASSWORD = 'JNW.R3AZ34jE9DT!';
const DB_NAME = 'n55403_seotexts_blog';


$conn = new \mysqli(DB_SERVER_NAME, DB_USER_NAME,
    DB_PASSWORD, DB_NAME);

$conn->set_charset('utf8mb4');

$sql = "SELECT post_date, post_title, post_name, t2.description 
        FROM `wp_posts` as t1 JOIN wp_yoast_indexable as t2 ON t1.ID = t2.object_id 
        where t1.post_type = 'post' and t1.post_status = 'publish' ORDER BY post_date DESC LIMIT 7";

$result = $conn->query($sql);

?>


<!-- Image Showcases-->
<section class="showcase">
    <div class="container-fluid p-0">

        <?php

        if ($result && $result->num_rows > 0) {


            while ($row = $result->fetch_assoc()) { ?>

                <div class="row g-0">
                    <div class="col-lg-6 order-lg-2 text-white showcase-img"
                         style="background-image: url('assets/img/notepad-3297994_1280.jpg')"></div>
                    <div class="col-lg-6 order-lg-1 my-auto showcase-text">
                        <h2>
                            <a href="https://seoelk.com/blog/<?= $row['post_name']; ?>/"><?= $row['post_title'] ?></a>
                        </h2>
                        <p class="lead mb-0"><?= $row['description']; ?></p>
                    </div>
                </div>

                <?php
                if ($row = $result->fetch_assoc()) { ?>

                    <div class="row g-0">
                        <div class="col-lg-6 text-white showcase-img"
                             style="background-image: url('assets/img/chem-opasen-copy-paste.jpeg')"></div>
                        <div class="col-lg-6 my-auto showcase-text">
                            <h2>
                                <a href="https://seoelk.com/blog/<?= $row['post_name']; ?>/"><?= $row['post_title'] ?></a>
                            </h2>
                            <p class="lead mb-0"><?= $row['description']; ?></p>
                        </div>
                    </div>
                    <?php

                }
            }
        }

        ?>


    </div>
</section>
<!-- Footer-->
<footer class="footer bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 h-100 text-center text-lg-start my-auto">

                <p class="text-muted small mb-4 mb-lg-0">&copy; SEOTexts 2021. All Rights Reserved.</p>
            </div>
            <div class="col-lg-6 h-100 text-center text-lg-end my-auto">
                <ul class="list-inline mb-0">
                    <li class="list-inline-item me-4">
                        <a href="#!"><i class="bi-facebook fs-3"></i></a>
                    </li>
                    <li class="list-inline-item me-4">
                        <a href="#!"><i class="bi-twitter fs-3"></i></a>
                    </li>
                    <li class="list-inline-item">
                        <a href="#!"><i class="bi-instagram fs-3"></i></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</footer>
<!-- Bootstrap core JS-->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Core theme JS-->
<script src="js/scripts.js"></script>
<!-- * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *-->
<!-- * *                               SB Forms JS                               * *-->
<!-- * * Activate your form at https://startbootstrap.com/solution/contact-forms * *-->
<!-- * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *-->
<script src="https://cdn.startbootstrap.com/sb-forms-latest.js"></script>
</body>
</html>
