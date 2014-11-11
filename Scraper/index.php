<?php
include('top-cache.php');
ini_set('max_execution_time', 300);
$data = curl_get_request("http://coursepress.lnu.se/kurser/");

$dom = new DOMDocument();
$array = array();

getAllCourses($dom ,$data, $array);

function getAllCourses($dom, $data, $array) {
    $urlArray = $array;
    $reg = "/kurs/";
    if($dom->loadHTML($data)){
        $xpath = new DOMXPath($dom);
        $items = $xpath->query('//ul[@id="blogs-list"]//div[@class="item-title"]/a');
        foreach($items as $item){
            $links = $item->getAttribute("href");
            if(preg_match($reg, $links)){
                $courseCode = getCourseCode($dom, $item->getAttribute('href'));
                $coursePlan = getCoursePlan($dom, $item->getAttribute('href'));
                $courseEntryText = getCourseEntryText($dom, $item->getAttribute('href'));
                $latestPost = getLatestPost($dom, $item->getAttribute('href'));
                $urlArray[] = "CourseName: " . $item->nodeValue . "--> Link:" . $item->getAttribute('href') .
                    "--> CourseCode: " . $courseCode . "--> CoursePlan: " . $coursePlan ." --> Course Entry Text: "
                    . $courseEntryText . " --> Latest Post: ".$latestPost."<br/>";
            }
        }
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

function getNextPage($dom, $data , $urlArray){
    if($dom->loadHTML($data)){

        $xpath = new DOMXPath($dom);
        $nextPageUrl = $xpath->query("//div[@id='pag-bottom']/div[@class='pagination-links']/a[@class='next page-numbers']");
        foreach($nextPageUrl as $href){
            $nextPageUrl =  $href->getAttribute('href') . "<br/>";
        }
        if($nextPageUrl == 1){
            var_dump($urlArray);
            include('bottom-cache.php');
        }
        $nextPageUrl = curl_get_request("http://coursepress.lnu.se" . $nextPageUrl);
        if(strlen($nextPageUrl) > 0){
            getAllCourses($dom,$nextPageUrl,$urlArray);
        }
    }
}

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

function curl_get_request($url){
    $agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);

    curl_setopt($ch, CURLOPT_USERAGENT, $agent);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($ch);
    curl_close($ch);

    return $data;
}