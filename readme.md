бекенг работает с бд Mysql, api принимает get запросы на
/api/chess/getall
/api/chess/startnew

, который возвращают всю информацию об игре (поле, кто ходит) в виде json соответственно

и post запрос на 
/api/chess/move
с параметрами from и to
