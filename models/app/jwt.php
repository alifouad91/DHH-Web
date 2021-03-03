<?php
defined('C5_EXECUTE') or die('Access Denied.');

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;
use Lcobucci\JWT\Signer\Hmac\Sha256;

class JWT
{

    const EXP_TIME = '1 year';

    protected $builder;

    public function __construct()
    {
        $this->builder   = new Builder();
        $now             = time();
        $expiration_time = strtotime('+' . self::EXP_TIME, $now);

        $this->builder->setIssuer(JWT_ISSUER)
            ->setAudience(JWT_AUDIENCE_APP)
            ->setId(JWT_ID)
            ->setIssuedAt($now)
            ->setNotBefore($now)
            ->setExpiration($expiration_time);
    }


    /**
     * Gets the signer for JWT
     *
     * @return Sha256
     */
    private static function getSigner()
    {
        return new Sha256();
    }


    /**
     * Sets the private claim
     *
     * You can pass user_id and/or username here
     *
     * @param $name
     * @param $value
     */
    public function setClaim($name, $value)
    {
        $this->builder->set($name, $value);
    }

    /**
     * Parse the string to Token Object so you can access
     * claims or validate it
     *
     * @param $token
     *
     * @return \Lcobucci\JWT\Token
     */
    public static function parse($token)
    {
        $parser = new Parser();

        return $parser->parse($token);
    }


    /**
     * Generates JSON Web Token
     *
     * Remember to set claims before generating token
     *
     * @return \Lcobucci\JWT\Token
     */
    public function generateToken()
    {
        return $this->sign()->getToken();
    }

    /**
     * Signs the token with server_secret
     *
     * @return Builder
     */
    protected function sign()
    {
        $signer = static::getSigner();

        return $this->getBuilder()->sign($signer, JWT_SERVER_SECRET);
    }


    /**
     * Validates token against the public claims
     * Checks the issuer, audience and JWT ID
     *
     * @param $token
     *
     * @return bool true - if valid, otherwise false
     */
    public static function validateClaims($token)
    {
        $token = static::parse($token);

        $issuer   = $token->getClaim('iss');
        $audience = $token->getClaim('aud');
        $jwt_id   = $token->getClaim('jti');

        $data = new ValidationData();
        $data->setIssuer($issuer);
        $data->setAudience($audience);
        $data->setId($jwt_id);

        return $token->validate($data);
    }


    /**
     * Verifies the token using the server_secret
     *
     * @param $token
     *
     * @return bool true - if verified, otherwise false
     */
    public static function verify($token)
    {
        $signer = static::getSigner();

        $token = static::parse($token);

        return $token->verify($signer, JWT_SERVER_SECRET);
    }


    /**
     * Validates token by validating claims and verifying the token
     *
     * @param $token
     *
     * @return bool true - if valid, otherwise false
     */
    public static function validate($token)
    {
        return static::validateClaims($token) && static::verify($token);
    }

    /**
     * @return Builder
     */
    public function getBuilder()
    {
        return $this->builder;
    }

}