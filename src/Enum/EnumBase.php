<?php

namespace Machuan\Base\Enum;

/**
 * Copyright (C), 2016-2018, Shall Buy Life info. Co., Ltd.
 * ClassName: EnumBase
 * Description: 枚举基类
 *
 * @author whoami
 * @Create Date    2021/8/3 14:43
 * @Update Date    2021/8/3 14:43 By whoami
 * @version
 *
 * @property $key
 * @property $value
 * @static $instance
 * @static  $ref
 */
class EnumBase
{
    private static $ref;
    private static $instance;
    public $key;
    public $value;

    /**
     * FunctionName: getInstance
     * Description: 获取实例
     * API: {}
     * Author: whoami
     * @return EnumBase
     */
    public static function getInstance(): EnumBase
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * FunctionName: getRef
     * Description: 获取反射
     * API: {}
     * Author: whoami
     * @return \ReflectionClass
     */
    private static function getRef(): \ReflectionClass
    {
        if (is_null(static::$ref)) {
            static::$ref = new \ReflectionClass(new static());
        }

        return static::$ref;
    }

    /**
     * FunctionName: getValues
     * Description: 获取所有值
     * API: {}
     * Author: whoami
     * @return array
     */
    public static function getValues(): array
    {
        $obj = static::getInstance();

        return array_values($obj->getConstants());
    }

    /**
     * FunctionName: getMessage
     * Description: 获取单个描述
     * API: {}
     * Author: whoami
     * @param $value
     * @return string|null
     */
    public static function getMessage($value): ?string
    {
        $obj = static::getInstance();

        return $obj->message($value);
    }


    /**
     * FunctionName: getMessages
     * Description: 获取多个描述
     * API: {}
     * Author: whoami
     * @param array $values
     * @return array
     */
    public static function getMessages(array $values = []): array
    {
        $obj = static::getInstance();
        $res = [];

        if ($values) {
            foreach ($values as $value) {
                $res[] = $obj->message($value);
            }
        } else {
            foreach ($obj->getConstants() as $value) {
                $res[] = $obj->message($value);
            }
        }

        return $res;
    }

    /**
     * FunctionName: getValue
     * Description: 获取值
     * API: {}
     * Author: whoami
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * FunctionName: message
     * Description: 获取对应值的描述
     * API: {}
     * Author: whoami
     * @param null $value
     * @return string|null
     */
    public function message($value = null): ?string
    {
        if (!is_null($value)) {
            if ($value instanceof $this) {
                $this->value = $value->value;
            } else {
                $this->value = $value;
            }
            foreach ($this->getConstants() as $k => $v) {
                if ($v === $this->value) {
                    $this->key = $k;
                }
            }
        }

        return $this->getComment();
    }

    /**
     * FunctionName: getComment
     * Description: 获取注释
     * API: {}
     * Author: whoami
     * @return string|null
     */
    private function getComment(): ?string
    {
        if (is_null($this->key)) {
            return null;
        }
        $pattern = "/@const\s*$this->key\s*(\S*)\s*\n/i";
        $doc = static::getRef()->getDocComment();

        preg_match($pattern, $doc, $res);

        return $res[1] ?? null;
    }

    /**
     * FunctionName: getConstants
     * Description: 获取所有常量信息
     * API: {}
     * Author: whoami
     * @return array
     */
    private function getConstants(): array
    {
        return static::getRef()->getConstants();
    }

    public static function __callStatic($name, $arguments)
    {
        return (static::getInstance())->$name(...$arguments);
    }

    public function __call($name, $arguments)
    {
        $constants = $this->getConstants();
        $this->key = null;
        $this->value = null;

        foreach ($constants as $key => $value) {
            if ($name == $key) {
                $this->key = $key;
                $this->value = $value;
            }
        }

        return $this;
    }

    public function __invoke($value): EnumBase
    {
        $obj = static::getInstance();

        $obj->value = $value;

        return $obj;
    }

    public function __toString()
    {
        return (string)$this->value;
    }
}
