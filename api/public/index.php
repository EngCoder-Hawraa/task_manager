<?php
// ✅ CORS HEADERS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../app/bootstrap.php';
// Init Core Library
$init = new Core;

//<?php
//// تحميل autoload و config
//require_once __DIR__ . '/../app/vendor/autoload.php';
//require_once __DIR__ . '/../app/config/config.php';
//
//
//$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
//$dotenv->load();
//
//// السماح بطلبات CORS من أي مصدر (خاصة للـ Vue frontend)
//header("Access-Control-Allow-Origin: *");
//header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
//header("Access-Control-Allow-Headers: Content-Type, Authorization");
//
//// استجابة لطلبات OPTIONS (preflight) التي يرسلها المتصفح قبل POST/GET
//if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
//    http_response_code(200);
//    exit();
//}
//
//// تحميل الملفات الأساسية
//require_once __DIR__ . '/../app/libraries/Database.php';
//require_once __DIR__ . '/../app/libraries/Controller.php';
//require_once __DIR__ . '/../app/helpers/jwt_helper.php';
//
//// تحميل الـ Controllers
//require_once __DIR__ . '/../app/controllers/Auth.php';
////require_once __DIR__ . '/../app/controllers/Dashboard.php';
//
//// الحصول على المسار من URI
//$uri = explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));
//$method = $_SERVER['REQUEST_METHOD'];
//
//// تحديد موقع 'api' في الرابط
//$apiIndex = array_search('api', $uri);
//if ($apiIndex === false) {
//    echo json_encode(['message' => 'Welcome to the API']);
//    exit();
//}
//
//header('Content-Type: application/json');
//
//// تحديد المسار المطلوب (register, login, logout)
//$endpoint = $uri[$apiIndex + 2] ?? '';
//
//$authController = new Auth();
////$dashboardController = new Dashboard();
//
//// التوجيه حسب المسار
//switch ($endpoint) {
//    case 'register':
//        if ($method === 'POST') {
//            $authController->register();
//        } else {
//            http_response_code(405);
//            echo json_encode(['error' => 'Method Not Allowed']);
//        }
//        break;
//
//    case 'login':
//        if ($method === 'POST') {
//            $authController->login();
//        } else {
//            http_response_code(405);
//            echo json_encode(['error' => 'Method Not Allowed']);
//        }
//        break;
//
//    case 'logout':
//        if ($method === 'POST') {
//            $authController->logout();
//        } else {
//            http_response_code(405);
//            echo json_encode(['error' => 'Method Not Allowed']);
//        }
//        break;
//
////    case 'index':  // 👈 هذا هو المسار الجديد
////        if ($method === 'GET') {
////            $dashboardController->index();
////        } else {
////            http_response_code(405);
////            echo json_encode(['error' => 'Method Not Allowed']);
////        }
////        break;
//
//    default:
//        http_response_code(404);
//        echo json_encode(['error' => 'API endpoint not found']);
//}
//
