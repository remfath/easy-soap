<?php

namespace Remfath\EasySoap;

use SoapClient;
use Exception;

class Client
{
    private $wsdl;
    private $client;

    /**
     * Client constructor.
     *
     * @param $wsdl
     */
    public function __construct($wsdl)
    {
        $this->wsdl = $wsdl;
        $this->client = new SoapClient($wsdl);
    }

    /**
     * @return SoapClient
     */
    public function getClient(): SoapClient
    {
        return $this->client;
    }

    /**
     * @return array
     */
    public function getMethods()
    {
        $data = [];
        $funcs = $this->getFuncs();
        $types = $this->getTypes();
        foreach($funcs as $funcName => $funcInfo) {
            $funcPrams = $types[$funcInfo['param']] ?? [];
            $data[$funcName] = $funcPrams;
        }
        return $data;
    }

    /**
     * @param $methodName
     * @param $params
     * @return mixed
     */
    public function call($methodName, $params)
    {
        return $this->client->__call($methodName, $params);
    }


    /**
     * @param $methodName
     * @param $params
     * @return mixed
     * @throws Exception
     */
    public function mustCall($methodName, $params)
    {
        $funcs = $this->getMethods();
        if(!array_key_exists($methodName, $funcs)) {
            throw new Exception('Method ' . $methodName . ' is not exists.');
        }

        $funcParams = array_keys($funcs[$methodName]);
        $providedParams = array_keys($params);
        foreach($funcParams as $param) {
            if(!in_array($param, $providedParams)) {
                throw new Exception('Need params: ' . implode(', ', $funcParams));
            }
        }

        return $this->call($methodName, $params);
    }

    /**
     * @return array
     */
    private function getFuncs()
    {
        $funcs = $this->client->__getFunctions();
        $result = [];
        foreach($funcs as $func) {
            $exp1 = explode(' ', $func);
            $responseType = $exp1[0];
            $exp2 = explode('(', $exp1[1]);
            $methodName = $exp2[0];
            $paramType = $exp2[1];
            $result[$methodName] = [
                'param'    => $paramType,
                'response' => $responseType,
            ];
        }
        return $result;
    }

    /**
     * @return array
     */
    private function getTypes()
    {
        $types = $this->client->__getTypes();
        $result = [];
        foreach($types as $type) {
            $type = str_replace("\n", "", $type);
            $exp1 = explode('{', $type);
            $paramType = trim(explode(' ', $exp1[0])[1]);
            $exp2 = explode('}', $exp1[1]);
            $params = explode(';', $exp2[0]);
            $paramList = [];
            foreach($params as $param) {
                if($param == "") {
                    continue;
                }
                $exp3 = explode(' ', trim($param));
                $type = $exp3[0];
                $name = $exp3[1];
                $paramList[$name] = $type;
            }
            $result[$paramType] = $paramList;
        }
        return $result;
    }

}