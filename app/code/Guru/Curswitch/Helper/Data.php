<?php

namespace Guru\Curswitch\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
      protected $logger;
      protected $objectManager;
      protected $_curl;
      protected $_stepsSession;
      

      public function __construct(
          \Psr\Log\LoggerInterface $logger,
          \Magento\Framework\Session\SessionManagerInterface $stepsSession,
          \Magento\Framework\ObjectManagerInterface $objectManager,
          \Magento\Framework\HTTP\Client\Curl $curl) {
              $this->logger = $logger;
              $this->_curl = $curl;
              $this->_stepsSession = $stepsSession;
              $this->objectManager = $objectManager;
      }

      public function getCountryName() {
           $visitorIp = $this->getVisitorIp();
           $url = "http://api.ipstack.com/".$visitorIp."?access_key=6183e4e99c3408b338e7f6dcfc51889c&output=json&legacy=1";
	         $this->_curl->get($url);
           $response = json_decode($this->_curl->getBody(), true);
           //var_dump($response);
           $countryName = $response['country_name'];
           $stateName = $response['region_name'];
          return $countryName;
       }
        public function getCurswitchData(){
                    $EuroCountries = array('Austria','Belgium','Cyprus','Estonia','Finland','France','Germany','Greece','Ireland','Italy','Latvia','Lithuania','Luxembourg','Malta','Netherlands','Portugal','Slovakia','Slovenia','Spain');
  
                    if($this->getCountryName()=='United States'){
                           $CurSymbol = '$';
                    }
                    if($this->getCountryName()=='United Kingdom'){
                           $CurSymbol = '£';
                    }
                    if($this->getCountryName()=='Australia'){
                           $CurSymbol = 'A$';
                    }
                    if($this->getCountryName()=='India'){
                           $CurSymbol = '₹';
                    }
                    if($this->getCountryName()=='United Arab Emirates'){
                           $CurSymbol = 'د.إ';
                    }
                    if(in_array($this->getCountryName(),$EuroCountries)){
                           $CurSymbol = '€';
                    }
                     $CurrSwitch['symbol'] = $CurSymbol;
                     $CurrSwitch['country'] = $this->getCountryName();
                     $this->_stepsSession->start();
                     $this->_stepsSession->setCursymbol($CurSymbol);
                     $this->_stepsSession->setCurcountry($this->getCountryName());
                 return $CurrSwitch;
      }

       function getVisitorIp() {
           $remoteAddress = $this->objectManager->create('Magento\Framework\HTTP\PhpEnvironment\RemoteAddress');
           return $remoteAddress->getRemoteAddress();
       }
}

