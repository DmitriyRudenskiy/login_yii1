<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class MyUserIdentity extends CBaseUserIdentity
{
    protected  $id;
    protected  $name;


    public function authenticate()
    {
        return true;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * Устанавливаем номер пользователя.
     *
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        if ($id > 0) {
            $this->id = (int)$id;
        }

        return $this;
    }

    /**
     * Устанавливаем имя пользователя.
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        if (!empty($name)) {
            $this->name = $name;
        }

        return $this;
    }
}