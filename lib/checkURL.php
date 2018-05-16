<?php
session_start();
const MAX_ROBOTS_FILESIZE = 32;

function parseHeaders($headers) {
    $head = array();
    foreach ($headers as $k => $v) {
        $t = explode(':', $v, 2);
        if (isset($t[1]))
            $head[trim($t[0])] = trim($t[1]);
        else {
            $head[] = $v;
            if (preg_match("#HTTP/[0-9\.]+\s+([0-9]+)#", $v, $out))
                $head['reponse_code'] = intval($out[1]);
        }
    }
    return $head;
}

$url = $_GET['url'];
if (!(strpos($url, "http://") !== false || strpos($url, "https://") !== false)) {
    $url = "http://" . $url;
}
$result = array();
$robots = file_get_contents($url . '/robots.txt');
$responseHeaders = parseHeaders($http_response_header);
if ($robots) {
    $countOfHostDir = substr_count($robots, "Host:");
    $sizeOfRobots = strlen($robots) / 1024;
    $sitemapDir = strpos($robots, 'Sitemap:');
    $responseCode = http_response_code();
    $result['location'] = $responseHeaders["Location"];
    $result['robots'] = array(
        'name' => "Проверка наличия файла robots.txt",
        "status" => "Ок",
        "state" => "Файл robots.txt присутствует",
        "advice" => "Доработки не требуются");
    if ($countOfHostDir) {
        $result['host'] = array(
            'name' => "Проверка указания директивы Host",
            "status" => "Ок",
            "state" => "Директива Host указана",
            "advice" => "Доработки не требуются");
        if ($countOfHostDir === 1) {
            $result['count_of_hosts'] = array(
                'name' => "Проверка количества директив Host, прописанных в файле",
                "status" => "Ок",
                "state" => "В файле прописана 1 директива Host",
                "advice" => "Доработки не требуются");
        } else {
            $result['count_of_hosts'] = array(
                'name' => "Проверка количества директив Host, прописанных в файле",
                "status" => "Ошибка",
                "state" => "В файле прописано несколько директив Host",
                "advice" => "Программист: Директива Host должна быть указана в файле толоко 1 раз. Необходимо удалить все дополнительные директивы Host и оставить только 1, корректную и соответствующую основному зеркалу сайта");
        }
    } else {
        $result['host'] = array(
            'name' => "Проверка указания директивы Host",
            "status" => "Ошибка",
            "state" => "В файле robots.txt не указана директива Host",
            "advice" => "Программист: Для того, чтобы поисковые системы знали, какая версия сайта является основных зеркалом, необходимо прописать адрес основного зеркала в директиве Host. В данный момент это не прописано. Необходимо добавить в файл robots.txt директиву Host. Директива Host задётся в файле 1 раз, после всех правил.");

        $result['count_of_hosts'] = array(
            'name' => "Проверка количества директив Host, прописанных в файле",
            "status" => "Ошибка",
            "state" => "Проверка невозможна, т.к. директива Host отсутствует",
            "advice" => "Программист: Для того, чтобы поисковые системы знали, какая версия сайта является основных зеркалом, необходимо прописать адрес основного зеркала в директиве Host. В данный момент это не прописано. Необходимо добавить в файл robots.txt директиву Host. Директива Host задётся в файле 1 раз, после всех правил.");
    }
    if ($sizeOfRobots < MAX_ROBOTS_FILESIZE) {
        $result['size'] = array(
            'name' => "Проверка размера файла robots.txt",
            "status" => "Ок",
            "state" => "Размер файла robots.txt составляет ".$sizeOfRobots."кб, что находится в пределах допустимой нормы",
            "advice" => "Доработки не требуются");
    } else {
        $result['size'] = array(
            'name' => "Проверка размера файла robots.txt",
            "status" => "Ошибка",
            "state" => "Размера файла robots.txt составляет ".$sizeOfRobots."кб, что превышает допустимую норму",
            "advice" => "Программист: Максимально допустимый размер файла robots.txt составляет 32 кб. Необходимо отредактировть файл robots.txt таким образом, чтобы его размер не превышал 32 Кб");
    }
    if($sitemapDir) {
        $result['sitemap'] = array(
            'name' => "Проверка указания директивы Sitemap",
            "status" => "Ок",
            "state" => "Директива Sitemap указана",
            "advice" => "Доработки не требуются");
    } else {
        $result['sitemap'] = array(
            'name' => "Проверка указания директивы Sitemap",
            "status" => "Ошибка",
            "state" => "В файле robots.txt не указана директива Sitemap",
            "advice" => "Программист: Добавить в файл robots.txt директиву Sitemap");
    }
    if($responseCode === 200) {
        $result['response_code'] = array(
            'name' => "Проверка кода ответа сервера для файла robots.txt",
            "status" => "Ок",
            "state" => "Файл robots.txt отдаёт код ответа сервера 200",
            "advice" => "Доработки не требуются");
    } else {
        $result['response_code'] = array(
            'name' => "Проверка кода ответа сервера для файла robots.txt",
            "status" => "Ошибка",
            "state" => "При обращении к файлу robots.txt сервер возвращает код ответа ".$responseCode,
            "advice" => "Программист: Файл robots.txt должны отдавать код ответа 200, иначе файл не будет обрабатываться. Необходимо настроить сайт таким образом, чтобы при обращении к файлу robots.txt сервер возвращает код ответа 200");
    }
} else {
    $result['robots'] = array(
        'name' => "Проверка наличия файла robots.txt",
        "status" => "Ошибка",
        "state" => "Файл robots.txt отсутствует",
        "advice" => "Программист: Создать файл robots.txt и разместить его на сайте.");
    $result['other'] = array(
        "name" => 'Дальнейшие проверки невзможны так как отсутствует файл robots.txt',
    );
}
$_SESSION['table'] = $result;
$_COOKIE['table'] = $result;
echo json_encode($result);