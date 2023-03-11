<?phphttps://github.com/jhowbhz/package-apigratis/tree/stable/Examples

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('vendor/autoload.php');

use ApiBrasil\Service;

class Example
{

    //Send Text
    static function sendText()
    {
        return Service::WhatsApp("sendText", [
            
            "Bearer" => "SEU TOKEN AQUI",
            "SecretKey" => "SEU SECRETKEY AQUI",
            "PublicToken" => "SEU PUBLICTOKEN AQUI", 
            "DeviceToken" => "SEU DEVICETOKEN AQUI",

            "body" => [
                "number" => "5531994359434",
                "text" => "🟢 https://apibrasil.com.br \n isso é um teste, caracteres especiais ? : !!! ççç  R$ 999,50"
            ]
        ]);
    }

}

print_r(Example::sendText());