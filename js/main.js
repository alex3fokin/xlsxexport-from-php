var sendUrlBtn = document.getElementById("sendURL");
sendUrlBtn.onclick = function () {
    let searchedURL = document.getElementById("searchedURL");
    let xhr = new XMLHttpRequest();
    xhr.open('GET', 'lib/checkURL.php?' + searchedURL.name + '=' + searchedURL.value);
    xhr.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            let result = JSON.parse(this.responseText);
            let html = "<table id='data-table'>\n\
            <tr>\n\
                <th>Проверяемый файл:</th>\n\
                <th colspan='3'>" + result['location'] + "</th>\n\
            </tr>\n\
            <tr>\n\
                <th>Название проверки</th>\n\
                <th>Статус</th>\n\
                <th>Состояние</th>\n\
                <th>Рекомендации</th>\n\
            </tr>";
            for (const prop in result) {
                if (prop == 'location')
                    continue;
                var bgColor;
                if(result[prop]['status'] === 'Ок') {
                    bgColor = 'green';
                } else {
                    bgColor = 'red';
                }
                html += "<tr>\n\
                            <td>" + result[prop]['name'] + "</td>\n\
                            <td bgcolor='"+bgColor+"'>" + result[prop]['status'] + "</td>\n\
                            <td>" + result[prop]['state'] + "</td>\n\
                            <td>" + result[prop]['advice'] + "</td>\n\
                        </tr>"
            }
            html += '</table>\n\
<p style="text-align:center"><form method="POST" action="lib/makeExcel.php" id="sendTableForm"><input type="submit" value="Сохранить"></form></p>';
            document.getElementById("result").innerHTML = html;
        }
    };
    xhr.send();
};
