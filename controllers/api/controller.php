<?php

defined('C5_EXECUTE') or die('Access Denied.');

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=' . APP_CHARSET, true);

class ApiController extends Controller
{

    const INVALID_ENDPOINT      = 1;
    const USER_NOT_LOGGED_IN    = 2;
    const INVALID_METHOD        = 3;
    const BAD_REQUEST           = 400;
    const UNATHORIZED           = 401;
    const NOT_FOUND             = 404;
    const INTERNAL_SERVER_ERROR = 500;
    const JWT_ERROR             = 600;

    protected $errors;
    protected $errorCode;

    public function __construct()
    {
        Loader::helper('validation/error');

        parent::__construct();

        $this->errors = new ValidationErrorHelper();
    }

    public function validateUser()
    {
        if (!User::isLoggedIn()) {
            throw new Exception('Authentication Failed', self::USER_NOT_LOGGED_IN);
        }
    }

    /**
     * Validates the token
     *
     * @return null|User
     * @throws Exception
     */
    public function validateToken()
    {
        /** @var HttpRequestHelper $httpRequestHelper */
        $httpRequestHelper = Loader::helper('http_request');

        $token = $httpRequestHelper->getBearerToken();

        try {

            $is_valid_token = JWT::validate($token);
            if (!$is_valid_token) {
                throw new Exception('Authentication Failed', self::JWT_ERROR);
            }

            $token = JWT::parse($token);

            $user_id = $token->getClaim('user_id');
            if (!$user_id) {
//                throw new Exception('User ID claim is missing', self::UNATHORIZED);
                throw new Exception('Authentication Failed', self::USER_NOT_LOGGED_IN);
            }

            $user = User::getByUserID($user_id);
            if (!$user) {
//                throw new Exception('User does not exists anymore', self::UNATHORIZED);
                throw new Exception('Authentication Failed', self::USER_NOT_LOGGED_IN);
            }

            return $user;

        } catch (Exception $e) {
            throw new Exception($e->getMessage(), self::JWT_ERROR);
        }
    }

    public function isAuthenticated()
    {
        /** @var HttpRequestHelper $httpRequestHelper */
        $httpRequestHelper = Loader::helper('http_request');

        $token = $httpRequestHelper->getBearerToken();

        if ($token) {
            return JWT::validate($token);
        }

        return false;
    }

    public function view()
    {
        throw new Exception('Invalid endpoint', self::INVALID_ENDPOINT);
    }

    public function setupAndRun()
    {
        Loader::helper('ajax');

        $result = null;
        $ajax   = new AjaxHelper();

        try {
            $this->setupRequestTask();
            $this->on_start();

            if ($this->task) {
                $result = $this->runTask($this->task, $this->parameters);
            }
        } catch (Exception $e) {
            $this->setErrorCode($e->getCode());
            $this->errors->add($e);
        }

        if ($this->errors->has()) {
            $ajax->sendResult(['success' => false, 'errorCode' => $this->getErrorCode(), 'errors' => $this->errors->getList()]);
        }

        $ajax->sendResult(['success' => true, 'data' => $result]);
    }

    protected function getErrorCode()
    {
        return (int) $this->errorCode;
    }

    protected function setErrorCode($errorCode)
    {
        return $this->errorCode = $errorCode;
    }

    /**
     * @param ValidationErrorHelper|Exception|string $error
     */
    protected function addError($error)
    {
        $this->errors->add($error);
    }

    protected function hasErrors()
    {
        return $this->errors->has();
    }

    protected function getErrors()
    {
        return $this->errors->getList();
    }

    protected function validUser()
    {
        $u = $this->validateToken();
        return $u;
    }

    protected function getLoggedInUser()
    {
        try{
            $u = $this->validateToken();
            return $u;
        } catch (Exception $e) {
            return false;
        }
    }

    protected function validateLandLordGroup()
    {
        $u = $this->validateToken();

        if (!$u->isLandLord() && !$u->isSuperUser()) {
            throw new Exception('Unauthorized User');
        }
        return $u;
    }

    protected function verifyAPIKey()
    {
        /** @var HttpRequestHelper $httpRequestHelper */
        /** @var TextHelper $th */
        /** @var Concrete5_Helper_Validation_Token $vth */
        $httpRequestHelper  = Loader::helper('http_request');
        $vth                = new Concrete5_Helper_Validation_Token();
        $th                 = Loader::helper('text');

        try {
            $token = $httpRequestHelper->getAPIToken();
            if (!$token) {
                $token = $th->sanitize($this->request('token'));
            }
            if ($token) {
                if (!$vth->validate('api', $token)) {
                    $this->setErrorCode(self::UNATHORIZED);
                    $this->addError('Unauthorized token');
                    $this->sendResult();
                }
            } else {
                $this->setErrorCode(self::UNATHORIZED);
                $this->addError('Unauthorized request');
                $this->sendResult();
            }
        }
        catch (Exception $e) {
            throw new Exception($e->getMessage(), self::UNATHORIZED);
        }
    }

    public function sendResult($result = null)
    {
        if ($this->hasErrors()) {
            echo json_encode(['success' => false, 'errorCode' => $this->getErrorCode(), 'errors' => $this->getErrors()]);
            die();
        }

        echo json_encode(['success' => true, 'data' => $result]);
        die();
    }

}
