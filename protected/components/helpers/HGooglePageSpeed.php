<?php
/**
 * Google PageSpeed Insights helper
 * 
 * @link https://developers.google.com/speed/pagespeed/insights/
 */
class HGooglePageSpeed 
{
    /**
     * onBeginRequest
     */
	public function onBeginRequest()
	{
		ob_start(function($buffer) {
			if(YII_DEBUG) return $buffer;
            return preg_replace("/(\s)+([^\n])/um", '$1$2', $buffer);
        });
        return true;
	}
    
    /**
     * onEndRequest
     */
    public function onEndRequest()
    {
        ob_end_flush();
        return true;
    }
}