<?php
include('top-cache.php');
//Kör scriptet i 5 minuter om det skulle behövas och inte avslutas efter 30 sec
ini_set('max_execution_time', 300);
//Läser sidan som ska skrapas
$data = curl_get_request("http://coursepress.lnu.se/kurser/");

//DOMDocument är en representation av ett dokument innehållande XML noder som är arrangerat som ett träd.
$dom = new DOMDocument();
$array = array();

getAllCourses($dom ,$data, $array);

function getAllCourses($dom, $data, $array) {
    $urlArray = $array;
    $reg = "/kurs/";
    //Kontroll så att HTML sidan är korrekt och inte null.
    if($dom->loadHTML($data)){
        //Xpath är en syntax för att kunna navigera sig igenom en DOM struktur och lokalisera en eller flera noder.
        $xpath = new DOMXPath($dom);
        //Säger till DOM att leta efter specifika element.
        $items = $xpath->query('//ul[@id="blogs-list"]//div[@class="item-title"]/a');
        foreach($items as $item){
            $links = $item->getAttribute("href");
            if(preg_match($reg, $links)){
                //Hämtar kurskod
                $courseCode = getCourseCode($dom, $item->getAttribute('href'));
                //Hämtar länk till kursplan
                $coursePlan = getCoursePlan($dom, $item->getAttribute('href'));
                //Hämtar introduktionstexten
                $courseEntryText = getCourseEntryText($dom, $item->getAttribute('href'));
                //Hämtar senaste inlägget med namn och rubrik
                $latestPost = getLatestPost($dom, $item->getAttribute('href'));
                $urlArray[] = array("CourseName:"=>$item->nodeValue,"Link:"=>$item->getAttribute('href'),"CourseCode:"=>$courseCode,"CoursePlan:"=>$coursePlan,"Course_Entry_Text:"=>$courseEntryText,"Latest_Post:"=>$latestPost."");
            }
        }
    }
    echo "Antal kurser: ".count($urlArray)." st.";
    echo json_encode($urlArray, JSON_PRETTY_PRINT);
    include('bottom-cache.php');
    //getNextPage($dom, $data, $urlArray);
}

//Hämtar ut introduktionstext om det finns annars skrivs No entry text ut
function getCourseEntryText($dom, $courseURL){
    $courseURL = curl_get_request($courseURL);
    libxml_use_internal_errors(true);
    if($dom->loadHTML($courseURL)){
        libxml_use_internal_errors(false);
        $xpath = new DOMXPath($dom);

        $courseEntryText = $xpath->query('//div[@class="entry-content"]/p/text()')->item(0);

        if($courseEntryText != null){
            return $courseEntryText->textContent;
        }
        else{
            return "No entry text";
        }

    }
}

//Hämtar ut kurskod om det finns annars skrivs No course code ut
function getCourseCode($dom, $courseURL){
    $courseURL = curl_get_request($courseURL);
    libxml_use_internal_errors(true);
    if($dom->loadHTML($courseURL)){
        libxml_use_internal_errors(false);
        $xpath = new DOMXPath($dom);
        $courseCode = $xpath->query('//div[@id = "header-wrapper"]/ul/li[last()]/a/text()')->item(0);

        if($courseCode != null){
            return $courseCode->textContent;
        }
        else{
            return "No course code";
        }
    }
}

//Hämtar ut länk till kursplanen annars skrivs No course plan ut
function getCoursePlan($dom, $courseURL){
    $courseURL = curl_get_request($courseURL);
    libxml_use_internal_errors(true);
    if($dom->loadHTML($courseURL)){
        libxml_use_internal_errors(false);
        $xpath = new DOMXPath($dom);
        $coursePlan = $xpath->query('//ul[@class = "sub-menu"]/li/a/text()[contains(., "Kursplan")]')->item(0);

        if($coursePlan != null){
            $href = $coursePlan->parentNode;
            if($href != null){
                return $href->getAttribute("href");
            }
            else{
                return "No course plan";
            }
        }
    }
}

//Hämtar ut kurser på nästa sida
function getNextPage($dom, $data , $urlArray){
    if($dom->loadHTML($data)){

        $xpath = new DOMXPath($dom);
        $nextPageUrl = $xpath->query("//div[@id='pag-bottom']/div[@class='pagination-links']/a[@class='next page-numbers']");
        foreach($nextPageUrl as $href){
            $nextPageUrl =  $href->getAttribute('href') . "<br/>";
        }
        if($nextPageUrl == 1){
            echo "Antal kurser: ".count($urlArray)." st.";
            var_dump($urlArray);
            include('bottom-cache.php');
        }
        $nextPageUrl = curl_get_request("http://coursepress.lnu.se" . $nextPageUrl);
        if(strlen($nextPageUrl) > 0){
            getAllCourses($dom,$nextPageUrl,$urlArray);
        }
    }
}

//Hämtar ut senaste inlägget om det finns annars skrivs No latest post ut
function getLatestPost($dom, $courseURL){
    $courseURL = curl_get_request($courseURL);
    libxml_use_internal_errors(true);
    if($dom->loadHTML($courseURL)){
        libxml_use_internal_errors(false);
        $xpath = new DOMXPath($dom);
        $latestPost = $xpath->query('//header[@class = "entry-header"]/h1[@class = "entry-title"]')->item(0);
        $latestPostTime = $xpath->query('//header[@class = "entry-header"]/p')->item(0);
        if($latestPost != null && $latestPostTime != null){
            $latestPostValue = $latestPost->nodeValue;
            $latestPostTimeValue = $latestPostTime->nodeValue;
            return $latestPostValue . $latestPostTimeValue;
        }
        else{
            return "No latest post";
        }
    }
}

//Tar emot en url. Initierar ett object exikverar objectet, stänger det och sedan returnerar datat.
function curl_get_request($url){
    $agent = 'mf222nb@student.lnu.se';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);

    curl_setopt($ch, CURLOPT_USERAGENT, $agent);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($ch);
    curl_close($ch);

    return $data;
}