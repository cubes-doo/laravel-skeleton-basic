<?php

namespace App\Http\Resources;

/*
 * Class
 *
 * PHP version 7.2
 *
 * @category   class
 * @copyright  2015-2018 Cubes d.o.o.
 * @license    GPL http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    GIT: 1.0.0
 */

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource as BaseResource;

/**
 * Description of JsonResource
 */
class Json extends BaseResource
{
    const STATUS_OK = 'ok';
    const STATUS_ERROR = 'error';
    
    private $locale;
    private $status = 'ok';
    private $message = '';
    private $httpStatus = null;
    
    
    /**
     * Create a new resource instance.
     *
     * @param mixed $resource
     */
    public function __construct($resource = null)
    {
        if ($resource === null) {
            $resource = [];
        }
        
        if (! is_object($resource) || ! method_exists($resource, 'toArray')) {
            $resource = collect($resource);
        }
        
        return parent::__construct($resource);
    }
    
    /**
     * Create new anonymous resource collection.
     *
     * @param mixed $resource
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public static function collection($resource)
    {
        return new JsonResourceCollection($resource, get_called_class());
    }
    
    /**
     * @return string The locale or app locale if not set
     */
    public function getLocale()
    {
        if (! $this->locale) {
            $this->locale = app()->getLocale();
        }
        
        return $this->locale;
    }
    
    /**
     * @param string $locale
     *
     * @return Json
     */
    public function setLocale(string $locale)
    {
        $this->locale = $locale;
        
        return $this;
    }
    
    /**
     * @param string $locale
     *
     * @return Json
     */
    public function withLocale($locale)
    {
        return $this->setLocale($locale);
    }
    
    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }
    
    /**
     * @param string $status
     *
     * @return Json
     */
    public function setStatus(string $status)
    {
        $this->status = $status;
        
        return $this;
    }
    
    /**
     * @return int
     */
    public function getHttpStatus()
    {
        return $this->httpStatus;
    }
    
    /**
     * @param string $status
     *
     * @return Json
     */
    public function setHttpStatus(int $httpStatus)
    {
        $this->httpStatus = $httpStatus;
        
        return $this;
    }
    
    /**
     * @param string $status
     *
     * @return Json
     */
    public function withStatus($status)
    {
        return $this->setStatus($status);
    }
    
    /**
     * @return bool
     */
    public function isStatusOk()
    {
        return self::STATUS_OK == $this->getStatus();
    }
    
    /**
     * @return bool
     */
    public function isStatusError()
    {
        return self::STATUS_ERROR == $this->getStatus();
    }
    
    /**
     * @param string $status
     *
     * @return Json
     */
    public function withHttpStatus($httpStatus)
    {
        return $this->setHttpStatus($httpStatus);
    }
    
    /**
     * @return bool
     */
    public function isHttpStatusOk()
    {
        return 200 == $this->getHttpStatus();
    }
    
    /**
     * @return bool
     */
    public function isHttpStatusError()
    {
        return 200 != $this->getHttpStatus();
    }
    
    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
    
    /**
     * @param string $message
     *
     * @return Json
     */
    public function setMessage(string $message)
    {
        $this->message = $message;
        
        return $this;
    }
    
    /**
     * @param string $message
     *
     * @return Json
     */
    public function withMessage($message)
    {
        return $this->setMessage($message);
    }
    
    /**
     * @param string $errorMessage
     *
     * @return Json
     */
    public function withError($errorMessage)
    {
        $this->withStatus(self::STATUS_ERROR)
            ->withHttpStatus(400)
            ->withMessage($errorMessage);
        
        return $this;
    }
    
    /**
     * @param string $successMessage
     *
     * @return Json
     */
    public function withSuccess($successMessage)
    {
        $this->withStatus(self::STATUS_OK)
            ->withMessage($successMessage);
        
        return $this;
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function with($request)
    {
        return [
            'locale' => $this->getLocale(),
            'message' => $this->getMessage(),
            'status' => $this->getStatus(),
        ];
    }
    
    /**
     * Customize the response for a request.
     *
     * @param \Illuminate\Http\Request      $request
     * @param \Illuminate\Http\JsonResponse $response
     */
    public function withResponse($request, $response)
    {
        if (!empty($this->getHttpStatus())) {
            $response->setStatusCode($this->getHttpStatus());
        }
    }
}
