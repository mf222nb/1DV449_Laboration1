<?php
ini_set('max_execution_time', 300);
$data = curl_get_request("http://coursepress.lnu.se/kurser/");

$dom = new DOMDocument();
$array = array();

getAllCourses($dom ,$data, $array);

function getAllCourses($dom, $data, $array) {
    $urlArray = $array;

    if($dom->loadHTML($data)){
        $xpath = new DOMXPath($dom);
        $items = $xpath->query('//ul[@id="blogs-list"]//div[@class="item-title"]/a');
        foreach($items as $item){
            $courseCode = getCourseCode($dom,$item->getAttribute('href'));
            $coursePlan = getCoursePlan($dom,$item->getAttribute('href'));
            $courseEntryText = getCourseEntryText($dom,$item->getAttribute('href'));
            $urlArray[] = "CourseName: " . $item->nodeValue . "--> Link:" . $item->getAttribute('href') . "--> CourseCode: " . $courseCode . "--> CoursePlan: " . $coursePlan ." --> Course Entry Text: " . $courseEntryText . "<br/>";;
        }
        echo "<br/>";
        var_dump($urlArray);
    }
    getNextPage($dom, $data, $urlArray);
}

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

function getCoursePlan($dom, $courseURL){
    $courseURL = curl_get_request($courseURL);
    libxml_use_internal_errors(true);
    if($dom->loadHTML($courseURL)){
        libxml_use_internal_errors(false);
        $xpath = new DOMXPath($dom);
        $coursePlan = $xpath->query('//ul[@class = "sub-menu"]/li/a/text()[contains(., "Kursplan")]')->item(0);

        $href = $coursePlan->parentNode;
        if($href != null){
            return $href->getAttribute("href");
        }
        else{
            return "No course plan";
        }
    }
}

function getNextPage($dom, $data , $urlArray){
    if($dom->loadHTML($data)){

        $xpath = new DOMXPath($dom);
        $nextPageUrl = $xpath->query("//div[@id='pag-bottom']/div[@class='pagination-links']/a[@class='next page-numbers']");
        foreach($nextPageUrl as $href){
            $nextPageUrl =  $href->getAttribute('href') . "<br/>";
        }

        $nextPageUrl = curl_get_request("http://coursepress.lnu.se" . $nextPageUrl);
        if(strlen($nextPageUrl) > 0){
            getAllCourses($dom,$nextPageUrl,$urlArray);
        }
    }
}

function curl_get_request($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $data = curl_exec($ch);

    curl_close($ch);

    return $data;
}