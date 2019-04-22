<?php

/**
 * Class
 *
 * PHP version 7.2
 *
 * @category   class
 *
 * @copyright  2015-2018 Cubes d.o.o.
 * @license    GPL http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @category   Class
 *
 * @copyright  2015-2018 Cubes d.o.o.
 */
class JsonResourceCollection extends AnonymousResourceCollection
{
    const STATUS_OK = 'ok';
    const STATUS_ERROR = 'error';
    
    protected $locale;
    protected $status = 'ok';
    protected $message = '';
    
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
     * @param \Illuminate\Http\Request  $request
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
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\JsonResponse  $response
     *
     * @return void
     */
    public function withResponse($request, $response)
    {
        if (! $this->isStatusOk()) {
            //Set status code to 400 if error is occured
            $response->setStatusCode(400);
        }
    }
}
