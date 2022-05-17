function performRemoteSearch() {

    let links = document.getElementsByClassName('links');


    let linksCount = links.length;
    let processedLinks = 0;
    let uniqueTextInPercents = 0;
    let results = [];

    console.log("linksCount:" + linksCount);

    for (let link of links) {
        let siteUrl = link.value;
        let title = link.title;

        const request = new XMLHttpRequest();
        const urlEncoded = encodeURI("/grabber.php?url=" + siteUrl + "&t=" + new Date().getTime());
        request.open('GET', urlEncoded);

        request.setRequestHeader('Content-Type', 'application/x-www-form-url');
        request.addEventListener("readystatechange", () => {

            if (request.readyState === 4 && request.status === 200) {

                let response = request.responseText;
                let verifyResult = JSON.parse(response);

                if (verifyResult.similarity > 0) {

                    let similarity = (Math.round(verifyResult.similarity * 100) / 100).toFixed(2);

                    if (uniqueTextInPercents < similarity)
                        uniqueTextInPercents = similarity;


                    let uniqueTextPercentItem = (Math.round(100 - similarity) / 100).toFixed(2);

                    let item = {
                        link: verifyResult.siteUrl,
                        similarity: similarity,
                        text: title + ' | уникальность: (' + ( uniqueTextPercentItem) + '%)'
                    };

                    results.push(item);

                }

            }

            processedLinks++;
            if (processedLinks === linksCount) {

                results.sort((a, b) => (a.similarity < b.similarity) ? 1 : ((b.similarity < a.similarity) ? -1 : 0))

                printResults(results);

                uniqueTextInPercents = (Math.round(100 - uniqueTextInPercents) / 100).toFixed(2);

                alert('Сканирование завершено! Текст уникален на: ' + uniqueTextInPercents + '%');
            }


        });
        request.send();
    }


    if(linksCount == 0) {
        alert('Плагиат в сети не найден! Для большей уверености, поменяйте системные настойки и повторите проверку.');
    }
}


function printResults(results) {

    let copyLinkBlock = document.getElementById('copyLinksId');

    for (let item of results) {

        let copyLink = document.createElement('a');
        copyLink.innerText = item.text;
        copyLink.href = item.link;
        copyLink.target = '_blank';

        copyLinkBlock.appendChild(copyLink);
        copyLinkBlock.appendChild(document.createElement('br'));
        copyLinkBlock.appendChild(document.createElement('br'));
    }
}


