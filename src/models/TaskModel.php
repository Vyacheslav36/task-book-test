<?php


namespace App\models;

use App\base\Model;
use App\helpers\ValidationHelper;

/**
 * Class TaskModel
 *
 * @property string $name
 * @property string $email
 * @property string $text
 * @property bool   $isCompleted
 *
 * @package App\models
 */
class TaskModel extends Model
{
    public $data = [];

    public function getName()
    {
        return $this->data && isset($this->data['name'])
            ? $this->data['name']
            : null;
    }

    public function getEmail()
    {
        return $this->data && isset($this->data['email'])
            ? $this->data['email']
            : null;
    }

    public function getText()
    {
        return $this->data && isset($this->data['text'])
            ? $this->data['text']
            : null;
    }

    public function getIsCompleted()
    {
        return $this->data && isset($this->data['is_completed'])
            ? $this->data['is_completed']
            : null;
    }

    public function getIsEdited()
    {
        return $this->data && isset($this->data['is_edited'])
            ? $this->data['is_edited']
            : null;
    }

    public function setName($value)
    {
        if (!ValidationHelper::checkOnRequired($value)) {
            $this->errors[] = 'Name is required';
        }
        $this->data['name'] = ValidationHelper::textFilter($value);
    }

    public function setEmail($value)
    {
        if (!ValidationHelper::checkOnRequired($value)) {
            $this->errors[] = 'Email is required';
        } else if (!ValidationHelper::checkOnEmail($value)) {
            $this->errors[] = 'Incorrect email';
        }
        $this->data['email'] = ValidationHelper::textFilter($value);
    }

    public function setText($value)
    {
        if (!ValidationHelper::checkOnRequired($value)) {
            $this->errors[] = 'Text is required';
        }
        $this->data['text'] = ValidationHelper::textFilter($value);
    }

    public function setIsCompleted($value)
    {
        $this->data['is_completed'] = $value;
    }

    public function setIsEdited($value)
    {
        $this->data['is_edited'] = $value;
    }
}